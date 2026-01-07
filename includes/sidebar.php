<?php
// Sidebar with active page detection
$role = $_SESSION['role'] ?? 'guest';
$currentPage = basename($_SERVER['PHP_SELF']);
$currentPath = $_SERVER['PHP_SELF'];

// Helper function to check if link is active
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
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <h2>SMS</h2>
        <p>School Management</p>
    </div>

    <?php if ($role === 'office_admin'): ?>
        <div class="sidebar-role-badge">Administrator</div>

        <a href="<?php echo $projectRoot; ?>/views/dashboard/index.php" class="<?php echo isActive(['dashboard', 'index.php']); ?>">
            <span class="icon icon-dashboard" aria-hidden="true"></span>
            <span class="label">Dashboard</span>
        </a>

        <a href="<?php echo $projectRoot; ?>/views/users/userManagement.php" class="<?php echo isActive(['users/', 'userManagement', 'userPage', 'userProfile']); ?>">
            <span class="icon icon-users" aria-hidden="true"></span>
            <span class="label">Users</span>
        </a>

        <a href="<?php echo $projectRoot; ?>/views/students/studentManagement.php" class="<?php echo isActive(['students/', 'studentManagement', 'studentPage', 'studentProfile']); ?>">
            <span class="icon icon-students" aria-hidden="true"></span>
            <span class="label">Students</span>
        </a>

        <a href="<?php echo $projectRoot; ?>/views/classes/classManagement.php" class="<?php echo isActive(['classes/', 'classManagement', 'classPage']); ?>">
            <span class="icon icon-classes" aria-hidden="true"></span>
            <span class="label">Classes</span>
        </a>

        <a href="<?php echo $projectRoot; ?>/views/subjects/subjects_list.php" class="<?php echo isActive(['subjects/', 'subject']); ?>">
            <span class="icon icon-subjects" aria-hidden="true"></span>
            <span class="label">Subjects</span>
        </a>

        <div class="sidebar-divider"></div>

        <a href="<?php echo $projectRoot; ?>/views/school/school_years_list.php" class="<?php echo isActive(['school_years', 'school_year', 'years_terms']); ?>">
            <span class="icon icon-years" aria-hidden="true"></span>
            <span class="label">School Years</span>
        </a>

        <a href="<?php echo $projectRoot; ?>/views/school/term_manage.php" class="<?php echo isActive(['term_manage', 'term_create', 'terms.php']); ?>">
            <span class="icon icon-terms" aria-hidden="true"></span>
            <span class="label">Terms</span>
        </a>

    <?php endif; ?>

    <?php if ($role === 'teacher'): ?>
        <div class="sidebar-role-badge">Teacher</div>

        <a href="<?php echo $projectRoot; ?>/views/scores/scores_entry.php" class="<?php echo isActive(['scores_entry', 'score.php']); ?>">
            <span class="icon icon-score" aria-hidden="true"></span>
            <span class="label">Enter Scores</span>
        </a>

        <a href="<?php echo $projectRoot; ?>/views/scores/scores_list.php" class="<?php echo isActive('scores_list'); ?>">
            <span class="icon icon-score" aria-hidden="true"></span>
            <span class="label">View Scores</span>
        </a>
    <?php endif; ?>

    <div class="sidebar-divider"></div>

    <a href="<?php echo $projectRoot; ?>/views/reports/report_student_card.php" class="<?php echo isActive(['reports/', 'report_']); ?>">
        <span class="icon icon-reports" aria-hidden="true"></span>
        <span class="label">Reports</span>
    </a>

    <?php if (!empty($_SESSION['username'])): ?>
        <a href="<?php echo $projectRoot; ?>/views/auth/logout.php" class="logout-link">
            <span class="icon icon-logout" aria-hidden="true"></span>
            <span class="label">Logout</span>
        </a>
    <?php endif; ?>
</div>