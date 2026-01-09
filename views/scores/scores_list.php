<?php
$headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
require_once $headerPath;
require_role(['teacher', 'office_admin']);

// Sample scores data
// TODO: Replace with actual database queries
$scores = [
    ['id' => '1', 'student_name' => 'John Smith', 'subject_name' => 'Mathematics', 'term' => 'Term 1', 'score' => '85', 'grade' => 'Grade 1'],
    ['id' => '2', 'student_name' => 'Sarah Johnson', 'subject_name' => 'English Language', 'term' => 'Term 1', 'score' => '92', 'grade' => 'Grade 2'],
    ['id' => '3', 'student_name' => 'Michael Brown', 'subject_name' => 'Science', 'term' => 'Term 2', 'score' => '78', 'grade' => 'Grade 3'],
    ['id' => '4', 'student_name' => 'Emily Davis', 'subject_name' => 'Social Studies', 'term' => 'Term 2', 'score' => '88', 'grade' => 'Grade 4'],
    ['id' => '5', 'student_name' => 'David Wilson', 'subject_name' => 'Mathematics', 'term' => 'Term 1', 'score' => '95', 'grade' => 'Grade 5'],
];
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

    <main class="main-dashboard">

        <div class="header">
            <h2>Scores Management</h2>
        </div>

        <h1>Student Scores</h1>

        <section style="padding: 0 40px 24px;">
            <a href="score_create.php" class="create-btn">
                + Add New Score
            </a>
        </section>

        <!-- Search and Filter Section -->
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
                <button class="voice-search-btn" title="Voice search">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                        <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                        <line x1="12" y1="19" x2="12" y2="23"></line>
                        <line x1="8" y1="23" x2="16" y2="23"></line>
                    </svg>
                </button>
            </div>

            <div class="filter-chips-container">
                <div class="filter-chip active" onclick="filterScores('all')">All</div>
                <div class="filter-chip" onclick="filterScores('term-1')">Term 1</div>
                <div class="filter-chip" onclick="filterScores('term-2')">Term 2</div>
                <div class="filter-chip" onclick="filterScores('term-3')">Term 3</div>
                <div class="filter-chip" onclick="filterScores('grade-1')">Grade 1</div>
                <div class="filter-chip" onclick="filterScores('grade-2')">Grade 2</div>
                <div class="filter-chip" onclick="filterScores('grade-3')">Grade 3</div>
                <div class="filter-chip" onclick="filterScores('grade-4')">Grade 4</div>
                <div class="filter-chip" onclick="filterScores('grade-5')">Grade 5</div>
                <div class="filter-chip" onclick="filterScores('grade-6')">Grade 6</div>
                <button class="filters-menu-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="4" y1="21" x2="4" y2="14"></line>
                        <line x1="4" y1="10" x2="4" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12" y2="3"></line>
                        <line x1="20" y1="21" x2="20" y2="16"></line>
                        <line x1="20" y1="12" x2="20" y2="3"></line>
                        <line x1="1" y1="14" x2="7" y2="14"></line>
                        <line x1="9" y1="8" x2="15" y2="8"></line>
                        <line x1="17" y1="16" x2="23" y2="16"></line>
                    </svg>
                    Filters
                </button>
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
            const chips = document.querySelectorAll('.filter-chip');
            chips.forEach(chip => chip.classList.remove('active'));
            event.target.classList.add('active');

            const table = document.querySelector('.table-card table tbody');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const termCell = rows[i].getElementsByTagName('td')[2]; // Term column
                const gradeCell = rows[i].getElementsByTagName('td')[4]; // Grade column

                if (!termCell || !gradeCell) continue;

                const term = termCell.textContent.trim();
                const grade = gradeCell.textContent.trim();
                let show = false;

                switch(category) {
                    case 'all':
                        show = true;
                        break;
                    case 'term-1':
                        show = term === 'Term 1';
                        break;
                    case 'term-2':
                        show = term === 'Term 2';
                        break;
                    case 'term-3':
                        show = term === 'Term 3';
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
        </script>

        <h2 class='heading users'>All Scores</h2>

        <section class="table-card">
            <?php if (empty($scores)): ?>
                <p style="text-align: center; color: #6b7280; padding: 40px;">No scores found. Add scores to get started.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Subject</th>
                            <th>Term</th>
                            <th>Score</th>
                            <th>Grade</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scores as $s): ?>
                        <tr>
                            <td><?php echo e($s['student_name']); ?></td>
                            <td><?php echo e($s['subject_name']); ?></td>
                            <td><?php echo e($s['term']); ?></td>
                            <td><strong><?php echo e($s['score']); ?></strong></td>
                            <td><?php echo e($s['grade']); ?></td>
                            <td>
                                <a href="score_edit.php?id=<?php echo urlencode($s['id']); ?>" class="action-btn edit-btn">Edit</a>
                                <a href="#" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this score?');">Delete</a>
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
