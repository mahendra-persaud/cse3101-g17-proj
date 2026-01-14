<?php
// debug_login.php
// Setup
require_once __DIR__ . '/config/database.php';
echo "<h1>Login Debugger</h1>";

try {
    $pdo = getDBConnection();
    echo "<p style='color:green'>✅ Database Connected Successfully</p>";
} catch (Exception $e) {
    die("<p style='color:red'>❌ Database Connection Failed: " . $e->getMessage() . "</p>");
}

// 1. Check Roles
$stmt = $pdo->query("SELECT * FROM roles");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<h3>Roles Table (" . count($roles) . ")</h3>";
if (empty($roles)) {
    echo "<p style='color:red'>❌ No roles found! Login query uses INNER JOIN so this will fail.</p>";
} else {
    echo "<ul>";
    foreach ($roles as $r) {
        echo "<li>ID: {$r['role_id']} - {$r['role_name']}</li>";
    }
    echo "</ul>";
}

// 2. Check Admin User
$username = 'admin';
$password = 'password123'; // The default password

echo "<h3>Checking User: 'admin'</h3>";
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<p style='color:red'>❌ User 'admin' NOT FOUND in 'users' table.</p>";
} else {
    echo "<p style='color:green'>✅ User found: ID {$user['user_id']}, Role ID: {$user['role_id']}</p>";
    
    // Check Role Link
    $roleExists = false;
    foreach ($roles as $r) {
        if ($r['role_id'] == $user['role_id']) $roleExists = true;
    }
    
    if (!$roleExists) {
        echo "<p style='color:red'>❌ User has Role ID {$user['role_id']}, but that ID does not exist in roles table!</p>";
    } else {
        echo "<p style='color:green'>✅ Role linkage looks good.</p>";
    }

    // 3. Test Password
    echo "<h3>Testing Password: 'password123'</h3>";
    echo "Stored Hash: " . $user['password_hash'] . "<br>";
    
    if (password_verify($password, $user['password_hash'])) {
        echo "<p style='color:green'>✅ password_verify() PASSED. The password is correct.</p>";
    } else {
        echo "<p style='color:red'>❌ password_verify() FAILED. The hash does not match 'password123'.</p>";
        
        // Offer fix
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        echo "<p><strong>Suggested Fix:</strong> Run the query below to reset the password:</p>";
        echo "<pre style='background:#eee;padding:10px'>UPDATE users SET password_hash = '$newHash' WHERE username = 'admin';</pre>";
    }
}
?>
