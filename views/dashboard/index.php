<?php
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/dashboard2.css?v=' . time() . '">';
$no_container = true;
require_once __DIR__ . '/../../includes/header.php';
require_role(['office_admin']);
?>

<div class="Main-Container">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="dashboard">
        <!-- top bar with search and profile -->
        <div class="header">
            <div class="top-search">
                <span class="search-icon">🔍</span>
                <input type="text" placeholder="Search for students, classes or reports...">
            </div>

            <div class="top-actions">
                <div class="notification-bell">
                    <span style="font-size: 20px;">🔔</span>
                    <div class="bell-badge"></div>
                </div>

                <div class="user-profile-group">
                    <div class="avatar-circle">
                        <?php 
                        $initials = '';
                        $names = explode(' ', $_SESSION['username'] ?? 'User');
                        foreach ($names as $n) $initials .= strtoupper($n[0]);
                        echo substr($initials, 0, 2);
                        ?>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?php echo e($_SESSION['username'] ?? 'User'); ?></span>
                        <span class="user-role"><?php echo str_replace('_', ' ', e($_SESSION['role'] ?? 'Role')); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <header>
            <div class="breadcrumbs">
                Home <span>/</span> Dashboard
            </div>
            
            <?php 
            require_once __DIR__ . '/../../models/School.php';
            $school = new School();
            $context = $school->getCurrentAcademicContext();
            ?>

            <div class="dynamic-greeting">
                <h1>Welcome back, <?php echo e(explode(' ', $_SESSION['username'] ?? 'User')[0]); ?>!</h1>
                <div class="academic-badge">
                    <span class="dot"></span>
                    <?php echo e($context['term_name']); ?> | <?php echo e($context['year_name']); ?>
                </div>
            </div>
        </header>

        <!-- quick stats summary -->
        <section class="stats-grid">
            <a href="<?php echo $projectRoot; ?>/views/users/userManagement.php" class="stat-card-link">
                <div class="stat-card">
                    <span class="icon icon-users-new stat-icon"></span>
                    <div class="stat-content">
                        <h2>Users</h2>
                        <p>25 Active</p>
                        <div class="stat-trend stat-trend-up">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                <polyline points="17 6 23 6 23 12"></polyline>
                            </svg>
                            <span>+3 this week</span>
                        </div>
                    </div>
                </div>
            </a>
            <a href="<?php echo $projectRoot; ?>/views/students/studentManagement.php" class="stat-card-link">
                <div class="stat-card">
                    <span class="icon icon-students-new stat-icon"></span>
                    <div class="stat-content">
                        <h2>Students</h2>
                        <p>320 Enrolled</p>
                        <div class="stat-trend stat-trend-up">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                <polyline points="17 6 23 6 23 12"></polyline>
                            </svg>
                            <span>+12 this month</span>
                        </div>
                    </div>
                </div>
            </a>
            <a href="<?php echo $projectRoot; ?>/views/classes/classManagement.php" class="stat-card-link">
                <div class="stat-card">
                    <span class="icon icon-classes-new stat-icon"></span>
                    <div class="stat-content">
                        <h2>Classes</h2>
                        <p>18 Running</p>
                        <div class="stat-trend stat-trend-neutral">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>No change</span>
                        </div>
                    </div>
                </div>
            </a>
            <a href="<?php echo $projectRoot; ?>/views/scores/scores_list.php" class="stat-card-link">
                <div class="stat-card">
                    <span class="icon icon-score-new stat-icon"></span>
                    <div class="stat-content">
                        <h2>Scores</h2>
                        <p>1,245 Recorded</p>
                        <div class="stat-trend stat-trend-up">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                <polyline points="17 6 23 6 23 12"></polyline>
                            </svg>
                            <span>+87 this week</span>
                        </div>
                    </div>
                </div>
            </a>
        </section>

        <!-- recent stuff table -->
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

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
