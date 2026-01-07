<?php
// index.php — Main entry point for the application
session_start();

// Check if user is logged in
if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    // User is logged in, redirect to dashboard
    header('Location: views/dashboard/index.php');
} else {
    // User not logged in, redirect to login page
    header('Location: views/auth/loginPage.php');
}
exit;

