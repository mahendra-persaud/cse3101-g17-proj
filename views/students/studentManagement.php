<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
require_once $headerPath;
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

        <main class="main-dashboard">

            <div class = "header">
                <h2>Student Management</h2></div>

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
                    <span class="breadcrumb-current">Students</span>
                </div>
            </nav>

            <h1>Students</h1>

            <!-- Search and Filter Section -->
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
                    <button class="voice-search-btn" title="Voice search">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                            <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                            <line x1="12" y1="19" x2="12" y2="23"></line>
                            <line x1="8" y1="23" x2="16" y2="23"></line>
                        </svg>
                    </button>
                    <a href="student_create.php" class="create-btn" style="margin-left: auto;">
                        + Create New Student
                    </a>
                </div>

                <div class="filter-chips-container">
                    <div class="filter-chip active" onclick="filterStudents('all')">All</div>
                    <div class="filter-chip" onclick="filterStudents('grade-1')">Grade 1</div>
                    <div class="filter-chip" onclick="filterStudents('grade-2')">Grade 2</div>
                    <div class="filter-chip" onclick="filterStudents('grade-3')">Grade 3</div>
                    <div class="filter-chip" onclick="filterStudents('grade-4')">Grade 4</div>
                    <div class="filter-chip" onclick="filterStudents('grade-5')">Grade 5</div>
                    <div class="filter-chip" onclick="filterStudents('grade-6')">Grade 6</div>
                    <div class="filter-chip" onclick="filterStudents('class-a')">Class A</div>
                    <div class="filter-chip" onclick="filterStudents('class-b')">Class B</div>
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
                    const gradeCell = rows[i].getElementsByTagName('td')[5]; // Grade column
                    const classCell = rows[i].getElementsByTagName('td')[6]; // Class column

                    if (!gradeCell || !classCell) continue;

                    const grade = gradeCell.textContent.trim();
                    const classValue = classCell.textContent.trim();
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
                        case 'class-a':
                            show = classValue === 'A';
                            break;
                        case 'class-b':
                            show = classValue === 'B';
                            break;
                    }

                    rows[i].style.display = show ? '' : 'none';
                }
            }
            </script>

             <h2 class = 'heading users'>Students</h2>

            <section class="table-card">
               
                <table>
                    <thead>
                    <tr>
                        <th>Select</th>                        
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date of Birth</th>
                        <th>Grade</th>
                        <th>Class</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><input type="checkbox" name="user[]" value="1"></td>
                        <td>001</td>
                        <td>John Smith</td>
                        <td>john@example.com</td>
                        <td>DOB</td>
                        <td>Grade 1</td>
                        <td>A</td>
                        <td><a href="#" class="action-btn edit-btn">Edit</a><a href="#" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="user[]" value="1"></td>
                        <td>001</td>
                        <td>John Smith</td>
                        <td>john@example.com</td>
                        <td>DOB</td>
                        <td>Grade 1</td>
                        <td>A</td>
                        <td><a href="#" class="action-btn edit-btn">Edit</a><a href="#" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="user[]" value="1"></td>
                        <td>001</td>
                        <td>John Smith</td>
                        <td>john@example.com</td>
                        <td>DOB</td>
                        <td>Grade 1</td>
                        <td>A</td>
                        <td><a href="#" class="action-btn edit-btn">Edit</a><a href="#" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="user[]" value="1"></td>
                        <td>001</td>
                        <td>John Smith</td>
                        <td>john@example.com</td>
                        <td>DOB</td>
                        <td>Grade 1</td>
                        <td>A</td>
                        <td><a href="#" class="action-btn edit-btn">Edit</a><a href="#" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="user[]" value="1"></td>
                        <td>001</td>
                        <td>John Smith</td>
                        <td>john@example.com</td>
                        <td>DOB</td>
                        <td>Grade 1</td>
                        <td>A</td>
                        <td><a href="#" class="action-btn edit-btn">Edit</a><a href="#" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a></td>
                    </tr>
                    <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </section>

        </main>
    </div>
</body>
</html>


