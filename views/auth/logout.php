<?php
require_once dirname(dirname(__DIR__)) . '/config/app.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
session_unset();
session_destroy();
header('Location: ' . $projectRoot . '/views/auth/loginPage.php');
exit;
