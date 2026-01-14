<?php
/**
 * Auth Controller
 * Handles user authentication (login, logout, session management)
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends BaseController {
    
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Handle login request
     * @param string $username
     * @param string $password
     * @return array Result with success status and message
     */
    public function login($username, $password) {
        // Validate input
        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Username and password are required.'
            ];
        }

        // Attempt authentication
        $user = $this->userModel->authenticate($username, $password);

        if ($user) {
            // Set session variables
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

    /**
     * Handle logout
     */
    public function logout() {
        // Clear all session data
        $_SESSION = [];
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy the session
        session_destroy();

        return [
            'success' => true,
            'message' => 'You have been logged out.'
        ];
    }

    /**
     * Get current logged in user info
     * @return array|null
     */
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

    /**
     * Check if user is office admin
     * @return bool
     */
    public function isAdmin() {
        return $this->hasRole('office_admin');
    }

    /**
     * Check if user is teacher
     * @return bool
     */
    public function isTeacher() {
        return $this->hasRole('teacher');
    }

    /**
     * Handle password change
     * @param int $userId
     * @param string $currentPassword
     * @param string $newPassword
     * @return array
     */
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
