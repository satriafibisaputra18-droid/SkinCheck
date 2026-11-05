<?php
/**
 * SkinCheck - Setting Model
 * Manages dynamic application configurations and external API keys stored in MySQL.
 */

class Setting extends Model {
    private static array $cache = [];
    private static bool $loaded = false;

    /**
     * Retrieve all settings grouped by setting_group.
     * @return array
     */
    public function getAllGrouped(): array {
        $this->db->query("SELECT * FROM settings ORDER BY setting_group ASC, id ASC");
        $results = $this->db->resultSet();

        $grouped = [];
        foreach ($results as $row) {
            $grouped[$row->setting_group][] = $row;
        }

        return $grouped;
    }

    /**
     * Retrieve all settings as associative array [key => value].
     * @return array
     */
    public function getAllAssoc(): array {
        $this->db->query("SELECT setting_key, setting_value FROM settings");
        $results = $this->db->resultSet();

        $assoc = [];
        foreach ($results as $row) {
            $assoc[$row->setting_key] = $row->setting_value;
        }

        return $assoc;
    }

    /**
     * Get single setting value by key.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null) {
        $this->db->query("SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1");
        $this->db->bind(':key', $key);
        $row = $this->db->single();

        return ($row && $row->setting_value !== null && $row->setting_value !== '') ? $row->setting_value : $default;
    }

    /**
     * Static helper to get setting value with in-memory request caching.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getVal(string $key, $default = null) {
        if (!self::$loaded) {
            try {
                $db = new Database();
                if ($db->isConnected()) {
                    $db->query("SELECT setting_key, setting_value FROM settings");
                    $rows = $db->resultSet();
                    foreach ($rows as $r) {
                        self::$cache[$r->setting_key] = $r->setting_value;
                    }
                    self::$loaded = true;
                }
            } catch (Throwable $e) {
                error_log("Failed to load settings cache: " . $e->getMessage());
            }
        }

        if (array_key_exists($key, self::$cache) && self::$cache[$key] !== null && self::$cache[$key] !== '') {
            return self::$cache[$key];
        }

        return $default;
    }

    /**
     * Update multiple settings at once.
     * @param array $settings [key => value]
     * @return bool
     */
    public function updateMultiple(array $settings): bool {
        try {
            foreach ($settings as $key => $value) {
                $this->db->query("UPDATE settings SET setting_value = :val, updated_at = NOW() WHERE setting_key = :key");
                $this->db->bind(':val', trim((string)$value));
                $this->db->bind(':key', $key);
                $this->db->execute();
            }

            // Invalidate static cache
            self::$cache = [];
            self::$loaded = false;

            // Sync to chatbot-server .env if related keys are present
            $this->syncEnvFile($settings);

            return true;
        } catch (Throwable $e) {
            error_log("Settings update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync AI keys into chatbot-server/.env so node server has persistent sync.
     * @param array $settings
     */
    private function syncEnvFile(array $settings): void {
        $envPath = APPROOT . '/../chatbot-server/.env';
        if (!file_exists($envPath)) {
            return;
        }

        $envContent = file_get_contents($envPath);
        if ($envContent === false) {
            return;
        }

        if (isset($settings['gemini_api_key']) && $settings['gemini_api_key'] !== '') {
            $envContent = preg_replace('/^GEMINI_API_KEY=.*/m', 'GEMINI_API_KEY=' . $settings['gemini_api_key'], $envContent);
        }
        if (isset($settings['gemini_primary_model']) && $settings['gemini_primary_model'] !== '') {
            $envContent = preg_replace('/^PRIMARY_MODEL=.*/m', 'PRIMARY_MODEL=' . $settings['gemini_primary_model'], $envContent);
        }
        if (isset($settings['gemini_fallback_models']) && $settings['gemini_fallback_models'] !== '') {
            $envContent = preg_replace('/^FALLBACK_MODELS=.*/m', 'FALLBACK_MODELS=' . $settings['gemini_fallback_models'], $envContent);
        }

        @file_put_contents($envPath, $envContent);
    }

    /**
     * Live test for Telegram Bot Token.
     * @param string $token
     * @return array
     */
    public function testTelegram(string $token): array {
        $token = trim($token);
        if (empty($token)) {
            return ['success' => false, 'message' => 'Token Telegram tidak boleh kosong.'];
        }

        $url = "https://api.telegram.org/bot{$token}/getMe";
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $res = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'message' => "Koneksi cURL gagal: {$err}"];
        }

        $json = json_decode($res, true);
        if ($httpCode === 200 && !empty($json['ok'])) {
            $bot = $json['result'] ?? [];
            return [
                'success' => true,
                'message' => "Koneksi Berhasil! Bot aktif: @" . ($bot['username'] ?? '') . " (" . ($bot['first_name'] ?? '') . ")"
            ];
        }

        $desc = $json['description'] ?? 'Token tidak valid atau tidak dikenali oleh Telegram.';
        return ['success' => false, 'message' => "Gagal terhubung: {$desc} (HTTP {$httpCode})"];
    }

    /**
     * Live test for Resend Email API Key.
     * @param string $apiKey
     * @return array
     */
    public function testResend(string $apiKey): array {
        $apiKey = trim($apiKey);
        if (empty($apiKey)) {
            return ['success' => false, 'message' => 'API Key Resend tidak boleh kosong.'];
        }

        // Test sending endpoint with empty payload to test key authorization without sending actual email
        $url = 'https://api.resend.com/emails';
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => '{}',
            CURLOPT_TIMEOUT => 8,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ],
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $res = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'message' => "Koneksi cURL gagal: {$err}"];
        }

        // HTTP 422 means authentication succeeded and reached validation layer
        if ($httpCode === 422 || $httpCode === 200) {
            return [
                'success' => true,
                'message' => 'Koneksi Berhasil! API Key Resend valid dan aktif untuk pengiriman email.'
            ];
        }

        $json = json_decode($res, true);
        $msg = $json['message'] ?? "HTTP {$httpCode} - Kunci API ditolak atau tidak memiliki akses.";
        return ['success' => false, 'message' => "Autentikasi Gagal: {$msg}"];
    }

    /**
     * Live test for Google Gemini API Key.
     * @param string $apiKey
     * @param string $model
     * @return array
     */
    public function testGemini(string $apiKey, string $model = 'gemini-3.8-flash'): array {
        $apiKey = trim($apiKey);
        $model = trim($model) ?: 'gemini-3.8-flash';

        if (empty($apiKey)) {
            return ['success' => false, 'message' => 'Gemini API Key tidak boleh kosong.'];
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
        $payload = json_encode([
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => 'Ping. Jawab 1 kata: OK']
                    ]
                ]
            ],
            'generationConfig' => [
                'maxOutputTokens' => 5,
                'temperature' => 0.1
            ]
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $res = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'message' => "Koneksi cURL gagal: {$err}"];
        }

        $json = json_decode($res, true);
        if ($httpCode === 200 && !empty($json['candidates'][0]['content']['parts'][0]['text'])) {
            $reply = trim($json['candidates'][0]['content']['parts'][0]['text']);
            return [
                'success' => true,
                'message' => "Koneksi Berhasil! Model '{$model}' merespon aktif: \"{$reply}\""
            ];
        }

        $errMsg = $json['error']['message'] ?? "HTTP {$httpCode} - API Key ditolak atau kuota habis.";
        return ['success' => false, 'message' => "Gemini API Error: {$errMsg}"];
    }
}
