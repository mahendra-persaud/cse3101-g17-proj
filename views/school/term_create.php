<?php
// create a new term for a school year

require_once __DIR__ . '/../../models/School.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

require_role(['office_admin']);

$errors = [];
require_once __DIR__ . '/../../controllers/BaseController.php';
$schoolModel = new School();
$years = $schoolModel->getYears();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baseController = new BaseController();
    if (!$baseController->validateCsrf()) {
        $errors[] = 'Invalid security token (CSRF).';
    } else {
        $name = trim($_POST['term_name'] ?? '');
        $year_id = $_POST['school_year_id'] ?? '';
    
        if ($name === '') $errors[] = 'Term name required.';
        if ($year_id === '') $errors[] = 'School year required.';
    
        if (empty($errors)) {
            $schoolModel->createTerm([
                'term_name' => $name,
                'school_year_id' => $year_id
            ]);
            header('Location: years_terms.php');
            exit;
        }
    }
}
?>

<div class="Main-Container">
    <main class="main-dashboard">
        <div class="header">
            <h2>School Settings</h2>
        </div>

        <nav class="breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo $projectRoot; ?>/views/dashboard/index.php">Home</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><a href="years_terms.php">Years & Terms</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><span class="breadcrumb-current">Create Term</span></div>
        </nav>

        <h1>Create Academic Term</h1>

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
                    <label>Term Name</label>
                    <input name="term_name" value="<?php echo e($_POST['term_name'] ?? ''); ?>" required placeholder="e.g., Term 1">
                </div>
                <div class="form-group">
                    <label>School Year</label>
                    <select name="school_year_id" required>
                        <option value="">Select Year</option>
                        <?php foreach ($years as $y): ?>
                            <option value="<?php echo $y['school_year_id']; ?>" <?php echo (isset($_POST['school_year_id']) && $_POST['school_year_id'] == $y['school_year_id']) ? 'selected' : ''; ?>>
                                <?php echo e($y['year_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="create-btn">Create Term</button>
                    <a href="years_terms.php" class="btn" style="background: #9ca3af; text-decoration: none;">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>