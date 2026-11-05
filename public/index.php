<?php
/**
 * SkinCheck - Front Controller
 * Single entry point for the application.
 */

// ── Session ───────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token once per session (used by scan upload and future forms)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── Configuration ─────────────────────────────────────────────────────────
if (file_exists(__DIR__ . '/../app/config/config.php')) {
    require_once __DIR__ . '/../app/config/config.php';
} else {
    die('Configuration file not found. Please set up app/config/config.php.');
}

// ── Autoloader ────────────────────────────────────────────────────────────
// Searches core, controllers, models, and services directories.
spl_autoload_register(function (string $className): void {
    $paths = [
        __DIR__ . '/../app/core/',
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/models/',
        __DIR__ . '/../app/services/',
    ];

    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ── Boot ──────────────────────────────────────────────────────────────────
$app = new App();
