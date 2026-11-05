<?php
/**
 * SkinCheck - Email Service (Powered by Resend API)
 * Handles transactional emails, OTP verification codes, and security notices.
 */

class EmailService {

    private string $apiKey;
    private string $fromEmail;
    private const RESEND_ENDPOINT = 'https://api.resend.com/emails';

    public function __construct(?string $apiKey = null, ?string $fromEmail = null) {
        $this->apiKey = $apiKey ?: (Setting::getVal('resend_api_key') ?: (defined('RESEND_API_KEY') ? RESEND_API_KEY : ''));
        $this->fromEmail = $fromEmail ?: (Setting::getVal('resend_from_email') ?: (defined('RESEND_FROM_EMAIL') ? RESEND_FROM_EMAIL : 'SkinCheck <onboarding@resend.dev>'));
    }

    /**
     * Send an email via Resend API
     * @param string|array $to
     * @param string $subject
     * @param string $htmlContent
     * @param string|null $textContent
     * @return array ['success' => bool, 'id' => string|null, 'error' => string|null]
     */
    public function send($to, string $subject, string $htmlContent, ?string $textContent = null): array {
        if (empty($this->apiKey)) {
            $err = 'Resend API Key is not configured in system.';
            error_log('[EmailService Error] ' . $err);
            return ['success' => false, 'id' => null, 'error' => $err];
        }

        $recipients = is_array($to) ? array_values($to) : [$to];

        $payload = [
            'from'    => $this->fromEmail,
            'to'      => $recipients,
            'subject' => $subject,
            'html'    => $htmlContent,
        ];

        if (!empty($textContent)) {
            $payload['text'] = $textContent;
        }

        $jsonPayload = json_encode($payload);

        $ch = curl_init(self::RESEND_ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonPayload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonPayload),
            ],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            $err = 'Gagal menghubungi server Resend: ' . $curlErr;
            error_log('[EmailService Error] ' . $err);
            return ['success' => false, 'id' => null, 'error' => $err];
        }

        $decoded = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            $msgId = $decoded['id'] ?? null;
            error_log("[EmailService Success] Resend sent email to " . implode(', ', $recipients) . " (ID: {$msgId})");
            return ['success' => true, 'id' => $msgId, 'error' => null];
        }

        $errorMsg = $decoded['message'] ?? ($decoded['name'] ?? ('HTTP ' . $httpCode . ' error'));
        error_log("[EmailService Failed] Resend HTTP {$httpCode}: {$errorMsg}");
        return ['success' => false, 'id' => null, 'error' => $errorMsg];
    }

    /**
     * Send OTP Verification Email with modern luxury clinical branding
     * @param string $email
     * @param string $name
     * @param string $otp
     * @return array
     */
    /**
     * Send OTP Verification Email with modern luxury clinical branding
     * @param string $email
     * @param string $name
     * @param string $otp
     * @param string|null $verifyLink Direct verification link
     * @return array
     */
    public function sendOtpEmail(string $email, string $name, string $otp, ?string $verifyLink = null): array {
        $subject = "Kode OTP Verifikasi Registrasi - SkinCheck";
        
        $linkButtonHtml = "";
        if (!empty($verifyLink)) {
            $linkButtonHtml = "
            <div style='text-align:center;margin:24px 0 10px;'>
                <a href='" . htmlspecialchars($verifyLink) . "' style='background:linear-gradient(135deg, #dfc68b 0%, #c4a968 100%);color:#05120f;padding:13px 30px;text-decoration:none;border-radius:999px;font-weight:bold;font-size:14px;display:inline-block;box-shadow:0 4px 16px rgba(191,161,95,0.35);'>
                    🔗 Verifikasi Otomatis Melalui Tautan Ini
                </a>
            </div>
            <p style='margin:0 0 18px;text-align:center;font-size:12px;color:#94a3b8;'>
                atau masukkan 6 digit angka kode OTP di bawah secara manual:
            </p>
            ";
        }

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$subject}</title>
        </head>
        <body style='margin:0;padding:0;background-color:#081512;font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Helvetica,Arial,sans-serif;'>
            <table width='100%' border='0' cellspacing='0' cellpadding='0' style='background-color:#081512;padding:40px 15px;'>
                <tr>
                    <td align='center'>
                        <table width='100%' border='0' cellspacing='0' cellpadding='0' style='max-width:520px;background:#0d2822;border:1px solid rgba(223,198,139,0.25);border-radius:16px;overflow:hidden;box-shadow:0 12px 36px rgba(0,0,0,0.4);'>
                            <!-- Header / Logo -->
                            <tr>
                                <td style='padding:32px 32px 20px;text-align:center;background:linear-gradient(135deg, #143c30 0%, #0d2822 100%);border-bottom:1px solid rgba(255,255,255,0.06);'>
                                    <div style='display:inline-block;margin-bottom:8px;'>
                                        <span style='font-size:24px;font-weight:bold;letter-spacing:1px;color:#ffffff;'>Skin<span style='color:#dfc68b;'>Check</span></span>
                                    </div>
                                    <p style='margin:0;font-size:13px;color:#c9ded5;letter-spacing:0.5px;'>Clinical AI Facial & Skin Health Platform</p>
                                </td>
                            </tr>
                            <!-- Body Content -->
                            <tr>
                                <td style='padding:32px;color:#ffffff;'>
                                    <h3 style='margin:0 0 12px;font-size:18px;font-weight:600;color:#ffffff;'>Halo, " . htmlspecialchars($name) . " 👋</h3>
                                    <p style='margin:0 0 20px;font-size:14px;line-height:1.6;color:#c9ded5;'>
                                        Terima kasih telah mendaftar di <strong>SkinCheck</strong>. Untuk mengaktifkan akun Anda, Anda dapat langsung mengklik tombol verifikasi cepat atau memasukkan kode OTP di bawah ini:
                                    </p>
                                    
                                    {$linkButtonHtml}

                                    <!-- OTP Code Box -->
                                    <div style='text-align:center;margin:20px 0;'>
                                        <div style='display:inline-block;padding:16px 36px;background:rgba(223,198,139,0.12);border:2px dashed #dfc68b;border-radius:12px;'>
                                            <span style='font-family:monospace,Consolas,Courier;font-size:36px;font-weight:bold;letter-spacing:10px;color:#dfc68b;text-shadow:0 0 12px rgba(223,198,139,0.3);'>
                                                {$otp}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Expiry Warning -->
                                    <div style='background:rgba(255,255,255,0.04);border-radius:10px;padding:14px 18px;margin-bottom:24px;border-left:3px solid #dfc68b;'>
                                        <p style='margin:0;font-size:13px;line-height:1.5;color:#c9ded5;'>
                                            <strong style='color:#dfc68b;'>⏳ Masa Berlaku:</strong> Tautan & kode OTP ini hanya berlaku selama <strong>5 menit</strong>. Jangan bagikan kepada siapa pun demi keamanan akun Anda.
                                        </p>
                                    </div>

                                    <p style='margin:0;font-size:12px;line-height:1.5;color:#94a3b8;'>
                                        Jika Anda tidak pernah merasa meminta pendaftaran akun di platform SkinCheck, silakan abaikan email ini dengan aman.
                                    </p>
                                </td>
                            </tr>
                            <!-- Footer -->
                            <tr>
                                <td style='padding:20px 32px;background:#081512;text-align:center;border-top:1px solid rgba(255,255,255,0.06);'>
                                    <p style='margin:0;font-size:11px;color:#64748b;'>
                                        © " . date('Y') . " SkinCheck Platform. Hak Cipta Dilindungi Undang-Undang.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";

        $text = "Halo {$name},\n\nKode OTP verifikasi pendaftaran SkinCheck Anda adalah: {$otp}\n\n";
        if (!empty($verifyLink)) {
            $text .= "Atau klik tautan ini untuk verifikasi langsung:\n{$verifyLink}\n\n";
        }
        $text .= "Kode ini berlaku selama 5 menit. Jangan bagikan kepada siapa pun demi keamanan akun Anda.";

        return $this->send($email, $subject, $html, $text);
    }

    /**
     * Send Password Reset Link Email
     * @param string $email
     * @param string $name
     * @param string $resetLink
     * @return array
     */
    public function sendPasswordResetEmail(string $email, string $name, string $resetLink): array {
        $subject = "Permintaan Reset Kata Sandi - SkinCheck";
        
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$subject}</title>
        </head>
        <body style='margin:0;padding:0;background-color:#081512;font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Helvetica,Arial,sans-serif;'>
            <table width='100%' border='0' cellspacing='0' cellpadding='0' style='background-color:#081512;padding:40px 15px;'>
                <tr>
                    <td align='center'>
                        <table width='100%' border='0' cellspacing='0' cellpadding='0' style='max-width:520px;background:#0d2822;border:1px solid rgba(223,198,139,0.25);border-radius:16px;overflow:hidden;box-shadow:0 12px 36px rgba(0,0,0,0.4);'>
                            <!-- Header / Logo -->
                            <tr>
                                <td style='padding:32px 32px 20px;text-align:center;background:linear-gradient(135deg, #143c30 0%, #0d2822 100%);border-bottom:1px solid rgba(255,255,255,0.06);'>
                                    <div style='display:inline-block;margin-bottom:8px;'>
                                        <span style='font-size:24px;font-weight:bold;letter-spacing:1px;color:#ffffff;'>Skin<span style='color:#dfc68b;'>Check</span></span>
                                    </div>
                                    <p style='margin:0;font-size:13px;color:#c9ded5;letter-spacing:0.5px;'>Clinical AI Facial & Skin Health Platform</p>
                                </td>
                            </tr>
                            <!-- Body Content -->
                            <tr>
                                <td style='padding:32px;color:#ffffff;'>
                                    <h3 style='margin:0 0 12px;font-size:18px;font-weight:600;color:#ffffff;'>Halo, " . htmlspecialchars($name) . " 🔑</h3>
                                    <p style='margin:0 0 20px;font-size:14px;line-height:1.6;color:#c9ded5;'>
                                        Kami menerima permintaan untuk mengatur ulang kata sandi akun SkinCheck Anda. Silakan klik tombol di bawah ini untuk membuat kata sandi baru:
                                    </p>
                                    
                                    <div style='text-align:center;margin:30px 0;'>
                                        <a href='" . htmlspecialchars($resetLink) . "' style='background:linear-gradient(135deg, #dfc68b 0%, #c4a968 100%);color:#05120f;padding:14px 34px;text-decoration:none;border-radius:999px;font-weight:bold;font-size:15px;display:inline-block;box-shadow:0 6px 20px rgba(191,161,95,0.4);'>
                                            🔒 Atur Ulang Kata Sandi Saya
                                        </a>
                                    </div>

                                    <!-- Expiry Warning -->
                                    <div style='background:rgba(255,255,255,0.04);border-radius:10px;padding:14px 18px;margin-bottom:24px;border-left:3px solid #dfc68b;'>
                                        <p style='margin:0;font-size:13px;line-height:1.5;color:#c9ded5;'>
                                            <strong style='color:#dfc68b;'>⏳ Masa Berlaku:</strong> Tautan ini hanya berlaku selama <strong>15 menit</strong>. Jangan bagikan tautan ini kepada siapa pun.
                                        </p>
                                    </div>

                                    <p style='margin:0;font-size:12px;line-height:1.5;color:#94a3b8;'>
                                        Jika tombol di atas tidak berfungsi, salin dan tempelkan tautan berikut ke browser Anda:<br>
                                        <a href='" . htmlspecialchars($resetLink) . "' style='color:#dfc68b;word-break:break-all;font-size:11px;'>" . htmlspecialchars($resetLink) . "</a>
                                    </p>
                                    <hr style='border:none;border-top:1px solid rgba(255,255,255,0.08);margin:20px 0;'>
                                    <p style='margin:0;font-size:12px;line-height:1.5;color:#94a3b8;'>
                                        Jika Anda tidak pernah meminta pengaturan ulang kata sandi, akun Anda tetap aman dan Anda dapat mengabaikan email ini.
                                    </p>
                                </td>
                            </tr>
                            <!-- Footer -->
                            <tr>
                                <td style='padding:20px 32px;background:#081512;text-align:center;border-top:1px solid rgba(255,255,255,0.06);'>
                                    <p style='margin:0;font-size:11px;color:#64748b;'>
                                        © " . date('Y') . " SkinCheck Platform. Hak Cipta Dilindungi Undang-Undang.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";

        $text = "Halo {$name},\n\nKami menerima permintaan untuk mengatur ulang kata sandi akun SkinCheck Anda.\n\nKlik tautan berikut untuk membuat kata sandi baru:\n{$resetLink}\n\nTautan ini berlaku selama 15 menit. Jika bukan Anda, abaikan pesan ini.";

        return $this->send($email, $subject, $html, $text);
    }
}
