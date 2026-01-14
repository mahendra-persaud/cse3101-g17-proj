<?php
// Login handler - uses MySQL database
if (session_status() === PHP_SESSION_NONE) session_start();

// Include database configuration
require_once dirname(dirname(__DIR__)) . '/config/database.php';

$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: loginPage.php'); exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Get database connection
$pdo = getDBConnection();
if (!$pdo) {
    header('Location: loginPage.php?error=db'); exit;
}

try {
    // Query user by username and join with roles table
    $stmt = $pdo->prepare("
        SELECT u.user_id, u.username, u.password_hash, r.role_name
        FROM users u
        INNER JOIN roles r ON u.role_id = r.role_id
        WHERE u.username = :username
    ");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Login successful - set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role_name'];

        header('Location: ' . $projectRoot . '/views/dashboard/index.php');
        exit;
    }

    // Login failed
    header('Location: loginPage.php?error=1');
    exit;

} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    header('Location: loginPage.php?error=db');
    exit;
}
