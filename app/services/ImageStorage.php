<?php
/**
 * SkinCheck - ImageStorage
 *
 * Manages secure storage of uploaded scan images.
 * - Files are stored OUTSIDE public/ to prevent direct URL access.
 * - Filenames are randomised (no predictable paths exposed).
 * - Serving is done via the ScanController::serveImage endpoint
 *   which enforces ownership before streaming.
 */

class ImageStorage {

    /** Absolute path to the private scan image directory. */
    private string $storageRoot;

    public function __construct() {
        // Store outside public/ — not directly accessible via HTTP
        $this->storageRoot = APPROOT . '/../storage/scans';
        $this->ensureDirectoryExists($this->storageRoot);
    }

    // ── Public API ────────────────────────────────────────────────────────

    /**
     * Store an uploaded image securely.
     *
     * @param string $tmpPath   PHP tmp file path (from $_FILES)
     * @param int    $userId    Owner user ID (used in directory namespacing)
     * @param string $mime      Validated MIME type
     * @return string           Relative storage path (stored in DB; never exposed in URL)
     * @throws RuntimeException on storage failure
     */
    public function store(string $tmpPath, int $userId, string $mime): string {
        $userDir = $this->storageRoot . '/' . $userId;
        $this->ensureDirectoryExists($userDir);

        $ext      = $this->mimeToExtension($mime);
        $filename = $this->generateFilename($ext);
        $destPath = $userDir . '/' . $filename;

        if (!move_uploaded_file($tmpPath, $destPath)) {
            throw new RuntimeException('Failed to move uploaded image to storage.');
        }

        // Return relative path (user_id/filename) — stored in DB
        return $userId . '/' . $filename;
    }

    /**
     * Get the absolute path for a stored image.
     * Used by serveImage to stream to the browser.
     *
     * @param string $relativePath  Path as stored in DB (e.g. "42/abc123.jpg")
     * @return string Absolute path
     */
    public function absolutePath(string $relativePath): string {
        return $this->storageRoot . '/' . ltrim($relativePath, '/');
    }

    /**
     * Check that a stored image file physically exists.
     */
    public function exists(string $relativePath): bool {
        return file_exists($this->absolutePath($relativePath));
    }

    /**
     * Delete a stored image (e.g. on scan deletion).
     */
    public function delete(string $relativePath): bool {
        $path = $this->absolutePath($relativePath);
        return file_exists($path) && unlink($path);
    }

    // ── Private Helpers ───────────────────────────────────────────────────

    private function generateFilename(string $ext): string {
        // 32 bytes of cryptographic randomness → 64-char hex filename
        return bin2hex(random_bytes(16)) . '_' . time() . '.' . $ext;
    }

    private function mimeToExtension(string $mime): string {
        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'jpg',
        };
    }

    private function ensureDirectoryExists(string $path): void {
        if (!is_dir($path)) {
            if (!mkdir($path, 0750, true)) {
                throw new RuntimeException('Cannot create storage directory: ' . $path);
            }
            // Drop an .htaccess to block direct web access (belt-and-suspenders)
            $htaccess = $path . '/.htaccess';
            if (!file_exists($htaccess)) {
                file_put_contents($htaccess, "Order Deny,Allow\nDeny from all\n");
            }
        }
    }
}
