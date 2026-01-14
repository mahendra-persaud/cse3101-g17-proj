<?php
// editing a subject

require_once __DIR__ . '/../../controllers/SubjectController.php';

$subjectController = new SubjectController();
$subjectController->requireRole('office_admin');

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: subjects_list.php');
    exit;
}

$subject = $subjectController->show($id);
if (!$subject) {
    header('Location: subjects_list.php');
    exit;
}

$errors = [];
$grades = $subjectController->getGrades();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'subject_name' => $_POST['subject_name'] ?? '',
        'grade_id' => $_POST['grade_id'] ?? '',
        'classes' => $_POST['classes'] ?? []
    ];
    
    $result = $subjectController->update($id, $data);
    if ($result['success']) {
        header('Location: subjects_list.php');
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
                <span class="breadcrumb-current">Edit Subject</span>
            </div>
        </nav>

        <h1>Edit Subject: <?php echo e($subject['subject_name']); ?></h1>

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
                    <input type="text" id="subject_name" name="subject_name" value="<?php echo e($subject['subject_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="grade_id">Grade</label>
                    </select>
                </div>

                <div class="form-group" id="class-selection-container">
                    <label>Assigned Classes</label>
                    <div id="class-checkboxes" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 10px; max-height: 200px; overflow-y: auto; background: #fff;">
                        <!-- JS will fill this -->
                    </div>
                </div>

                <script>
                const gradesData = <?php 
                    $allClasses = [];
                    foreach ($grades as $grade) {
                        $allClasses[$grade['grade_id']] = $subjectController->getClassesByGrade($grade['grade_id']);
                    }
                    echo json_encode($allClasses); 
                ?>;
                const assignedClasses = <?php echo json_encode($subjectController->getAssignedClasses($id)); ?>;

                function updateClasses() {
                    const gradeId = document.getElementById('grade_id').value;
                    const container = document.getElementById('class-selection-container');
                    const checkboxDiv = document.getElementById('class-checkboxes');
                    
                    checkboxDiv.innerHTML = '';
                    
                    if (gradeId && gradesData[gradeId] && gradesData[gradeId].length > 0) {
                        container.style.display = 'block';
                        gradesData[gradeId].forEach(cls => {
                            const isChecked = assignedClasses.includes(cls.class_id.toString()) || assignedClasses.includes(parseInt(cls.class_id)) ? 'checked' : '';
                            const label = document.createElement('label');
                            label.style.display = 'flex';
                            label.style.alignItems = 'center';
                            label.style.gap = '10px';
                            label.style.padding = '6px 0';
                            label.style.borderBottom = '1px solid #f3f4f6';
                            label.style.cursor = 'pointer';
                            
                            label.innerHTML = `
                                <input type="checkbox" name="classes[]" value="${cls.class_id}" ${isChecked}>
                                <span style="font-size: 14px; color: #374151;">Class ${cls.class_name}</span>
                            `;
                            checkboxDiv.appendChild(label);
                        });
                    } else {
                        container.style.display = 'none';
                    }
                }

                document.getElementById('grade_id').addEventListener('change', updateClasses);
                window.addEventListener('DOMContentLoaded', updateClasses);
                </script>

                <div class="form-actions">
                    <button type="submit" class="create-btn">Update Subject</button>
                    <a href="subjects_list.php" class="btn" style="background: #9ca3af; text-decoration: none;">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>