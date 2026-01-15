<?php
// main config file
require_once __DIR__ . '/../config/app.php';

// start session if not started
// so we know who is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); } // cleaning input stuff
// checks if they are allowed here
// redirects them if they are not allowed
function require_role(array $roles) {
    $role = $_SESSION['role'] ?? 'guest';
    if (!in_array($role, $roles)) {
        // fixed the redirect path
        $parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
        $root = (isset($parts[1]) ? '/' . $parts[0] : '');
        header('Location: ' . $root . '/views/auth/loginPage.php');
        exit;
    }
}
$role = $_SESSION['role'] ?? 'guest';
// figuring out the base url
// helpful for linking images and styles
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>School Management System</title>
    <link rel="stylesheet" href="<?php echo $projectRoot; ?>/public/assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $projectRoot; ?>/public/assets/css/sidebar.css?v=<?php echo time(); ?>">
    <?php
    // extra head stuff if needed
    if (!empty($extra_head)) echo $extra_head;
    ?>
    <style>/* Minimal fallback styling */ body{font-family:Arial,Helvetica,sans-serif;margin:0;padding:0} .container{padding:16px}</style>
</head>
<body class="<?php echo isset($body_class) ? $body_class : ''; ?>">
<?php if (!isset($no_container) || !$no_container): ?>
<div class="container">
<?php endif; ?>