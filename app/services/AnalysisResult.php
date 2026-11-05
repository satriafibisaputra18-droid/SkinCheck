<?php
/**
 * SkinCheck - AnalysisResult
 *
 * Immutable Value Object representing the output of a skin visual analysis.
 * All terminology strictly adheres to non-medical visual indicator phrasing:
 * (indication, score, visual change, estimated, confidence).
 */

class AnalysisResult {
    public readonly int $acneScore;
    public readonly int $rednessScore;
    public readonly int $textureScore;
    public readonly int $hyperpigmentationScore;
    public readonly int $overallScore;
    public readonly float $confidence;          // 0.0 to 1.0 (e.g. 0.92 = 92% estimated confidence)
    public readonly string $analysisStatus;      // 'completed' | 'failed'
    public readonly array $visualIndications;    // List of qualitative visual observation notes
    public readonly string $notes;               // Summary of visual indications
    public readonly ?string $errorMessage;       // Error explanation if failed
    public readonly string $providerName;        // AI / CV provider identifier

    public function __construct(
        int $acneScore,
        int $rednessScore,
        int $textureScore,
        int $hyperpigmentationScore,
        int $overallScore,
        float $confidence = 0.85,
        string $analysisStatus = 'completed',
        array $visualIndications = [],
        string $notes = '',
        ?string $errorMessage = null,
        string $providerName = 'ComputerVision-Local'
    ) {
        $this->acneScore = max(0, min(100, $acneScore));
        $this->rednessScore = max(0, min(100, $rednessScore));
        $this->textureScore = max(0, min(100, $textureScore));
        $this->hyperpigmentationScore = max(0, min(100, $hyperpigmentationScore));
        $this->overallScore = max(0, min(100, $overallScore));
        $this->confidence = max(0.0, min(1.0, round($confidence, 2)));
        $this->analysisStatus = in_array($analysisStatus, ['completed', 'failed', 'processing', 'pending'], true)
            ? $analysisStatus
            : 'completed';
        $this->visualIndications = $visualIndications;
        $this->notes = $notes;
        $this->errorMessage = $errorMessage;
        $this->providerName = $providerName;
    }

    /**
     * Factory for a failed analysis result.
     */
    public static function failure(string $errorMessage, string $providerName = 'ComputerVision-Local'): self {
        return new self(
            acneScore: 0,
            rednessScore: 0,
            textureScore: 0,
            hyperpigmentationScore: 0,
            overallScore: 0,
            confidence: 0.0,
            analysisStatus: 'failed',
            visualIndications: [],
            notes: 'Analisis visual gagal diproses: ' . $errorMessage,
            errorMessage: $errorMessage,
            providerName: $providerName
        );
    }

    /**
     * Convert result to structured array.
     */
    public function toArray(): array {
        return [
            'acne_score' => $this->acneScore,
            'redness_score' => $this->rednessScore,
            'texture_score' => $this->textureScore,
            'hyperpigmentation_score' => $this->hyperpigmentationScore,
            'overall_score' => $this->overallScore,
            'confidence' => $this->confidence,
            'confidence_percentage' => round($this->confidence * 100),
            'analysis_status' => $this->analysisStatus,
            'visual_indications' => $this->visualIndications,
            'notes' => $this->notes,
            'error_message' => $this->errorMessage,
            'provider_name' => $this->providerName,
        ];
    }

    /**
     * Convert to JSON representation for DB storage or API output.
     */
    public function toJson(): string {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
