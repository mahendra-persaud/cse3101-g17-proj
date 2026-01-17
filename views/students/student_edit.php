<?php
// editing a student
require_once __DIR__ . '/../../config/database.php';

$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
$body_class = 'management-page';
$no_container = true;

$pdo = getDBConnection();
$success = '';
$error = '';
$studentId = $_GET['id'] ?? '';

if (empty($studentId)) {
    header('Location: studentManagement.php');
    exit;
}

// finding the student
$stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: studentManagement.php?error=Student+not+found');
    exit;
}

// grabbing classes for the dropdown
$stmt = $pdo->query("
    SELECT c.class_id, c.class_name, g.grade_name
    FROM classes c
    INNER JOIN grades g ON c.grade_id = g.grade_id
    ORDER BY g.grade_id, c.class_name
");
$classes = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $classId = $_POST['class_id'] ?? '';
    $dateOfBirth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';

    if (empty($firstName) || empty($lastName) || empty($classId)) {
        $error = 'First name, last name, and class are required.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE students SET first_name = ?, last_name = ?, class_id = ?, date_of_birth = ?, gender = ? WHERE student_id = ?");
            $stmt->execute([$firstName, $lastName, $classId, $dateOfBirth ?: null, $gender ?: null, $studentId]);

            header('Location: studentManagement.php?success=Student+updated+successfully');
            exit;
        } catch (PDOException $e) {
            $error = 'Error updating student: ' . $e->getMessage();
        }
    }
} else {
    $_POST['first_name'] = $student['first_name'];
    $_POST['last_name'] = $student['last_name'];
    $_POST['class_id'] = $student['class_id'];
    $_POST['date_of_birth'] = $student['date_of_birth'] ?? '';
    $_POST['gender'] = $student['gender'] ?? '';
}

require_once $headerPath;
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

    <main class="main-dashboard">
        <div class="header">
            <h2>Edit Student</h2>
        </div>

        <!-- breadcrumbs -->
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
                <a href="studentManagement.php" class="breadcrumb-link">Students</a>
            </div>
            <div class="breadcrumb-separator">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </div>
            <div class="breadcrumb-item">
                <span class="breadcrumb-current">Edit</span>
            </div>
        </nav>

        <?php if ($error): ?>
            <div style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin: 20px 0;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <section class="table-card" style="max-width: 600px;">
            <form method="POST" style="padding: 24px;">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">First Name *</label>
                    <input type="text" name="first_name" required
                           style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;"
                           value="<?php echo htmlspecialchars($_POST['first_name']); ?>">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">Last Name *</label>
                    <input type="text" name="last_name" required
                           style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;"
                           value="<?php echo htmlspecialchars($_POST['last_name']); ?>">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">Date of Birth</label>
                    <input type="date" name="date_of_birth"
                           style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;"
                           value="<?php echo htmlspecialchars($_POST['date_of_birth']); ?>">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">Gender</label>
                    <select name="gender"
                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        <option value="">Select gender</option>
                        <option value="Male" <?php echo ($_POST['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($_POST['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">Class *</label>
                    <select name="class_id" required
                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        <option value="">Select a class</option>
                        <?php foreach ($classes as $class): ?>
                            <?php
                            $gradeNum = str_replace('Grade ', '', $class['grade_name']);
                            $className = "Grade {$gradeNum}{$class['class_name']}";
                            ?>
                            <option value="<?php echo $class['class_id']; ?>"
                                    <?php echo ($_POST['class_id'] == $class['class_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($className); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <button type="submit"
                            style="padding: 10px 24px; background: #0891b2; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                        Update Student
                    </button>
                    <a href="studentManagement.php"
                       style="padding: 10px 24px; background: #f3f4f6; color: #374151; border: none; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-block;">
                        Cancel
                    </a>
                </div>
            </form>
        </section>
    </main>
</div>
</body>
</html>
