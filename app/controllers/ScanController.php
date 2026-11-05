<?php
/**
 * SkinCheck - ScanController
 *
 * Handles:
 *   GET  /scan/faceScan          — Face scan UI page
 *   POST /scan/submitScan        — AJAX image upload + validation + AI visual analysis + DB save
 *   POST /scan/retryAnalysis/{id}— Retry visual skin analysis on existing scan
 *   GET  /scan/serveImage/{id}   — Secure image streaming (ownership-scoped)
 */

require_once APPROOT . '/services/ImageValidator.php';
require_once APPROOT . '/services/ImageStorage.php';
require_once APPROOT . '/services/FaceDetectorInterface.php';
require_once APPROOT . '/services/NullFaceDetector.php';
require_once APPROOT . '/services/AnalysisResult.php';
require_once APPROOT . '/services/SkinAnalysisException.php';
require_once APPROOT . '/services/SkinAnalysisProviderInterface.php';
require_once APPROOT . '/services/ComputerVisionSkinProvider.php';
require_once APPROOT . '/services/SkinAnalysisService.php';

class ScanController extends Controller {

    private $scanModel;
    private SkinAnalysisService $analysisService;

    public function __construct() {
        $this->requireCustomerAuth();
        $this->scanModel = $this->model('Scan');
        $this->analysisService = new SkinAnalysisService(
            provider: new ComputerVisionSkinProvider(),
            scanModel: $this->scanModel
        );
    }

    // ── Auth Guard ────────────────────────────────────────────────────────

    private function requireCustomerAuth(): void {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
            if ($this->isAjax()) {
                $this->jsonError('Authentication required.', 401);
            } else {
                header('Location: ' . URLROOT . '/auth/login');
                exit;
            }
        }
    }

    private function currentUserId(): int {
        return (int)$_SESSION['user_id'];
    }

    // ── Pages ─────────────────────────────────────────────────────────────

    /**
     * Face scan UI page.
     * Route: GET /scan/faceScan
     */
    public function faceScan(): void {
        $data = [
            'title'       => 'Face Scan – SkinCheck',
            'pageHeading' => 'Face Scan',
            'upload_url'  => URLROOT . '/scan/submitScan',
        ];
        $this->view('customer/scan_face', $data);
    }

    // ── AJAX Endpoints ────────────────────────────────────────────────────

    /**
     * Receive captured image, validate, store, perform AI visual analysis, and save DB record.
     * Route: POST /scan/submitScan
     * Expects: multipart/form-data with field "scan_image"
     * Returns: JSON
     */
    public function submitScan(): void {
        if (!$this->isAjax() && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed.', 405);
        }

        // CSRF token check
        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            $this->jsonError('Invalid security token. Please refresh and try again.', 403);
        }

        $userId = $this->currentUserId();

        // 1. Validate uploaded image (size, format, resolution, brightness, face detection interface)
        $validator = new ImageValidator(new NullFaceDetector());
        $result    = $validator->validate($_FILES['scan_image'] ?? []);

        if (!$result['valid']) {
            $this->jsonError(implode(' ', $result['errors']), 422, ['validation_errors' => $result['errors']]);
        }

        // 2. Create pending DB record in processing state
        $scanId = $this->scanModel->createPendingScan($userId, 'processing');
        if (!$scanId) {
            // Fallback for demo when DB is not connected
            $this->jsonSuccess([
                'scan_id'       => 0,
                'status'        => 'completed',
                'quality_score' => $result['quality_score'],
                'message'       => 'Scan visual capture complete. (Database offline)',
                'redirect_url'  => URLROOT . '/customer/dashboard',
            ]);
        }

        // 3. Store image securely outside public path
        $storage = new ImageStorage();
        try {
            $relativePath = $storage->store(
                $_FILES['scan_image']['tmp_name'],
                $userId,
                $result['mime']
            );
        } catch (RuntimeException $e) {
            error_log('Image storage error: ' . $e->getMessage());
            $this->scanModel->updateScanStatus($scanId, $userId, 'failed');
            $this->jsonError('Failed to save image. Please try again.', 500);
        }

        // 4. Update scan image path
        $this->scanModel->updateScanStatus(
            $scanId,
            $userId,
            'processing',
            $relativePath,
            (float)$result['quality_score']
        );

        // 5. Execute Computer Vision / AI skin condition visual analysis
        $absolutePath = $storage->absolutePath($relativePath);
        $analysisResult = $this->analysisService->analyzeScan($scanId, $userId, $absolutePath);

        if ($analysisResult->analysisStatus === 'completed') {
            // Check if user has Telegram linked and dispatch instant completion alert
            try {
                $userModel = $this->model('User');
                $customer = $userModel->findUserById($userId);
                if ($customer && !empty($customer->telegram_chat_id)) {
                    $tgService = new TelegramService();
                    $tgService->sendScanCompleteNotification($customer->telegram_chat_id, $customer->name, [
                        'scanId' => $scanId,
                        'overallScore' => $analysisResult->overallScore,
                        'acneScore' => $analysisResult->acneScore,
                        'rednessScore' => $analysisResult->rednessScore,
                        'textureScore' => $analysisResult->textureScore,
                        'hyperpigmentationScore' => $analysisResult->hyperpigmentationScore,
                        'detailUrl' => URLROOT . '/customer/scanDetail/' . $scanId,
                    ]);
                }
            } catch (Throwable $e) {
                error_log('[ScanController] Notification dispatch error: ' . $e->getMessage());
            }

            // Set flash success message for the destination scanDetail page
            $_SESSION['flash_success'] = 'Analisis Wajah Selesai. Skor Kesehatan Kulit Anda: ' . $analysisResult->overallScore . '/100.';

            $this->jsonSuccess([
                'scan_id'       => $scanId,
                'status'        => 'completed',
                'quality_score' => $result['quality_score'],
                'overall_score' => $analysisResult->overallScore,
                'acne_score'    => $analysisResult->acneScore,
                'redness_score' => $analysisResult->rednessScore,
                'texture_score' => $analysisResult->textureScore,
                'hyperpigmentation_score' => $analysisResult->hyperpigmentationScore,
                'confidence'    => $analysisResult->confidence,
                'resolution'    => ($result['width'] ?? 0) . '×' . ($result['height'] ?? 0),
                'message'       => 'Analisis kondisi kulit selesai diproses. Skor: ' . $analysisResult->overallScore . '/100',
                'notification_title' => 'Analisis Wajah Selesai',
                'notification_body'  => 'Skor kesehatan kulit: ' . $analysisResult->overallScore . '/100. Membuka laporan lengkap...',
                'redirect_url'  => URLROOT . '/customer/scanDetail/' . $scanId,
            ]);
        } else {
            // Failed analysis
            $this->jsonSuccess([
                'scan_id'       => $scanId,
                'status'        => 'failed',
                'error_message' => $analysisResult->errorMessage ?? 'Analisis visual gagal.',
                'message'       => 'Proses analisis visual gagal, Anda dapat mencoba analisis ulang.',
                'redirect_url'  => URLROOT . '/customer/scanDetail/' . $scanId,
            ]);
        }
    }

    /**
     * Retry visual skin analysis on an existing scan record.
     * Route: POST /scan/retryAnalysis/{scanId}
     */
    public function retryAnalysis(int $scanId = 0): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($this->isAjax()) {
                $this->jsonError('Method not allowed.', 405);
            }
            header('Location: ' . URLROOT . '/customer/history');
            exit;
        }

        $userId = $this->currentUserId();

        // Validate scan ownership
        $scan = $this->scanModel->getScanById($scanId, $userId);
        if (!$scan || empty($scan->image_path)) {
            if ($this->isAjax()) {
                $this->jsonError('Scan data not found or image missing.', 404);
            }
            $_SESSION['flash_error'] = 'Scan tidak ditemukan.';
            header('Location: ' . URLROOT . '/customer/history');
            exit;
        }

        $storage = new ImageStorage();
        $absPath = $storage->absolutePath($scan->image_path);

        if (!$storage->exists($scan->image_path)) {
            if ($this->isAjax()) {
                $this->jsonError('Image file missing from storage.', 404);
            }
            $_SESSION['flash_error'] = 'File gambar tidak ditemukan di penyimpanan.';
            header('Location: ' . URLROOT . '/customer/scanDetail/' . $scanId);
            exit;
        }

        // Run analysis
        $analysisResult = $this->analysisService->analyzeScan($scanId, $userId, $absPath);

        if ($this->isAjax()) {
            if ($analysisResult->analysisStatus === 'completed') {
                $this->jsonSuccess([
                    'scan_id' => $scanId,
                    'status' => 'completed',
                    'overall_score' => $analysisResult->overallScore,
                    'message' => 'Analisis ulang berhasil diproses.',
                    'redirect_url' => URLROOT . '/customer/scanDetail/' . $scanId
                ]);
            } else {
                $this->jsonError('Analisis ulang gagal: ' . ($analysisResult->errorMessage ?? 'Error'), 400);
            }
        } else {
            if ($analysisResult->analysisStatus === 'completed') {
                $_SESSION['flash_success'] = 'Analisis visual kulit berhasil diperbarui!';
            } else {
                $_SESSION['flash_error'] = 'Analisis ulang gagal: ' . ($analysisResult->errorMessage ?? 'Terjadi kesalahan');
            }
            header('Location: ' . URLROOT . '/customer/scanDetail/' . $scanId);
            exit;
        }
    }

    /**
     * Serve a stored scan image, gated by ownership.
     * Route: GET /scan/serveImage/{scanId}
     * Only streams the image if the scan belongs to the current user.
     */
    public function serveImage(int $scanId = 0): void {
        if (!$scanId) { http_response_code(404); exit; }

        $userId = $this->currentUserId();
        $scan   = $this->scanModel->getScanById($scanId, $userId);

        if (!$scan || empty($scan->image_path)) {
            http_response_code(404);
            echo 'Image not found.';
            exit;
        }

        $storage = new ImageStorage();
        $absPath = $storage->absolutePath($scan->image_path);

        if (!$storage->exists($scan->image_path)) {
            http_response_code(404);
            echo 'Image file missing from storage.';
            exit;
        }

        // Determine MIME from actual file (not DB string)
        $mime = mime_content_type($absPath) ?: 'image/jpeg';
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            http_response_code(403);
            exit;
        }

        // Stream the image
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($absPath));
        header('Cache-Control: private, max-age=3600');
        header('X-Content-Type-Options: nosniff');
        readfile($absPath);
        exit;
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function isAjax(): bool {
        return (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest')
            || (($_SERVER['CONTENT_TYPE'] ?? '') !== ''
                && str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'multipart'));
    }

    private function validateCsrfToken(string $token): bool {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    private function jsonSuccess(array $payload, int $code = 200): never {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['success' => true] + $payload);
        exit;
    }

    private function jsonError(string $message, int $code = 400, array $extra = []): never {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $message] + $extra);
        exit;
    }
}
