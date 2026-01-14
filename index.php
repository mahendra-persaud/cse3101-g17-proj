<?php
// main entry point file
require_once __DIR__ . '/config/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// seeing if user is logged in
if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    // user logged in? dashboard time
    header('Location: views/dashboard/index.php');
} else {
    // nope, go to login
    
    header('Location: views/auth/loginPage.php');
}
exit;

