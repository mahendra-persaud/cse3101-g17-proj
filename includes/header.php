<?php
// Minimal shared header (moved to includes/)
if (session_status() === PHP_SESSION_NONE) session_start();
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function require_role(array $roles) {
    $role = $_SESSION['role'] ?? 'guest';
    if (!in_array($role, $roles)) {
        // Redirect to auth login page (project-aware)
        $parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
        $root = (isset($parts[1]) ? '/' . $parts[0] : '');
        header('Location: ' . $root . '/auth/loginPage.php');
        exit;
    }
}
$role = $_SESSION['role'] ?? 'guest';
// Compute project root for asset paths (use first path segment)
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
    // Allow pages to inject extra head tags (e.g., per-page styles)
    if (!empty($extra_head)) echo $extra_head;
    ?>
    <style>/* Minimal fallback styling */ body{font-family:Arial,Helvetica,sans-serif;margin:0;padding:0} .container{padding:16px}</style>
</head>
<body>
<div class="container">