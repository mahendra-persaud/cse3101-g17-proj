<?php
$headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
require_once $headerPath;
require_role(['office_admin']);

// Fetch real subjects using the controller
require_once __DIR__ . '/../../controllers/SubjectController.php';
$subjectController = new SubjectController();
$subjects = $subjectController->index();
$flash = SubjectController::getFlash();
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

    <main class="main-dashboard">

        <div class="header">
            <h2>Subjects</h2>
        </div>

        <!-- Breadcrumb Navigation -->
        <nav class="breadcrumb">
            <div class="breadcrumb-item">
                <a href="<?php echo $projectRoot; ?>/views/dashboard/index.php" class="breadcrumb-link">Home</a>
            </div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item">
                <span class="breadcrumb-current">Subjects</span>
            </div>
        </nav>

        <h1>School Curriculum</h1>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo e($flash['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Search and Filter Section -->
        <section class="search-filter-section">
            <div class="search-bar-container">
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search subjects..." onkeyup="searchSubjects()">
                    <svg class="clear-icon" onclick="clearSearch()" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </div>
                <a href="subject_create.php" class="create-btn" style="margin-left: auto;">
                    + Create New Subject
                </a>
            </div>

            <div class="filter-chips-container">
                <div class="filter-chip active" onclick="filterSubjects('all')">All Grades</div>
                <?php for ($g=1; $g<=6; $g++): ?>
                    <div class="filter-chip" onclick="filterSubjects('grade-<?php echo $g; ?>')">Grade <?php echo $g; ?></div>
                <?php endfor; ?>
            </div>
        </section>

        <script>
        function searchSubjects() {
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
            searchSubjects();
        }

        function filterSubjects(category) {
            const chips = document.querySelectorAll('.filter-chip');
            chips.forEach(chip => chip.classList.remove('active'));
            event.target.classList.add('active');

            const table = document.querySelector('.table-card table tbody');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const gradeCell = rows[i].getElementsByTagName('td')[2]; // Grade column

                if (!gradeCell) continue;

                const grade = gradeCell.textContent.trim();
                let show = false;

                if (category === 'all') {
                    show = true;
                } else {
                    const gradeNum = category.split('-')[1];
                    show = grade.includes(gradeNum);
                }

                rows[i].style.display = show ? '' : 'none';
            }
        }
        </script>

        <section class="table-card">
            <?php if (empty($subjects)): ?>
                <div class="empty-state">
                    <div class="empty-state-content">
                        <svg class="empty-state-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <h3>No subjects found</h3>
                        <p>Get started by creating your first subject for the school curriculum.</p>
                        <a href="subject_create.php" class="empty-state-btn">+ Create First Subject</a>
                    </div>
                </div>
            <?php else: ?>
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
                        <?php foreach ($subjects as $s): ?>
                        <tr>
                            <td><?php echo e($s['subject_id']); ?></td>
                            <td><strong><?php echo e($s['subject_name']); ?></strong></td>
                            <td>
                                <span class="badge badge-info">Grade <?php echo e($s['grade_name']); ?></span>
                            </td>
                            <td>
                                <a href="subject_edit.php?id=<?php echo urlencode($s['subject_id']); ?>" class="action-btn edit-btn">Edit</a>
                                <a href="<?php echo $projectRoot; ?>/controllers/SubjectController.php?action=delete&id=<?php echo $s['subject_id']; ?>" 
                                   class="action-btn delete-btn" 
                                   onclick="return confirm('Are you sure you want to delete this subject?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

    </main>
</div>
</body>
</html>