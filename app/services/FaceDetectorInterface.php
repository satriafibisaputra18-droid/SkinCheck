<?php
/**
 * SkinCheck - FaceDetectorInterface
 *
 * Abstraction layer for face detection so a real Computer Vision model
 * (MediaPipe, AWS Rekognition, local ML model, etc.) can be plugged in
 * later without touching the controller or validator.
 *
 * CONTRACT:
 *   detect(string $absoluteImagePath): FaceDetectionResult
 */

interface FaceDetectorInterface {
    /**
     * Analyse an image for face presence.
     *
     * @param string $imagePath Absolute path to the image file on disk.
     * @return FaceDetectionResult
     */
    public function detect(string $imagePath): FaceDetectionResult;
}

/**
 * Value object returned by any FaceDetector implementation.
 */
class FaceDetectionResult {
    public readonly bool   $faceFound;
    public readonly int    $faceCount;
    public readonly float  $confidence;   // 0.0 – 1.0
    public readonly string $message;

    public function __construct(
        bool   $faceFound,
        int    $faceCount   = 0,
        float  $confidence  = 0.0,
        string $message     = ''
    ) {
        $this->faceFound   = $faceFound;
        $this->faceCount   = $faceCount;
        $this->confidence  = $confidence;
        $this->message     = $message;
    }
}
