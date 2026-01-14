<?php
require_once __DIR__ . '/Model.php';

/**
 * User Model
 * Handles all user-related database operations and authentication
 */
class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    /**
     * Get allowed fields for this model
     */
    protected function getAllowedFields() {
        return ['username', 'password_hash', 'role_id'];
    }

    /**
     * Get all users with role information
     * @return array
     */
    public function getAll() {
        $sql = "SELECT u.user_id, u.username, u.role_id, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.role_id 
                ORDER BY u.username";
        return $this->query($sql);
    }

    /**
     * Find user by ID with role info
     * @param int $id
     * @return array|null
     */
    public function find($id) {
        $sql = "SELECT u.user_id, u.username, u.role_id, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.role_id 
                WHERE u.user_id = ?";
        return $this->queryOne($sql, [$id]);
    }

    /**
     * Find user by username
     * @param string $username
     * @return array|null
     */
    public function findByUsername($username) {
        $sql = "SELECT u.*, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.role_id 
                WHERE u.username = ?";
        return $this->queryOne($sql, [$username]);
    }

    /**
     * Get users by role
     * @param int $roleId
     * @return array
     */
    public function getByRole($roleId) {
        $sql = "SELECT u.user_id, u.username, u.role_id, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.role_id 
                WHERE u.role_id = ? 
                ORDER BY u.username";
        return $this->query($sql, [$roleId]);
    }

    /**
     * Authenticate user
     * @param string $username
     * @param string $password
     * @return array|false User data on success, false on failure
     */
    public function authenticate($username, $password) {
        $sql = "SELECT u.*, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.role_id 
                WHERE u.username = ?";
        $user = $this->queryOne($sql, [$username]);

        if ($user && isset($user['password_hash']) && password_verify($password, $user['password_hash'])) {
            // Remove password from returned data
            unset($user['password_hash']);
            return $user;
        }

        return false;
    }

    /**
     * Create new user with password hashing
     * @param array $data
     * @return int|bool Insert ID or false on failure
     */
    public function createUser($data) {
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        return $this->create($data);
    }

    /**
     * Update user password
     * @param int $userId
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword($userId, $newPassword) {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, ['password_hash' => $hash]);
    }

    /**
     * Validate user data
     * @param array $data
     * @param bool $isUpdate
     * @return array
     */
    public function validateUser($data, $isUpdate = false) {
        $rules = [
            'username' => 'required|min:3|max:50',
            'role_id' => 'required|numeric'
        ];

        if (!$isUpdate) {
            $rules['password'] = 'required|min:6';
        }

        return $this->validate($data, $rules);
    }

    /**
     * Check if username already exists
     * @param string $username
     * @param int|null $excludeId
     * @return bool
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = ?";
        $params = [$username];
        
        if ($excludeId !== null) {
            $sql .= " AND user_id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->queryOne($sql, $params);
        return ($result['count'] ?? 0) > 0;
    }

    /**
     * Get all roles
     * @return array
     */
    public function getRoles() {
        $sql = "SELECT * FROM roles ORDER BY role_id";
        return $this->query($sql);
    }
}
