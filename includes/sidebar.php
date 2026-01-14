<?php
// sidebar logic
$role = $_SESSION['role'] ?? 'guest';
$currentPage = basename($_SERVER['PHP_SELF']);
$currentPath = $_SERVER['PHP_SELF'];

// checks if we are on this page to highlight the link
function isActive($pagePatterns) {
    global $currentPage, $currentPath;
    if (!is_array($pagePatterns)) {
        $pagePatterns = [$pagePatterns];
    }
    foreach ($pagePatterns as $pattern) {
        if (strpos($currentPath, $pattern) !== false || $currentPage === $pattern) {
            return 'active';
        }
    }
    return '';
}
?>
<div class="sidebar">
    <!-- Logo stuff -->
    <div class="sidebar-header">
        <img src="<?php echo $projectRoot; ?>/public/assets/img/logo.png" alt="SMS Logo" class="sidebar-logo">
        <div class="header-text">
            <h2>SchoolStream</h2>
            <p>School Management</p>
        </div>
    </div>

    <?php if ($role === 'office_admin'): ?>
        <div class="sidebar-role-badge">Administrator</div>

        <a href="<?php echo $projectRoot; ?>/views/dashboard/index.php" class="<?php echo isActive(['dashboard', 'index.php']); ?>">
            <span class="icon icon-dashboard-new" aria-hidden="true"></span>
            <span class="label">Dashboard</span>
        </a>

        <a href="<?php echo $projectRoot; ?>/views/users/userManagement.php" class="<?php echo isActive(['users/', 'userManagement', 'userPage', 'userProfile']); ?>">
            <span class="icon icon-users-new" aria-hidden="true"></span>
            <span class="label">Users</span>
        </a>

        <a href="<?php echo $projectRoot; ?>/views/students/studentManagement.php" class="<?php echo isActive(['students/', 'studentManagement', 'studentPage', 'studentProfile']); ?>">
            <span class="icon icon-students-new" aria-hidden="true"></span>
            <span class="label">Students</span>
        </a>

        <a href="<?php echo $projectRoot; ?>/views/classes/classManagement.php" class="<?php echo isActive(['classes/', 'classManagement', 'classPage']); ?>">
            <span class="icon icon-classes-new" aria-hidden="true"></span>
            <span class="label">Classes</span>
        </a>

        <a href="<?php echo $projectRoot; ?>/views/subjects/subjects_list.php" class="<?php echo isActive(['subjects/', 'subject']); ?>">
            <span class="icon icon-subjects-new" aria-hidden="true"></span>
            <span class="label">Subjects</span>
        </a>

        <a href="<?php echo $projectRoot; ?>/views/scores/scores_list.php" class="<?php echo isActive(['scores/']); ?>">
            <span class="icon icon-score-new" aria-hidden="true"></span>
            <span class="label">Scores</span>
        </a>

        <div class="sidebar-divider"></div>

        <a href="<?php echo $projectRoot; ?>/views/school/school_years_list.php" class="<?php echo isActive(['school_years', 'school_year', 'years_terms']); ?>">
            <span class="icon icon-years-new" aria-hidden="true"></span>
            <span class="label">School Years</span>
        </a>

        <a href="<?php echo $projectRoot; ?>/views/school/term_manage.php" class="<?php echo isActive(['term_manage', 'term_create', 'terms.php']); ?>">
            <span class="icon icon-terms-new" aria-hidden="true"></span>
            <span class="label">Terms</span>
        </a>

    <?php endif; ?>

    <?php if ($role === 'teacher'): ?>
        <div class="sidebar-role-badge">Teacher</div>

        <a href="<?php echo $projectRoot; ?>/views/scores/scores_entry.php" class="<?php echo isActive(['scores_entry', 'score.php']); ?>">
            <span class="icon icon-score-new" aria-hidden="true"></span>
            <span class="label">Enter Scores</span>
        </a>

        <a href="<?php echo $projectRoot; ?>/views/scores/scores_list.php" class="<?php echo isActive('scores_list'); ?>">
            <span class="icon icon-score-new" aria-hidden="true"></span>
            <span class="label">View Scores</span>
        </a>
    <?php endif; ?>

    <div class="sidebar-divider"></div>

    <a href="<?php echo $projectRoot; ?>/views/reports/report_student_card.php" class="<?php echo isActive(['reports/', 'report_']); ?>">
        <span class="icon icon-reports-new" aria-hidden="true"></span>
        <span class="label">Reports</span>
    </a>

    <?php if (!empty($_SESSION['username'])): ?>
        <a href="<?php echo $projectRoot; ?>/views/auth/logout.php" class="logout-link">
            <span class="icon icon-logout" aria-hidden="true"></span>
            <span class="label">Logout</span>
        </a>
    <?php endif; ?>
</div>