<?php
// score_create.php
require_once __DIR__ . '/../../controllers/ScoreController.php';
require_once __DIR__ . '/../../controllers/StudentController.php';

$scoreController = new ScoreController();
$scoreController->requireRole(['teacher', 'office_admin']);

$studentController = new StudentController();
$students = $studentController->index();
$terms = $scoreController->getTerms();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $scoreController->store($_POST);
    if ($result['success']) {
        ScoreController::setFlash('success', 'Score added successfully.');
        header('Location: scores_list.php');
        exit;
    } else {
        $errors = $result['errors'] ?? [$result['message'] ?? 'An error occurred.'];
    }
}

$projectRoot = '/cse3101-g17-proj';
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
$no_container = true;
$body_class = 'management-page';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="Main-Container">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="main-dashboard">
        <div class="header">
            <h2>Score Management</h2>
        </div>

        <nav class="breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo $projectRoot; ?>/views/dashboard/index.php">Home</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><a href="scores_list.php">Scores</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><span class="breadcrumb-current">Add Score</span></div>
        </nav>

        <h1>Record New Score</h1>

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
            <h2>Score Details</h2>
            <form method="POST" action="">
                <?php echo ScoreController::csrfField(); ?>
                
                <div class="form-group">
                    <label for="student_id">Student</label>
                    <select id="student_id" name="student_id" required>
                        <option value="">Select Student</option>
                        <?php foreach ($students as $st): ?>
                            <option value="<?php echo $st['student_id']; ?>" <?php echo (isset($_POST['student_id']) && $_POST['student_id'] == $st['student_id']) ? 'selected' : ''; ?>>
                                <?php echo e($st['first_name'] . ' ' . $st['last_name']); ?> (<?php echo e($st['class_name']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject_id">Subject</label>
                    <select id="subject_id" name="subject_id" required disabled>
                        <option value="">Select Student First</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="term_id">Term</label>
                    <select id="term_id" name="term_id" required>
                        <?php foreach ($terms as $term): ?>
                            <option value="<?php echo $term['term_id']; ?>" <?php echo (isset($_POST['term_id']) && $_POST['term_id'] == $term['term_id']) ? 'selected' : ''; ?>>
                                <?php echo e($term['term_name']); ?> (<?php echo e($term['year_name']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="score">Score (0-100)</label>
                    <input type="number" id="score" name="score" value="<?php echo e($_POST['score'] ?? ''); ?>" min="0" max="100" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="create-btn">Save Score</button>
                    <a href="scores_list.php" class="btn" style="background: #9ca3af; text-decoration: none;">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</div>

<script>
document.getElementById('student_id').addEventListener('change', function() {
    const studentId = this.value;
    const subjectSelect = document.getElementById('subject_id');
    
    subjectSelect.disabled = true;
    subjectSelect.innerHTML = '<option value="">Loading subjects...</option>';
    
    if (!studentId) {
        subjectSelect.innerHTML = '<option value="">Select Student First</option>';
        return;
    }

    // Usually we would use a proper API endpoint, but let's use the controller direct check for now
    // Actually, I'll just fetch them via a small helper script or AJAX
    fetch(`../../api/get_student_subjects.php?student_id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            subjectSelect.innerHTML = '<option value="">Select Subject</option>';
            if (data.length > 0) {
                data.forEach(sub => {
                    const opt = document.createElement('option');
                    opt.value = sub.subject_id;
                    opt.textContent = sub.subject_name;
                    subjectSelect.appendChild(opt);
                });
                subjectSelect.disabled = false;
            } else {
                subjectSelect.innerHTML = '<option value="">No subjects assigned to this student\'s class</option>';
            }
        })
        .catch(err => {
            console.error(err);
            subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
        });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
