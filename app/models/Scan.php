<?php
/**
 * SkinCheck - Scan Model
 * Handles database operations for skin scans, analysis results, and sunscreen checks.
 * All customer-facing queries are strictly scoped to the authenticated user's ID.
 */

class Scan extends Model {

    /**
     * Get all scans for a specific user (most recent first).
     * Strictly filtered by user_id – customers only ever see their own data.
     * @param int $userId
     * @return array
     */
    public function getScansByUserId($userId) {
        if ($this->db->isConnected()) {
            $this->db->query("
                SELECT
                    s.id,
                    s.user_id,
                    s.image_path,
                    s.status,
                    s.image_quality_score,
                    s.created_at,
                    a.acne_score,
                    a.redness_score,
                    a.texture_score,
                    a.hyperpigmentation_score,
                    a.overall_score,
                    a.ai_response        AS notes,
                    sn.coverage_score    AS sunscreen_coverage_score,
                    sn.coverage_status   AS sunscreen_status
                FROM skin_scans s
                LEFT JOIN analysis_results  a  ON s.id = a.skin_scan_id
                LEFT JOIN sunscreen_results sn ON s.id = sn.skin_scan_id
                WHERE s.user_id = :user_id
                ORDER BY s.created_at DESC
            ");
            $this->db->bind(':user_id', (int)$userId);
            return $this->db->resultSet();
        }
        return [];
    }

    /**
     * Get a single scan by its ID, strictly scoped to the given user_id.
     * Returns false if the scan does not belong to the user.
     * @param int $scanId
     * @param int $userId  The authenticated user's ID (security scope)
     * @return object|false
     */
    public function getScanById($scanId, $userId) {
        if ($this->db->isConnected()) {
            $this->db->query("
                SELECT
                    s.id,
                    s.user_id,
                    s.image_path,
                    s.status,
                    s.image_quality_score,
                    s.created_at,
                    a.acne_score,
                    a.redness_score,
                    a.texture_score,
                    a.hyperpigmentation_score,
                    a.overall_score,
                    a.ai_response        AS notes,
                    sn.coverage_score    AS sunscreen_coverage_score,
                    sn.coverage_status   AS sunscreen_status,
                    sn.heatmap_path      AS sunscreen_heatmap_path,
                    sn.low_coverage_areas,
                    sn.analysis_method   AS sunscreen_analysis_method,
                    sn.hardware_type     AS sunscreen_hardware_type
                FROM skin_scans s
                LEFT JOIN analysis_results  a  ON s.id = a.skin_scan_id
                LEFT JOIN sunscreen_results sn ON s.id = sn.skin_scan_id
                WHERE s.id = :scan_id AND s.user_id = :user_id
                LIMIT 1
            ");
            $this->db->bind(':scan_id', (int)$scanId);
            $this->db->bind(':user_id', (int)$userId);
            $scan = $this->db->single();

            if ($scan) {
                // Parse ai_response if it contains JSON payload
                $scan->confidence = null;
                $scan->visual_indications = [];
                $scan->error_message = null;
                $scan->provider_name = 'SkinCheck-CV-Engine';

                if (!empty($scan->notes)) {
                    $decoded = json_decode($scan->notes, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $scan->confidence = $decoded['confidence'] ?? null;
                        $scan->visual_indications = $decoded['visual_indications'] ?? [];
                        $scan->error_message = $decoded['error_message'] ?? null;
                        $scan->provider_name = $decoded['provider_name'] ?? 'SkinCheck-CV-Engine';
                        $scan->notes_text = $decoded['notes'] ?? '';
                    } else {
                        $scan->notes_text = $scan->notes;
                    }
                } else {
                    $scan->notes_text = '';
                }

                // Parse low_coverage_areas if JSON
                $scan->low_coverage_areas_list = [];
                if (!empty($scan->low_coverage_areas)) {
                    $decodedAreas = json_decode($scan->low_coverage_areas, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedAreas)) {
                        $scan->low_coverage_areas_list = $decodedAreas;
                    } else {
                        $scan->low_coverage_areas_list = array_filter(array_map('trim', explode(',', $scan->low_coverage_areas)));
                    }
                }
            }

            return $scan;
        }
        return false;
    }

    /**
     * Get total scan count for a specific user.
     * @param int $userId
     * @return int
     */
    public function getScansCountByUserId($userId) {
        if ($this->db->isConnected()) {
            $this->db->query('SELECT COUNT(*) AS cnt FROM skin_scans WHERE user_id = :user_id');
            $this->db->bind(':user_id', (int)$userId);
            $row = $this->db->single();
            return $row ? (int)$row->cnt : 0;
        }
        return 0;
    }

    /**
     * Get recent scans for admin portal.
     * @return array
     */
    public function getRecentScans() {
        if ($this->db->isConnected()) {
            $this->db->query("
                SELECT
                    s.id,
                    u.name             AS user_name,
                    a.overall_score    AS score,
                    sn.coverage_status AS sunscreen_status,
                    s.created_at
                FROM skin_scans s
                JOIN users u ON s.user_id = u.id
                LEFT JOIN analysis_results  a  ON s.id = a.skin_scan_id
                LEFT JOIN sunscreen_results sn ON s.id = sn.skin_scan_id
                ORDER BY s.created_at DESC
                LIMIT 10
            ");
            return $this->db->resultSet();
        }
        return [];
    }

    /**
     * Get aggregate platform scan metrics for admin dashboard.
     * @return object
     */
    public function getAdminMetrics() {
        if ($this->db->isConnected()) {
            $this->db->query("
                SELECT 
                    COUNT(s.id) as total_scans,
                    COALESCE(ROUND(AVG(a.overall_score), 1), 0) as avg_score
                FROM skin_scans s
                LEFT JOIN analysis_results a ON s.id = a.skin_scan_id
            ");
            return $this->db->single();
        }
        return (object)['total_scans' => 0, 'avg_score' => 0];
    }

    /**
     * Create a new skin_scans row with 'pending' status.
     * Returns the new scan ID so the caller can update it after validation.
     * @param int    $userId
     * @param string $status  'pending' | 'processing' | 'completed' | 'failed'
     * @return int|false  New scan ID or false on failure
     */
    public function createPendingScan(int $userId, string $status = 'pending') {
        if (!$this->db->isConnected()) return false;

        $this->db->query(
            'INSERT INTO skin_scans (user_id, image_path, status, image_quality_score)
             VALUES (:uid, :img, :status, :iq)'
        );
        $this->db->bind(':uid',    $userId);
        $this->db->bind(':img',    '');
        $this->db->bind(':status', $status);
        $this->db->bind(':iq',     0.0);

        if (!$this->db->execute()) return false;
        return (int)$this->db->lastInsertId();
    }

    /**
     * Update the status and optional image path of a scan row.
     * Scoped to the owner user_id so a user can never update another's row.
     * @param int    $scanId
     * @param int    $userId  Owner (security scope)
     * @param string $status
     * @param string $imagePath  Relative storage path (empty = no change)
     * @param float  $qualityScore
     * @return bool
     */
    public function updateScanStatus(
        int    $scanId,
        int    $userId,
        string $status,
        string $imagePath   = '',
        float  $qualityScore = 0.0
    ): bool {
        if (!$this->db->isConnected()) return false;

        if ($imagePath !== '') {
            $this->db->query(
                'UPDATE skin_scans
                 SET status = :status, image_path = :img, image_quality_score = :iq
                 WHERE id = :id AND user_id = :uid
                 LIMIT 1'
            );
            $this->db->bind(':img', $imagePath);
        } else {
            $this->db->query(
                'UPDATE skin_scans
                 SET status = :status, image_quality_score = :iq
                 WHERE id = :id AND user_id = :uid
                 LIMIT 1'
            );
        }

        $this->db->bind(':status', $status);
        $this->db->bind(':iq',     $qualityScore);
        $this->db->bind(':id',     $scanId);
        $this->db->bind(':uid',    $userId);
        return $this->db->execute();
    }

    /**
     * Save or update visual skin analysis result for a scan.
     *
     * @param int $scanId
     * @param AnalysisResult $result
     * @return bool
     */
    public function saveAnalysisResult(int $scanId, AnalysisResult $result): bool {
        if (!$this->db->isConnected()) return false;

        try {
            // Check if analysis_results row already exists
            $this->db->query('SELECT id FROM analysis_results WHERE skin_scan_id = :sid LIMIT 1');
            $this->db->bind(':sid', $scanId);
            $existing = $this->db->single();

            $aiPayload = $result->toJson();

            if ($existing) {
                $this->db->query(
                    'UPDATE analysis_results
                     SET acne_score = :acne,
                         redness_score = :red,
                         texture_score = :tex,
                         hyperpigmentation_score = :hyp,
                         overall_score = :overall,
                         ai_response = :ai
                     WHERE skin_scan_id = :sid'
                );
            } else {
                $this->db->query(
                    'INSERT INTO analysis_results
                        (skin_scan_id, acne_score, redness_score, texture_score,
                         hyperpigmentation_score, overall_score, ai_response)
                     VALUES (:sid, :acne, :red, :tex, :hyp, :overall, :ai)'
                );
            }

            $this->db->bind(':sid',     $scanId);
            $this->db->bind(':acne',    $result->acneScore);
            $this->db->bind(':red',     $result->rednessScore);
            $this->db->bind(':tex',     $result->textureScore);
            $this->db->bind(':hyp',     $result->hyperpigmentationScore);
            $this->db->bind(':overall', $result->overallScore);
            $this->db->bind(':ai',      $aiPayload);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Scan::saveAnalysisResult error – ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Save or update visual sunscreen coverage estimation result for a scan.
     *
     * @param int $scanId
     * @param SunscreenResult $result
     * @return bool
     */
    public function saveSunscreenResult(int $scanId, SunscreenResult $result): bool {
        if (!$this->db->isConnected()) return false;

        try {
            $this->db->query('SELECT id FROM sunscreen_results WHERE skin_scan_id = :sid LIMIT 1');
            $this->db->bind(':sid', $scanId);
            $existing = $this->db->single();

            $lowAreasJson = !empty($result->lowCoverageAreas) ? json_encode($result->lowCoverageAreas, JSON_UNESCAPED_UNICODE) : '[]';

            if ($existing) {
                $this->db->query(
                    'UPDATE sunscreen_results
                     SET coverage_score = :cov,
                         coverage_status = :cov_status,
                         heatmap_path = :heatmap,
                         low_coverage_areas = :low_areas,
                         analysis_method = :method,
                         hardware_type = :hardware
                     WHERE skin_scan_id = :sid'
                );
            } else {
                $this->db->query(
                    'INSERT INTO sunscreen_results
                        (skin_scan_id, coverage_score, coverage_status, heatmap_path,
                         low_coverage_areas, analysis_method, hardware_type)
                     VALUES (:sid, :cov, :cov_status, :heatmap, :low_areas, :method, :hardware)'
                );
            }

            $this->db->bind(':sid',        $scanId);
            $this->db->bind(':cov',        $result->coverageScore);
            $this->db->bind(':cov_status', $result->coverageStatus);
            $this->db->bind(':heatmap',    $result->heatmapPath);
            $this->db->bind(':low_areas',  $lowAreasJson);
            $this->db->bind(':method',     $result->analysisMethod);
            $this->db->bind(':hardware',   $result->hardwareType);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Scan::saveSunscreenResult error – ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create placeholder analysis_results row (all zeros) for a scan.
     * Will be populated later when AI analysis is integrated.
     * @param int $scanId
     * @return bool
     */
    public function createEmptyAnalysisResult(int $scanId): bool {
        if (!$this->db->isConnected()) return false;

        $this->db->query(
            'INSERT INTO analysis_results
                (skin_scan_id, acne_score, redness_score, texture_score,
                 hyperpigmentation_score, overall_score, ai_response)
             VALUES (:sid, 0, 0, 0, 0, 0, "")'
        );
        $this->db->bind(':sid', $scanId);
        return $this->db->execute();
    }

    /**
     * Insert a new scan record across three normalized tables.
     * @param array $data
     * @return bool
     */
    public function addScan($data) {
        if (!$this->db->isConnected()) return false;

        try {
            // 1. skin_scans
            $this->db->query(
                'INSERT INTO skin_scans (user_id, image_path, status, image_quality_score)
                 VALUES (:user_id, :image_path, :status, :iq)'
            );
            $this->db->bind(':user_id',    $data['user_id']);
            $this->db->bind(':image_path', $data['image_path']           ?? '');
            $this->db->bind(':status',     $data['status']               ?? 'completed');
            $this->db->bind(':iq',         $data['image_quality_score']  ?? 95.00);
            if (!$this->db->execute()) return false;

            $scanId = $this->db->lastInsertId();

            // 2. analysis_results
            $this->db->query(
                'INSERT INTO analysis_results
                    (skin_scan_id, acne_score, redness_score, texture_score, hyperpigmentation_score, overall_score, ai_response)
                 VALUES (:sid, :acne, :red, :tex, :hyp, :overall, :ai)'
            );
            $this->db->bind(':sid',     $scanId);
            $this->db->bind(':acne',    $data['acne_score']              ?? 0);
            $this->db->bind(':red',     $data['redness_score']           ?? 0);
            $this->db->bind(':tex',     $data['texture_score']           ?? 0);
            $this->db->bind(':hyp',     $data['hyperpigmentation_score'] ?? 0);
            $this->db->bind(':overall', $data['overall_score']           ?? 0);
            $this->db->bind(':ai',      $data['notes']                   ?? '');
            if (!$this->db->execute()) return false;

            // 3. sunscreen_results
            $this->db->query(
                'INSERT INTO sunscreen_results
                    (skin_scan_id, coverage_score, coverage_status, heatmap_path, low_coverage_areas, analysis_method, hardware_type)
                 VALUES (:sid, :cov, :covs, :hm, :lca, :am, :ht)'
            );
            $this->db->bind(':sid',  $scanId);
            $this->db->bind(':cov',  $data['sunscreen_coverage_score'] ?? 0);
            $this->db->bind(':covs', $data['sunscreen_status']         ?? 'Unprotected');
            $this->db->bind(':hm',   $data['heatmap_path']             ?? '');
            $this->db->bind(':lca',  $data['low_coverage_areas']       ?? 'N/A');
            $this->db->bind(':am',   $data['analysis_method']          ?? 'RGB Camera');
            $this->db->bind(':ht',   $data['hardware_type']            ?? 'Webcam');
            return $this->db->execute();

        } catch (Exception $e) {
            error_log('Scan::addScan error – ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Platform-wide scan count (admin).
     * @return int
     */
    public function getTotalScansCount() {
        if ($this->db->isConnected()) {
            $this->db->query('SELECT COUNT(*) AS cnt FROM skin_scans');
            $row = $this->db->single();
            return $row ? (int)$row->cnt : 0;
        }
        return 0;
    }

    /**
     * Platform average overall health score (admin).
     * @return float
     */
    public function getAverageHealthScore() {
        if ($this->db->isConnected()) {
            $this->db->query('SELECT AVG(overall_score) AS avg_score FROM analysis_results');
            $row = $this->db->single();
            return ($row && $row->avg_score !== null) ? round((float)$row->avg_score, 1) : 0.0;
        }
        return 0.0;
    }
}
