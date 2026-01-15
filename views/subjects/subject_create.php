<?php
// page to make a new subject

require_once __DIR__ . '/../../controllers/SubjectController.php';

$subjectController = new SubjectController();
$subjectController->requireRole('office_admin');

$errors = [];
$grades = $subjectController->getGrades();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // expecting subject name, grade id, and optionally classes
    $data = [
        'subject_name' => $_POST['subject_name'] ?? '',
        'grade_id' => $_POST['grade_id'] ?? '',
        'classes' => $_POST['classes'] ?? []
    ];
    
    $result = $subjectController->store($data);
    if ($result['success']) {
        header('Location: subjects_list.php');
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
            <h2>Subject Management</h2>
        </div>

        <nav class="breadcrumb">
            <div class="breadcrumb-item">
                <a href="<?php echo $projectRoot; ?>/views/dashboard/index.php" class="breadcrumb-link">Home</a>
            </div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item">
                <a href="subjects_list.php" class="breadcrumb-link">Subjects</a>
            </div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item">
                <span class="breadcrumb-current">Create Subject</span>
            </div>
        </nav>

        <h1>Create New Subject</h1>

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
            <h2>Subject Details</h2>
            <form method="POST" action="">
                <?php echo SubjectController::csrfField(); ?>
                
                <div class="form-group">
                    <label for="subject_name">Subject Name</label>
                    <input type="text" id="subject_name" name="subject_name" value="<?php echo e($_POST['subject_name'] ?? ''); ?>" required placeholder="e.g., Mathematics">
                </div>

                <div class="form-group">
                    <label for="grade_id">Grade</label>
                    <select id="grade_id" name="grade_id" required>
                        <option value="">Select Grade</option>
                        <?php foreach ($grades as $grade): ?>
                            <option value="<?php echo $grade['grade_id']; ?>" <?php echo (isset($_POST['grade_id']) && $_POST['grade_id'] == $grade['grade_id']) ? 'selected' : ''; ?>>
                                Grade <?php echo e($grade['grade_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="create-btn">Create Subject</button>
                    <a href="subjects_list.php" class="btn" style="background: #9ca3af; text-decoration: none;">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>