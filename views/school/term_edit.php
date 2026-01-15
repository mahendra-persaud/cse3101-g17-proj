<?php
// edit an existing term

require_once __DIR__ . '/../../models/School.php';
require_once __DIR__ . '/../../controllers/BaseController.php';

$projectRoot = '/cse3101-g17-proj';
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
$no_container = true;
$body_class = 'management-page';
require_once __DIR__ . '/../../includes/header.php';
require_role(['office_admin']);

$termId = $_GET['id'] ?? null;
if (!$termId) {
    header('Location: years_terms.php');
    exit;
}

$errors = [];
$schoolModel = new School();
$term = $schoolModel->getTerm($termId);

if (!$term) {
    header('Location: years_terms.php?error=Term+not+found');
    exit;
}

$years = $schoolModel->getYears();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baseController = new BaseController();
    if (!$baseController->validateCsrf()) {
        $errors[] = 'Invalid security token (CSRF).';
    } else {
        $name = trim($_POST['term_name'] ?? '');
        $yearId = $_POST['school_year_id'] ?? '';
        $startDate = $_POST['start_date'] ?? null;
        $endDate = $_POST['end_date'] ?? null;

        if ($name === '') $errors[] = 'Term name required.';
        if ($yearId === '') $errors[] = 'School year required.';

        if (empty($errors)) {
            $schoolModel->updateTerm($termId, [
                'term_name' => $name,
                'school_year_id' => $yearId,
                'start_date' => $startDate ?: null,
                'end_date' => $endDate ?: null
            ]);
            header('Location: years_terms.php?success=Term+updated+successfully');
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
            <div class="breadcrumb-item"><a href="years_terms.php" class="breadcrumb-link">Years & Terms</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><span class="breadcrumb-current">Edit Term</span></div>
        </nav>

        <h1>Edit Term: <?php echo e($term['term_name']); ?></h1>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?>
            </div>
        <?php endif; ?>

        <section class="form-card">
            <h2>Term Information</h2>
            <form method="post">
                <?php echo BaseController::csrfField(); ?>
                <div class="form-group">
                    <label for="term_name">Term Name</label>
                    <input type="text" id="term_name" name="term_name" value="<?php echo e($_POST['term_name'] ?? $term['term_name']); ?>" required placeholder="e.g., Term 1">
                </div>
                <div class="form-group">
                    <label for="school_year_id">School Year</label>
                    <select id="school_year_id" name="school_year_id" required>
                        <option value="">Select Year</option>
                        <?php foreach ($years as $y): ?>
                            <option value="<?php echo $y['school_year_id']; ?>" <?php echo ($term['school_year_id'] == $y['school_year_id']) ? 'selected' : ''; ?>>
                                <?php echo e($y['year_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="start_date">Start Date (Optional)</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo e($term['start_date'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="end_date">End Date (Optional)</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo e($term['end_date'] ?? ''); ?>">
                </div>
                <div class="form-actions">
                    <button type="submit" class="create-btn">Update Term</button>
                    <a href="years_terms.php" class="btn" style="background: #9ca3af; text-decoration: none;">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
