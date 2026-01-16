<?php
// teacher_list.php - Teacher Management List
require_once __DIR__ . '/../../controllers/TeacherController.php';

$teacherController = new TeacherController();
$teacherController->requireRole('office_admin');

$teachers = $teacherController->index();
$flash = TeacherController::getFlash();

$projectRoot = '/cse3101-g17-proj';
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
$no_container = true;
$body_class = 'management-page';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="Main-Container">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="main-dashboard">
        <div class="header">
            <h2>Teacher Management</h2>
        </div>

        <nav class="breadcrumb">
            <div class="breadcrumb-item">
                <a href="<?php echo $projectRoot; ?>/views/dashboard/index.php">Home</a>
            </div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item">
                <span class="breadcrumb-current">Teachers</span>
            </div>
        </nav>

        <h1>Teachers</h1>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo e($flash['message']); ?>
            </div>
        <?php endif; ?>

        <section class="search-filter-section">
            <div class="search-bar-container">
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search teachers..." onkeyup="searchTeachers()">
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
        </section>

        <script>
        function searchTeachers() {
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
            searchTeachers();
        }
        </script>

        <section class="table-card">
            <?php if (empty($teachers)): ?>
                <p style="text-align: center; color: #6b7280; padding: 40px;">No teachers found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Subjects</th>
                            <th>Classes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $teacher):
                            $summary = $teacherController->getAssignmentSummary($teacher['teacher_id']);
                        ?>
                            <tr>
                                <td><?php echo e($teacher['teacher_id']); ?></td>
                                <td><strong><?php echo e($teacher['first_name'] . ' ' . $teacher['last_name']); ?></strong></td>
                                <td><?php echo e($teacher['email']); ?></td>
                                <td><?php echo e($teacher['username']); ?></td>
                                <td>
                                    <span class="badge badge-info"><?php echo $summary['subject_count']; ?> subjects</span>
                                </td>
                                <td>
                                    <span class="badge badge-success"><?php echo $summary['class_count']; ?> classes</span>
                                </td>
                                <td>
                                    <a href="teacher_assignments.php?id=<?php echo $teacher['teacher_id']; ?>" class="action-btn edit-btn">Manage Assignments</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
