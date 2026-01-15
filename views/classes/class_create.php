<?php
require_once __DIR__ . '/../../config/database.php';

$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
$body_class = 'management-page';

$pdo = getDBConnection();

// get grades for the dropdown menu
$stmt = $pdo->query("SELECT grade_id, grade_name FROM grades ORDER BY grade_id");
$grades = $stmt->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $className = trim($_POST['class_name'] ?? '');
    $gradeId = $_POST['grade_id'] ?? '';

    // validation usually happens here
    if (empty($className)) {
        $error = 'Class name is required.';
    } elseif (empty($gradeId)) {
        $error = 'Grade is required.';
    } else {
        try {
            // checking if we already have this class in this grade
            $stmt = $pdo->prepare("SELECT class_id FROM classes WHERE class_name = ? AND grade_id = ?");
            $stmt->execute([$className, $gradeId]);

            if ($stmt->fetch()) {
                $error = 'A class with this name already exists for the selected grade.';
            } else {
                // making the new class
                $stmt = $pdo->prepare("INSERT INTO classes (class_name, grade_id) VALUES (?, ?)");
                $stmt->execute([$className, $gradeId]);

                header('Location: classManagement.php?success=Class+created+successfully');
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Error creating class: ' . $e->getMessage();
        }
    }
}

$no_container = true;
require_once $headerPath;
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

    <main class="main-dashboard">
        <div class="header">
            <h2>Create New Class</h2>
        </div>

        <!-- breadcrumb nav -->
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
                <span class="breadcrumb-current">Create</span>
            </div>
        </nav>

        <?php if (!empty($error)): ?>
            <div style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin: 20px 0;">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <section class="table-card" style="max-width: 600px;">
            <h3 style="margin-bottom: 20px;">Class Information</h3>

            <form method="POST" action="">
                <div style="margin-bottom: 20px;">
                    <label for="grade_id" style="display: block; margin-bottom: 8px; font-weight: 500;">Grade</label>
                    <select
                        id="grade_id"
                        name="grade_id"
                        required
                        style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        <option value="">Select a grade</option>
                        <?php foreach ($grades as $grade): ?>
                            <option value="<?php echo $grade['grade_id']; ?>"
                                    <?php echo (isset($_POST['grade_id']) && $_POST['grade_id'] == $grade['grade_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($grade['grade_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="class_name" style="display: block; margin-bottom: 8px; font-weight: 500;">Class Name</label>
                    <input
                        type="text"
                        id="class_name"
                        name="class_name"
                        value="<?php echo htmlspecialchars($_POST['class_name'] ?? ''); ?>"
                        placeholder="e.g., A, B, C"
                        required
                        maxlength="10"
                        style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <small style="color: #6b7280; font-size: 12px;">Enter a single letter or name for the class (e.g., A, B, C)</small>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 30px;">
                    <button
                        type="submit"
                        class="create-btn"
                        style="padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer;">
                        Create Class
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
</body>
</html>
