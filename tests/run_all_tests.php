<?php
/**
 * SkinCheck - Master Automated Test Runner for CI/CD Pipeline
 * Executes all core unit and integration test suites.
 */

echo "\n======================================================\n";
echo "    🌿 SkinCheck Automated CI/CD Test Pipeline       \n";
echo "    Author: Satria Fibi Saputra (NIM: 41257159)      \n";
echo "    Curriculum: OBE STMIK IKMI Cirebon               \n";
echo "======================================================\n\n";

$suites = [
    'Skin Condition 4-Pillar AI Analysis' => __DIR__ . '/test_skin_analysis.php',
    'Sunscreen Coverage & Heatmap Engine' => __DIR__ . '/test_sunscreen_analysis.php'
];

$failedSuites = 0;

foreach ($suites as $name => $path) {
    echo "======================================================\n";
    echo "▶ RUNNING TEST SUITE: {$name}\n";
    echo "======================================================\n";

    passthru("php " . escapeshellarg($path), $returnCode);

    if ($returnCode !== 0) {
        $failedSuites++;
        echo "\n❌ [ERROR] Suite '{$name}' exited with status code: {$returnCode}\n\n";
    } else {
        echo "\n✅ [SUCCESS] Suite '{$name}' completed successfully.\n\n";
    }
}

echo "======================================================\n";
echo "                 PIPELINE SUMMARY                     \n";
echo "======================================================\n";

if ($failedSuites === 0) {
    echo "🎉 ALL TEST SUITES PASSED! STATUS: GREEN (PASSED)\n";
    echo "Ready for production deployment.\n";
    exit(0);
} else {
    echo "⚠️ {$failedSuites} TEST SUITE(S) FAILED! STATUS: RED\n";
    exit(1);
}
