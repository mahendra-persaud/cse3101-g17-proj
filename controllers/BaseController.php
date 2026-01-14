<?php
// main controller stuff
// helper functions for all the other controllers

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class BaseController {
    
    /**
     * Check if user is authenticated
     * @return bool
     */
    public function isAuthenticated() {
        return isset($_SESSION['user_id']) && isset($_SESSION['username']);
    }

    // makes sure they are logged in
    // kicks them out if not
    public function requireAuth() {
        if (!$this->isAuthenticated()) {
            $this->redirect('/views/auth/loginPage.php');
            exit;
        }
    }

    // checks if they have the right permission
    // redirects to dashboard if they try to be sneaky
    public function requireRole($roles) {
        $this->requireAuth();
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles)) {
            $this->setFlash('error', 'You do not have permission to access this page.');
            $this->redirect('/views/dashboard/index.php');
            exit;
        }
    }

    /**
     * Check if current user has a specific role
     * @param string $role
     * @return bool
     */
    protected function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }

    /**
     * Get current user ID
     * @return int|null
     */
    protected function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current username
     * @return string|null
     */
    protected function getUsername() {
        return $_SESSION['username'] ?? null;
    }

    /**
     * Get current user role
     * @return string|null
     */
    protected function getRole() {
        return $_SESSION['role'] ?? null;
    }

    // show a message to the user
    // like "success" or "error"
    public static function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Get and clear flash message
     * @return array|null
     */
    public static function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    // redirect to another page
    // fixes the path so it works on xampp
    public function redirect($url) {
        // Get project root for proper redirects
        $projectRoot = '/cse3101-g17-proj';
        
        if (strpos($url, '/') === 0 && strpos($url, $projectRoot) !== 0) {
            $url = $projectRoot . $url;
        }
        
        header("Location: $url");
        exit;
    }

    // sends data back as json (for javascript stuff)
    public function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // gets data from $_POST safely
    // cleans it up so no hacks
    public function getPost($key, $default = null) {
        if (!isset($_POST[$key])) {
            return $default;
        }
        
        $value = $_POST[$key];
        
        if (is_string($value)) {
            return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        
        return $value;
    }

    /**
     * Get GET data with sanitization
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getQuery($key, $default = null) {
        if (!isset($_GET[$key])) {
            return $default;
        }
        
        $value = $_GET[$key];
        
        if (is_string($value)) {
            return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        
        return $value;
    }

    /**
     * Get all POST data
     * @return array
     */
    protected function getAllPost() {
        $data = [];
        foreach ($_POST as $key => $value) {
            $data[$key] = $this->getPost($key);
        }
        return $data;
    }

    /**
     * Check if request is POST
     * @return bool
     */
    public function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if request is AJAX
     * @return bool
     */
    protected function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    // checks the csrf token
    // security stuff i guess
    public function validateCsrf() {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    /**
     * Generate CSRF token
     * @return string
     */
    public static function generateCsrf() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Get CSRF token input field HTML
     * @return string
     */
    public static function csrfField() {
        return '<input type="hidden" name="csrf_token" value="' . self::generateCsrf() . '">';
    }
}
