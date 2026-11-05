<?php
/**
 * SkinCheck - SunscreenResult
 *
 * Immutable Value Object representing the visual sunscreen coverage analysis output.
 * Strictly adheres to non-UV terminology for standard RGB cameras:
 * (AI-based visual sunscreen coverage estimation, RGB Visual Estimation, Standard RGB Camera).
 */

class SunscreenResult {
    public readonly int $coverageScore;          // 0 - 100 percentage score
    public readonly string $coverageStatus;      // e.g. "Sangat Merata", "Cukup Merata", "Kurang Merata", "Belum Terlindungi"
    public readonly array $lowCoverageAreas;     // e.g. ["Sekitar hidung", "Pipi kanan"]
    public readonly string $heatmapPath;         // Relative storage path to generated heatmap overlay
    public readonly float $confidence;           // 0.0 - 1.0
    public readonly string $analysisMethod;      // e.g. "RGB Visual Estimation"
    public readonly string $hardwareType;        // e.g. "Standard RGB Camera"
    public readonly bool $isLowConfidence;       // True if confidence < 0.60
    public readonly string $status;              // 'completed' | 'failed'
    public readonly string $notes;               // Summary description
    public readonly ?string $errorMessage;       // Error description if failed
    public readonly string $disclaimer;          // Mandatory non-UV clarification

    public function __construct(
        int $coverageScore,
        string $coverageStatus,
        array $lowCoverageAreas = [],
        string $heatmapPath = '',
        float $confidence = 0.85,
        string $analysisMethod = 'RGB Visual Estimation',
        string $hardwareType = 'Standard RGB Camera',
        bool $isLowConfidence = false,
        string $status = 'completed',
        string $notes = '',
        ?string $errorMessage = null
    ) {
        $this->coverageScore = max(0, min(100, $coverageScore));
        $this->coverageStatus = $coverageStatus;
        $this->lowCoverageAreas = $lowCoverageAreas;
        $this->heatmapPath = $heatmapPath;
        $this->confidence = max(0.0, min(1.0, round($confidence, 2)));
        $this->analysisMethod = $analysisMethod;
        $this->hardwareType = $hardwareType;
        $this->isLowConfidence = $isLowConfidence || ($this->confidence < 0.60);
        $this->status = in_array($status, ['completed', 'failed', 'processing', 'pending'], true) ? $status : 'completed';
        $this->notes = $notes;
        $this->errorMessage = $errorMessage;
        $this->disclaimer = 'Hasil ini merupakan estimasi visual berbasis AI dan bukan pengukuran UV langsung.';
    }

    /**
     * Helper factory for failed analysis.
     */
    public static function failure(
        string $errorMessage,
        string $method = 'RGB Visual Estimation',
        string $hardware = 'Standard RGB Camera'
    ): self {
        return new self(
            coverageScore: 0,
            coverageStatus: 'Gagal Dianalisis',
            lowCoverageAreas: [],
            heatmapPath: '',
            confidence: 0.0,
            analysisMethod: $method,
            hardwareType: $hardware,
            isLowConfidence: true,
            status: 'failed',
            notes: 'Analisis pemerataan sunscreen gagal: ' . $errorMessage,
            errorMessage: $errorMessage
        );
    }

    /**
     * Map numerical score (0-100) to standard coverage status string.
     */
    public static function determineStatus(int $score): string {
        if ($score >= 85) return 'Sangat Merata';
        if ($score >= 70) return 'Cukup Merata';
        if ($score >= 50) return 'Kurang Merata';
        return 'Belum Terlindungi';
    }

    /**
     * Convert to array for JSON responses and database mapping.
     */
    public function toArray(): array {
        return [
            'coverage_score' => $this->coverageScore,
            'coverage_status' => $this->coverageStatus,
            'low_coverage_areas' => $this->lowCoverageAreas,
            'low_coverage_areas_text' => implode(', ', $this->lowCoverageAreas),
            'heatmap_path' => $this->heatmapPath,
            'confidence' => $this->confidence,
            'confidence_percentage' => round($this->confidence * 100),
            'analysis_method' => $this->analysisMethod,
            'hardware_type' => $this->hardwareType,
            'is_low_confidence' => $this->isLowConfidence,
            'status' => $this->status,
            'notes' => $this->notes,
            'error_message' => $this->errorMessage,
            'disclaimer' => $this->disclaimer,
        ];
    }

    public function toJson(): string {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
