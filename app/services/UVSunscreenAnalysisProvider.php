<?php
/**
 * SkinCheck - UVSunscreenAnalysisProvider
 *
 * Future hardware provider abstraction for UV sensor & UV-bandpass camera devices.
 * Reserved for future hardware integration (UV-sensitive sensor arrays).
 */

require_once __DIR__ . '/SunscreenAnalysisProviderInterface.php';

class UVSunscreenAnalysisProvider implements SunscreenAnalysisProviderInterface {

    private string $providerName;

    public function __construct(string $providerName = 'SkinCheck-UV-Hardware-Sensor-v1') {
        $this->providerName = $providerName;
    }

    public function getProviderName(): string {
        return $this->providerName;
    }

    public function getAnalysisMethod(): string {
        return 'UV Reflectance Spectral Measurement';
    }

    public function getHardwareType(): string {
        return 'UV-Bandpass Specialized Sensor';
    }

    public function analyze(string $absoluteImagePath, string $outputHeatmapDir, array $options = []): SunscreenResult {
        // Placeholder for future specialized UV camera driver connection
        throw new SunscreenAnalysisException(
            message: 'UV Hardware sensor is not connected. Please use the standard RGB Visual Estimation provider.',
            providerName: $this->providerName
        );
    }
}
