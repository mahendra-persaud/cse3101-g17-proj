<?php
require_once __DIR__ . '/../../config/database.php';

$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
$body_class = 'management-page';

$pdo = getDBConnection();

// getting all the students join with class/grade info
$stmt = $pdo->query("
    SELECT
        s.student_id,
        s.first_name,
        s.last_name,
        c.class_name,
        g.grade_name
    FROM students s
    INNER JOIN classes c ON s.class_id = c.class_id
    INNER JOIN grades g ON c.grade_id = g.grade_id
    ORDER BY g.grade_id, c.class_name, s.last_name, s.first_name
");
$students = $stmt->fetchAll();

$no_container = true;
require_once $headerPath;
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

    <main class="main-dashboard">
        <div class="header">
            <h2>Student Management</h2>
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
                <span class="breadcrumb-current">Students</span>
            </div>
        </nav>

        <?php if (isset($_GET['success'])): ?>
            <div style="background: #d1fae5; border: 1px solid #10b981; color: #065f46; padding: 12px 16px; border-radius: 8px; margin: 20px 0;">
                ✅ <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin: 20px 0;">
                ⚠️ <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <h1>Students</h1>

        <!-- search stuff -->
        <section class="search-filter-section">
            <div class="search-bar-container">
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search students..." onkeyup="searchStudents()">
                    <svg class="clear-icon" onclick="clearSearch()" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </div>
                <a href="student_create.php" class="create-btn" style="margin-left: auto;">
                    + Create New Student
                </a>
            </div>

            <div class="filter-chips-container">
                <div class="filter-chip active" onclick="filterStudents('all')">All (<?php echo count($students); ?>)</div>
                <div class="filter-chip" onclick="filterStudents('grade-1')">Grade 1</div>
                <div class="filter-chip" onclick="filterStudents('grade-2')">Grade 2</div>
                <div class="filter-chip" onclick="filterStudents('grade-3')">Grade 3</div>
                <div class="filter-chip" onclick="filterStudents('grade-4')">Grade 4</div>
                <div class="filter-chip" onclick="filterStudents('grade-5')">Grade 5</div>
                <div class="filter-chip" onclick="filterStudents('grade-6')">Grade 6</div>
            </div>
        </section>

        <script>
        function searchStudents() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.querySelector('.table-card table tbody');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    const cell = cells[j];
                    if (cell) {
                        const textValue = cell.textContent || cell.innerText;
                        if (textValue.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }

                rows[i].style.display = found ? '' : 'none';
            }
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            searchStudents();
        }

        function filterStudents(category) {
            const chips = document.querySelectorAll('.filter-chip');
            chips.forEach(chip => chip.classList.remove('active'));
            event.target.classList.add('active');

            const table = document.querySelector('.table-card table tbody');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const gradeCell = rows[i].getElementsByTagName('td')[3]; // checking the grade

                if (!gradeCell) continue;

                const grade = gradeCell.textContent.trim();
                let show = false;

                switch(category) {
                    case 'all':
                        show = true;
                        break;
                    case 'grade-1':
                        show = grade === 'Grade 1';
                        break;
                    case 'grade-2':
                        show = grade === 'Grade 2';
                        break;
                    case 'grade-3':
                        show = grade === 'Grade 3';
                        break;
                    case 'grade-4':
                        show = grade === 'Grade 4';
                        break;
                    case 'grade-5':
                        show = grade === 'Grade 5';
                        break;
                    case 'grade-6':
                        show = grade === 'Grade 6';
                        break;
                }

                rows[i].style.display = show ? '' : 'none';
            }
        }

        function confirmDelete(studentId, studentName) {
            if (confirm('Are you sure you want to delete ' + studentName + '? This action cannot be undone.')) {
                window.location.href = 'student_delete.php?id=' + studentId;
            }
        }
        </script>

        <h2 class='heading users'>Students</h2>

        <section class="table-card">
            <table>
                <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Grade</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="5" class="empty-state">
                            <div class="empty-state-content">
                                <svg class="empty-state-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <h3>No students found</h3>
                                <p>Get started by creating your first student.</p>
                                <a href="student_create.php" class="empty-state-btn">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Create First Student
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $student): ?>
                        <?php
                        $gradeNum = str_replace('Grade ', '', $student['grade_name']);
                        $className = "Grade {$gradeNum}{$student['class_name']}";
                        $fullName = htmlspecialchars($student['first_name'] . ' ' . $student['last_name']);
                        ?>
                        <tr>
                            <td><?php echo str_pad($student['student_id'], 3, '0', STR_PAD_LEFT); ?></td>
                            <td><strong><?php echo $fullName; ?></strong></td>
                            <td><?php echo htmlspecialchars($className); ?></td>
                            <td><?php echo htmlspecialchars($student['grade_name']); ?></td>
                            <td>
                                <a href="student_edit.php?id=<?php echo $student['student_id']; ?>" class="action-btn edit-btn">Edit</a>
                                <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $student['student_id']; ?>, '<?php echo addslashes($fullName); ?>')" class="action-btn delete-btn">Delete</a>
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
