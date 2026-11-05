<?php
/**
 * SkinCheck - ImageValidator
 *
 * Server-side image validation for uploaded scan images.
 * Validates: presence, size, MIME (via GD/fileinfo, NOT client header),
 * minimum resolution, brightness (darkness detection), and runs
 * the injected face detector.
 */

require_once __DIR__ . '/FaceDetectorInterface.php';
require_once __DIR__ . '/NullFaceDetector.php';

class ImageValidator {

    // ── Constants ────────────────────────────────────────────────────────
    const MAX_BYTES        = 10 * 1024 * 1024; // 10 MB hard limit
    const MIN_WIDTH        = 320;
    const MIN_HEIGHT       = 240;
    const ALLOWED_MIMES    = ['image/jpeg', 'image/png', 'image/webp'];
    const BRIGHTNESS_FLOOR = 30;  // 0-255 average luminance; below = "too dark"

    private FaceDetectorInterface $faceDetector;

    public function __construct(?FaceDetectorInterface $detector = null) {
        $this->faceDetector = $detector ?? new NullFaceDetector();
    }

    // ── Public API ───────────────────────────────────────────────────────

    /**
     * Validate an uploaded image file.
     *
     * @param array  $file          $_FILES entry (tmp_name, size, etc.)
     * @param bool   $detectFace    Run face detection (default: true)
     * @return array  ['valid' => bool, 'errors' => string[], 'quality_score' => float]
     */
    public function validate(array $file, bool $detectFace = true): array {
        $errors = [];

        // 1. File present & no upload error
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['valid' => false, 'errors' => ['No image file received.'], 'quality_score' => 0];
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'errors' => [$this->uploadErrorMessage($file['error'])], 'quality_score' => 0];
        }

        // 2. File size (server-enforced, ignores client claim)
        if ($file['size'] > self::MAX_BYTES) {
            $errors[] = 'Image too large. Maximum allowed size is 10 MB.';
        }
        if ($file['size'] < 1024) {
            $errors[] = 'Image is too small to be valid.';
        }

        // 3. MIME type — use fileinfo (reads magic bytes, not client header)
        $mime = $this->detectMimeType($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED_MIMES, true)) {
            $errors[] = 'Invalid image format. Only JPEG, PNG, and WebP are accepted.';
            // Cannot do pixel-level checks on unsupported format
            return ['valid' => false, 'errors' => $errors, 'quality_score' => 0];
        }

        // 4. Load image with GD for pixel-level checks
        $img = $this->loadImage($file['tmp_name'], $mime);
        if (!$img) {
            return ['valid' => false, 'errors' => ['Could not process the image file.'], 'quality_score' => 0];
        }

        $w = imagesx($img);
        $h = imagesy($img);

        // 5. Minimum resolution
        if ($w < self::MIN_WIDTH || $h < self::MIN_HEIGHT) {
            $errors[] = "Image resolution too low ({$w}×{$h}). Minimum is " . self::MIN_WIDTH . '×' . self::MIN_HEIGHT . '.';
        }

        // 6. Brightness check (too dark detection)
        $brightness = $this->averageBrightness($img, $w, $h);
        if ($brightness < self::BRIGHTNESS_FLOOR) {
            $errors[] = 'Image appears too dark. Please improve lighting and retake.';
        }

        imagedestroy($img);

        // 7. Face detection (via injected detector)
        if ($detectFace && empty($errors)) {
            $result = $this->faceDetector->detect($file['tmp_name']);
            if (!$result->faceFound) {
                $errors[] = 'No face detected. Please centre your face in the frame and retake.';
            }
        }

        // Quality score: composite of brightness + resolution adequacy
        $qualityScore = $this->calculateQualityScore($w, $h, $brightness);

        return [
            'valid'         => empty($errors),
            'errors'        => $errors,
            'quality_score' => $qualityScore,
            'width'         => $w ?? 0,
            'height'        => $h ?? 0,
            'mime'          => $mime,
            'brightness'    => $brightness,
        ];
    }

    // ── Private Helpers ──────────────────────────────────────────────────

    private function detectMimeType(string $path): string {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $path);
            finfo_close($finfo);
            return $mime ?: '';
        }
        // Fallback: getimagesize (less reliable but widely available)
        $info = @getimagesize($path);
        return $info['mime'] ?? '';
    }

    private function loadImage(string $path, string $mime): ?\GdImage {
        return match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png'  => @imagecreatefrompng($path),
            'image/webp' => @imagecreatefromwebp($path),
            default      => null,
        };
    }

    /**
     * Samples a grid of pixels and returns average luminance (0–255).
     * Uses ITU-R BT.601 luma coefficients for perceptual accuracy.
     */
    private function averageBrightness(\GdImage $img, int $w, int $h): float {
        $total   = 0;
        $samples = 0;
        $step    = max(1, (int)($w / 20));   // sample ~20×20 grid

        for ($x = 0; $x < $w; $x += $step) {
            for ($y = 0; $y < $h; $y += $step) {
                $rgb    = imagecolorat($img, $x, $y);
                $r      = ($rgb >> 16) & 0xFF;
                $g      = ($rgb >> 8)  & 0xFF;
                $b      =  $rgb        & 0xFF;
                $total += 0.299 * $r + 0.587 * $g + 0.114 * $b;
                $samples++;
            }
        }

        return $samples > 0 ? round($total / $samples, 2) : 0.0;
    }

    /**
     * Composite quality score 0–100.
     * Weights: 60% resolution adequacy, 40% brightness adequacy.
     */
    private function calculateQualityScore(int $w, int $h, float $brightness): float {
        $resScore   = min(100, ($w * $h) / (1280 * 960) * 100);
        $brightScore= min(100, ($brightness / 200) * 100);  // 200 = comfortable brightness
        return round(0.6 * $resScore + 0.4 * $brightScore, 1);
    }

    private function uploadErrorMessage(int $code): string {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the maximum allowed size.',
            UPLOAD_ERR_PARTIAL    => 'The file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server missing temp folder.',
            UPLOAD_ERR_CANT_WRITE => 'Server failed to write file.',
            default               => 'An unknown upload error occurred (code: ' . $code . ').',
        };
    }
}
