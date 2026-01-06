<?php
require_once __DIR__ . '/../includes/header.php';
$users_file = __DIR__ . '/temp/users.json';
$errors = [];
$old = ['username'=>'','email'=>'','role'=>'teacher'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = ($_POST['role'] ?? 'teacher') === 'office_admin' ? 'office_admin' : 'teacher';
    $old = ['username'=>$username,'email'=>$email,'role'=>$role];

    if ($username === '') $errors[] = 'Username is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';

    if (empty($errors)) {
        if (!file_exists($users_file)) {
            @mkdir(dirname($users_file), 0755, true);
            file_put_contents($users_file, json_encode([]));
        }
        $users = json_decode(file_get_contents($users_file), true) ?: [];
        foreach ($users as $u) {
            if (strcasecmp($u['email'], $email) === 0) {
                $errors[] = 'Email is already registered.'; break;
            }
            if (strcasecmp($u['username'], $username) === 0) {
                $errors[] = 'Username is already taken.'; break;
            }
        }
    }

    if (empty($errors)) {
        $users[] = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
        ];
        file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));
        // Redirect to the login page using the project root computed in the header include
        header('Location: ' . ($projectRoot ?? '') . '/auth/loginPage.php?registered=1');
        exit;
    }
}
?>

<div style="max-width:600px;margin:24px auto;padding:16px;border:1px solid #eee;border-radius:6px;background:#fff">
    <h2>Register a new account</h2>

    <?php if (!empty($errors)): ?>
        <div style="background:#ffe6e6;padding:10px;border-radius:4px;margin-bottom:12px;color:#900">
            <strong>Please fix the following:</strong>
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?php echo e($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="register.php">
        <label>Username</label><br>
        <input type="text" name="username" value="<?php echo e($old['username']); ?>" required style="width:100%;padding:8px;margin-bottom:8px"><br>
        <p style="font-size:12px;color:#666">Note: This form stores users in a temporary JSON file at <code>temp/users.json</code>. It will be removed when a database is added.</p>
        <label>Email</label><br>
        <input type="email" name="email" value="<?php echo e($old['email']); ?>" required style="width:100%;padding:8px;margin-bottom:8px"><br>

        <label>Password</label><br>
        <input type="password" name="password" required style="width:100%;padding:8px;margin-bottom:8px"><br>

        <label>Role</label><br>
        <select name="role" style="padding:8px;margin-bottom:12px">
            <option value="teacher" <?php echo $old['role']==='teacher' ? 'selected' : ''; ?>>Teacher</option>
            <option value="office_admin" <?php echo $old['role']==='office_admin' ? 'selected' : ''; ?>>Office Admin</option>
        </select>

        <div>
            <button type="submit" style="padding:10px 16px">Register</button>
            &nbsp; <a href="loginPage.php">Back to Login</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
