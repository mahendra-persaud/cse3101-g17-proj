<?php
// edit a school year

require_once __DIR__ . '/../../models/School.php';
require_once __DIR__ . '/../../controllers/BaseController.php';

$projectRoot = '/cse3101-g17-proj';
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
$no_container = true;
$body_class = 'management-page';
require_once __DIR__ . '/../../includes/header.php';
require_role(['office_admin']);

$yearId = $_GET['id'] ?? null;
if (!$yearId) {
    header('Location: school_years_list.php');
    exit;
}

$errors = [];
$schoolModel = new School();
$year = $schoolModel->getYear($yearId);

if (!$year) {
    header('Location: school_years_list.php?error=School+year+not+found');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baseController = new BaseController();
    if (!$baseController->validateCsrf()) {
        $errors[] = 'Invalid security token (CSRF).';
    } else {
        $label = trim($_POST['label'] ?? '');
        $startDate = $_POST['start_date'] ?? null;
        $endDate = $_POST['end_date'] ?? null;
        $status = $_POST['status'] ?? 'Current';

        if ($label === '') {
            $errors[] = 'Label required.';
        } else {
            $schoolModel->updateYear($yearId, [
                'year_name' => $label,
                'start_date' => $startDate ?: null,
                'end_date' => $endDate ?: null,
                'status' => $status
            ]);
            header('Location: school_years_list.php?success=School+year+updated+successfully');
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
            <div class="breadcrumb-item"><a href="<?php echo $projectRoot; ?>/views/dashboard/index.php" class="breadcrumb-link">Home</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><a href="school_years_list.php" class="breadcrumb-link">School Years</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><span class="breadcrumb-current">Edit Year</span></div>
        </nav>

        <h1>Edit School Year: <?php echo e($year['year_name']); ?></h1>

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
                    <label for="label">Label (e.g., 2025/2026)</label>
                    <input type="text" id="label" name="label" value="<?php echo e($_POST['label'] ?? $year['year_name']); ?>" required placeholder="YYYY/YYYY">
                </div>
                <div class="form-group">
                    <label for="start_date">Start Date (Optional)</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo e($year['start_date'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="end_date">End Date (Optional)</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo e($year['end_date'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="Current" <?php echo (($year['status'] ?? '') === 'Current') ? 'selected' : ''; ?>>Current</option>
                        <option value="Upcoming" <?php echo (($year['status'] ?? '') === 'Upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                        <option value="Completed" <?php echo (($year['status'] ?? '') === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="create-btn">Update Year</button>
                    <a href="school_years_list.php" class="btn" style="background: #9ca3af; text-decoration: none;">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
