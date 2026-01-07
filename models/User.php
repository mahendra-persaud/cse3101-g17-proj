<?php
require_once __DIR__ . '/Model.php';

/**
 * User Model
 * Handles all user-related database operations and authentication
 */
class User extends Model {
    protected $table = 'users';

    /**
     * Get sample data for testing
     */
    protected function getSampleData() {
        // Load from JSON file (current authentication method)
        $jsonFile = __DIR__ . '/../temp/users.json';
        if (file_exists($jsonFile)) {
            $json = file_get_contents($jsonFile);
            $users = json_decode($json, true);
            return is_array($users) ? $users : [];
        }

        // Check session
        if (isset($_SESSION[$this->table]) && !empty($_SESSION[$this->table])) {
            return $_SESSION[$this->table];
        }

        // Default sample data
        return [
            ['id' => 1, 'username' => 'admin', 'email' => 'admin@school.com', 'role' => 'office_admin', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 2, 'username' => 'teacher1', 'email' => 'teacher1@school.com', 'role' => 'teacher', 'created_at' => date('Y-m-d H:i:s')],
        ];
    }

    /**
     * Find user by email
     * @param string $email
     * @return array|null
     */
    public function findByEmail($email) {
        $users = $this->getAll();
        foreach ($users as $user) {
            if (isset($user['email']) && $user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Find user by username
     * @param string $username
     * @return array|null
     */
    public function findByUsername($username) {
        $users = $this->getAll();
        foreach ($users as $user) {
            if (isset($user['username']) && $user['username'] === $username) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Get users by role
     * @param string $role
     * @return array
     */
    public function getByRole($role) {
        return $this->findBy('role', $role);
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
            'email' => 'required|email',
            'role' => 'required'
        ];

        if (!$isUpdate) {
            $rules['password'] = 'required|min:6';
        }

        return $this->validate($data, $rules);
    }

    /**
     * Check if email already exists
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function emailExists($email, $excludeId = null) {
        $users = $this->getAll();
        foreach ($users as $user) {
            if ($user['email'] === $email && $user['id'] != $excludeId) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if username already exists
     * @param string $username
     * @param int|null $excludeId
     * @return bool
     */
    public function usernameExists($username, $excludeId = null) {
        $users = $this->getAll();
        foreach ($users as $user) {
            if ($user['username'] === $username && $user['id'] != $excludeId) {
                return true;
            }
        }
        return false;
    }

    /**
     * Authenticate user
     * @param string $email
     * @param string $password
     * @return array|false User data on success, false on failure
     */
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);

        if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
            // Remove password from returned data
            unset($user['password']);
            return $user;
        }

        return false;
    }
}
