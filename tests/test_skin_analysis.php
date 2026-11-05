<?php
/**
 * SkinCheck - Skin Condition Analysis Automated Verification Test
 *
 * Tests:
 * 1. Successful Analysis (5 MVP indicators, confidence, compliant terminology)
 * 2. AI Provider Failure & Retry Fallback
 * 3. Invalid / Missing Image Handling
 * 4. Value Object Serialization & Deserialization
 */

require_once dirname(__DIR__) . '/app/config/config.php';
require_once APPROOT . '/core/Model.php';
require_once APPROOT . '/core/Database.php';
require_once APPROOT . '/models/Scan.php';
require_once APPROOT . '/services/AnalysisResult.php';
require_once APPROOT . '/services/SkinAnalysisException.php';
require_once APPROOT . '/services/SkinAnalysisProviderInterface.php';
require_once APPROOT . '/services/ComputerVisionSkinProvider.php';
require_once APPROOT . '/services/SkinAnalysisService.php';

echo "======================================================\n";
echo "   SkinCheck - Skin Condition Analysis Unit & Flow Tests\n";
echo "======================================================\n\n";

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

// ── 1. Create a sample synthetic facial patch image for testing ──────
$tmpDir = sys_get_temp_dir();
$testImgPath = $tmpDir . '/skincheck_test_face_' . time() . '.jpg';

$im = imagecreatetruecolor(400, 400);
// Warm natural skin background (R: 220, G: 180, B: 150)
$skinColor = imagecolorallocate($im, 220, 180, 150);
imagefilledrectangle($im, 0, 0, 400, 400, $skinColor);

// Add slight redness area (cheek)
$rednessColor = imagecolorallocate($im, 235, 150, 140);
imagefilledellipse($im, 150, 200, 80, 60, $rednessColor);

// Add mild spot / hyperpigmentation area
$spotColor = imagecolorallocate($im, 160, 120, 95);
imagefilledellipse($im, 260, 220, 15, 15, $spotColor);

imagejpeg($im, $testImgPath, 90);
imagedestroy($im);

// ── Test 1: Successful Analysis with ComputerVisionSkinProvider ───────
echo "\n--- Test Suite 1: Successful Visual Skin Analysis ---\n";
$provider = new ComputerVisionSkinProvider('SkinCheck-CV-Engine-v1');
$service = new SkinAnalysisService($provider, null, 2);

$result = $service->analyzeScan(scanId: 0, userId: 1, absoluteImagePath: $testImgPath);

assertCondition($result instanceof AnalysisResult, "Result is instance of AnalysisResult");
assertCondition($result->analysisStatus === 'completed', "Status is 'completed'");
assertCondition($result->acneScore >= 0 && $result->acneScore <= 100, "Acne score ({$result->acneScore}) is within [0, 100]");
assertCondition($result->rednessScore >= 0 && $result->rednessScore <= 100, "Redness score ({$result->rednessScore}) is within [0, 100]");
assertCondition($result->textureScore >= 0 && $result->textureScore <= 100, "Texture score ({$result->textureScore}) is within [0, 100]");
assertCondition($result->hyperpigmentationScore >= 0 && $result->hyperpigmentationScore <= 100, "Hyperpigmentation score ({$result->hyperpigmentationScore}) is within [0, 100]");
assertCondition($result->overallScore >= 0 && $result->overallScore <= 100, "Overall score ({$result->overallScore}) is within [0, 100]");
assertCondition($result->confidence >= 0.5 && $result->confidence <= 1.0, "Confidence ({$result->confidence}) is within [0.5, 1.0]");
assertCondition(!empty($result->visualIndications), "Visual indications list is populated");
assertCondition(!empty($result->notes), "Visual summary notes generated");

// Verify Terminology Compliance (must NOT contain prohibited diagnosis words)
$forbiddenWords = ['diagnosis', 'penyakit', 'terapi obat', 'resep', 'confirmed disease'];
$allText = strtolower($result->notes . ' ' . implode(' ', $result->visualIndications));
$foundForbidden = false;
foreach ($forbiddenWords as $w) {
    if (str_contains($allText, $w)) {
        $foundForbidden = true;
        break;
    }
}
assertCondition(!$foundForbidden, "Terminology compliance: No medical diagnosis / disease terms present");

// ── Test Suite 2: AI Provider Failure & Retry Simulation ─────────────
echo "\n--- Test Suite 2: AI Failure & Recovery Simulation ---\n";
$failResult = $service->analyzeScan(
    scanId: 0,
    userId: 1,
    absoluteImagePath: $testImgPath,
    options: ['simulate_failure' => true]
);

assertCondition($failResult instanceof AnalysisResult, "Failure returns AnalysisResult");
assertCondition($failResult->analysisStatus === 'failed', "Failure status is marked as 'failed'");
assertCondition(!empty($failResult->errorMessage), "Error message is recorded: {$failResult->errorMessage}");
assertCondition($failResult->overallScore === 0, "Overall score reset on failure");

// Recovery retry with valid options
$recoveredResult = $service->analyzeScan(
    scanId: 0,
    userId: 1,
    absoluteImagePath: $testImgPath,
    options: ['simulate_failure' => false]
);
assertCondition($recoveredResult->analysisStatus === 'completed', "Retry analysis recovered to 'completed'");

// ── Test Suite 3: Invalid / Missing Image Handling ────────────────────
echo "\n--- Test Suite 3: Invalid Image File Handling ---\n";
$invalidPath = $tmpDir . '/non_existent_image_' . rand(1000, 9999) . '.jpg';
$invalidResult = $service->analyzeScan(
    scanId: 0,
    userId: 1,
    absoluteImagePath: $invalidPath
);

assertCondition($invalidResult->analysisStatus === 'failed', "Missing image gracefully handled as 'failed'");
assertCondition(str_contains($invalidResult->errorMessage, 'not found') || str_contains($invalidResult->errorMessage, 'inaccessible'), "Error message mentions missing file");

// ── Test Suite 4: Serialization & Array Representation ────────────────
echo "\n--- Test Suite 4: AnalysisResult Serialization ---\n";
$arrayData = $result->toArray();
assertCondition(isset($arrayData['acne_score']) && isset($arrayData['overall_score']), "toArray() contains required keys");
assertCondition(isset($arrayData['confidence_percentage']), "toArray() contains confidence_percentage");

$json = $result->toJson();
$decoded = json_decode($json, true);
assertCondition($decoded !== null && $decoded['overall_score'] === $result->overallScore, "toJson() output is valid JSON matching original result");

// Cleanup temp image
if (file_exists($testImgPath)) {
    unlink($testImgPath);
}

echo "\n======================================================\n";
echo "   Tests Completed: {$testsPassed} / {$totalTests} Passed\n";
echo "======================================================\n";

exit($testsPassed === $totalTests ? 0 : 1);
