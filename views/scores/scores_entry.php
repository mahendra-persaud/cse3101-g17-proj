<?php
// scores_entry.php - Enter/Update Scores
require_once __DIR__ . '/../../controllers/ScoreController.php';
require_once __DIR__ . '/../../controllers/StudentController.php';

$scoreController = new ScoreController();
$scoreController->requireRole(['teacher', 'office_admin']);

$studentController = new StudentController();
$students = $studentController->index();
$terms = $scoreController->getTerms();
$classes = $scoreController->getClasses();

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
$extra_head = '
<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--single {
        height: 48px !important;
        padding: 10px 16px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 8px !important;
        background: #ffffff !important;
        font-size: 15px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px !important;
        color: #1a1a1a !important;
        padding-left: 0 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
        right: 10px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #9ca3af !important;
    }
    .select2-dropdown {
        border: 1px solid #d1d5db !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1) !important;
    }
    .select2-search--dropdown .select2-search__field {
        padding: 10px 12px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 6px !important;
        font-size: 14px !important;
    }
    .select2-results__option {
        padding: 10px 12px !important;
        font-size: 14px !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #7c3aed !important;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #ede9fe !important;
        color: #5b21b6 !important;
    }
    .select2-container--default.select2-container--disabled .select2-selection--single {
        background-color: #f3f4f6 !important;
        cursor: not-allowed !important;
    }
    .filter-note {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }
</style>';
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
            <div class="breadcrumb-item"><span class="breadcrumb-current">Enter Score</span></div>
        </nav>

        <h1>Enter / Update Score</h1>

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
                    <label for="class_filter">Filter by Class</label>
                    <select id="class_filter" class="searchable-select">
                        <option value="">All Classes</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['class_id']; ?>">
                                <?php echo e($class['grade_name'] . ' - ' . $class['class_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="filter-note">Optional: Select a class to filter students</p>
                </div>

                <div class="form-group">
                    <label for="student_id">Student</label>
                    <select id="student_id" name="student_id" class="searchable-select" required>
                        <option value="">Search or select student...</option>
                        <?php foreach ($students as $st): ?>
                            <option value="<?php echo $st['student_id']; ?>" data-class-id="<?php echo $st['class_id']; ?>" <?php echo (isset($_POST['student_id']) && $_POST['student_id'] == $st['student_id']) ? 'selected' : ''; ?>>
                                <?php echo e($st['first_name'] . ' ' . $st['last_name']); ?> (<?php echo e($st['class_name']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject_id">Subject</label>
                    <select id="subject_id" name="subject_id" class="searchable-select" required disabled>
                        <option value="">Select Student First</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="term_id">Term</label>
                    <select id="term_id" name="term_id" class="searchable-select" required>
                        <option value="">Search or select term...</option>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Store all students data for filtering
    const allStudents = [];
    $('#student_id option').each(function() {
        if (this.value) {
            allStudents.push({
                id: this.value,
                text: this.text,
                classId: $(this).data('class-id')
            });
        }
    });

    // Initialize Select2 on class filter
    $('#class_filter').select2({
        placeholder: 'All Classes',
        allowClear: true
    });

    // Initialize Select2 on student dropdown
    $('#student_id').select2({
        placeholder: 'Search or select student...',
        allowClear: true
    });

    $('#term_id').select2({
        placeholder: 'Search or select term...',
        allowClear: true
    });

    // Subject dropdown - initialize but keep disabled until student is selected
    $('#subject_id').select2({
        placeholder: 'Select Student First',
        allowClear: true
    });

    // Handle class filter change
    $('#class_filter').on('change', function() {
        const selectedClassId = this.value;
        const $studentSelect = $('#student_id');

        // Clear current selection
        $studentSelect.val(null).trigger('change');

        // Clear and rebuild student options
        $studentSelect.empty().append('<option value="">Search or select student...</option>');

        allStudents.forEach(student => {
            if (!selectedClassId || student.classId == selectedClassId) {
                const option = new Option(student.text, student.id, false, false);
                $(option).data('class-id', student.classId);
                $studentSelect.append(option);
            }
        });

        $studentSelect.trigger('change');

        // Reset subject dropdown
        const $subjectSelect = $('#subject_id');
        $subjectSelect.prop('disabled', true);
        $subjectSelect.empty().append('<option value="">Select Student First</option>');
        $subjectSelect.trigger('change');
    });

    // Handle student change to load subjects
    $('#student_id').on('change', function() {
        const studentId = this.value;
        const $subjectSelect = $('#subject_id');

        // Destroy and recreate to update properly
        $subjectSelect.prop('disabled', true);
        $subjectSelect.empty().append('<option value="">Loading subjects...</option>');
        $subjectSelect.trigger('change');

        if (!studentId) {
            $subjectSelect.empty().append('<option value="">Select Student First</option>');
            $subjectSelect.trigger('change');
            return;
        }

        fetch(`../../api/get_student_subjects.php?student_id=${studentId}`)
            .then(response => response.json())
            .then(data => {
                $subjectSelect.empty().append('<option value="">Search or select subject...</option>');
                if (data.length > 0) {
                    data.forEach(sub => {
                        $subjectSelect.append(new Option(sub.subject_name, sub.subject_id, false, false));
                    });
                    $subjectSelect.prop('disabled', false);
                } else {
                    $subjectSelect.empty().append('<option value="">No subjects for this student\'s grade</option>');
                }
                $subjectSelect.trigger('change');
            })
            .catch(err => {
                console.error(err);
                $subjectSelect.empty().append('<option value="">Error loading subjects</option>');
                $subjectSelect.trigger('change');
            });
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
