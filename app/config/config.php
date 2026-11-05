<?php
/**
 * SkinCheck - Application Configuration
 * Holds global constants, database parameters, and URL configurations.
 */

// Database Parameters (Loaded from environment variables with fallbacks)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'skincheck');

// Application Path Root
define('APPROOT', dirname(dirname(__FILE__)));

// Dynamic URL Root Resolution (Handles XAMPP subfolders and production domains gracefully)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseDir = str_replace('/public/index.php', '', $scriptName);

// Fallback in case of command-line environment or parsing anomalies
if ($baseDir === $scriptName) {
    $baseDir = '/Skin Check';
}

define('URLROOT', getenv('BASE_URL') ?: ($protocol . '://' . $host . $baseDir));

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


