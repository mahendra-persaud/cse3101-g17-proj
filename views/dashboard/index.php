<?php
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/dashboard2.css?v=' . time() . '">';
$no_container = true;
require_once __DIR__ . '/../../includes/header.php';
require_role(['office_admin', 'teacher']);

// Get real stats from database
require_once __DIR__ . '/../../config/database.php';
$pdo = getDBConnection();

// Count users
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Count students
$studentCount = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();

// Count classes
$classCount = $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();

// Count scores
$scoreCount = $pdo->query("SELECT COUNT(*) FROM scores")->fetchColumn();

// Count subjects
$subjectCount = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();

// Get recent scores activity
$recentActivity = $pdo->query("
    SELECT
        DATE(s.modified_at) as activity_date,
        COALESCE(u.username, 'System') as username,
        'Score Entry' as action,
        CONCAT(st.first_name, ' ', st.last_name, ' - ', sub.subject_name, ' (', s.score, ')') as details
    FROM scores s
    LEFT JOIN users u ON s.created_by = u.user_id
    LEFT JOIN students st ON s.student_id = st.student_id
    LEFT JOIN subjects sub ON s.subject_id = sub.subject_id
    ORDER BY s.created_at DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
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
                        <p><?php echo number_format($userCount); ?> Active</p>
                        <div class="stat-trend stat-trend-neutral">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>System Users</span>
                        </div>
                    </div>
                </div>
            </a>
            <a href="<?php echo $projectRoot; ?>/views/students/studentManagement.php" class="stat-card-link">
                <div class="stat-card">
                    <span class="icon icon-students-new stat-icon"></span>
                    <div class="stat-content">
                        <h2>Students</h2>
                        <p><?php echo number_format($studentCount); ?> Enrolled</p>
                        <div class="stat-trend stat-trend-up">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                <polyline points="17 6 23 6 23 12"></polyline>
                            </svg>
                            <span>All Grades</span>
                        </div>
                    </div>
                </div>
            </a>
            <a href="<?php echo $projectRoot; ?>/views/classes/classManagement.php" class="stat-card-link">
                <div class="stat-card">
                    <span class="icon icon-classes-new stat-icon"></span>
                    <div class="stat-content">
                        <h2>Classes</h2>
                        <p><?php echo number_format($classCount); ?> Active</p>
                        <div class="stat-trend stat-trend-neutral">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Grades 1-6</span>
                        </div>
                    </div>
                </div>
            </a>
            <a href="<?php echo $projectRoot; ?>/views/scores/scores_list.php" class="stat-card-link">
                <div class="stat-card">
                    <span class="icon icon-score-new stat-icon"></span>
                    <div class="stat-content">
                        <h2>Scores</h2>
                        <p><?php echo number_format($scoreCount); ?> Recorded</p>
                        <div class="stat-trend stat-trend-up">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                <polyline points="17 6 23 6 23 12"></polyline>
                            </svg>
                            <span><?php echo number_format($subjectCount); ?> Subjects</span>
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
                    <?php if (empty($recentActivity)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #6b7280;">No recent activity yet. Start by adding scores!</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentActivity as $activity): ?>
                            <tr>
                                <td><?php echo e($activity['activity_date'] ?? date('Y-m-d')); ?></td>
                                <td><?php echo e($activity['username']); ?></td>
                                <td><?php echo e($activity['action']); ?></td>
                                <td><?php echo e($activity['details']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
