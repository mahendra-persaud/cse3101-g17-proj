<?php
// Bulk assign subjects to classes
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/SchoolClass.php';
require_once __DIR__ . '/../../models/Subject.php';

$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
$body_class = 'management-page';
$no_container = true;

$pdo = getDBConnection();
$classModel = new SchoolClass($pdo);
$subjectModel = new Subject($pdo);

$grades = $classModel->getGrades();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gradeId = $_POST['grade_id'] ?? null;
    $subjectIds = $_POST['subjects'] ?? [];

    if (empty($gradeId)) {
        $error = 'Please select a grade.';
    } elseif (empty($subjectIds)) {
        $error = 'Please select at least one subject.';
    } else {
        try {
            // Get all classes for this grade
            $classes = $classModel->getByGrade($gradeId);
            
            if (empty($classes)) {
                $error = 'No classes found for this grade.';
            } else {
                $assignedCount = 0;
                
                foreach ($classes as $class) {
                    // Sync subjects for each class
                    $classModel->syncSubjects($class['class_id'], $subjectIds);
                    $assignedCount++;
                }
                
                $message = "Successfully assigned subjects to $assignedCount class(es) in this grade.";
            }
        } catch (PDOException $e) {
            $error = 'Error assigning subjects: ' . $e->getMessage();
        }
    }
}

require_once $headerPath;
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

    <main class="main-dashboard">
        <div class="header">
            <h2>Bulk Assign Subjects to Classes</h2>
        </div>

        <nav class="breadcrumb">
            <div class="breadcrumb-item">
                <a href="<?php echo $projectRoot; ?>/views/dashboard/index.php" class="breadcrumb-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Home
                </a>
            </div>
            <div class="breadcrumb-separator">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </div>
            <div class="breadcrumb-item">
                <a href="classManagement.php" class="breadcrumb-link">Classes</a>
            </div>
            <div class="breadcrumb-separator">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </div>
            <div class="breadcrumb-item">
                <span class="breadcrumb-current">Bulk Assign Subjects</span>
            </div>
        </nav>

        <?php if (!empty($message)): ?>
            <div style="background: #dcfce7; border: 1px solid #22c55e; color: #166534; padding: 12px 16px; border-radius: 8px; margin: 20px 0;">
                ✅ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin: 20px 0;">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <section class="table-card" style="max-width: 700px;">
            <h3 style="margin-bottom: 20px;">Assign Subjects to All Classes in a Grade</h3>
            <p style="color: #6b7280; margin-bottom: 20px; font-size: 14px;">
                Select a grade and the subjects you want to assign to all classes in that grade. This will assign the same subjects to every class in the selected grade.
            </p>

            <form method="POST" action="">
                <div style="margin-bottom: 20px;">
                    <label for="grade_id" style="display: block; margin-bottom: 8px; font-weight: 500;">Select Grade</label>
                    <select
                        id="grade_id"
                        name="grade_id"
                        required
                        onchange="loadSubjectsForGrade()"
                        style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        <option value="">-- Choose a grade --</option>
                        <?php foreach ($grades as $grade): ?>
                            <option value="<?php echo $grade['grade_id']; ?>">
                                <?php echo htmlspecialchars($grade['grade_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Select Subjects</label>
                    <div style="border: 1px solid #d1d5db; border-radius: 6px; padding: 12px; max-height: 300px; overflow-y: auto; background: #fff;" id="subjects_container">
                        <p style="color: #9ca3af; font-size: 14px; margin: 0;">Please select a grade first.</p>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 30px;">
                    <button
                        type="submit"
                        class="create-btn"
                        style="padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer;">
                        Assign Subjects to All Classes
                    </button>
                    <a
                        href="classManagement.php"
                        class="action-btn delete-btn"
                        style="padding: 10px 20px; text-decoration: none; display: inline-block; text-align: center;">
                        Cancel
                    </a>
                </div>
            </form>
        </section>
    </main>
</div>

<script>
function loadSubjectsForGrade() {
    const gradeId = document.getElementById('grade_id').value;
    const container = document.getElementById('subjects_container');

    if (!gradeId) {
        container.innerHTML = '<p style="color: #9ca3af; font-size: 14px; margin: 0;">Please select a grade first.</p>';
        return;
    }

    // Fetch subjects for the selected grade
    fetch(`../../controllers/ClassController.php?action=grades`)
        .then(response => response.json())
        .then(data => {
            // This is a fallback - we'll use a simpler approach
            loadSubjectsSimple(gradeId);
        })
        .catch(err => {
            console.error(err);
            loadSubjectsSimple(gradeId);
        });
}

function loadSubjectsSimple(gradeId) {
    const container = document.getElementById('subjects_container');
    
    // Create a form and submit to get subjects
    fetch(`../../api/get_subjects_by_grade.php?grade_id=${gradeId}`)
        .then(response => response.json())
        .then(subjects => {
            if (subjects.length === 0) {
                container.innerHTML = '<p style="color: #6b7280; font-size: 14px; margin: 0;">No subjects found for this grade.</p>';
                return;
            }

            let html = '';
            subjects.forEach(subject => {
                html += `
                    <label style="display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid #f3f4f6; cursor: pointer;">
                        <input type="checkbox" name="subjects[]" value="${subject.subject_id}">
                        <span style="font-size: 14px; color: #374151;">${subject.subject_name}</span>
                    </label>
                `;
            });
            container.innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = '<p style="color: #ef4444; font-size: 14px; margin: 0;">Error loading subjects.</p>';
        });
}
</script>

</body>
</html>
