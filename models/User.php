<?php
require_once __DIR__ . '/Model.php';

// user model
// handles logins and users
class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    // allowed fields
    protected function getAllowedFields() {
        return ['username', 'password_hash', 'role_id'];
    }

    // get all users and their role names
    public function getAll() {
        $sql = "SELECT u.user_id, u.username, u.role_id, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.role_id 
                ORDER BY u.username";
        return $this->query($sql);
    }

    // find user by id
    public function find($id) {
        $sql = "SELECT u.user_id, u.username, u.role_id, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.role_id 
                WHERE u.user_id = ?";
        return $this->queryOne($sql, [$id]);
    }

    // find user by username
    public function findByUsername($username) {
        $sql = "SELECT u.*, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.role_id 
                WHERE u.username = ?";
        return $this->queryOne($sql, [$username]);
    }

    // get users who have a specific role
    public function getByRole($roleId) {
        $sql = "SELECT u.user_id, u.username, u.role_id, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.role_id 
                WHERE u.role_id = ? 
                ORDER BY u.username";
        return $this->query($sql, [$roleId]);
    }

    // checks username and password to log them in
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

    // creates a new user
    // hashes password so its secure
    public function createUser($data) {
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        return $this->create($data);
    }

    // changes user password
    public function updatePassword($userId, $newPassword) {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, ['password_hash' => $hash]);
    }

    // validates user input
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

    // checks if username is taken
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

    // gets list of roles
    public function getRoles() {
        $sql = "SELECT * FROM roles ORDER BY role_id";
        return $this->query($sql);
    }
}
