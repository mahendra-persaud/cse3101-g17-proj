<?php
require_once __DIR__ . '/config/database.php';

echo "<h2>Complete Login Fix</h2>";

$pdo = getDBConnection();
if (!$pdo) {
    die("<p style='color: red;'>❌ Cannot connect to database!</p>");
}

// Generate a fresh hash for 'password123'
echo "<h3>Step 1: Generate New Password Hash</h3>";
$newHash = password_hash('password123', PASSWORD_DEFAULT);
echo "<p>New hash: <code>" . htmlspecialchars($newHash) . "</code></p>";

// Test the new hash immediately
if (password_verify('password123', $newHash)) {
    echo "<p style='color: green;'>✅ New hash verified successfully</p>";
} else {
    die("<p style='color: red;'>❌ Hash generation failed!</p>");
}

// Show current database state
echo "<h3>Step 2: Current Database State</h3>";
try {
    $stmt = $pdo->query("SELECT user_id, username, LEFT(password_hash, 30) as hash_start FROM users ORDER BY user_id");
    $users = $stmt->fetchAll();

    echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
    echo "<tr style='background: #0891b2; color: white;'><th>User ID</th><th>Username</th><th>Current Hash (first 30 chars)</th></tr>";
    foreach ($users as $user) {
        echo "<tr><td>{$user['user_id']}</td><td><strong>{$user['username']}</strong></td><td><code>{$user['hash_start']}...</code></td></tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    die("<p style='color: red;'>Error reading users: " . $e->getMessage() . "</p>");
}

// Update ALL users with the new hash
echo "<h3>Step 3: Update All Users</h3>";
try {
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ?");
    $stmt->execute([$newHash]);
    $count = $stmt->rowCount();
    echo "<p style='color: green;'>✅ Updated $count users</p>";
} catch (PDOException $e) {
    die("<p style='color: red;'>Error updating: " . $e->getMessage() . "</p>");
}

// Verify the update worked
echo "<h3>Step 4: Verify Update</h3>";
try {
    $stmt = $pdo->prepare("SELECT username, password_hash FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();

    if (!$admin) {
        die("<p style='color: red;'>❌ Admin user not found!</p>");
    }

    echo "<p>Admin username: <strong>{$admin['username']}</strong></p>";
    echo "<p>Admin hash (first 60 chars): <code>" . substr($admin['password_hash'], 0, 60) . "...</code></p>";

    if (password_verify('password123', $admin['password_hash'])) {
        echo "<p style='color: green; font-size: 18px; font-weight: bold;'>✅ SUCCESS! Password 'password123' now works!</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ FAILED: Password still doesn't match!</p>";
        echo "<p>Trying to test what password THIS hash matches...</p>";

        // Debug: Try common passwords
        $testPasswords = ['password123', 'admin', '123456', 'password'];
        foreach ($testPasswords as $testPw) {
            if (password_verify($testPw, $admin['password_hash'])) {
                echo "<p style='color: orange;'>⚠️ Hash matches password: <strong>$testPw</strong></p>";
                break;
            }
        }
    }
} catch (PDOException $e) {
    die("<p style='color: red;'>Error verifying: " . $e->getMessage() . "</p>");
}

// Test actual login logic
echo "<h3>Step 5: Test Login Logic</h3>";
try {
    $testUsername = 'admin';
    $testPassword = 'password123';

    $stmt = $pdo->prepare("
        SELECT u.user_id, u.username, u.password_hash, r.role_name
        FROM users u
        INNER JOIN roles r ON u.role_id = r.role_id
        WHERE u.username = :username
    ");
    $stmt->execute(['username' => $testUsername]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "<p style='color: red;'>❌ User not found in query</p>";
    } else {
        echo "<p>✅ User found: {$user['username']}, Role: {$user['role_name']}</p>";

        if (password_verify($testPassword, $user['password_hash'])) {
            echo "<p style='color: green; font-size: 18px; font-weight: bold;'>✅ LOGIN WOULD SUCCEED!</p>";
        } else {
            echo "<p style='color: red; font-weight: bold;'>❌ LOGIN WOULD FAIL - Password doesn't match</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error testing login: " . $e->getMessage() . "</p>";
}

// Final instructions
echo "<hr>";
echo "<h3>✅ All Done!</h3>";
echo "<p><strong>Login with:</strong></p>";
echo "<ul>";
echo "<li>Username: <strong>admin</strong></li>";
echo "<li>Password: <strong>password123</strong></li>";
echo "</ul>";
echo "<p><a href='views/auth/loginPage.php' style='display: inline-block; background: #0891b2; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;'>Go to Login Page</a></p>";
?>
