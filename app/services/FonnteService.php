<?php
/**
 * SkinCheck - Fonnte WhatsApp Gateway Service
 * Handles transactional OTP verification codes and alerts via Fonnte WhatsApp API.
 */

class FonnteService {
    private string $token;
    private string $apiUrl;

    public function __construct(?string $token = null) {
        $this->token = trim($token ?: (string)Setting::getVal('fonnte_token', 'mu2K7LxWSs8KNRLBqJ3v'));
        $this->apiUrl = 'https://api.fonnte.com';
    }

    /**
     * Set / override Fonnte token
     */
    public function setToken(string $token): void {
        $this->token = trim($token);
    }

    /**
     * Get active token
     */
    public function getToken(): string {
        return $this->token;
    }

    /**
     * Normalize Indonesian phone number (removes dashes, spaces, symbols).
     * Accepts formats like 0812..., +62812..., 62812...
     * @param string $phone
     * @return string
     */
    public function formatPhoneNumber(string $phone): string {
        // Strip non-digit characters except leading plus if any
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 628, keep it; if starts with 08, convert to 628 or keep as 08 (Fonnte accepts both with countryCode 62)
        if (str_starts_with($cleaned, '62')) {
            return $cleaned;
        } elseif (str_starts_with($cleaned, '0')) {
            return '62' . substr($cleaned, 1);
        }

        return $cleaned;
    }

    /**
     * Send WhatsApp Message via Fonnte API
     * @param string $target  Phone number
     * @param string $message Text content
     * @param array  $extra   Additional parameters (e.g. schedule, typing, etc.)
     * @return array ['success' => bool, 'message' => string, 'raw' => array]
     */
    public function sendWhatsApp(string $target, string $message, array $extra = []): array {
        $targetFormatted = $this->formatPhoneNumber($target);

        if (empty($this->token)) {
            return [
                'success' => false,
                'message' => 'Token Fonnte belum dikonfigurasi di pengaturan sistem.'
            ];
        }

        if (empty($targetFormatted)) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp tujuan tidak valid.'
            ];
        }

        $postData = array_merge([
            'target' => $targetFormatted,
            'message' => $message,
            'countryCode' => '62'
        ], $extra);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->apiUrl . '/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->token
            ],
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $res = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            error_log('Fonnte cURL error: ' . $err);
            return [
                'success' => false,
                'message' => 'Gagal menghubungi server WhatsApp Fonnte: ' . $err
            ];
        }

        $json = json_decode($res, true);
        if ($httpCode === 200 && !empty($json['status'])) {
            return [
                'success' => true,
                'message' => 'Pesan WhatsApp berhasil dikirim ke ' . $targetFormatted,
                'raw' => $json
            ];
        }

        $reason = $json['reason'] ?? $json['message'] ?? 'Pengiriman gagal dari gateway Fonnte.';
        error_log("Fonnte send failed: {$reason} (HTTP {$httpCode})");

        return [
            'success' => false,
            'message' => "Pengiriman WhatsApp gagal: {$reason}",
            'raw' => $json
        ];
    }

    /**
     * Send Transactional OTP via WhatsApp
     * @param string $target Phone number
     * @param string $name   User full name
     * @param string $otp    6-digit OTP code
     * @return array
     */
    public function sendOtp(string $target, string $name, string $otp): array {
        $expiryMinutes = (int)Setting::getVal('otp_expiry_minutes', 5);

        $message = "🛡️ *SkinCheck AI — Kode Verifikasi OTP*\n\n";
        $message .= "Halo *{$name}*,\n\n";
        $message .= "Terima kasih telah mendaftar di *SkinCheck Clinical Intelligence System*.\n";
        $message .= "Berikut adalah 6 digit kode One-Time Password (OTP) verifikasi akun Anda:\n\n";
        $message .= "👉 *{$otp}* 👈\n\n";
        $message .= "⏳ *Masa Berlaku:* {$expiryMinutes} Menit\n";
        $message .= "🔒 *Peringatan Keamanan:* Jangan berikan kode ini kepada siapa pun demi keamanan akun Anda.\n\n";
        $message .= "_Pesan ini dikirim otomatis oleh SkinCheck Security Gateway._";

        return $this->sendWhatsApp($target, $message);
    }

    /**
     * Get Device & Connection Status from Fonnte
     * @return array
     */
    public function getDeviceStatus(): array {
        if (empty($this->token)) {
            return ['success' => false, 'message' => 'Token Fonnte kosong.'];
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->apiUrl . '/device',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->token
            ],
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $res = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'message' => 'Gagal menghubungi Fonnte: ' . $err];
        }

        $json = json_decode($res, true);
        if ($httpCode === 200 && !empty($json['status'])) {
            return [
                'success' => true,
                'device' => $json['device'] ?? '',
                'name' => $json['name'] ?? '',
                'status' => $json['device_status'] ?? '',
                'quota' => $json['quota'] ?? 0,
                'package' => $json['package'] ?? '',
                'expired' => $json['expired'] ?? ''
            ];
        }

        $reason = $json['reason'] ?? 'Token Fonnte tidak valid atau tidak terdaftar.';
        return ['success' => false, 'message' => $reason];
    }
}
