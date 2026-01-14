<?php
// editing an existing user

require_once __DIR__ . '/../../controllers/UserController.php';

$userController = new UserController();
$userController->requireRole('office_admin');

$userId = $_GET['id'] ?? null;
if (!$userId) {
    header('Location: userManagement.php');
    exit;
}

$user = $userController->show($userId);
if (!$user) {
    header('Location: userManagement.php');
    exit;
}

$errors = [];
$success = false;
$roles = $userController->getRoles();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $userController->update($userId, $_POST);
    if ($result['success']) {
        header('Location: userManagement.php');
        exit;
    } else {
        $errors = $result['errors'] ?? [$result['message'] ?? 'An error occurred.'];
    }
}

$projectRoot = '/cse3101-g17-proj';
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="Main-Container">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="main-dashboard">
        <div class="header">
            <h2>User Management</h2>
        </div>

        <nav class="breadcrumb">
            <div class="breadcrumb-item">
                <a href="<?php echo $projectRoot; ?>/views/dashboard/index.php" class="breadcrumb-link">Home</a>
            </div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item">
                <a href="userManagement.php" class="breadcrumb-link">Users</a>
            </div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item">
                <span class="breadcrumb-current">Edit User</span>
            </div>
        </nav>

        <h1>Edit User: <?php echo e($user['username']); ?></h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <section class="form-card">
            <h2>User Information</h2>
            <form method="POST" action="">
                <?php echo UserController::csrfField(); ?>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo e($user['username']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">New Password (Leave blank to keep current)</label>
                    <input type="password" id="password" name="password" placeholder="Enter new password">
                </div>

                <div class="form-group">
                    <label for="role_id">Role</label>
                    <select id="role_id" name="role_id" required>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo $role['role_id']; ?>" <?php echo ($user['role_id'] == $role['role_id']) ? 'selected' : ''; ?>>
                                <?php echo e($role['role_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="create-btn">Update User</button>
                    <a href="userManagement.php" class="btn" style="background: #9ca3af; text-decoration: none;">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
