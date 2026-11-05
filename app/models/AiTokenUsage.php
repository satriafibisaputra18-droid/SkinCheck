<?php
/**
 * SkinCheck - AI Token Usage Model
 * Tracks and reports Google Gemini AI token consumption and analytics.
 */

class AiTokenUsage extends Model {

    /**
     * Record a new AI inference token consumption entry.
     * @param array $data
     * @return bool
     */
    public function recordUsage(array $data): bool {
        if (!$this->db->isConnected()) {
            return false;
        }

        $userId = !empty($data['user_id']) ? (int)$data['user_id'] : null;
        $userRole = $data['user_role'] ?? 'guest';
        $feature = $data['feature'] ?? 'chat';
        $model = $data['model'] ?? 'gemini-flash-lite-latest';
        $promptTokens = (int)($data['prompt_tokens'] ?? 0);
        $completionTokens = (int)($data['completion_tokens'] ?? 0);
        $totalTokens = (int)($data['total_tokens'] ?? ($promptTokens + $completionTokens));
        $durationMs = (int)($data['duration_ms'] ?? 0);
        $status = $data['status'] ?? 'success';

        $this->db->query("
            INSERT INTO `ai_token_usage` 
            (`user_id`, `user_role`, `feature`, `model`, `prompt_tokens`, `completion_tokens`, `total_tokens`, `duration_ms`, `status`)
            VALUES 
            (:user_id, :user_role, :feature, :model, :prompt_tokens, :completion_tokens, :total_tokens, :duration_ms, :status)
        ");

        $this->db->bind(':user_id', $userId);
        $this->db->bind(':user_role', $userRole);
        $this->db->bind(':feature', $feature);
        $this->db->bind(':model', $model);
        $this->db->bind(':prompt_tokens', $promptTokens);
        $this->db->bind(':completion_tokens', $completionTokens);
        $this->db->bind(':total_tokens', $totalTokens);
        $this->db->bind(':duration_ms', $durationMs);
        $this->db->bind(':status', $status);

        return $this->db->execute();
    }

    /**
     * Get aggregate token consumption metrics for executive admin dashboard.
     * @return object
     */
    public function getTokenMetrics(): object {
        $defaultMetrics = (object)[
            'total_tokens'       => 0,
            'prompt_tokens'      => 0,
            'completion_tokens'  => 0,
            'total_requests'     => 0,
            'tokens_today'       => 0,
            'requests_today'     => 0,
            'avg_tokens_per_req' => 0,
            'latest_model'       => 'gemini-flash-lite-latest',
            'tier_status'        => 'Free Tier (Normal)',
            'quota_indicator'    => 'Safe',
            'model_breakdown'    => [],
            'recent_logs'        => []
        ];

        if (!$this->db->isConnected()) {
            return $defaultMetrics;
        }

        // 1. Overall Aggregates
        $this->db->query("
            SELECT 
                COUNT(*) AS total_requests,
                COALESCE(SUM(total_tokens), 0) AS total_tokens,
                COALESCE(SUM(prompt_tokens), 0) AS prompt_tokens,
                COALESCE(SUM(completion_tokens), 0) AS completion_tokens,
                COALESCE(SUM(CASE WHEN DATE(created_at) = CURDATE() THEN total_tokens ELSE 0 END), 0) AS tokens_today,
                COALESCE(SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END), 0) AS requests_today
            FROM `ai_token_usage`
        ");
        $agg = $this->db->single();

        $totalReq = $agg ? (int)$agg->total_requests : 0;
        $totalTok = $agg ? (int)$agg->total_tokens : 0;
        $promptTok = $agg ? (int)$agg->prompt_tokens : 0;
        $compTok = $agg ? (int)$agg->completion_tokens : 0;
        $tokToday = $agg ? (int)$agg->tokens_today : 0;
        $reqToday = $agg ? (int)$agg->requests_today : 0;
        $avgTokens = $totalReq > 0 ? (int)round($totalTok / $totalReq) : 0;

        // 2. Latest active model used
        $this->db->query("SELECT model FROM `ai_token_usage` ORDER BY id DESC LIMIT 1");
        $latest = $this->db->single();
        $latestModel = $latest ? $latest->model : 'gemini-flash-lite-latest';

        // 3. Breakdown per model
        $this->db->query("
            SELECT 
                model, 
                COUNT(*) as request_count, 
                SUM(total_tokens) as tokens_count 
            FROM `ai_token_usage` 
            GROUP BY model 
            ORDER BY tokens_count DESC
        ");
        $breakdown = $this->db->resultSet();

        // 4. Latest 5 logs
        $this->db->query("
            SELECT id, user_role, feature, model, prompt_tokens, completion_tokens, total_tokens, duration_ms, created_at
            FROM `ai_token_usage`
            ORDER BY id DESC
            LIMIT 5
        ");
        $recentLogs = $this->db->resultSet();

        return (object)[
            'total_tokens'       => $totalTok,
            'prompt_tokens'      => $promptTok,
            'completion_tokens'  => $compTok,
            'total_requests'     => $totalReq,
            'tokens_today'       => $tokToday,
            'requests_today'     => $reqToday,
            'avg_tokens_per_req' => $avgTokens,
            'latest_model'       => $latestModel,
            'tier_status'        => 'Free Tier (Normal)',
            'quota_indicator'    => 'Safe',
            'model_breakdown'    => $breakdown ?: [],
            'recent_logs'        => $recentLogs ?: []
        ];
    }
}
