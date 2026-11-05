<?php
/**
 * SkinCheck - Sunscreen Evenness Detection Automated Verification Test Suite
 *
 * Tests:
 * 1. RGB Sunscreen Visual Estimation Flow (coverage score, status, zones, heatmap generation)
 * 2. Terminology Compliance (strictly non-UV claims for standard RGB camera)
 * 3. Low Confidence Condition Detection (< 0.60)
 * 4. AI Provider Failure & Error Handling
 * 5. Invalid / Missing Image Handling
 * 6. UV Provider Abstraction & Value Object Serialization
 */

require_once dirname(__DIR__) . '/app/config/config.php';
require_once APPROOT . '/core/Model.php';
require_once APPROOT . '/core/Database.php';
require_once APPROOT . '/models/Scan.php';
require_once APPROOT . '/services/SunscreenResult.php';
require_once APPROOT . '/services/SunscreenAnalysisException.php';
require_once APPROOT . '/services/SunscreenAnalysisProviderInterface.php';
require_once APPROOT . '/services/RGBSunscreenAnalysisProvider.php';
require_once APPROOT . '/services/UVSunscreenAnalysisProvider.php';
require_once APPROOT . '/services/SunscreenAnalysisService.php';

echo "==============================================================\n";
echo "   SkinCheck - Sunscreen Evenness Detection Test Suite\n";
echo "==============================================================\n\n";

$testsPassed = 0;
$totalTests = 0;

function assertCondition(bool $condition, string $testName): void {
    global $testsPassed, $totalTests;
    $totalTests++;
    if ($condition) {
        $testsPassed++;
        echo " [PASS] " . $testName . "\n";
    } else {
        echo " [FAIL] " . $testName . "\n";
    }
}

// ── 1. Create a synthetic face image for sunscreen testing ────────────
$tmpDir = sys_get_temp_dir();
$testImgPath = $tmpDir . '/skincheck_sunscreen_test_' . time() . '.jpg';
$heatmapOutDir = $tmpDir . '/skincheck_heatmaps_' . time();
@mkdir($heatmapOutDir, 0755, true);

$im = imagecreatetruecolor(400, 400);
$skin = imagecolorallocate($im, 215, 175, 145);
imagefilledrectangle($im, 0, 0, 400, 400, $skin);

// Simulate forehead & cheeks with moderate sheen
$sheen = imagecolorallocate($im, 228, 188, 158);
imagefilledellipse($im, 200, 100, 160, 50, $sheen); // forehead
imagefilledellipse($im, 120, 220, 70, 70, $sheen);   // left cheek

// Simulate lower coverage / shadow area around nose
$noseShadow = imagecolorallocate($im, 170, 130, 105);
imagefilledellipse($im, 200, 200, 35, 45, $noseShadow);

imagejpeg($im, $testImgPath, 90);
imagedestroy($im);

// ── Test Suite 1: RGB Sunscreen Visual Estimation Flow ───────────────
echo "\n--- Test Suite 1: RGB Sunscreen Visual Estimation Flow ---\n";
$provider = new RGBSunscreenAnalysisProvider();
$service = new SunscreenAnalysisService($provider, null, $tmpDir);

$result = $service->analyzeSunscreen(scanId: 0, userId: 1, absoluteImagePath: $testImgPath);

assertCondition($result instanceof SunscreenResult, "Result is instance of SunscreenResult");
assertCondition($result->status === 'completed', "Status is 'completed'");
assertCondition($result->coverageScore >= 0 && $result->coverageScore <= 100, "Coverage score ({$result->coverageScore}%) is within [0, 100]");
assertCondition(in_array($result->coverageStatus, ['Sangat Merata', 'Cukup Merata', 'Kurang Merata', 'Belum Terlindungi'], true), "Coverage status is valid ({$result->coverageStatus})");
assertCondition(!empty($result->heatmapPath), "Heatmap path is generated: {$result->heatmapPath}");
assertCondition($result->confidence >= 0.50 && $result->confidence <= 1.0, "Confidence ({$result->confidence}) is within valid range");
assertCondition($result->analysisMethod === 'RGB Visual Estimation', "Analysis method is 'RGB Visual Estimation'");
assertCondition($result->hardwareType === 'Standard RGB Camera', "Hardware type is 'Standard RGB Camera'");
assertCondition(is_array($result->lowCoverageAreas), "Low coverage areas is an array");

// ── Test Suite 2: Terminology Compliance (Non-UV Claims) ─────────────
echo "\n--- Test Suite 2: Terminology & Non-UV Compliance ---\n";
$disclaimer = $result->disclaimer;
assertCondition(str_contains($disclaimer, 'bukan pengukuran UV langsung'), "Disclaimer explicitly states non-UV measurement: '{$disclaimer}'");

$allNotes = strtolower($result->notes . ' ' . $result->analysisMethod . ' ' . $result->hardwareType);
// Remove the valid negative disclaimer phrase to check for any positive/false claims
$strippedNotes = str_replace('bukan pengukuran uv langsung', '', $allNotes);
$prohibitedClaims = ['uv sensor measurement', 'pengukuran uv langsung', 'uv protection measurement', 'sensor uv terkalibrasi', 'direct uv reading'];
$foundClaim = false;
foreach ($prohibitedClaims as $claim) {
    if (str_contains($strippedNotes, $claim)) {
        $foundClaim = true;
        break;
    }
}
assertCondition(!$foundClaim, "Strict compliance: RGB camera makes zero positive claims of direct UV measurement");

// ── Test Suite 3: Low Confidence Detection (< 0.60) ───────────────────
echo "\n--- Test Suite 3: Low Confidence Condition Handling ---\n";
$lowConfResult = $service->analyzeSunscreen(
    scanId: 0,
    userId: 1,
    absoluteImagePath: $testImgPath,
    options: ['simulate_low_confidence' => true]
);

assertCondition($lowConfResult->isLowConfidence === true, "Low confidence flag is TRUE when confidence is < 0.60");
assertCondition($lowConfResult->confidence < 0.60, "Confidence is {$lowConfResult->confidence}");
assertCondition(str_contains($lowConfResult->notes, 'Hasil kurang meyakinkan') || str_contains($lowConfResult->notes, 'pencahayaan'), "Notes contain low-confidence advisory");

// ── Test Suite 4: AI Failure Handling ─────────────────────────────────
echo "\n--- Test Suite 4: AI Failure & Error Recovery ---\n";
$failResult = $service->analyzeSunscreen(
    scanId: 0,
    userId: 1,
    absoluteImagePath: $testImgPath,
    options: ['simulate_failure' => true]
);

assertCondition($failResult->status === 'failed', "Failure returns status 'failed'");
assertCondition(!empty($failResult->errorMessage), "Error message is recorded: {$failResult->errorMessage}");
assertCondition($failResult->coverageScore === 0, "Coverage score is 0 on failure");

// ── Test Suite 5: Invalid / Missing Image Handling ────────────────────
echo "\n--- Test Suite 5: Invalid Image Handling ---\n";
$invalidResult = $service->analyzeSunscreen(
    scanId: 0,
    userId: 1,
    absoluteImagePath: $tmpDir . '/missing_sunscreen_img_' . rand(100, 999) . '.jpg'
);
assertCondition($invalidResult->status === 'failed', "Missing image handled as 'failed'");

// ── Test Suite 6: UV Provider Abstraction & Array Serialization ───────
echo "\n--- Test Suite 6: UV Provider Abstraction & Serialization ---\n";
$uvProvider = new UVSunscreenAnalysisProvider();
assertCondition($uvProvider->getHardwareType() === 'UV-Bandpass Specialized Sensor', "UV Provider declares UV hardware classification");

$arrayData = $result->toArray();
assertCondition(isset($arrayData['coverage_score']) && isset($arrayData['disclaimer']), "toArray() contains required keys");
assertCondition(isset($arrayData['analysis_method']) && $arrayData['analysis_method'] === 'RGB Visual Estimation', "toArray() preserves method");

// Clean up temporary files
if (file_exists($testImgPath)) unlink($testImgPath);

echo "\n==============================================================\n";
echo "   Sunscreen Tests Completed: {$testsPassed} / {$totalTests} Passed\n";
echo "==============================================================\n";

exit($testsPassed === $totalTests ? 0 : 1);
