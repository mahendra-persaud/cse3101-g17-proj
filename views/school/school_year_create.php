<?php
// page to make a new school year

require_once __DIR__ . '/../../models/School.php';
require_once __DIR__ . '/../../controllers/BaseController.php';

$projectRoot = '/cse3101-g17-proj';
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
$no_container = true;
require_once __DIR__ . '/../../includes/header.php';
require_role(['office_admin']);

$errors = [];
$schoolModel = new School();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baseController = new BaseController();
        if (!$baseController->validateCsrf()) {
            $errors[] = 'Invalid security token (CSRF).';
        } else {
            $label = trim($_POST['label'] ?? '');
            if ($label === '') {
                $errors[] = 'Label required.';
            } else {
                // saving the new year
                $schoolModel->createYear(['year_name' => $label]);
                header('Location: school_years_list.php');
                exit;
            }
        }
}
?>

<div class="Main-Container">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
    <main class="main-dashboard">
        <div class="header">
            <h2>School Settings</h2>
        </div>

        <nav class="breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo $projectRoot; ?>/views/dashboard/index.php">Home</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><a href="school_years_list.php">School Years</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><span class="breadcrumb-current">Create Year</span></div>
        </nav>

        <h1>Create School Year</h1>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?>
            </div>
        <?php endif; ?>

        <section class="form-card">
            <h2>Year Information</h2>
            <form method="post">
                <?php echo BaseController::csrfField(); ?>
                <div class="form-group">
                    <label>Label (e.g., 2025/2026)</label>
                    <input name="label" value="<?php echo e($_POST['label'] ?? ''); ?>" required placeholder="YYYY/YYYY">
                </div>
                <div class="form-actions">
                    <button type="submit" class="create-btn">Create Year</button>
                    <a href="school_years_list.php" class="btn" style="background: #9ca3af; text-decoration: none;">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>