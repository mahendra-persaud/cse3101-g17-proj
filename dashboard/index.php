<?php
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/assets/css/dashboard2.css">';
require_once __DIR__ . '/../includes/header.php';
require_role(['office_admin']);
?>

<div class="Main-Container">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="dashboard">
        <!-- Top Bar -->
        <div class="header">
            <h2>Dashboard</h2>

            <div class="user-info">
                <p>Logged in as: &nbsp;<span><strong><?php echo e($_SESSION['username'] ?? 'Admin'); ?></strong></span></p>
                <a class="logout-btn" href="<?php echo $projectRoot; ?>/auth/logout.php" style="text-decoration: none;">Logout</a>
            </div>
        </div>

        <header>
            <h1>System Overview</h1>
        </header>

        <!-- Quick Stats -->
        <section class="stats-grid">
            <a href="<?php echo $projectRoot; ?>/users/userManagement.php" class="stat-card-link">
                <div class="stat-card">
                    <h2>👤 Users</h2>
                    <p>25 Active</p>
                </div>
            </a>
            <a href="<?php echo $projectRoot; ?>/students/studentManagement.php" class="stat-card-link">
                <div class="stat-card">
                    <h2>🎓 Students</h2>
                    <p>320 Enrolled</p>
                </div>
            </a>
            <a href="<?php echo $projectRoot; ?>/classes/classManagement.php" class="stat-card-link">
                <div class="stat-card">
                    <h2>🧑‍🏫 Classes</h2>
                    <p>18 Running</p>
                </div>
            </a>
            <a href="<?php echo $projectRoot; ?>/scores/scores_list.php" class="stat-card-link">
                <div class="stat-card">
                    <h2>📝 Scores</h2>
                    <p>1,245 Recorded</p>
                </div>
            </a>
        </section>

        <!-- Recent Activity -->
        <section class="table-card">
            <h2>Recent Activity</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2026-01-05</td>
                        <td>Sarah Johnson</td>
                        <td>Added Student</td>
                        <td>Enrolled Liam Johnson in Grade 2</td>
                    </tr>
                    <tr>
                        <td>2026-01-06</td>
                        <td>Michael Brown</td>
                        <td>Generated Report</td>
                        <td>Grade 1 Mathematics Term 1</td>
                    </tr>
                    <tr>
                        <td>2026-01-06</td>
                        <td>Emily Davis</td>
                        <td>Updated Scores</td>
                        <td>Grade 3 Science Term 2</td>
                    </tr>
                    <tr>
                        <td>2026-01-06</td>
                        <td>David Wilson</td>
                        <td>Created Class</td>
                        <td>Grade 4 - Class B</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
