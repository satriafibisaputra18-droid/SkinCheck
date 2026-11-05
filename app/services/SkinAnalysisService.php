<?php
/**
 * SkinCheck - SkinAnalysisService
 *
 * Core service orchestrating the skin condition analysis workflow:
 * 1. Coordinates with SkinAnalysisProviderInterface (swappable AI / CV backend).
 * 2. Manages scan lifecycle state transitions: pending -> processing -> completed / failed.
 * 3. Handles automatic retry policies on provider errors.
 * 4. Persists analysis metrics, confidence, and visual indicators into the database.
 */

require_once __DIR__ . '/AnalysisResult.php';
require_once __DIR__ . '/SkinAnalysisException.php';
require_once __DIR__ . '/SkinAnalysisProviderInterface.php';
require_once __DIR__ . '/ComputerVisionSkinProvider.php';
require_once __DIR__ . '/OnnxSkinAnalysisProvider.php';

class SkinAnalysisService {

    private SkinAnalysisProviderInterface $provider;
    private $scanModel;
    private int $maxRetries;

    public function __construct(?SkinAnalysisProviderInterface $provider = null, $scanModel = null, int $maxRetries = 2) {
        $this->provider = $provider ?? new OnnxSkinAnalysisProvider();
        $this->scanModel = $scanModel;
        $this->maxRetries = max(1, $maxRetries);
    }

    /**
     * Lazy-load Scan model if not injected.
     */
    private function getScanModel() {
        if (!$this->scanModel) {
            $modelFile = APPROOT . '/models/Scan.php';
            if (file_exists($modelFile)) {
                require_once $modelFile;
                $this->scanModel = new Scan();
            }
        }
        return $this->scanModel;
    }

    /**
     * Perform visual skin analysis for a given scan and persist results.
     *
     * @param int $scanId
     * @param int $userId
     * @param string $absoluteImagePath
     * @param array $options
     * @return AnalysisResult
     */
    public function analyzeScan(
        int $scanId,
        int $userId,
        string $absoluteImagePath,
        array $options = []
    ): AnalysisResult {
        $scanModel = $this->getScanModel();

        // 1. Mark scan as processing in database
        if ($scanModel && $scanId > 0) {
            $scanModel->updateScanStatus($scanId, $userId, 'processing');
        }

        $attempt = 0;
        $lastException = null;

        // 2. Execute analysis with retry capability
        while ($attempt < $this->maxRetries) {
            $attempt++;
            try {
                $result = $this->provider->analyze($absoluteImagePath, $options);

                // 3. Persist successful results
                if ($scanModel && $scanId > 0) {
                    $scanModel->saveAnalysisResult($scanId, $result);
                    $scanModel->updateScanStatus(
                        $scanId,
                        $userId,
                        'completed',
                        '',
                        (float)round($result->confidence * 100, 2)
                    );
                }

                return $result;

            } catch (SkinAnalysisException $e) {
                $lastException = $e;
                error_log(sprintf(
                    "[SkinAnalysisService] Attempt %d/%d failed for Scan #%d with provider %s: %s",
                    $attempt,
                    $this->maxRetries,
                    $scanId,
                    $e->getProviderName() ?? $this->provider->getProviderName(),
                    $e->getMessage()
                ));

                // If this is a fatal image error (e.g. invalid file), no need to retry
                if (str_contains($e->getMessage(), 'not found') || str_contains($e->getMessage(), 'dimensions are too low')) {
                    break;
                }

                // Short sleep before retry if applicable
                if ($attempt < $this->maxRetries) {
                    usleep(150000); // 150ms
                }
            } catch (Throwable $e) {
                $lastException = $e;
                error_log(sprintf(
                    "[SkinAnalysisService] Unexpected error on Scan #%d: %s",
                    $scanId,
                    $e->getMessage()
                ));
                break;
            }
        }

        // 4. Handle final failure state
        $errorMsg = $lastException ? $lastException->getMessage() : 'Analysis failed after retries.';
        $failureResult = AnalysisResult::failure($errorMsg, $this->provider->getProviderName());

        if ($scanModel && $scanId > 0) {
            $scanModel->saveAnalysisResult($scanId, $failureResult);
            $scanModel->updateScanStatus($scanId, $userId, 'failed');
        }

        return $failureResult;
    }

    /**
     * Get the active provider instance.
     */
    public function getProvider(): SkinAnalysisProviderInterface {
        return $this->provider;
    }
}
