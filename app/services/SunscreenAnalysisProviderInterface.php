<?php
/**
 * SkinCheck - SunscreenAnalysisProviderInterface
 *
 * Abstraction layer for sunscreen evenness analysis providers.
 * Allows decoupling between:
 * 1. RGBSunscreenAnalysisProvider (Standard RGB camera visual estimation for MVP)
 * 2. UVSunscreenAnalysisProvider (Future UV-capable hardware integration)
 *
 * Enforces hardware classification transparency so standard RGB cameras
 * never claim UV measurement.
 */

require_once __DIR__ . '/SunscreenResult.php';
require_once __DIR__ . '/SunscreenAnalysisException.php';

interface SunscreenAnalysisProviderInterface {
    /**
     * Analyze image for sunscreen coverage evenness.
     *
     * @param string $absoluteImagePath Path to the face image file.
     * @param string $outputHeatmapDir Directory where heatmap visualizer output should be saved.
     * @param array $options Optional execution flags (e.g. simulate_failure, low_confidence_test).
     * @return SunscreenResult
     * @throws SunscreenAnalysisException
     */
    public function analyze(string $absoluteImagePath, string $outputHeatmapDir, array $options = []): SunscreenResult;

    /**
     * Return provider identification name.
     */
    public function getProviderName(): string;

    /**
     * Return analysis methodology description (e.g. "RGB Visual Estimation").
     */
    public function getAnalysisMethod(): string;

    /**
     * Return hardware classification (e.g. "Standard RGB Camera" vs "UV Sensor / UV-Band Camera").
     */
    public function getHardwareType(): string;
}
