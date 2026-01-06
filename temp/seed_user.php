<?php
// Seed script moved to temp/ (note: remove before production DB migration)
require_once __DIR__ . '/../includes/header.php';
$users_file = __DIR__ . '/users.json';
$seed = [
    'username' => 'dave',
    'email' => 'dave@example.com',
    'password' => password_hash('password123', PASSWORD_DEFAULT),
    'role' => 'office_admin',
];
$users = [];
if (file_exists($users_file)) {
    $users = json_decode(file_get_contents($users_file), true) ?: [];
}
$exists = false;
foreach ($users as $u) {
    if (strcasecmp($u['email'], $seed['email']) === 0 || strcasecmp($u['username'], $seed['username']) === 0) {
        $exists = true; break;
    }
}
if (!$exists) {
    $users[] = $seed;
    file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));
    echo "<div style='max-width:700px;margin:20px auto;padding:12px;background:#e6ffea;border:1px solid #cce'>Seeded user <strong>dave</strong> (email: <strong>dave@example.com</strong>, password: <strong>password123</strong>)</div>";
} else {
    echo "<div style='max-width:700px;margin:20px auto;padding:12px;background:#fff3cd;border:1px solid #ffeeba'>User 'dave' already exists.</div>";
}
?>
<p style="max-width:700px;margin:8px auto">Run <a href="../auth/loginPage.php">Login page</a> to sign in.</p>
