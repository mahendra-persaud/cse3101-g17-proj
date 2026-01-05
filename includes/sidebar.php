<?php
// Minimal sidebar moved to includes/
$role = $_SESSION['role'] ?? 'guest';
?>
<div class="sidebar">
    <?php if ($role === 'office_admin'): ?>
        <a href="subjects_list.php">Subjects</a>
        <a href="school_years_list.php">School Years</a>
        <a href="term_manage.php">Terms</a>
    <?php endif; ?>
</div>