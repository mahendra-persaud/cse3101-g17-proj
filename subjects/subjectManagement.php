<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/assets/css/darkManagement.css">';
require_once $headerPath;

// Sample subjects for Guyana's primary school system
// TODO: Replace with actual database queries
$subjects = [
    // Core subjects offered across all grades
    ['code' => 'MATH', 'name' => 'Mathematics', 'grade' => 'All Grades', 'teacher' => 'Various', 'description' => 'Core mathematics curriculum'],
    ['code' => 'ENG', 'name' => 'English Language', 'grade' => 'All Grades', 'teacher' => 'Various', 'description' => 'Reading, writing, grammar and comprehension'],
    ['code' => 'SCI', 'name' => 'Science', 'grade' => 'All Grades', 'teacher' => 'Various', 'description' => 'General science and scientific inquiry'],
    ['code' => 'SOC', 'name' => 'Social Studies', 'grade' => 'All Grades', 'teacher' => 'Various', 'description' => 'History, geography, and civics'],
    ['code' => 'HFLE', 'name' => 'Health and Family Life Education', 'grade' => 'All Grades', 'teacher' => 'Various', 'description' => 'Health, wellness, and life skills'],
    ['code' => 'PE', 'name' => 'Physical Education', 'grade' => 'All Grades', 'teacher' => 'Various', 'description' => 'Sports, fitness, and physical activities'],
    ['code' => 'MUS', 'name' => 'Music', 'grade' => 'All Grades', 'teacher' => 'Various', 'description' => 'Music theory and practice'],
    ['code' => 'ART', 'name' => 'Arts and Crafts', 'grade' => 'All Grades', 'teacher' => 'Various', 'description' => 'Visual arts, drawing, and crafts'],
    ['code' => 'SPAN', 'name' => 'Spanish', 'grade' => 'Grades 4-6', 'teacher' => 'Various', 'description' => 'Spanish language instruction'],
    ['code' => 'ICT', 'name' => 'Information Technology', 'grade' => 'Grades 4-6', 'teacher' => 'Various', 'description' => 'Computer literacy and technology'],
    ['code' => 'REL', 'name' => 'Religious Education', 'grade' => 'All Grades', 'teacher' => 'Various', 'description' => 'Moral and religious studies'],
];
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

        <main class="main-dashboard">

            <div class = "header">
                <h2>Subject Management</h2>

                <div class="user-info">
                    <p>Logged in as: &nbsp<span><strong><?php echo e($_SESSION['username'] ?? 'Admin'); ?></strong></span></p>
                    <a href="<?php echo $projectRoot; ?>/auth/logout.php" class="logout-btn">Logout</a>
                </div>
            </div>

            <h1>Add New Subject</h1>

            <section class="form-card">
                <form method="POST">
                    <input type="text" name="subject_code" placeholder="Subject Code (e.g., MATH)" required>
                    <input type="text" name="subject_name" placeholder="Subject Name" required>
                    <select name="grade" required>
                        <option value="">Select Grade</option>
                        <option value="Grade 1">Grade 1</option>
                        <option value="Grade 2">Grade 2</option>
                        <option value="Grade 3">Grade 3</option>
                        <option value="Grade 4">Grade 4</option>
                        <option value="Grade 5">Grade 5</option>
                        <option value="Grade 6">Grade 6</option>
                        <option value="All Grades">All Grades</option>
                    </select>
                    <input type="text" name="description" placeholder="Description" required>

                    <button type="submit">Add Subject</button>
                </form>
            </section>

            <h2 class = 'heading subjects'>Guyanese Primary School Subjects</h2>

            <section class="table-card">

                <table>
                    <thead>
                    <tr>
                        <th>Select</th>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Grade</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($subjects as $index => $subject): ?>
                    <tr>
                        <td><input type="checkbox" name="subject[]" value="<?php echo $index; ?>"></td>
                        <td><?php echo e($subject['code']); ?></td>
                        <td><?php echo e($subject['name']); ?></td>
                        <td><?php echo e($subject['grade']); ?></td>
                        <td><?php echo e($subject['description']); ?></td>
                        <td>
                            <a href="subjectPage.php?id=<?php echo $index; ?>" class="action-link">View</a> |
                            <a href="#" class="action-link">Edit</a> |
                            <a href="#" class="action-link delete">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

        </main>
    </div>
</body>
</html>


