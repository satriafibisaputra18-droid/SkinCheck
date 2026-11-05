<?php
/**
 * SkinCheck - NullFaceDetector
 *
 * Null-Object implementation of FaceDetectorInterface.
 * Always returns "face assumed present" so the scan pipeline works
 * end-to-end before a real CV model is integrated.
 *
 * Replace this class (or inject a different implementation via DI)
 * when a real face detection model becomes available.
 */

require_once __DIR__ . '/FaceDetectorInterface.php';

class NullFaceDetector implements FaceDetectorInterface {

    public function detect(string $imagePath): FaceDetectionResult {
        // No model available yet — assume face is present.
        // Log so developers know this is a stub.
        error_log('[NullFaceDetector] Face detection skipped for: ' . $imagePath);

        return new FaceDetectionResult(
            faceFound:  true,
            faceCount:  1,
            confidence: 0.0,
            message:    'No face detection model configured. Detection skipped.'
        );
    }
}
