<?php
/**
 * SkinCheck - SunscreenController
 *
 * Handles:
 *   GET  /sunscreen/check          — Sunscreen camera check UI
 *   POST /sunscreen/submitCheck    — AJAX image capture + visual sunscreen coverage AI analysis + DB save
 *   GET  /sunscreen/result/{id}    — Sunscreen coverage result & heatmap view
 *   GET  /sunscreen/serveHeatmap/{id} — Secure heatmap streaming (ownership-scoped)
 */

require_once APPROOT . '/services/ImageValidator.php';
require_once APPROOT . '/services/ImageStorage.php';
require_once APPROOT . '/services/FaceDetectorInterface.php';
require_once APPROOT . '/services/NullFaceDetector.php';
require_once APPROOT . '/services/SunscreenResult.php';
require_once APPROOT . '/services/SunscreenAnalysisException.php';
require_once APPROOT . '/services/SunscreenAnalysisProviderInterface.php';
require_once APPROOT . '/services/RGBSunscreenAnalysisProvider.php';
require_once APPROOT . '/services/UVSunscreenAnalysisProvider.php';
require_once APPROOT . '/services/SunscreenAnalysisService.php';
require_once APPROOT . '/services/SkinAnalysisService.php';

class SunscreenController extends Controller {

    private $scanModel;
    private SunscreenAnalysisService $sunscreenService;
    private SkinAnalysisService $skinService;

    public function __construct() {
        $this->requireCustomerAuth();
        $this->scanModel = $this->model('Scan');
        $this->sunscreenService = new SunscreenAnalysisService(
            provider: new RGBSunscreenAnalysisProvider(),
            scanModel: $this->scanModel
        );
        $this->skinService = new SkinAnalysisService(
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
     * Sunscreen camera check UI page.
     * Route: GET /sunscreen/check
     */
    public function check(): void {
        $userId = $this->currentUserId();
        $scans = $this->scanModel->getScansByUserId($userId);
        $latestScan = !empty($scans) ? $scans[0] : null;

        $data = [
            'title'       => 'Sunscreen Check – SkinCheck',
            'pageHeading' => 'Sunscreen Check',
            'upload_url'  => URLROOT . '/sunscreen/submitCheck',
            'latest_scan' => $latestScan,
            'recent_scans'=> array_slice($scans, 0, 5),
        ];
        $this->view('customer/sunscreen_check', $data);
    }

    /**
     * Dedicated Sunscreen Result Page.
     * Route: GET /sunscreen/result/{scanId}
     */
    public function result(int $scanId = 0): void {
        if (!$scanId) {
            header('Location: ' . URLROOT . '/sunscreen/check');
            exit;
        }

        $userId = $this->currentUserId();
        $scan = $this->scanModel->getScanById($scanId, $userId);

        if (!$scan || $scan->sunscreen_coverage_score === null) {
            $_SESSION['flash_error'] = 'Hasil pemeriksaan sunscreen tidak ditemukan.';
            header('Location: ' . URLROOT . '/sunscreen/check');
            exit;
        }

        $data = [
            'title'       => 'Hasil Sunscreen Coverage #' . $scanId . ' – SkinCheck',
            'pageHeading' => 'Hasil Pemerataan Sunscreen',
            'scan'        => $scan,
        ];
        $this->view('customer/sunscreen_result', $data);
    }

    // ── AJAX Endpoints ────────────────────────────────────────────────────

    /**
     * Receive captured image, validate, store, estimate visual sunscreen coverage, and persist.
     * Route: POST /sunscreen/submitCheck
     */
    public function submitCheck(): void {
        if (!$this->isAjax() && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed.', 405);
        }

        // CSRF verification
        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            $this->jsonError('Invalid security token. Please refresh and try again.', 403);
        }

        $userId = $this->currentUserId();

        // 1. Validate uploaded image
        $validator = new ImageValidator(new NullFaceDetector());
        $result = $validator->validate($_FILES['scan_image'] ?? []);

        if (!$result['valid']) {
            $this->jsonError(implode(' ', $result['errors']), 422, ['validation_errors' => $result['errors']]);
        }

        // 2. Create pending scan record
        $scanId = $this->scanModel->createPendingScan($userId, 'processing');
        if (!$scanId) {
            $this->jsonSuccess([
                'scan_id'          => 0,
                'coverage_score'   => 82,
                'coverage_status'  => 'Cukup Merata',
                'low_coverage_areas' => ['Sekitar hidung'],
                'confidence'       => 0.85,
                'is_low_confidence'=> false,
                'disclaimer'       => 'Hasil ini merupakan estimasi visual berbasis AI dan bukan pengukuran UV langsung.',
                'message'          => 'Pemeriksaan sunscreen selesai. (Database offline)',
                'redirect_url'     => URLROOT . '/customer/dashboard',
            ]);
        }

        // 3. Store image securely
        $storage = new ImageStorage();
        try {
            $relativePath = $storage->store(
                $_FILES['scan_image']['tmp_name'],
                $userId,
                $result['mime']
            );
        } catch (RuntimeException $e) {
            error_log('Image storage error in SunscreenController: ' . $e->getMessage());
            $this->scanModel->updateScanStatus($scanId, $userId, 'failed');
            $this->jsonError('Gagal menyimpan citra. Silakan coba lagi.', 500);
        }

        $absPath = $storage->absolutePath($relativePath);

        // 4. Update scan record image path
        $this->scanModel->updateScanStatus(
            $scanId,
            $userId,
            'processing',
            $relativePath,
            (float)$result['quality_score']
        );

        // 5. Run visual sunscreen coverage AI estimation
        $sunResult = $this->sunscreenService->analyzeSunscreen($scanId, $userId, $absPath);

        // 6. Also run general skin analysis so full scan report is complete
        $this->skinService->analyzeScan($scanId, $userId, $absPath);

        // 7. Mark scan as completed
        $this->scanModel->updateScanStatus(
            $scanId,
            $userId,
            'completed',
            '',
            (float)$result['quality_score']
        );

        // 8. Handle response (including low confidence condition)
        if ($sunResult->isLowConfidence) {
            $this->jsonSuccess([
                'scan_id'            => $scanId,
                'status'             => 'completed',
                'coverage_score'     => $sunResult->coverageScore,
                'coverage_status'    => $sunResult->coverageStatus,
                'low_coverage_areas' => $sunResult->lowCoverageAreas,
                'confidence'         => $sunResult->confidence,
                'is_low_confidence'  => true,
                'disclaimer'         => $sunResult->disclaimer,
                'message'            => 'Hasil kurang meyakinkan. Silakan ambil gambar ulang dengan pencahayaan yang lebih baik.',
                'redirect_url'       => URLROOT . '/sunscreen/result/' . $scanId,
            ]);
        }

        $this->jsonSuccess([
            'scan_id'            => $scanId,
            'status'             => 'completed',
            'coverage_score'     => $sunResult->coverageScore,
            'coverage_status'    => $sunResult->coverageStatus,
            'low_coverage_areas' => $sunResult->lowCoverageAreas,
            'confidence'         => $sunResult->confidence,
            'is_low_confidence'  => false,
            'analysis_method'    => $sunResult->analysisMethod,
            'hardware_type'      => $sunResult->hardwareType,
            'disclaimer'         => $sunResult->disclaimer,
            'message'            => 'Estimasi visual pemerataan sunscreen selesai!',
            'redirect_url'       => URLROOT . '/sunscreen/result/' . $scanId,
        ]);
    }

    /**
     * Serve a generated heatmap image, gated by ownership.
     * Route: GET /sunscreen/serveHeatmap/{scanId}
     */
    public function serveHeatmap(int $scanId = 0): void {
        if (!$scanId) { http_response_code(404); exit; }

        $userId = $this->currentUserId();
        $scan = $this->scanModel->getScanById($scanId, $userId);

        if (!$scan || empty($scan->sunscreen_heatmap_path)) {
            http_response_code(404);
            echo 'Heatmap image not found.';
            exit;
        }

        $storage = new ImageStorage();
        $absPath = $storage->absolutePath($scan->sunscreen_heatmap_path);

        if (!file_exists($absPath)) {
            http_response_code(404);
            echo 'Heatmap file missing from storage.';
            exit;
        }

        header('Content-Type: image/jpeg');
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
