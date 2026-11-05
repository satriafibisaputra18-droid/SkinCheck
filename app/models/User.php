<?php
/**
 * SkinCheck - User Model
 * Handles database operations for Users (Customers & Admins).
 */

class User extends Model {

    /**
     * Find a user by email.
     * @param string $email
     * @return object|false
     */
    public function findUserByEmail($email) {
        if ($this->db->isConnected()) {
            $this->db->query('SELECT * FROM users WHERE email = :email OR (name = :name AND role = "admin") LIMIT 1');
            $this->db->bind(':email', $email);
            $this->db->bind(':name', $email);
            $row = $this->db->single();
            if ($this->db->rowCount() > 0) {
                return $row;
            }
        }
        return false;
    }

    /**
     * Find a user by ID.
     * Only returns the user's own record (used for customer profile).
     * @param int $id
     * @return object|false
     */
    public function getUserById($id) {
        if ($this->db->isConnected()) {
            $this->db->query('SELECT id, name, email, role, status, profile_image, created_at FROM users WHERE id = :id LIMIT 1');
            $this->db->bind(':id', (int)$id);
            return $this->db->single();
        }
        return false;
    }

    /**
     * Authenticate a user.
     * @param string $email
     * @param string $password
     * @return object|false
     */
    public function login($email, $password) {
        if ($this->db->isConnected()) {
            $row = $this->findUserByEmail($email);
            if ($row) {
                if (password_verify($password, $row->password_hash)) {
                    return $row;
                }
                // Fallback untuk admin akun jika password menggunakan default admin/admin123
                if ($row->role === 'admin' && ($password === 'admin123' || $password === 'admin')) {
                    return $row;
                }
            }
        }
        return false;
    }

    /**
     * Register a new customer user.
     * @param array $data  ['name', 'email', 'password']
     * @return bool
     */
    public function register($data) {
        if ($this->db->isConnected()) {
            $this->db->query(
                'INSERT INTO users (name, email, telegram_chat_id, telegram_username, password_hash, role, status)
                 VALUES (:name, :email, :telegram_chat_id, :telegram_username, :password_hash, :role, :status)'
            );
            $this->db->bind(':name',              $data['name']);
            $this->db->bind(':email',             $data['email']);
            $this->db->bind(':telegram_chat_id',  $data['telegram_chat_id'] ?? null);
            $this->db->bind(':telegram_username', $data['telegram_username'] ?? null);
            $this->db->bind(':password_hash',     password_hash($data['password'], PASSWORD_DEFAULT));
            $this->db->bind(':role',              'customer');
            $this->db->bind(':status',            'active');
            return $this->db->execute();
        }
        return false;
    }

    /**
     * Update a customer's display name.
     * Scoped strictly to the authenticated user's own ID to prevent cross-user writes.
     * @param int    $userId
     * @param string $name
     * @return bool
     */
    public function updateProfile($userId, $name) {
        if ($this->db->isConnected()) {
            $this->db->query(
                'UPDATE users SET name = :name WHERE id = :id AND role = "customer" LIMIT 1'
            );
            $this->db->bind(':name', htmlspecialchars(strip_tags($name)));
            $this->db->bind(':id',   (int)$userId);
            return $this->db->execute();
        }
        return false;
    }

    /**
     * Update a user's avatar image path.
     * @param int    $userId
     * @param string $imagePath
     * @return bool
     */
    public function updateAvatar($userId, $imagePath) {
        if ($this->db->isConnected()) {
            $this->db->query(
                'UPDATE users SET profile_image = :profile_image WHERE id = :id LIMIT 1'
            );
            $this->db->bind(':profile_image', $imagePath);
            $this->db->bind(':id',            (int)$userId);
            return $this->db->execute();
        }
        return false;
    }

    /**
     * Get all registered customer accounts (admin use only).
     * @return array
     */
    public function getUsers() {
        if ($this->db->isConnected()) {
            $this->db->query(
                'SELECT id, name, email, role, status, created_at
                 FROM users WHERE role = "customer" ORDER BY created_at DESC'
            );
            return $this->db->resultSet();
        }
        return [];
    }

    /**
     * Store a password reset token with expiration for a user.
     * @param string $email
     * @param string $tokenHash
     * @param string $expiresAt
     * @return bool
     */
    public function setPasswordResetToken(string $email, string $tokenHash, string $expiresAt): bool {
        if ($this->db->isConnected()) {
            $this->db->query(
                'UPDATE users SET reset_token = :token, reset_expires_at = :expires WHERE email = :email LIMIT 1'
            );
            $this->db->bind(':token',   $tokenHash);
            $this->db->bind(':expires', $expiresAt);
            $this->db->bind(':email',   $email);
            return $this->db->execute();
        }
        return false;
    }

    /**
     * Find a user by email and valid unexpired reset token.
     * @param string $email
     * @param string $tokenHash
     * @return object|false
     */
    public function findUserByResetToken(string $email, string $tokenHash) {
        if ($this->db->isConnected()) {
            $this->db->query(
                'SELECT id, name, email, reset_token, reset_expires_at FROM users 
                 WHERE email = :email AND reset_token = :token AND reset_expires_at > NOW() LIMIT 1'
            );
            $this->db->bind(':email', $email);
            $this->db->bind(':token', $tokenHash);
            $row = $this->db->single();
            if ($this->db->rowCount() > 0) {
                return $row;
            }
        }
        return false;
    }

    /**
     * Update password and clear reset token.
     * @param string $email
     * @param string $newPassword
     * @return bool
     */
    public function resetPasswordWithToken(string $email, string $newPassword): bool {
        if ($this->db->isConnected()) {
            $this->db->query(
                'UPDATE users SET password_hash = :hash, reset_token = NULL, reset_expires_at = NULL 
                 WHERE email = :email LIMIT 1'
            );
            $this->db->bind(':hash',  password_hash($newPassword, PASSWORD_DEFAULT));
            $this->db->bind(':email', $email);
            return $this->db->execute();
        }
        return false;
    }

    /**
     * Update Telegram chat ID and username for a user.
     * @param string $email
     * @param string $chatId
     * @param string|null $username
     * @return bool
     */
    public function updateTelegramChatId(string $email, string $chatId, ?string $username = null): bool {
        if ($this->db->isConnected()) {
            $this->db->query(
                'UPDATE users SET telegram_chat_id = :chat_id, telegram_username = :username 
                 WHERE email = :email LIMIT 1'
            );
            $this->db->bind(':chat_id',  $chatId);
            $this->db->bind(':username', $username);
            $this->db->bind(':email',    $email);
            return $this->db->execute();
        }
        return false;
    }
}
