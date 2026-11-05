<?php
/**
 * SkinCheck - Application Configuration
 * Holds global constants, database parameters, and URL configurations.
 */

// Optional custom hosting config override (e.g. for InfinityFree / cPanel production)
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

// Database Parameters (Loaded from environment variables with fallbacks)
defined('DB_HOST') || define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
defined('DB_USER') || define('DB_USER', getenv('DB_USER') ?: 'root');
defined('DB_PASS') || define('DB_PASS', getenv('DB_PASS') ?: '');
defined('DB_NAME') || define('DB_NAME', getenv('DB_NAME') ?: 'skincheck');

// Application Path Root
define('APPROOT', dirname(dirname(__FILE__)));

// Dynamic URL Root Resolution (Handles XAMPP subfolders and production domains gracefully)
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
$protocol = $isHttps ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

if (defined('STDIN') || php_sapi_name() === 'cli') {
    $baseDir = '/Skin Check';
} else {
    // Strip /public/index.php or /index.php from script path
    $baseDir = preg_replace('#/(public/)?index\.php$#', '', $scriptName);
    // If running in root directory, $baseDir will be empty string ''
    $baseDir = rtrim($baseDir, '/');
}

defined('URLROOT') || define('URLROOT', getenv('BASE_URL') ?: ($protocol . '://' . $host . $baseDir));

// Site Brand Name
define('SITENAME', 'SkinCheck');
define('SITE_VERSION', '1.0.0');

// Resend Email API Configuration (Transactional OTP & Notifications)
define('RESEND_API_KEY', getenv('RESEND_API_KEY') ?: '');
define('RESEND_FROM_EMAIL', getenv('RESEND_FROM_EMAIL') ?: 'SkinCheck <onboarding@resend.dev>');

// Telegram Bot Configuration (Transactional OTP & Notifications via KodeOTPSkinCheckBot)
define('TELEGRAM_BOT_TOKEN', getenv('TELEGRAM_BOT_TOKEN') ?: '');
define('TELEGRAM_BOT_USERNAME', getenv('TELEGRAM_BOT_USERNAME') ?: 'Kode_OTP_SkinCheckBot');
define('TELEGRAM_BOT_NAME', getenv('TELEGRAM_BOT_NAME') ?: 'KodeOTPSkinCheckBot');


