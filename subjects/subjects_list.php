<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/assets/css/darkManagement.css">';
require_once $headerPath;
require_role(['office_admin']);

// Sample subjects for Guyana's primary school system
// TODO: Replace with actual database queries
$subjects = [
    // Core subjects offered across all grades
    ['id' => '1', 'code' => 'MATH', 'name' => 'Mathematics', 'grade' => 'All Grades', 'description' => 'Core mathematics curriculum'],
    ['id' => '2', 'code' => 'ENG', 'name' => 'English Language', 'grade' => 'All Grades', 'description' => 'Reading, writing, grammar and comprehension'],
    ['id' => '3', 'code' => 'SCI', 'name' => 'Science', 'grade' => 'All Grades', 'description' => 'General science and scientific inquiry'],
    ['id' => '4', 'code' => 'SOC', 'name' => 'Social Studies', 'grade' => 'All Grades', 'description' => 'History, geography, and civics'],
    ['id' => '5', 'code' => 'HFLE', 'name' => 'Health and Family Life Education', 'grade' => 'All Grades', 'description' => 'Health, wellness, and life skills'],
    ['id' => '6', 'code' => 'PE', 'name' => 'Physical Education', 'grade' => 'All Grades', 'description' => 'Sports, fitness, and physical activities'],
    ['id' => '7', 'code' => 'MUS', 'name' => 'Music', 'grade' => 'All Grades', 'description' => 'Music theory and practice'],
    ['id' => '8', 'code' => 'ART', 'name' => 'Arts and Crafts', 'grade' => 'All Grades', 'description' => 'Visual arts, drawing, and crafts'],
    ['id' => '9', 'code' => 'SPAN', 'name' => 'Spanish', 'grade' => 'Grades 4-6', 'description' => 'Spanish language instruction'],
    ['id' => '10', 'code' => 'ICT', 'name' => 'Information Technology', 'grade' => 'Grades 4-6', 'description' => 'Computer literacy and technology'],
    ['id' => '11', 'code' => 'REL', 'name' => 'Religious Education', 'grade' => 'All Grades', 'description' => 'Moral and religious studies'],
];
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

    <main class="main-dashboard">

        <div class="header">
            <h2>Subjects</h2>

            <div class="user-info">
                <p>Logged in as: &nbsp<span><strong><?php echo e($_SESSION['username'] ?? 'Admin'); ?></strong></span></p>
                <a href="<?php echo $projectRoot; ?>/auth/logout.php" class="logout-btn" style="text-decoration: none;">Logout</a>
            </div>
        </div>

        <h1>Guyanese Primary School Subjects</h1>

        <section style="padding: 0 70px;">
            <a href="subject_create.php" style="background: rgba(35, 140, 246, 0.8); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block; font-family: 'Segoe UI', sans-serif; font-weight: 500;">
                + Create New Subject
            </a>
        </section>

        <h2 class='heading subjects'>All Subjects</h2>

        <section class="table-card">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Grade</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($subjects)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #999;">
                        No subjects found. Click "Create New Subject" to add one.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($subjects as $s): ?>
                    <tr>
                        <td><?php echo e($s['id']); ?></td>
                        <td><?php echo e($s['code']); ?></td>
                        <td><?php echo e($s['name']); ?></td>
                        <td><?php echo e($s['grade']); ?></td>
                        <td><?php echo e($s['description']); ?></td>
                        <td>
                            <a href="subjectPage.php?id=<?php echo urlencode($s['id']); ?>" class="action-link">View</a> |
                            <a href="subject_edit.php?id=<?php echo urlencode($s['id']); ?>" class="action-link">Edit</a> |
                            <a href="#" class="action-link delete" onclick="return confirm('Are you sure you want to delete this subject?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </section>

    </main>
</div>
</body>
</html>