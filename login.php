<?php
// Simple login handler
if (session_status() === PHP_SESSION_NONE) session_start();
$users_file = __DIR__ . '/temp/users.json';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: loginPage.php'); exit;
}
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
if (!file_exists($users_file)) {
    header('Location: loginPage.php?error=1'); exit;
}
$users = json_decode(file_get_contents($users_file), true) ?: [];
foreach ($users as $u) {
    if (strcasecmp($u['email'], $email) === 0) {
        if (password_verify($password, $u['password'])) {
            $_SESSION['username'] = $u['username'];
            $_SESSION['role'] = $u['role'];
            header('Location: index.php'); exit;
        }
        break;
    }
}
header('Location: loginPage.php?error=1'); exit;
