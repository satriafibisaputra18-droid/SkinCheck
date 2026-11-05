<?php
/**
 * SkinCheck - Activity Log Model
 * Logs user login and logout activities to the database.
 */

class ActivityLog extends Model {
    /**
     * Records a new activity entry.
     * @param int|null $userId User ID initiating action
     * @param string $activity Description of action performed
     * @return bool
     */
    public function log($userId, $activity) {
        if ($this->db->isConnected()) {
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            
            $this->db->query('INSERT INTO activity_logs (user_id, activity, ip_address) VALUES (:user_id, :activity, :ip_address)');
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':activity', $activity);
            $this->db->bind(':ip_address', $ipAddress);
            
            return $this->db->execute();
        }
        return false;
    }
}
