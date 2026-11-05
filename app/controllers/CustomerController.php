<?php
/**
 * SkinCheck - Customer Controller
 * Handles all customer-facing pages and actions.
 * Every method enforces session authentication before execution.
 */

class CustomerController extends Controller {

    private $scanModel;
    private $userModel;

    public function __construct() {
        $this->requireCustomerAuth();
        $this->scanModel = $this->model('Scan');
        $this->userModel = $this->model('User');
    }

    // ─── Private Helpers ──────────────────────────────────────────────────

    /**
     * Ensure the visitor is an authenticated customer.
     * Redirects to login if not logged in or wrong role.
     */
    private function requireCustomerAuth() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }

    /**
     * Returns the current authenticated customer's ID.
     * @return int
     */
    private function currentUserId() {
        return (int)$_SESSION['user_id'];
    }

    // ─── Pages ────────────────────────────────────────────────────────────

    /**
     * Main customer dashboard.
     * Route: GET /customer/dashboard
     */
    public function dashboard() {
        $userId     = $this->currentUserId();
        $scans      = $this->scanModel->getScansByUserId($userId);
        $latestScan = !empty($scans) ? $scans[0] : null;
        $scanCount  = count($scans);

        // Take up to 5 most recent for the history widget
        $recentScans = array_slice($scans, 0, 5);

        $data = [
            'title'       => 'My Dashboard – SkinCheck',
            'pageHeading' => 'Dashboard',
            'latest_scan' => $latestScan,
            'recent_scans'=> $recentScans,
            'scan_count'  => $scanCount,
        ];

        $this->view('customer/dashboard', $data);
    }

    /**
     * Face scan page — delegates to ScanController.
     * Route: GET /customer/scanFace  (kept for backward-compat with sidebar links)
     */
    public function scanFace(): void {
        header('Location: ' . URLROOT . '/scan/faceScan');
        exit;
    }

    /**
     * Sunscreen check page — delegates to SunscreenController.
     * Route: GET /customer/sunscreenCheck
     */
    public function sunscreenCheck(): void {
        header('Location: ' . URLROOT . '/sunscreen/check');
        exit;
    }

    /**
     * AI Assistant placeholder page.
     * Route: GET /customer/aiAssistant
     */
    public function aiAssistant() {
        $data = [
            'title'       => 'AI Assistant – SkinCheck',
            'pageHeading' => 'AI Assistant',
        ];
        $this->view('customer/ai_assistant', $data);
    }

    /**
     * Full scan history listing.
     * Route: GET /customer/history
     */
    public function history() {
        $userId = $this->currentUserId();
        $scans  = $this->scanModel->getScansByUserId($userId);

        $data = [
            'title'       => 'Scan History – SkinCheck',
            'pageHeading' => 'Scan History',
            'scans'       => $scans,
        ];
        $this->view('customer/history', $data);
    }

    /**
     * Scan detail page.
     * Route: GET /customer/scanDetail/{id}
     * The scan is fetched WITH the user_id scope so customers can never
     * view another user's data even by guessing an ID.
     * @param int $scanId
     */
    public function scanDetail($scanId = null) {
        if (!$scanId || !is_numeric($scanId)) {
            header('Location: ' . URLROOT . '/customer/history');
            exit;
        }

        $userId = $this->currentUserId();
        $scan   = $this->scanModel->getScanById((int)$scanId, $userId);

        // If scan not found or doesn't belong to this user, redirect
        if (!$scan) {
            $_SESSION['flash_error'] = 'Scan record not found.';
            header('Location: ' . URLROOT . '/customer/history');
            exit;
        }

        $data = [
            'title'       => 'Scan Detail #' . $scanId . ' – SkinCheck',
            'pageHeading' => 'Scan Detail',
            'scan'        => $scan,
        ];
        $this->view('customer/scan_detail', $data);
    }

    /**
     * Customer profile page (view & edit name and profile image).
     * Route: GET/POST /customer/profile
     */
    public function profile() {
        $userId = $this->currentUserId();
        $user   = $this->userModel->getUserById($userId);

        if ($user && isset($user->profile_image)) {
            $_SESSION['user_avatar'] = $user->profile_image;
        }

        $data = [
            'title'       => 'My Profile – SkinCheck',
            'pageHeading' => 'My Profile',
            'user'        => $user,
            'name_err'    => '',
            'avatar_err'  => '',
            'success'     => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $avatarUpdated = false;

            // Handle Avatar Upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['profile_image'];

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $data['avatar_err'] = 'Gagal mengunggah foto profil (Error Code: ' . (int)$file['error'] . ').';
                } else {
                    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $file['tmp_name']);
                    finfo_close($finfo);

                    $maxBytes = 5 * 1024 * 1024; // 5 MB

                    if (!in_array($mimeType, $allowedMimes, true)) {
                        $data['avatar_err'] = 'Format file tidak valid. Harap unggah foto dengan format JPG, PNG, atau WebP.';
                    } elseif ($file['size'] > $maxBytes) {
                        $data['avatar_err'] = 'Ukuran foto terlalu besar. Maksimal 5 MB.';
                    } else {
                        $extMap = [
                            'image/jpeg' => 'jpg',
                            'image/png'  => 'png',
                            'image/webp' => 'webp',
                        ];
                        $extension = $extMap[$mimeType] ?? 'jpg';
                        $filename = 'avatar_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
                        $uploadDir = APPROOT . '/../public/uploads/avatars/';

                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        $destination = $uploadDir . $filename;

                        if (move_uploaded_file($file['tmp_name'], $destination)) {
                            // Clean up previous avatar if exists
                            if (!empty($user->profile_image)) {
                                $oldPath = APPROOT . '/../public/' . $user->profile_image;
                                if (file_exists($oldPath) && is_file($oldPath)) {
                                    @unlink($oldPath);
                                }
                            }

                            $relPath = 'uploads/avatars/' . $filename;
                            if ($this->userModel->updateAvatar($userId, $relPath)) {
                                $_SESSION['user_avatar'] = $relPath;
                                $avatarUpdated = true;
                            } else {
                                $data['avatar_err'] = 'Gagal menyimpan foto profil ke database.';
                            }
                        } else {
                            $data['avatar_err'] = 'Gagal memindahkan file ke direktori server.';
                        }
                    }
                }
            }

            // Handle Display Name Update
            $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
            $nameUpdated = false;

            if (!empty($name) && empty($data['avatar_err'])) {
                if (strlen($name) < 2) {
                    $data['name_err'] = 'Nama harus memiliki minimal 2 karakter.';
                } elseif (strlen($name) > 100) {
                    $data['name_err'] = 'Nama tidak boleh melebihi 100 karakter.';
                } else {
                    if ($this->userModel->updateProfile($userId, $name)) {
                        $_SESSION['user_name'] = $name;
                        $nameUpdated = true;
                    } else {
                        $data['name_err'] = 'Tidak dapat memperbarui nama profil. Silakan coba lagi.';
                    }
                }
            }

            // Set success feedback
            if ($avatarUpdated && $nameUpdated) {
                $data['success'] = 'Foto profil dan nama berhasil diperbarui!';
            } elseif ($avatarUpdated) {
                $data['success'] = 'Foto profil berhasil diperbarui!';
            } elseif ($nameUpdated) {
                $data['success'] = 'Profil berhasil diperbarui!';
            }

            // Refresh user object
            $user = $this->userModel->getUserById($userId);
            $data['user'] = $user;
        }

        $this->view('customer/profile', $data);
    }
}
