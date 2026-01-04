<?php
// Minimal shared header
if (session_status() === PHP_SESSION_NONE) session_start();
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function require_role(array $roles) {
    $role = $_SESSION['role'] ?? 'guest';
    if (!in_array($role, $roles)) {
        header('Location: loginPage.php');
        exit;
    }
}
$role = $_SESSION['role'] ?? 'guest';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>School Management System</title>
    <style>/* Minimal styling */ body{font-family:Arial,Helvetica,sans-serif;margin:0;padding:0} .nav{background:#2c3e50;color:#fff;padding:10px} .nav a{color:#fff;margin-right:12px;text-decoration:none} .container{padding:16px}</style>
</head>
<body>
<div class="nav">
    <a href="index.php">Home</a>
    <?php if ($role === 'office_admin'): ?>
        <a href="subjects_list.php">Subjects</a>
        <a href="school_years_list.php">School Years</a>
        <a href="term_manage.php">Terms</a>
    <?php endif; ?>
    <?php if ($role === 'teacher'): ?>
        <a href="scores_entry.php">Enter Scores</a>
        <a href="scores_list.php">View Scores</a>
    <?php endif; ?>
    <a href="report_student_card.php">Reports</a>
    <?php if (!empty($_SESSION['username'])): ?>
        <span style="float:right">Logged in as <?php echo e($_SESSION['username']); ?> (<?php echo e($role); ?>)</span>
    <?php else: ?>
        <a style="float:right" href="loginPage.php">Login</a>
    <?php endif; ?>
</div>
<div class="container">
