<?php
/**
 * SkinCheck - SkinAnalysisProviderInterface
 *
 * Pluggable contract for Computer Vision / AI analysis providers.
 * Allows switching between local CV heuristic analyzer, cloud ML API,
 * MediaPipe, or external deep learning services without modifying the core business logic.
 */

require_once __DIR__ . '/AnalysisResult.php';
require_once __DIR__ . '/SkinAnalysisException.php';

interface SkinAnalysisProviderInterface {
    /**
     * Analyzes skin features from an image file path.
     *
     * @param string $absoluteImagePath Absolute path to the image on disk.
     * @param array $options Optional execution parameters (e.g. timeout, thresholds, simulation flags).
     * @return AnalysisResult
     * @throws SkinAnalysisException When provider cannot analyze the image.
     */
    public function analyze(string $absoluteImagePath, array $options = []): AnalysisResult;

    /**
     * Return provider identification name.
     *
     * @return string
     */
    public function getProviderName(): string;
}
