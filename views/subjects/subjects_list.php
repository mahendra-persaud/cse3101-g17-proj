<?php
$headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
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
        </div>

        <!-- Breadcrumb Navigation -->
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
                <span class="breadcrumb-current">Subjects</span>
            </div>
        </nav>

        <h1>Guyanese Primary School Subjects</h1>

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
                <button class="voice-search-btn" title="Voice search">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                        <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                        <line x1="12" y1="19" x2="12" y2="23"></line>
                        <line x1="8" y1="23" x2="16" y2="23"></line>
                    </svg>
                </button>
                <a href="subject_create.php" class="create-btn" style="margin-left: auto;">
                    + Create New Subject
                </a>
            </div>

            <div class="filter-chips-container">
                <div class="filter-chip active" onclick="filterSubjects('all')">All</div>
                <div class="filter-chip" onclick="filterSubjects('all-grades')">All Grades</div>
                <div class="filter-chip" onclick="filterSubjects('grades-4-6')">Grades 4-6</div>
                <div class="filter-chip" onclick="filterSubjects('core')">Core</div>
                <div class="filter-chip" onclick="filterSubjects('arts')">Arts</div>
                <div class="filter-chip" onclick="filterSubjects('language')">Languages</div>
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

        <h2 class='heading subjects'>All Subjects</h2>

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
            // Remove active class from all chips
            const chips = document.querySelectorAll('.filter-chip');
            chips.forEach(chip => chip.classList.remove('active'));

            // Add active class to clicked chip
            event.target.classList.add('active');

            const table = document.querySelector('.table-card table tbody');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const gradeCell = rows[i].getElementsByTagName('td')[3]; // Grade column
                const nameCell = rows[i].getElementsByTagName('td')[2]; // Name column

                if (!gradeCell || !nameCell) continue;

                const grade = gradeCell.textContent.trim();
                const name = nameCell.textContent.trim().toLowerCase();
                let show = false;

                switch(category) {
                    case 'all':
                        show = true;
                        break;
                    case 'all-grades':
                        show = grade === 'All Grades';
                        break;
                    case 'grades-4-6':
                        show = grade === 'Grades 4-6';
                        break;
                    case 'core':
                        show = ['mathematics', 'english language', 'science', 'social studies'].includes(name);
                        break;
                    case 'arts':
                        show = ['music', 'arts and crafts', 'physical education'].includes(name);
                        break;
                    case 'language':
                        show = ['english language', 'spanish'].includes(name);
                        break;
                }

                rows[i].style.display = show ? '' : 'none';
            }
        }
        </script>

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
                    <td colspan="6" class="empty-state">
                        <div class="empty-state-content">
                            <svg class="empty-state-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <h3>No subjects found</h3>
                            <p>Get started by creating your first subject for the school curriculum.</p>
                            <a href="subject_create.php" class="empty-state-btn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Create First Subject
                            </a>
                        </div>
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
                            <a href="subject_edit.php?id=<?php echo urlencode($s['id']); ?>" class="action-btn edit-btn">Edit</a>
                            <a href="#" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this subject?');">Delete</a>
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