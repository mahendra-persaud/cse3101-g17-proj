<?php
// fix_password.php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDBConnection();
    
    // The hash for 'password123'
    $newHash = password_hash('password123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin'");
    $stmt->execute([$newHash]);
    
    echo "<h1 style='color:green'>✅ Password Reset Successfully</h1>";
    echo "<p>The password for user <strong>admin</strong> is now: <strong>password123</strong></p>";
    echo "<p><a href='views/auth/loginPage.php'>Click here to Login</a></p>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
