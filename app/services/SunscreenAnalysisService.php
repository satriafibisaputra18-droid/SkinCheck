<?php
/**
 * SkinCheck - SunscreenAnalysisService
 *
 * Coordinates visual sunscreen coverage evenness estimation:
 * 1. Executes SunscreenAnalysisProviderInterface (defaults to RGBSunscreenAnalysisProvider).
 * 2. Manages storage directory for heatmap overlays.
 * 3. Persists coverage_score, coverage_status, heatmap_path, low_coverage_areas,
 *    analysis_method, and hardware_type to sunscreen_results table.
 * 4. Implements fallback and error handling.
 */

require_once __DIR__ . '/SunscreenResult.php';
require_once __DIR__ . '/SunscreenAnalysisException.php';
require_once __DIR__ . '/SunscreenAnalysisProviderInterface.php';
require_once __DIR__ . '/RGBSunscreenAnalysisProvider.php';

class SunscreenAnalysisService {

    private SunscreenAnalysisProviderInterface $provider;
    private $scanModel;
    private string $storageRoot;

    public function __construct(
        ?SunscreenAnalysisProviderInterface $provider = null,
        $scanModel = null,
        ?string $storageRoot = null
    ) {
        $this->provider = $provider ?? new RGBSunscreenAnalysisProvider();
        $this->scanModel = $scanModel;
        $this->storageRoot = $storageRoot ?? (APPROOT . '/../storage/scans');
    }

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
     * Analyze sunscreen coverage for a scan and save results.
     */
    public function analyzeSunscreen(
        int $scanId,
        int $userId,
        string $absoluteImagePath,
        array $options = []
    ): SunscreenResult {
        $scanModel = $this->getScanModel();

        // 1. Prepare user heatmap directory
        $heatmapDir = rtrim($this->storageRoot, '/\\') . '/' . $userId . '/heatmaps';
        if (!is_dir($heatmapDir)) {
            @mkdir($heatmapDir, 0755, true);
        }

        try {
            // 2. Run analysis with provider
            $result = $this->provider->analyze($absoluteImagePath, $heatmapDir, $options);

            // 3. Format heatmap relative path (e.g. "42/heatmaps/heatmap_xyz.jpg")
            $relativeHeatmapPath = $userId . '/heatmaps/' . basename($result->heatmapPath);

            $finalResult = new SunscreenResult(
                coverageScore: $result->coverageScore,
                coverageStatus: $result->coverageStatus,
                lowCoverageAreas: $result->lowCoverageAreas,
                heatmapPath: $relativeHeatmapPath,
                confidence: $result->confidence,
                analysisMethod: $result->analysisMethod,
                hardwareType: $result->hardwareType,
                isLowConfidence: $result->isLowConfidence,
                status: 'completed',
                notes: $result->notes,
                errorMessage: null
            );

            // 4. Persist to database
            if ($scanModel && $scanId > 0) {
                $scanModel->saveSunscreenResult($scanId, $finalResult);
            }

            return $finalResult;

        } catch (SunscreenAnalysisException $e) {
            error_log(sprintf(
                "[SunscreenAnalysisService] Analysis failed for Scan #%d with provider %s: %s",
                $scanId,
                $this->provider->getProviderName(),
                $e->getMessage()
            ));

            $failure = SunscreenResult::failure(
                errorMessage: $e->getMessage(),
                method: $this->provider->getAnalysisMethod(),
                hardware: $this->provider->getHardwareType()
            );

            if ($scanModel && $scanId > 0) {
                $scanModel->saveSunscreenResult($scanId, $failure);
            }

            return $failure;

        } catch (Throwable $e) {
            error_log(sprintf(
                "[SunscreenAnalysisService] Unexpected error on Scan #%d: %s",
                $scanId,
                $e->getMessage()
            ));

            $failure = SunscreenResult::failure(
                errorMessage: 'Terjadi kesalahan internal saat analisis sunscreen: ' . $e->getMessage(),
                method: $this->provider->getAnalysisMethod(),
                hardware: $this->provider->getHardwareType()
            );

            if ($scanModel && $scanId > 0) {
                $scanModel->saveSunscreenResult($scanId, $failure);
            }

            return $failure;
        }
    }

    public function getProvider(): SunscreenAnalysisProviderInterface {
        return $this->provider;
    }
}
