<?php
// page to create a new user

require_once __DIR__ . '/../../controllers/UserController.php';

$userController = new UserController();
$userController->requireRole('office_admin');

$errors = [];
$success = false;
$roles = $userController->getRoles();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $userController->store($_POST);
    if ($result['success']) {
        $success = true;
        // flash message set in controller, but redirecting anyway
        header('Location: userManagement.php');
        exit;
    } else {
        $errors = $result['errors'] ?? [$result['message'] ?? 'An error occurred.'];
    }
}

$projectRoot = '/cse3101-g17-proj';
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
$no_container = true;
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
                <span class="breadcrumb-current">Create User</span>
            </div>
        </nav>

        <h1>Create New User</h1>

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
                    <input type="text" id="username" name="username" value="<?php echo e($_POST['username'] ?? ''); ?>" required placeholder="Enter username">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter password (min 6 characters)">
                </div>

                <div class="form-group">
                    <label for="role_id">Role</label>
                    <select id="role_id" name="role_id" required>
                        <option value="">Select a role</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo $role['role_id']; ?>" <?php echo (isset($_POST['role_id']) && $_POST['role_id'] == $role['role_id']) ? 'selected' : ''; ?>>
                                <?php echo e($role['role_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="create-btn">Create User</button>
                    <a href="userManagement.php" class="btn" style="background: #9ca3af; text-decoration: none;">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
