<?php
/**
 * Global Floating Toast Notification Container & Session Flash Flusher
 * Renders floating toasts at bottom-left corner across all views.
 */

$toastQueue = [];

// Collect Flash Messages from Session
$flashTypes = [
    'success' => ['flash_success', 'admin_flash_success', 'login_success', 'register_success', 'reset_success', 'settings_success', 'profile_success'],
    'error'   => ['flash_error', 'admin_flash_error', 'login_error', 'register_error', 'reset_error', 'settings_error', 'otp_error'],
    'warning' => ['flash_warning', 'warning_message'],
    'info'    => ['flash_info', 'flash_notice', 'otp_notice']
];

foreach ($flashTypes as $type => $keys) {
    foreach ($keys as $key) {
        if (!empty($_SESSION[$key])) {
            $toastQueue[] = [
                'type' => $type,
                'message' => (string)$_SESSION[$key]
            ];
            unset($_SESSION[$key]);
        }
    }
}
?>

<!-- Global Floating Toast Container (Bottom-Left) -->
<div id="scToastContainer" class="sc-toast-container" aria-live="polite" aria-atomic="true"></div>

<?php if (!empty($toastQueue)) : ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const queue = <?php echo json_encode($toastQueue, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    queue.forEach(function (item, index) {
        setTimeout(function () {
            if (window.showToast) {
                window.showToast(item.message, item.type);
            }
        }, index * 220 + 120);
    });
});
</script>
<?php endif; ?>
