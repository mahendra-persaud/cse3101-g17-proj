<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function sidebar_links($role){
    $links = [];
    if ($role === 'office_admin'){
        $links = [
            'Subjects' => 'subjects_list.php',
            'Create Subject' => 'subject_create.php',
            'School Years' => 'school_years_list.php',
            'Create Term' => 'term_create.php'
        ];
    } elseif ($role === 'teacher'){
        $links = [
            'Enter Scores' => 'scores_entry.php',
            'View Scores' => 'scores_list.php'
        ];
    }
    $links['Reports'] = 'report_student_card.php';
    return $links;
}
$role = $_SESSION['role'] ?? 'guest';
?>
<aside style="width:220px;float:left;padding:8px;border-right:1px solid #ddd">
    <h4>Menu</h4>
    <ul style="list-style:none;padding-left:0">
        <?php foreach (sidebar_links($role) as $label => $href): ?>
            <li><a href="<?php echo htmlspecialchars($href,ENT_QUOTES,'UTF-8'); ?>"><?php echo htmlspecialchars($label,ENT_QUOTES,'UTF-8'); ?></a></li>
        <?php endforeach; ?>
    </ul>
</aside>
<div style="margin-left:240px;padding:8px"> <!-- main content wrapper -->
