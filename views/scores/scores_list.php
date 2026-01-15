<?php
$headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
$no_container = true;
require_once $headerPath;
require_role(['teacher', 'office_admin']);

// fake scores for now
// TODO: hook up to real db later
// grab real scores from controller
require_once __DIR__ . '/../../controllers/ScoreController.php';
$scoreController = new ScoreController();
$scores = $scoreController->index();
$flash = ScoreController::getFlash();
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

    <main class="main-dashboard">

        <div class="header">
            <h2>Scores Management</h2>
        </div>

        <nav class="breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo $projectRoot; ?>/views/dashboard/index.php">Home</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><span class="breadcrumb-current">Scores</span></div>
        </nav>

        <h1>Student Academic Performance</h1>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo e($flash['message']); ?>
            </div>
        <?php endif; ?>

        <section style="padding: 0 40px 24px;">
            <a href="score_create.php" class="create-btn">
                + Add New Score
            </a>
        </section>

        <!-- search and filters -->
        <section class="search-filter-section">
            <div class="search-bar-container">
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search scores..." onkeyup="searchScores()">
                    <svg class="clear-icon" onclick="clearSearch()" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </div>
            </div>

            <div class="filter-chips-container">
                <div class="filter-chip active" onclick="filterScores('all')">All Terms</div>
                <?php for ($t=1; $t<=3; $t++): ?>
                    <div class="filter-chip" onclick="filterScores('term-<?php echo $t; ?>')">Term <?php echo $t; ?></div>
                <?php endfor; ?>
                <div class="filter-chip" style="margin-left: 20px;" onclick="filterScores('grade-all')">All Grades</div>
                <?php for ($g=1; $g<=6; $g++): ?>
                    <div class="filter-chip" onclick="filterScores('grade-<?php echo $g; ?>')">Grade <?php echo $g; ?></div>
                <?php endfor; ?>
            </div>
        </section>

        <script>
        function searchScores() {
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
            searchScores();
        }

        function filterScores(category) {
            const table = document.querySelector('.table-card table tbody');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const termCell = rows[i].getElementsByTagName('td')[2]; // checking the term
                const gradeCell = rows[i].getElementsByTagName('td')[4]; // checking the grade

                if (!termCell || !gradeCell) continue;

                const term = termCell.textContent.trim();
                const grade = gradeCell.textContent.trim();
                let show = true;

                if (category === 'all') {
                    show = true;
                } else if (category.startsWith('term-')) {
                    const termNum = category.split('-')[1];
                    show = term.includes(termNum);
                } else if (category.startsWith('grade-')) {
                    if (category === 'grade-all') {
                        show = true;
                    } else {
                        const gradeNum = category.split('-')[1];
                        show = grade.includes(gradeNum);
                    }
                }

                rows[i].style.display = show ? '' : 'none';
            }
        }
        </script>

        <section class="table-card">
            <?php if (empty($scores)): ?>
                <div class="empty-state">
                    <div class="empty-state-content" style="text-align: center; padding: 40px;">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        <h3 style="color: #4b5563; margin-top: 16px;">No scores recorded yet</h3>
                        <p style="color: #6b7280;">Student academic records will appear here once entered by teachers.</p>
                        <a href="score_create.php" class="create-btn" style="display: inline-block; margin-top: 20px;">+ Add First Score</a>
                    </div>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Subject</th>
                            <th>Term / Year</th>
                            <th>Score</th>
                            <th>Grade Level</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scores as $s): ?>
                        <tr>
                            <td><strong><?php echo e($s['student_name']); ?></strong></td>
                            <td><?php echo e($s['subject_name']); ?></td>
                            <td>
                                <div style="font-size: 0.9em;"><?php echo e($s['term_name']); ?></div>
                                <div style="font-size: 0.8em; color: #6b7280;"><?php echo e($s['year_name']); ?></div>
                            </td>
                            <td>
                                <span class="score-value" style="font-weight: 600; font-size: 1.1em;">
                                    <?php echo e($s['score']); ?>%
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-info">Grade <?php echo e($s['grade_name']); ?></span>
                            </td>
                            <td>
                                <a href="score_edit.php?id=<?php echo urlencode($s['score_id']); ?>" class="action-btn edit-btn">Edit</a>
                                <a href="<?php echo $projectRoot; ?>/controllers/ScoreController.php?action=delete&id=<?php echo $s['score_id']; ?>" 
                                   class="action-btn delete-btn" 
                                   onclick="return confirm('Are you sure you want to delete this score?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

    </main>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
