<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
$no_container = true;
$body_class = 'management-page';
require_once $headerPath;
require_role(['office_admin']);

// Get real subjects from database
require_once __DIR__ . '/../../config/database.php';
$pdo = getDBConnection();

// Handle form submission for adding new subject
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subjectName = trim($_POST['subject_name'] ?? '');
    $gradeId = $_POST['grade_id'] ?? '';

    if (empty($subjectName) || empty($gradeId)) {
        $error = 'Subject name and grade are required.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO subjects (subject_name, grade_id) VALUES (?, ?)");
            $stmt->execute([$subjectName, $gradeId]);
            $success = 'Subject added successfully!';
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'This subject already exists for the selected grade.';
            } else {
                $error = 'Error adding subject: ' . $e->getMessage();
            }
        }
    }
}

// Fetch all subjects with grade info
$subjects = $pdo->query("
    SELECT s.subject_id, s.subject_name, g.grade_id, g.grade_name
    FROM subjects s
    INNER JOIN grades g ON s.grade_id = g.grade_id
    ORDER BY g.grade_id, s.subject_name
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch grades for dropdown
$grades = $pdo->query("SELECT grade_id, grade_name FROM grades ORDER BY grade_id")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

    <main class="main-dashboard">
        <div class="header">
            <h2>Subject Management</h2>
        </div>

        <nav class="breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo $projectRoot; ?>/views/dashboard/index.php">Home</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><span class="breadcrumb-current">Subjects</span></div>
        </nav>

        <h1>Add New Subject</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo e($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo e($success); ?></div>
        <?php endif; ?>

        <section class="form-card">
            <h2>Subject Information</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="subject_name">Subject Name</label>
                    <input type="text" id="subject_name" name="subject_name" placeholder="e.g., Mathematics" required>
                </div>
                <div class="form-group">
                    <label for="grade_id">Grade</label>
                    <select id="grade_id" name="grade_id" required>
                        <option value="">Select Grade</option>
                        <?php foreach ($grades as $grade): ?>
                            <option value="<?php echo $grade['grade_id']; ?>"><?php echo e($grade['grade_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="create-btn">Add Subject</button>
                </div>
            </form>
        </section>

        <h2 class="heading subjects">All Subjects (<?php echo count($subjects); ?>)</h2>

        <section class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject Name</th>
                        <th>Grade</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subjects)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #6b7280;">No subjects found. Add one above!</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td><?php echo $subject['subject_id']; ?></td>
                                <td><?php echo e($subject['subject_name']); ?></td>
                                <td><?php echo e($subject['grade_name']); ?></td>
                                <td>
                                    <a href="subject_edit.php?id=<?php echo $subject['subject_id']; ?>" class="action-btn edit-btn">Edit</a>
                                    <a href="subject_delete.php?id=<?php echo $subject['subject_id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this subject?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
