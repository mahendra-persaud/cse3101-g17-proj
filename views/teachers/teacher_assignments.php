<?php
// teacher_assignments.php - Manage Teacher Subject and Class Assignments
require_once __DIR__ . '/../../controllers/TeacherController.php';

$teacherController = new TeacherController();
$teacherController->requireRole('office_admin');

$teacherId = $_GET['id'] ?? null;
if (!$teacherId) {
    header('Location: teacher_list.php');
    exit;
}

$teacher = $teacherController->show($teacherId);
if (!$teacher) {
    TeacherController::setFlash('error', 'Teacher not found.');
    header('Location: teacher_list.php');
    exit;
}

// Handle form submissions
$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'assign_subjects') {
        $subjectIds = $_POST['subject_ids'] ?? [];
        $result = $teacherController->assignSubjects($teacherId, $subjectIds);
        if ($result['success']) {
            $successMessage = 'Subjects assigned successfully.';
        } else {
            $errors[] = $result['message'];
        }
    } elseif ($action === 'assign_classes') {
        $classIds = $_POST['class_ids'] ?? [];
        $result = $teacherController->assignClasses($teacherId, $classIds);
        if ($result['success']) {
            $successMessage = 'Classes assigned successfully.';
        } else {
            $errors[] = $result['message'];
        }
    }
}

// Get current assignments and available options
$assignedSubjects = $teacherController->getSubjects($teacherId);
$assignedClasses = $teacherController->getClasses($teacherId);
$allSubjects = $teacherController->getAllSubjects();
$allClasses = $teacherController->getAllClasses();

// Create arrays of assigned IDs for checkbox checking
$assignedSubjectIds = array_column($assignedSubjects, 'subject_id');
$assignedClassIds = array_column($assignedClasses, 'class_id');

// Group subjects by grade
$subjectsByGrade = [];
foreach ($allSubjects as $subject) {
    $gradeId = $subject['grade_id'];
    if (!isset($subjectsByGrade[$gradeId])) {
        $subjectsByGrade[$gradeId] = [
            'grade_name' => $subject['grade_name'],
            'subjects' => []
        ];
    }
    $subjectsByGrade[$gradeId]['subjects'][] = $subject;
}

// Group classes by grade
$classesByGrade = [];
foreach ($allClasses as $class) {
    $gradeId = $class['grade_id'];
    if (!isset($classesByGrade[$gradeId])) {
        $classesByGrade[$gradeId] = [
            'grade_name' => $class['grade_name'],
            'classes' => []
        ];
    }
    $classesByGrade[$gradeId]['classes'][] = $class;
}

$projectRoot = '/cse3101-g17-proj';
$extra_head = '
<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">
<style>
    .assignment-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-top: 20px;
    }
    @media (max-width: 1200px) {
        .assignment-grid {
            grid-template-columns: 1fr;
        }
    }
    .assignment-card {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 24px;
    }
    .assignment-card h3 {
        color: #a5d8ff;
        margin: 0 0 20px;
        font-size: 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 12px;
    }
    .grade-section {
        margin-bottom: 20px;
    }
    .grade-header {
        color: #fff;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 10px;
        padding: 8px 12px;
        background: rgba(124, 58, 237, 0.3);
        border-radius: 6px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .select-all-btn {
        font-size: 11px;
        padding: 4px 8px;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: #fff;
        border-radius: 4px;
        cursor: pointer;
    }
    .select-all-btn:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    .checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 0 12px;
    }
    .checkbox-item {
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.05);
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .checkbox-item:hover {
        background: rgba(255, 255, 255, 0.1);
    }
    .checkbox-item input {
        margin-right: 8px;
        cursor: pointer;
    }
    .checkbox-item label {
        color: rgba(255, 255, 255, 0.9);
        font-size: 13px;
        cursor: pointer;
    }
    .checkbox-item.checked {
        background: rgba(74, 222, 128, 0.2);
        border: 1px solid rgba(74, 222, 128, 0.5);
    }
    .class-item {
        min-width: 120px;
    }
    .student-count {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.5);
        margin-left: 4px;
    }
    .teacher-info {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .teacher-avatar {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #7c3aed, #a78bfa);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        font-weight: 600;
    }
    .teacher-details h2 {
        margin: 0;
        color: #fff;
        font-size: 20px;
    }
    .teacher-details p {
        margin: 4px 0 0;
        color: rgba(255, 255, 255, 0.7);
        font-size: 14px;
    }
    .save-btn {
        background: linear-gradient(135deg, #7c3aed, #a78bfa);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 15px;
        width: 100%;
    }
    .save-btn:hover {
        opacity: 0.9;
    }
    .current-count {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.6);
        margin-top: 10px;
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
            <h2>Teacher Assignments</h2>
        </div>

        <nav class="breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo $projectRoot; ?>/views/dashboard/index.php">Home</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><a href="teacher_list.php">Teachers</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><span class="breadcrumb-current">Assignments</span></div>
        </nav>

        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?php echo e($successMessage); ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Teacher Info Card -->
        <div class="teacher-info">
            <div class="teacher-avatar">
                <?php echo strtoupper(substr($teacher['first_name'], 0, 1) . substr($teacher['last_name'], 0, 1)); ?>
            </div>
            <div class="teacher-details">
                <h2><?php echo e($teacher['first_name'] . ' ' . $teacher['last_name']); ?></h2>
                <p><?php echo e($teacher['email']); ?> | @<?php echo e($teacher['username']); ?></p>
            </div>
        </div>

        <div class="assignment-grid">
            <!-- Subject Assignments -->
            <div class="assignment-card">
                <h3>Subject Assignments</h3>
                <form method="POST" action="">
                    <?php echo TeacherController::csrfField(); ?>
                    <input type="hidden" name="action" value="assign_subjects">

                    <?php foreach ($subjectsByGrade as $gradeId => $gradeData): ?>
                        <div class="grade-section">
                            <div class="grade-header">
                                <span><?php echo e($gradeData['grade_name']); ?></span>
                                <button type="button" class="select-all-btn" onclick="toggleAllInGrade('subject', <?php echo $gradeId; ?>)">
                                    Select All
                                </button>
                            </div>
                            <div class="checkbox-group">
                                <?php foreach ($gradeData['subjects'] as $subject):
                                    $isChecked = in_array($subject['subject_id'], $assignedSubjectIds);
                                ?>
                                    <div class="checkbox-item <?php echo $isChecked ? 'checked' : ''; ?>">
                                        <input type="checkbox"
                                               name="subject_ids[]"
                                               value="<?php echo $subject['subject_id']; ?>"
                                               id="subject_<?php echo $subject['subject_id']; ?>"
                                               data-grade="<?php echo $gradeId; ?>"
                                               class="subject-checkbox"
                                               <?php echo $isChecked ? 'checked' : ''; ?>
                                               onchange="updateCheckboxStyle(this)">
                                        <label for="subject_<?php echo $subject['subject_id']; ?>">
                                            <?php echo e($subject['subject_name']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <p class="current-count">
                        Currently assigned: <strong id="subjectCount"><?php echo count($assignedSubjectIds); ?></strong> subjects
                    </p>
                    <button type="submit" class="save-btn">Save Subject Assignments</button>
                </form>
            </div>

            <!-- Class Assignments -->
            <div class="assignment-card">
                <h3>Class Assignments</h3>
                <form method="POST" action="">
                    <?php echo TeacherController::csrfField(); ?>
                    <input type="hidden" name="action" value="assign_classes">

                    <?php foreach ($classesByGrade as $gradeId => $gradeData): ?>
                        <div class="grade-section">
                            <div class="grade-header">
                                <span><?php echo e($gradeData['grade_name']); ?></span>
                                <button type="button" class="select-all-btn" onclick="toggleAllInGrade('class', <?php echo $gradeId; ?>)">
                                    Select All
                                </button>
                            </div>
                            <div class="checkbox-group">
                                <?php foreach ($gradeData['classes'] as $class):
                                    $isChecked = in_array($class['class_id'], $assignedClassIds);
                                ?>
                                    <div class="checkbox-item class-item <?php echo $isChecked ? 'checked' : ''; ?>">
                                        <input type="checkbox"
                                               name="class_ids[]"
                                               value="<?php echo $class['class_id']; ?>"
                                               id="class_<?php echo $class['class_id']; ?>"
                                               data-grade="<?php echo $gradeId; ?>"
                                               class="class-checkbox"
                                               <?php echo $isChecked ? 'checked' : ''; ?>
                                               onchange="updateCheckboxStyle(this)">
                                        <label for="class_<?php echo $class['class_id']; ?>">
                                            Class <?php echo e($class['class_name']); ?>
                                            <span class="student-count">(<?php echo $class['student_count']; ?>)</span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <p class="current-count">
                        Currently assigned: <strong id="classCount"><?php echo count($assignedClassIds); ?></strong> classes
                    </p>
                    <button type="submit" class="save-btn">Save Class Assignments</button>
                </form>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <a href="teacher_list.php" class="btn" style="background: #6b7280; text-decoration: none;">Back to Teachers</a>
        </div>
    </main>
</div>

<script>
function updateCheckboxStyle(checkbox) {
    const item = checkbox.closest('.checkbox-item');
    if (checkbox.checked) {
        item.classList.add('checked');
    } else {
        item.classList.remove('checked');
    }
    updateCounts();
}

function toggleAllInGrade(type, gradeId) {
    const checkboxes = document.querySelectorAll(`.${type}-checkbox[data-grade="${gradeId}"]`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);

    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
        updateCheckboxStyle(cb);
    });
}

function updateCounts() {
    const subjectCount = document.querySelectorAll('.subject-checkbox:checked').length;
    const classCount = document.querySelectorAll('.class-checkbox:checked').length;

    document.getElementById('subjectCount').textContent = subjectCount;
    document.getElementById('classCount').textContent = classCount;
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
