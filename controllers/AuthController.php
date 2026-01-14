<?php
// auth controller
// handles login, logout, and session stuff

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends BaseController {
    
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // logs the user in
    // checks username and password against the db
    public function login($username, $password) {
        // making sure they actually typed something
        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Username and password are required.'
            ];
        }

        // check if creds are valid
        $user = $this->userModel->authenticate($username, $password);

        if ($user) {
            // save user info in session so they stay logged in
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role_name'];

            return [
                'success' => true,
                'message' => 'Login successful.',
                'user' => $user
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid username or password.'
        ];
    }

    // logs the user out
    public function logout() {
        // empty the session array
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // kill the session completely
        session_destroy();

        return [
            'success' => true,
            'message' => 'You have been logged out.'
        ];
    }

    // gets the user who is currently logged in
    public function getCurrentUser() {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return [
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role']
        ];
    }

    // checks if user is an admin
    public function isAdmin() {
        return $this->hasRole('office_admin');
    }

    // checks if user is a teacher
    public function isTeacher() {
        return $this->hasRole('teacher');
    }

    // lets users change their password
    public function changePassword($userId, $currentPassword, $newPassword) {
        // Validate new password
        if (strlen($newPassword) < 6) {
            return [
                'success' => false,
                'message' => 'New password must be at least 6 characters.'
            ];
        }

        // Get user with password
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            return [
                'success' => false,
                'message' => 'Current password is incorrect.'
            ];
        }

        // Update password
        if ($this->userModel->updatePassword($userId, $newPassword)) {
            return [
                'success' => true,
                'message' => 'Password changed successfully.'
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to change password.'
        ];
    }
}

// Handle direct requests to this controller
if (basename($_SERVER['PHP_SELF']) === 'AuthController.php') {
    $controller = new AuthController();
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $result = $controller->login(
                    $_POST['username'] ?? '',
                    $_POST['password'] ?? ''
                );
                
                if ($result['success']) {
                    header('Location: /cse3101-g17-proj/views/dashboard/index.php');
                } else {
                    $_SESSION['login_error'] = $result['message'];
                    header('Location: /cse3101-g17-proj/views/auth/loginPage.php');
                }
                exit;
            }
            break;

        case 'logout':
            $controller->logout();
            header('Location: /cse3101-g17-proj/views/auth/loginPage.php');
            exit;
            break;
    }
}
