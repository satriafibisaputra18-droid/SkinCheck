<?php
/**
 * SkinCheck - Auth Controller
 * Handles customer authentication (Register, Login, and Logout).
 */

class AuthController extends Controller {
    private $userModel;
    private $emailService;
    private $telegramService;

    public function __construct() {
        // Instantiate the User model, Email service, and Telegram Bot service
        $this->userModel = $this->model('User');
        $this->emailService = new EmailService();
        $this->telegramService = new TelegramService();
    }

    /**
     * Customer Login page and post action.
     */
    public function login() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_role'])) {
            if ($_SESSION['user_role'] === 'admin') {
                header('Location: ' . URLROOT . '/admin/dashboard');
                exit;
            } else {
                header('Location: ' . URLROOT . '/customer/dashboard');
                exit;
            }
        }

        $data = [
            'title' => 'Login to SkinCheck',
            'email' => '',
            'password' => '',
            'email_err' => '',
            'password_err' => ''
        ];

        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize inputs
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data['email'] = trim($_POST['email'] ?? '');
            $data['password'] = trim($_POST['password'] ?? '');

            // Validate fields
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter your email.';
            }
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter your password.';
            }

            // Check if errors are empty
            if (empty($data['email_err']) && empty($data['password_err'])) {
                // Check and login user
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                if ($loggedInUser) {
                    // Create Session variables
                    $_SESSION['user_id'] = $loggedInUser->id;
                    $_SESSION['user_name'] = $loggedInUser->name;
                    $_SESSION['user_email'] = $loggedInUser->email;
                    $_SESSION['user_role'] = $loggedInUser->role;
                    $_SESSION['user_avatar'] = $loggedInUser->profile_image ?? null;
                    
                    // Route based on role
                    if ($loggedInUser->role === 'admin') {
                        header('Location: ' . URLROOT . '/admin/dashboard');
                    } else {
                        header('Location: ' . URLROOT . '/customer/dashboard');
                    }
                    exit;
                } else {
                    $data['password_err'] = 'Incorrect password or email credentials.';
                }
            }
        }

        $this->view('auth/login', $data);
    }

    /**
     * Customer Registration page and post action with OTP initiation.
     */
    public function register() {
        // If already logged in, redirect
        if (isset($_SESSION['user_role'])) {
            header('Location: ' . URLROOT . '/customer/dashboard');
            exit;
        }

        $data = [
            'title' => 'Register a SkinCheck Account',
            'name' => '',
            'email' => '',
            'password' => '',
            'confirm_password' => '',
            'name_err' => '',
            'email_err' => '',
            'password_err' => '',
            'confirm_password_err' => ''
        ];

        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize inputs
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data['name'] = trim($_POST['name'] ?? '');
            $data['email'] = trim($_POST['email'] ?? '');
            $data['password'] = trim($_POST['password'] ?? '');
            $data['confirm_password'] = trim($_POST['confirm_password'] ?? '');

            // Validate Name
            if (empty($data['name'])) {
                $data['name_err'] = 'Nama lengkap wajib diisi.';
            }

            // Validate Email
            if (empty($data['email'])) {
                $data['email_err'] = 'Alamat email wajib diisi.';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Format email tidak valid.';
            } elseif ($this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'Email ini sudah terdaftar. Silakan gunakan email lain atau langsung login.';
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'Password wajib diisi.';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password minimal terdiri dari 6 karakter.';
            }

            // Validate Confirm Password
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Konfirmasi password wajib diisi.';
            } else {
                if ($data['password'] !== $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Konfirmasi password tidak cocok.';
                }
            }

            // Check if errors are empty -> Generate OTP and route to verification
            if (empty($data['name_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                // Generate 6-digit random OTP
                $otpCode = (string)random_int(100000, 999999);
                // Generate secure random verification token for 1-click link
                $regToken = bin2hex(random_bytes(16));

                // Direct verification link
                $verifyLink = URLROOT . '/auth/verifyDirect?token=' . $regToken;

                $otpExpirySec = ((int)Setting::getVal('otp_expiry_minutes', 5)) * 60;
                $resendCooldownSec = (int)Setting::getVal('otp_resend_cooldown', 60);

                // Store registration data in session with expiration
                $_SESSION['pending_otp_registration'] = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'telegram_username' => null,
                    'telegram_chat_id' => null,
                    'password' => $data['password'],
                    'otp' => $otpCode,
                    'token' => $regToken,
                    'expires_at' => time() + $otpExpirySec,
                    'resend_available_at' => time() + $resendCooldownSec,
                    'attempts' => 0,
                    'created_at' => time(),
                    'otp_sent_to_tg' => false
                ];

                // Attempt sending OTP to email as well if email service is configured
                try {
                    $this->sendOtpEmail($data['email'], $data['name'], $otpCode);
                } catch (\Throwable $e) {
                    error_log('Failed to send OTP email: ' . $e->getMessage());
                }

                unset($_SESSION['otp_notice']);
                header('Location: ' . URLROOT . '/auth/verifyOtp');
                exit;
            }
        }

        $this->view('auth/register', $data);
    }

    /**
     * Customer OTP Verification page and submit action.
     * Route: GET /auth/verifyOtp and POST /auth/verifyOtp
     */
    public function verifyOtp() {
        // If already logged in, redirect
        if (isset($_SESSION['user_role'])) {
            header('Location: ' . URLROOT . '/customer/dashboard');
            exit;
        }

        // Must have pending registration in session
        if (empty($_SESSION['pending_otp_registration'])) {
            $_SESSION['register_error'] = 'Sesi verifikasi tidak ditemukan atau telah kedaluwarsa. Silakan isi form pendaftaran.';
            header('Location: ' . URLROOT . '/auth/register');
            exit;
        }

        $pending = &$_SESSION['pending_otp_registration'];

        $telegramBotLink = $this->telegramService->getBotStartLink($pending['token'] ?? '');
        $telegramWebLink = $this->telegramService->getBotWebLink($pending['token'] ?? '');
        $telegramAppLink = $this->telegramService->getBotAppProtocolLink($pending['token'] ?? '');

        $data = [
            'title' => 'Verifikasi Akun - SkinCheck',
            'email' => $pending['email'],
            'name' => $pending['name'],
            'token' => $pending['token'] ?? '',
            'telegram_bot_name' => $this->telegramService->getBotName(),
            'telegram_bot_link' => $telegramBotLink,
            'telegram_web_link' => $telegramWebLink,
            'telegram_app_link' => $telegramAppLink,
            'otp' => '',
            'otp_err' => '',
            'expires_in' => max(0, $pending['expires_at'] - time()),
            'can_resend_in' => max(0, $pending['resend_available_at'] - time())
        ];

        // Process OTP submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $otpSubmitted = '';
            if (isset($_POST['otp_digit']) && is_array($_POST['otp_digit'])) {
                $otpSubmitted = trim(implode('', $_POST['otp_digit']));
            } else {
                $otpSubmitted = trim($_POST['otp'] ?? '');
            }

            $data['otp'] = $otpSubmitted;

            // Check expiration
            if (time() > $pending['expires_at']) {
                $data['otp_err'] = 'Kode OTP telah kedaluwarsa. Silakan klik "Kirim Ulang OTP" di bawah.';
            } elseif (empty($otpSubmitted)) {
                $data['otp_err'] = 'Harap masukkan 6 digit kode OTP.';
            } elseif (strlen($otpSubmitted) !== 6 || !ctype_digit($otpSubmitted)) {
                $data['otp_err'] = 'Kode OTP harus berupa 6 digit angka.';
            } elseif ($pending['attempts'] >= 5) {
                $data['otp_err'] = 'Batas percobaan salah telah habis. Silakan klik "Kirim Ulang OTP".';
            } elseif ($otpSubmitted !== $pending['otp']) {
                $pending['attempts']++;
                $remaining = max(0, 5 - $pending['attempts']);
                $data['otp_err'] = "Kode OTP yang Anda masukkan salah. Sisa kesempatan: {$remaining} kali.";
            } else {
                // OTP VALID! Register user in MySQL
                $registerData = [
                    'name'              => $pending['name'],
                    'email'             => $pending['email'],
                    'telegram_chat_id'  => $pending['telegram_chat_id'] ?? null,
                    'telegram_username' => $pending['telegram_username'] ?? null,
                    'password'          => $pending['password']
                ];

                if ($this->userModel->register($registerData)) {
                    // Retrieve newly registered customer
                    $newUser = $this->userModel->findUserByEmail($pending['email']);
                    unset($_SESSION['pending_otp_registration']);

                    if ($newUser) {
                        // Auto-login customer
                        $_SESSION['user_id'] = $newUser->id;
                        $_SESSION['user_name'] = $newUser->name;
                        $_SESSION['user_email'] = $newUser->email;
                        $_SESSION['user_role'] = $newUser->role;
                        $_SESSION['user_avatar'] = $newUser->profile_image ?? null;
                        $_SESSION['flash_success'] = 'Selamat datang, ' . htmlspecialchars($newUser->name) . '! Pendaftaran dan verifikasi akun berhasil.';
                        header('Location: ' . URLROOT . '/customer/dashboard');
                    } else {
                        $_SESSION['register_success'] = 'Pendaftaran & verifikasi akun berhasil! Silakan masuk ke akun Anda.';
                        header('Location: ' . URLROOT . '/auth/login');
                    }
                    exit;
                } else {
                    $data['otp_err'] = 'Terjadi kesalahan sistem saat menyimpan akun. Silakan coba kembali.';
                }
            }
        }

        $this->view('auth/verify_otp', $data);
    }

    /**
     * Direct link handler - redirects to manual OTP entry page
     * Route: GET /auth/verifyDirect?token={token}
     */
    public function verifyDirect() {
        if (empty($_SESSION['pending_otp_registration'])) {
            $_SESSION['register_error'] = 'Sesi pendaftaran tidak ditemukan atau telah kedaluwarsa.';
            header('Location: ' . URLROOT . '/auth/register');
            exit;
        }

        $_SESSION['otp_notice'] = 'Silakan masukkan 6 digit kode OTP yang Anda terima pada kotak verifikasi di bawah ini.';
        header('Location: ' . URLROOT . '/auth/verifyOtp');
        exit;
    }

    /**
     * AJAX Endpoint for Real-time Polling: Check if user tapped START in Telegram
     * Route: GET /auth/checkOtpStatus
     */
    public function checkOtpStatus() {
        header('Content-Type: application/json');

        if (isset($_SESSION['user_id'])) {
            echo json_encode([
                'verified' => true,
                'redirect' => URLROOT . '/customer/dashboard'
            ]);
            exit;
        }

        if (empty($_SESSION['pending_otp_registration'])) {
            echo json_encode(['verified' => false, 'otp_sent' => false]);
            exit;
        }

        $pending = &$_SESSION['pending_otp_registration'];
        $token = $pending['token'] ?? '';

        // If OTP was already sent to Telegram, notify frontend
        if (!empty($pending['otp_sent_to_tg'])) {
            echo json_encode([
                'verified' => false,
                'otp_sent' => true,
                'message' => 'Kode OTP telah terkirim ke Telegram Anda. Silakan ketik 6 digit kode pada kotak di bawah.'
            ]);
            exit;
        }

        if (!empty($token)) {
            // Check Telegram getUpdates for /start REG_{token}
            $tgUser = $this->telegramService->checkStartToken($token);

            if ($tgUser && !empty($tgUser['chat_id'])) {
                $pending['telegram_chat_id'] = $tgUser['chat_id'];
                $pending['telegram_username'] = $tgUser['username'] ?? null;
                $pending['otp_sent_to_tg'] = true;

                // Send 6-digit OTP code to Telegram (manual entry only, no auto-register!)
                $this->telegramService->sendOtp($tgUser['chat_id'], $pending['name'], $pending['otp']);

                echo json_encode([
                    'verified' => false,
                    'otp_sent' => true,
                    'message' => 'Kode OTP berhasil dikirimkan ke Telegram! Silakan ketik 6 digit kode di bawah.'
                ]);
                exit;
            }
        }

        echo json_encode(['verified' => false, 'otp_sent' => false]);
        exit;
    }

    /**
     * Resend a fresh OTP code.
     * Route: GET or POST /auth/resendOtp
     */
    public function resendOtp() {
        if (empty($_SESSION['pending_otp_registration'])) {
            header('Location: ' . URLROOT . '/auth/register');
            exit;
        }

        $pending = &$_SESSION['pending_otp_registration'];

        // Enforce cooldown
        if (time() < $pending['resend_available_at']) {
            $waitSec = $pending['resend_available_at'] - time();
            $_SESSION['otp_error'] = "Harap tunggu {$waitSec} detik sebelum meminta kode baru.";
            header('Location: ' . URLROOT . '/auth/verifyOtp');
            exit;
        }

        // Generate fresh OTP & Token
        $newOtp = (string)random_int(100000, 999999);
        $newToken = bin2hex(random_bytes(16));

        $otpExpirySec = ((int)Setting::getVal('otp_expiry_minutes', 5)) * 60;
        $resendCooldownSec = (int)Setting::getVal('otp_resend_cooldown', 60);

        $pending['otp'] = $newOtp;
        $pending['token'] = $newToken;
        $pending['expires_at'] = time() + $otpExpirySec;
        $pending['resend_available_at'] = time() + $resendCooldownSec;
        $pending['attempts'] = 0;
        $pending['otp_sent_to_tg'] = false;

        // If telegram chat ID is already known, send to Telegram immediately
        if (!empty($pending['telegram_chat_id'])) {
            $this->telegramService->sendOtp($pending['telegram_chat_id'], $pending['name'], $newOtp);
            $pending['otp_sent_to_tg'] = true;
        }

        // Also try resending to email if configured
        try {
            $this->sendOtpEmail($pending['email'], $pending['name'], $newOtp);
        } catch (\Throwable $e) {
            error_log('Failed to resend OTP email: ' . $e->getMessage());
        }

        $_SESSION['otp_notice'] = 'Kode OTP baru telah diperbarui. Silakan periksa Telegram / Email Anda lalu masukkan 6 digit kode di bawah.';
        unset($_SESSION['otp_error']);

        header('Location: ' . URLROOT . '/auth/verifyOtp');
        exit;
    }

    /**
     * Cancel pending OTP registration and return to registration page.
     * Route: GET /auth/cancelRegistration
     */
    public function cancelRegistration() {
        unset($_SESSION['pending_otp_registration']);
        unset($_SESSION['otp_notice']);
        unset($_SESSION['otp_error']);
        header('Location: ' . URLROOT . '/auth/register');
        exit;
    }

    /**
     * Forgot Password request page and submission.
     * Route: GET /auth/forgotPassword and POST /auth/forgotPassword
     */
    public function forgotPassword() {
        if (isset($_SESSION['user_role'])) {
            header('Location: ' . URLROOT . '/customer/dashboard');
            exit;
        }

        $data = [
            'title' => 'Lupa Kata Sandi - SkinCheck',
            'email' => '',
            'email_err' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data['email'] = trim($_POST['email'] ?? '');

            if (empty($data['email'])) {
                $data['email_err'] = 'Silakan masukkan alamat email Anda.';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Format email tidak valid.';
            } else {
                $user = $this->userModel->findUserByEmail($data['email']);

                if ($user) {
                    // Generate 64-character secure random token
                    $rawToken = bin2hex(random_bytes(32));
                    $tokenHash = hash('sha256', $rawToken);
                    $expiresAt = date('Y-m-d H:i:s', time() + 900); // 15 menit

                    // Store token in database
                    $this->userModel->setPasswordResetToken($user->email, $tokenHash, $expiresAt);

                    // Build Reset Link
                    $resetLink = URLROOT . '/auth/resetPassword?token=' . $rawToken . '&email=' . urlencode($user->email);

                    // Send Email via Resend
                    $this->emailService->sendPasswordResetEmail($user->email, $user->name, $resetLink);

                    // If user has linked Telegram chat ID, also send reset link to Telegram!
                    if (!empty($user->telegram_chat_id)) {
                        $this->telegramService->sendPasswordResetLink($user->telegram_chat_id, $user->name, $resetLink);
                    }

                    $_SESSION['flash_success'] = 'Tautan untuk mereset kata sandi telah dikirimkan ke email Anda (dan ke Telegram jika terhubung). Silakan periksa kotak masuk Anda.';
                    header('Location: ' . URLROOT . '/auth/login');
                    exit;
                } else {
                    $data['email_err'] = 'Alamat email tersebut tidak terdaftar di sistem kami.';
                }
            }
        }

        $this->view('auth/forgot_password', $data);
    }

    /**
     * Reset Password page and submission.
     * Route: GET /auth/resetPassword and POST /auth/resetPassword
     */
    public function resetPassword() {
        if (isset($_SESSION['user_role'])) {
            header('Location: ' . URLROOT . '/customer/dashboard');
            exit;
        }

        $token = trim($_GET['token'] ?? ($_POST['token'] ?? ''));
        $email = trim($_GET['email'] ?? ($_POST['email'] ?? ''));

        if (empty($token) || empty($email)) {
            $_SESSION['flash_error'] = 'Tautan reset kata sandi tidak valid atau tidak lengkap.';
            header('Location: ' . URLROOT . '/auth/forgotPassword');
            exit;
        }

        // Validate token in database
        $tokenHash = hash('sha256', $token);
        $user = $this->userModel->findUserByResetToken($email, $tokenHash);

        if (!$user) {
            $_SESSION['flash_error'] = 'Tautan reset kata sandi telah kedaluwarsa atau sudah digunakan. Silakan minta tautan baru.';
            header('Location: ' . URLROOT . '/auth/forgotPassword');
            exit;
        }

        $data = [
            'title' => 'Atur Kata Sandi Baru - SkinCheck',
            'token' => $token,
            'email' => $email,
            'password' => '',
            'confirm_password' => '',
            'password_err' => '',
            'confirm_password_err' => '',
            'general_err' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data['password'] = trim($_POST['password'] ?? '');
            $data['confirm_password'] = trim($_POST['confirm_password'] ?? '');

            if (empty($data['password'])) {
                $data['password_err'] = 'Kata sandi baru wajib diisi.';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Kata sandi minimal terdiri dari 6 karakter.';
            }

            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Konfirmasi kata sandi wajib diisi.';
            } elseif ($data['password'] !== $data['confirm_password']) {
                $data['confirm_password_err'] = 'Konfirmasi kata sandi tidak cocok.';
            }

            if (empty($data['password_err']) && empty($data['confirm_password_err'])) {
                if ($this->userModel->resetPasswordWithToken($email, $data['password'])) {
                    $_SESSION['flash_success'] = 'Kata sandi Anda berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.';
                    header('Location: ' . URLROOT . '/auth/login');
                    exit;
                } else {
                    $data['general_err'] = 'Terjadi kesalahan sistem saat memperbarui kata sandi. Silakan coba kembali.';
                }
            }
        }

        $this->view('auth/reset_password', $data);
    }

    /**
     * Send formatted HTML email containing the OTP code via Resend API.
     * @return array ['success' => bool, 'id' => string|null, 'error' => string|null]
     */
    private function sendOtpEmail(string $email, string $name, string $otp, ?string $verifyLink = null): array {
        if (!isset($this->emailService)) {
            $this->emailService = new EmailService();
        }
        return $this->emailService->sendOtpEmail($email, $name, $otp, $verifyLink);
    }

    /**
     * Terminate user session and log out.
     */
    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);
        unset($_SESSION['user_avatar']);
        
        session_destroy();
        header('Location: ' . URLROOT . '/auth/login');
        exit;
    }
}
