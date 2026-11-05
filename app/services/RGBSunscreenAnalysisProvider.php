<?php
/**
 * SkinCheck - RGBSunscreenAnalysisProvider
 *
 * Visual Sunscreen Coverage Estimation Provider for Standard RGB Cameras.
 *
 * IMPORTANT: Standard RGB cameras do not possess UV bandpass filters or UV sensors.
 * This provider estimates visual application evenness based on optical reflectance,
 * surface sheen uniformity, and chromatic balance across anatomical facial zones.
 *
 * Terminology: "AI-based visual sunscreen coverage estimation" / "RGB Visual Estimation".
 */

require_once __DIR__ . '/SunscreenAnalysisProviderInterface.php';

class RGBSunscreenAnalysisProvider implements SunscreenAnalysisProviderInterface {

    private string $providerName;

    public function __construct(string $providerName = 'SkinCheck-RGB-Sunscreen-Engine-v1') {
        $this->providerName = $providerName;
    }

    public function getProviderName(): string {
        return $this->providerName;
    }

    public function getAnalysisMethod(): string {
        return 'RGB Visual Estimation';
    }

    public function getHardwareType(): string {
        return 'Standard RGB Camera';
    }

    /**
     * Analyze face image for sunscreen coverage evenness and generate coverage heatmap.
     */
    public function analyze(string $absoluteImagePath, string $outputHeatmapDir, array $options = []): SunscreenResult {
        // 1. Support simulated failure testing
        if (!empty($options['simulate_failure'])) {
            throw new SunscreenAnalysisException(
                message: 'Simulated AI sunscreen estimation engine timeout or provider unreachable.',
                providerName: $this->providerName,
                context: ['path' => $absoluteImagePath]
            );
        }

        // 2. Validate input image
        if (!file_exists($absoluteImagePath) || !is_readable($absoluteImagePath)) {
            throw new SunscreenAnalysisException(
                message: 'Input image file not found or inaccessible for sunscreen analysis: ' . $absoluteImagePath,
                providerName: $this->providerName
            );
        }

        $img = $this->loadImage($absoluteImagePath);
        if (!$img) {
            throw new SunscreenAnalysisException(
                message: 'Unable to decode image file for visual sunscreen estimation.',
                providerName: $this->providerName
            );
        }

        $w = imagesx($img);
        $h = imagesy($img);

        if ($w < 120 || $h < 120) {
            imagedestroy($img);
            throw new SunscreenAnalysisException(
                message: 'Image resolution is too low for facial zoning analysis.',
                providerName: $this->providerName
            );
        }

        try {
            // 3. Define facial zones (relative coordinates)
            $zones = $this->getFacialZones($w, $h);

            // 4. Sample and score each facial zone
            $zoneScores = [];
            $lowCoverageAreas = [];
            $leftLuma = 0;
            $rightLuma = 0;

            foreach ($zones as $zoneKey => $zone) {
                $zoneMetric = $this->evaluateZoneSheenAndReflectance($img, $zone);
                $zoneScores[$zoneKey] = $zoneMetric['score'];

                if ($zoneKey === 'pipi_kiri')  $leftLuma = $zoneMetric['luma'];
                if ($zoneKey === 'pipi_kanan') $rightLuma = $zoneMetric['luma'];

                if ($zoneMetric['score'] < 72) {
                    $lowCoverageAreas[] = $zone['label'];
                }
            }

            // 5. Calculate overall coverage score (weighted average)
            $weights = [
                'dahi' => 0.22,
                'pipi_kiri' => 0.22,
                'pipi_kanan' => 0.22,
                'hidung' => 0.16,
                'dagu' => 0.10,
                'bawah_mata' => 0.08,
            ];

            $weightedScore = 0;
            foreach ($weights as $key => $weight) {
                $weightedScore += ($zoneScores[$key] ?? 70) * $weight;
            }
            $coverageScore = (int)max(10, min(98, round($weightedScore)));
            $coverageStatus = SunscreenResult::determineStatus($coverageScore);

            // 6. Check lighting symmetry & quality for confidence score
            $lumaDelta = abs($leftLuma - $rightLuma);
            $symmetryFactor = max(0.0, 1.0 - ($lumaDelta / 120.0)); // Lower if one side is in deep shadow

            // Allow forced low confidence test option
            if (!empty($options['simulate_low_confidence'])) {
                $confidence = 0.48;
            } else {
                $resFactor = min(1.0, ($w * $h) / (1280 * 960));
                $confidence = round(0.55 + (0.25 * $symmetryFactor) + (0.15 * $resFactor), 2);
            }

            $isLowConfidence = ($confidence < 0.60);

            // 7. Generate Heatmap Visualization Overlay Image
            $heatmapRelativePath = $this->generateHeatmapImage($img, $w, $h, $zones, $zoneScores, $outputHeatmapDir);
            imagedestroy($img);

            // 8. Generate notes
            $notes = $this->buildNotes($coverageScore, $coverageStatus, $lowCoverageAreas, $confidence, $isLowConfidence);

            return new SunscreenResult(
                coverageScore: $coverageScore,
                coverageStatus: $coverageStatus,
                lowCoverageAreas: $lowCoverageAreas,
                heatmapPath: $heatmapRelativePath,
                confidence: $confidence,
                analysisMethod: $this->getAnalysisMethod(),
                hardwareType: $this->getHardwareType(),
                isLowConfidence: $isLowConfidence,
                status: 'completed',
                notes: $notes,
                errorMessage: null
            );

        } catch (Throwable $e) {
            if (isset($img) && is_resource($img)) {
                imagedestroy($img);
            }
            throw new SunscreenAnalysisException(
                message: 'Sunscreen visual estimation error: ' . $e->getMessage(),
                previous: $e,
                providerName: $this->providerName
            );
        }
    }

    // ── Private CV & Zoning Helpers ───────────────────────────────────────

    private function loadImage(string $path): ?\GdImage {
        $info = @getimagesize($path);
        if (!$info) return null;

        $mime = $info['mime'] ?? '';
        return match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png'  => @imagecreatefrompng($path),
            'image/webp' => @imagecreatefromwebp($path),
            default      => null,
        };
    }

    /**
     * Define the 6 functional facial zone bounding boxes.
     */
    private function getFacialZones(int $w, int $h): array {
        return [
            'dahi' => [
                'label' => 'Dahi (Forehead)',
                'x1' => (int)($w * 0.25), 'y1' => (int)($h * 0.15),
                'x2' => (int)($w * 0.75), 'y2' => (int)($h * 0.35),
            ],
            'hidung' => [
                'label' => 'Sekitar hidung & T-Zone',
                'x1' => (int)($w * 0.40), 'y1' => (int)($h * 0.36),
                'x2' => (int)($w * 0.60), 'y2' => (int)($h * 0.62),
            ],
            'pipi_kiri' => [
                'label' => 'Pipi kiri',
                'x1' => (int)($w * 0.15), 'y1' => (int)($h * 0.40),
                'x2' => (int)($w * 0.39), 'y2' => (int)($h * 0.68),
            ],
            'pipi_kanan' => [
                'label' => 'Pipi kanan',
                'x1' => (int)($w * 0.61), 'y1' => (int)($h * 0.40),
                'x2' => (int)($w * 0.85), 'y2' => (int)($h * 0.68),
            ],
            'bawah_mata' => [
                'label' => 'Area bawah mata',
                'x1' => (int)($w * 0.25), 'y1' => (int)($h * 0.35),
                'x2' => (int)($w * 0.75), 'y2' => (int)($h * 0.44),
            ],
            'dagu' => [
                'label' => 'Dagu (Chin)',
                'x1' => (int)($w * 0.35), 'y1' => (int)($h * 0.69),
                'x2' => (int)($w * 0.65), 'y2' => (int)($h * 0.85),
            ],
        ];
    }

    /**
     * Evaluate skin surface reflectance and sheen balance for a zone.
     */
    private function evaluateZoneSheenAndReflectance(\GdImage $img, array $zone): array {
        $x1 = max(0, $zone['x1']);
        $y1 = max(0, $zone['y1']);
        $x2 = min(imagesx($img) - 1, $zone['x2']);
        $y2 = min(imagesy($img) - 1, $zone['y2']);

        $step = max(2, (int)(min($x2 - $x1, $y2 - $y1) / 15));
        $lumas = [];
        $skinCount = 0;

        for ($y = $y1; $y <= $y2; $y += $step) {
            for ($x = $x1; $x <= $x2; $x += $step) {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                if ($r > 30 && $g > 20 && $b > 15 && $r >= $g) {
                    $skinCount++;
                    $lumas[] = 0.299 * $r + 0.587 * $g + 0.114 * $b;
                }
            }
        }

        if ($skinCount < 5 || empty($lumas)) {
            return ['score' => 68, 'luma' => 120];
        }

        $avgLuma = array_sum($lumas) / count($lumas);

        // Standard deviation of luminance in zone (even coverage produces smoother specular transition)
        $variance = 0;
        foreach ($lumas as $l) {
            $variance += pow($l - $avgLuma, 2);
        }
        $stdDev = sqrt($variance / count($lumas));

        // Even application produces balanced moderate sheen (avgLuma in 90-210 range, stdDev 6-25)
        $lumaSuitability = max(0.4, 1.0 - (abs($avgLuma - 150) / 160));
        $sheenEvenness = max(0.4, 1.0 - (min(35, $stdDev) / 45));

        $zoneScore = (int)max(20, min(98, round(88 * $lumaSuitability * $sheenEvenness + 10)));

        return ['score' => $zoneScore, 'luma' => $avgLuma];
    }

    /**
     * Generate visual Heatmap PNG/JPG with colored semi-transparent zones.
     */
    private function generateHeatmapImage(
        \GdImage $srcImg,
        int $w,
        int $h,
        array $zones,
        array $zoneScores,
        string $outputDir
    ): string {
        if (!is_dir($outputDir)) {
            @mkdir($outputDir, 0755, true);
        }

        // Create canvas for heatmap
        $heatmap = imagecreatetruecolor($w, $h);
        imagecopy($heatmap, $srcImg, 0, 0, 0, 0, $w, $h);

        // Enable alpha blending
        imagealphablending($heatmap, true);

        // Draw translucent zone overlay
        foreach ($zones as $key => $zone) {
            $score = $zoneScores[$key] ?? 70;

            // Choose color by score:
            // >= 85: Bright Green (well covered)
            // 70 - 84: Yellow-Green (moderate/good)
            // 50 - 69: Orange (low coverage)
            // < 50: Red (missed / unprotected)
            if ($score >= 85) {
                $color = imagecolorallocatealpha($heatmap, 40, 200, 80, 75); // Green (alpha 0-127)
            } elseif ($score >= 70) {
                $color = imagecolorallocatealpha($heatmap, 220, 190, 40, 75); // Yellow
            } elseif ($score >= 50) {
                $color = imagecolorallocatealpha($heatmap, 240, 120, 30, 70); // Orange
            } else {
                $color = imagecolorallocatealpha($heatmap, 230, 40, 40, 65);  // Red
            }

            // Draw filled rounded rectangle for the zone
            imagefilledrectangle($heatmap, $zone['x1'], $zone['y1'], $zone['x2'], $zone['y2'], $color);

            // Draw border
            $borderColor = imagecolorallocatealpha($heatmap, 255, 255, 255, 50);
            imagerectangle($heatmap, $zone['x1'], $zone['y1'], $zone['x2'], $zone['y2'], $borderColor);
        }

        $filename = 'heatmap_' . bin2hex(random_bytes(10)) . '_' . time() . '.jpg';
        $destPath = rtrim($outputDir, '/\\') . '/' . $filename;

        imagejpeg($heatmap, $destPath, 85);
        imagedestroy($heatmap);

        return $filename;
    }

    private function buildNotes(
        int $score,
        string $status,
        array $lowCoverageAreas,
        float $confidence,
        bool $isLowConfidence
    ): string {
        $confPct = round($confidence * 100);
        $notes = "Hasil estimasi visual pemerataan sunscreen menunjukkan status: {$status} ({$score}%).\n";

        if (!empty($lowCoverageAreas)) {
            $notes .= "• Area yang terindikasi perlu perhatian / aplikasi ulang: " . implode(', ', $lowCoverageAreas) . ".\n";
        } else {
            $notes .= "• Seluruh zona wajah utama terindikasi terlindungi dengan merata.\n";
        }

        $notes .= "• Metode: RGB Visual Estimation (Keyakinan: {$confPct}%).\n\n";

        if ($isLowConfidence) {
            $notes .= "Perhatian: Hasil kurang meyakinkan akibat variasi pencahayaan. Silakan ambil gambar ulang dengan pencahayaan merata dari arah depan.\n";
        }

        $notes .= "Pemberitahuan: Hasil ini merupakan estimasi visual berbasis AI dan bukan pengukuran UV langsung.";
        return $notes;
    }
}
