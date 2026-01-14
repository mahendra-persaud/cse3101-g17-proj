<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
require_once $headerPath;
?>
<?php
require_once __DIR__ . '/../../controllers/UserController.php';
$userController = new UserController();
$users = $userController->index();
$flash = UserController::getFlash();
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

        <main class="main-dashboard">

            <div class = "header">
                <h2>User Management</h2></div>

            <!-- breadcrumb nav -->
            <nav class="breadcrumb">
                <div class="breadcrumb-item">
                    <a href="<?php echo $projectRoot; ?>/views/dashboard/index.php" class="breadcrumb-link">Home</a>
                </div>
                <div class="breadcrumb-separator">></div>
                <div class="breadcrumb-item">
                    <span class="breadcrumb-current">Users</span>
                </div>
            </nav>

            <h1>Users</h1>

            <?php if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo e($flash['message']); ?>
                </div>
            <?php endif; ?>

            <!-- search and filters -->
            <section class="search-filter-section">
                <div class="search-bar-container">
                    <div class="search-bar">
                        <input type="text" id="searchInput" placeholder="Search users..." onkeyup="searchUsers()">
                        <svg class="clear-icon" onclick="clearSearch()" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </div>
                    <a href="user_create.php" class="create-btn" style="margin-left: auto;">
                        + Create New User
                    </a>
                </div>

                <div class="filter-chips-container">
                    <div class="filter-chip active" onclick="filterUsers('all')">All</div>
                    <div class="filter-chip" onclick="filterUsers('office-admin')">Office Admin</div>
                    <div class="filter-chip" onclick="filterUsers('teacher')">Teacher</div>
                </div>
            </section>

            <script>
            function searchUsers() {
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
                searchUsers();
            }

            function filterUsers(category) {
                const chips = document.querySelectorAll('.filter-chip');
                chips.forEach(chip => chip.classList.remove('active'));
                event.target.classList.add('active');

                const table = document.querySelector('.table-card table tbody');
                const rows = table.getElementsByTagName('tr');

                for (let i = 0; i < rows.length; i++) {
                    const roleCell = rows[i].getElementsByTagName('td')[3]; // checking the role

                    if (!roleCell) continue;

                    const role = roleCell.textContent.trim().toLowerCase();
                    let show = false;

                    switch(category) {
                        case 'all':
                            show = true;
                            break;
                        case 'teacher':
                            show = role === 'teacher';
                            break;
                        case 'office-admin':
                            show = role === 'office admin';
                            break;
                    }

                    rows[i].style.display = show ? '' : 'none';
                }
            }

            function deleteUser(id) {
                if (confirm('Are you sure you want to delete this user?')) {
                    const projectRoot = '<?php echo $projectRoot; ?>';
                    window.location.href = `${projectRoot}/controllers/UserController.php?action=delete&id=${id}`;
                }
            }
            </script>

            <section class="table-card">
                <?php if (empty($users)): ?>
                    <p style="text-align: center; color: #6b7280; padding: 40px;">No users found.</p>
                <?php else: ?>
                    <table>
                        <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo e($user['user_id']); ?></td>
                                <td><strong><?php echo e($user['username']); ?></strong></td>
                                <td>
                                    <span class="badge <?php echo ($user['role_name'] === 'office_admin') ? 'badge-info' : 'badge-success'; ?>">
                                        <?php echo e($user['role_name']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="user_edit.php?id=<?php echo $user['user_id']; ?>" class="action-btn edit-btn">Edit</a>
                                    <a href="<?php echo $projectRoot; ?>/controllers/UserController.php?action=delete&id=<?php echo $user['user_id']; ?>" 
                                       class="action-btn delete-btn" 
                                       onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>

        </main>
    </div>


        </main>
    </div>
</body>
</html>


