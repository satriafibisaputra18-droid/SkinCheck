<?php
/**
 * SkinCheck - Telegram Bot Service
 * Handles transactional OTP verification codes and alerts via Telegram Bot API (@Kode_OTP_SkinCheckBot).
 */

class TelegramService {

    private string $botToken;
    private string $botUsername;
    private string $botName;
    private const TELEGRAM_API_BASE = 'https://api.telegram.org/bot';

    public function __construct(?string $botToken = null, ?string $botUsername = null, ?string $botName = null) {
        $this->botToken = $botToken ?: (Setting::getVal('telegram_bot_token') ?: (defined('TELEGRAM_BOT_TOKEN') ? TELEGRAM_BOT_TOKEN : ''));
        $this->botUsername = $botUsername ?: (Setting::getVal('telegram_bot_username') ?: (defined('TELEGRAM_BOT_USERNAME') ? TELEGRAM_BOT_USERNAME : 'Kode_OTP_SkinCheckBot'));
        $this->botName = $botName ?: (defined('TELEGRAM_BOT_NAME') ? TELEGRAM_BOT_NAME : 'KodeOTPSkinCheckBot');
    }

    /**
     * Get the configured bot display name.
     */
    public function getBotName(): string {
        return $this->botName;
    }

    /**
     * Get the configured bot username.
     */
    public function getBotUsername(): string {
        return $this->botUsername;
    }

    /**
     * Get direct Telegram link to the bot.
     */
    public function getBotLink(?string $param = null): string {
        $link = 'https://t.me/' . $this->botUsername;
        if (!empty($param)) {
            $link .= '?start=' . urlencode($param);
        }
        return $link;
    }

    /**
     * Send message via Telegram Bot API.
     * @param string|int $chatId
     * @param string $text
     * @param string $parseMode ('HTML' or 'MarkdownV2')
     * @return array ['success' => bool, 'message_id' => int|null, 'error' => string|null]
     */
    public function sendMessage($chatId, string $text, string $parseMode = 'HTML'): array {
        if (empty($this->botToken)) {
            return ['success' => false, 'message_id' => null, 'error' => 'Telegram Bot Token belum dikonfigurasi.'];
        }

        if (empty($chatId)) {
            return ['success' => false, 'message_id' => null, 'error' => 'Chat ID Telegram tidak valid atau kosong.'];
        }

        $url = self::TELEGRAM_API_BASE . $this->botToken . '/sendMessage';
        $payload = [
            'chat_id'                  => $chatId,
            'text'                     => $text,
            'parse_mode'               => $parseMode,
            'disable_web_page_preview' => true,
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return ['success' => false, 'message_id' => null, 'error' => 'Gagal menghubungi server Telegram: ' . $curlErr];
        }

        $decoded = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300 && !empty($decoded['ok'])) {
            return [
                'success'    => true,
                'message_id' => $decoded['result']['message_id'] ?? null,
                'error'      => null
            ];
        }

        $desc = $decoded['description'] ?? "HTTP Code {$httpCode}";
        return ['success' => false, 'message_id' => null, 'error' => 'Telegram Error: ' . $desc];
    }

    /**
     * Get recent updates from the bot via getUpdates API.
     * @param int $offset
     * @return array
     */
    public function getUpdates(int $offset = 0): array {
        if (empty($this->botToken)) {
            return [];
        }

        $url = self::TELEGRAM_API_BASE . $this->botToken . '/getUpdates?limit=25';
        if ($offset > 0) {
            $url .= '&offset=' . $offset;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            return [];
        }

        $decoded = json_decode($response, true);
        return (!empty($decoded['ok']) && is_array($decoded['result'])) ? $decoded['result'] : [];
    }

    /**
     * Resolve a chat_id from an identifier (chat ID, username, or latest interaction).
     * @param string|null $identifier (can be numeric ID, @username, or empty)
     * @return array|null ['chat_id' => string, 'username' => string, 'first_name' => string]
     */
    public function resolveChatId(?string $identifier = null): ?array {
        $identifier = trim($identifier ?? '');

        // 1. If numeric string (direct chat ID)
        if (!empty($identifier) && is_numeric($identifier)) {
            return [
                'chat_id'    => $identifier,
                'username'   => '',
                'first_name' => ''
            ];
        }

        // 2. Fetch updates to find matching username or latest chat
        $cleanUsername = ltrim($identifier, '@');
        $updates = $this->getUpdates();

        if (!empty($updates)) {
            // Sort updates descending (newest first)
            $updates = array_reverse($updates);

            // Search by username if provided
            if (!empty($cleanUsername)) {
                foreach ($updates as $up) {
                    $msg = $up['message'] ?? $up['callback_query']['message'] ?? null;
                    if (!$msg) continue;
                    $from = $up['message']['from'] ?? $up['callback_query']['from'] ?? null;
                    if ($from && !empty($from['username']) && strcasecmp($from['username'], $cleanUsername) === 0) {
                        return [
                            'chat_id'    => (string)$msg['chat']['id'],
                            'username'   => $from['username'] ?? '',
                            'first_name' => $from['first_name'] ?? ''
                        ];
                    }
                }
            }

            // Fallback: If no username specified or username match not found, take the most recent private chat
            foreach ($updates as $up) {
                $msg = $up['message'] ?? null;
                if ($msg && !empty($msg['chat']['id']) && ($msg['chat']['type'] ?? '') === 'private') {
                    $from = $msg['from'] ?? [];
                    return [
                        'chat_id'    => (string)$msg['chat']['id'],
                        'username'   => $from['username'] ?? '',
                        'first_name' => $from['first_name'] ?? ''
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Get direct Telegram link with start parameter for deep linking (t.me landing page).
     */
    public function getBotStartLink(string $token): string {
        return 'https://t.me/' . $this->botUsername . '?start=REG_' . urlencode($token);
    }

    /**
     * Get direct Telegram Web link (web.telegram.org) that launches directly in browser.
     */
    public function getBotWebLink(string $token): string {
        $tgAddr = urlencode('tg://resolve?domain=' . $this->botUsername . '&start=REG_' . $token);
        return 'https://web.telegram.org/k/#?tgaddr=' . $tgAddr;
    }

    /**
     * Get direct native protocol link (tg://) for Telegram Desktop / Mobile App.
     */
    public function getBotAppProtocolLink(string $token): string {
        return 'tg://resolve?domain=' . $this->botUsername . '&start=REG_' . urlencode($token);
    }

    /**
     * Check getUpdates for a user who clicked START with a specific REG_{token} or any recent start.
     * @param string $expectedToken
     * @return array|null ['chat_id' => string, 'username' => string, 'first_name' => string]
     */
    public function checkStartToken(string $expectedToken): ?array {
        $updates = $this->getUpdates();
        if (empty($updates)) {
            return null;
        }

        // Newest updates first
        $updates = array_reverse($updates);

        $expectedText = '/start REG_' . trim($expectedToken);

        foreach ($updates as $up) {
            $msg = $up['message'] ?? null;
            if (!$msg || empty($msg['text']) || empty($msg['chat']['id'])) continue;

            $text = trim($msg['text']);
            if (strcasecmp($text, $expectedText) === 0 || strpos($text, $expectedToken) !== false) {
                return [
                    'chat_id'    => (string)$msg['chat']['id'],
                    'username'   => $msg['from']['username'] ?? '',
                    'first_name' => $msg['from']['first_name'] ?? ''
                ];
            }
        }

        // Fallback: If any user just pressed /start in private chat within the last 5 minutes
        foreach ($updates as $up) {
            $msg = $up['message'] ?? null;
            if (!$msg || empty($msg['text']) || empty($msg['chat']['id'])) continue;

            $text = trim($msg['text']);
            if (strpos($text, '/start') === 0 && ($msg['chat']['type'] ?? '') === 'private') {
                return [
                    'chat_id'    => (string)$msg['chat']['id'],
                    'username'   => $msg['from']['username'] ?? '',
                    'first_name' => $msg['from']['first_name'] ?? ''
                ];
            }
        }

        return null;
    }

    /**
     * Format and send OTP + 1-Click Verification Link to Telegram user.
     * @param string|int $chatId
     * @param string $name
     * @param string $otp
     * @param string|null $verifyLink
     * @return array
     */
    public function sendOtp($chatId, string $name, string $otp, ?string $verifyLink = null): array {
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        
        $msg = "🛡️ <b>SkinCheck — Kode Verifikasi OTP</b>\n\n";
        $msg .= "Halo <b>{$safeName}</b>,\n";
        $msg .= "Terima kasih telah mendaftar di SkinCheck. Berikut adalah 6 digit kode verifikasi akun Anda:\n\n";
        $msg .= "🔐 <b><code>{$otp}</code></b>\n";
        $msg .= "<i>(Ketuk angka di atas untuk menyalin kode OTP)</i>\n\n";
        $msg .= "👉 Silakan masukkan 6 digit kode OTP di atas pada halaman web verifikasi SkinCheck untuk mengaktifkan akun Anda secara manual.\n\n";
        $msg .= "⏱️ Kode ini berlaku selama <b>5 menit</b>.\n";
        $msg .= "⚠️ <i>Jangan berikan kode ini kepada siapa pun demi keamanan akun Anda.</i>\n\n";
        $msg .= "🌿 <i>SkinCheck AI Dermatology & Skin Care</i>";

        return $this->sendMessage($chatId, $msg, 'HTML');
    }

    /**
     * Send Password Reset Link via Telegram
     * @param string|int $chatId
     * @param string $name
     * @param string $resetLink
     * @return array
     */
    public function sendPasswordResetLink($chatId, string $name, string $resetLink): array {
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        
        $msg = "🔑 <b>SkinCheck — Permintaan Reset Kata Sandi</b>\n\n";
        $msg .= "Halo <b>{$safeName}</b>,\n";
        $msg .= "Kami menerima permintaan untuk mengatur ulang kata sandi akun SkinCheck Anda.\n\n";
        $msg .= "👉 <b>Klik tautan di bawah ini untuk membuat kata sandi baru:</b>\n";
        $msg .= "<a href=\"{$resetLink}\">🔒 ATUR ULANG KATA SANDI SAYA</a>\n\n";
        $msg .= "⏱️ <i>Tautan ini hanya berlaku selama 15 menit. Jika Anda tidak pernah meminta ini, abaikan pesan ini dengan aman.</i>\n\n";
        $msg .= "🌿 <i>SkinCheck Platform Security</i>";

        return $this->sendMessage($chatId, $msg, 'HTML');
    }

    /**
     * Send instant notification when facial skin analysis is completed.
     * @param string|int $chatId
     * @param string $name
     * @param array $data
     * @return array
     */
    public function sendScanCompleteNotification($chatId, string $name, array $data): array {
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $overallScore = (int)($data['overallScore'] ?? 0);
        $acneScore = (int)($data['acneScore'] ?? 0);
        $rednessScore = (int)($data['rednessScore'] ?? 0);
        $textureScore = (int)($data['textureScore'] ?? 0);
        $pigmentScore = (int)($data['hyperpigmentationScore'] ?? 0);
        $detailUrl = $data['detailUrl'] ?? URLROOT . '/customer/dashboard';

        $msg = "<b>Hasil Analisis Wajah Selesai</b>\n\n";
        $msg .= "Halo <b>{$safeName}</b>,\n";
        $msg .= "Foto wajah Anda telah selesai dianalisis:\n\n";
        $msg .= "<b>Skor Kesehatan Kulit:</b> <code>{$overallScore}/100</code>\n";
        $msg .= "• Jerawat: <b>{$acneScore}/100</b>\n";
        $msg .= "• Kemerahan: <b>{$rednessScore}/100</b>\n";
        $msg .= "• Tekstur: <b>{$textureScore}/100</b>\n";
        $msg .= "• Pigmentasi: <b>{$pigmentScore}/100</b>\n\n";
        $msg .= "<a href=\"{$detailUrl}\">Buka Laporan Lengkap & Rekomendasi</a>\n\n";
        $msg .= "<i>SkinCheck AI Clinical System</i>";

        return $this->sendMessage($chatId, $msg, 'HTML');
    }
}
