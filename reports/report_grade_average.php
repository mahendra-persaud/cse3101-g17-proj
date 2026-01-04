<?php
require_once __DIR__ . '/../header.php';
// Minimal average performance by grade and subject
$grade = isset($_GET['grade'])? (int)$_GET['grade'] : 0;
$subject_id = isset($_GET['subject_id'])? (int)$_GET['subject_id'] : 0;
$term = isset($_GET['term'])? (int)$_GET['term'] : 0;
if ($grade < 1 || $grade > 6) { echo '<p>Select a valid grade.</p>'; require 'footer.php'; exit; }
// TODO: Calculate averages using DB queries (AVG over scores per grade/subject/term)
$averages = []; // Example: SELECT class_id, AVG(score) as avg_score FROM ... GROUP BY class_id
?>
<h2>Average Performance — Grade <?php echo htmlspecialchars($grade,ENT_QUOTES,'UTF-8'); ?></h2>
<?php if ($subject_id): ?><p>Subject ID: <?php echo htmlspecialchars($subject_id,ENT_QUOTES,'UTF-8'); ?></p><?php endif; ?>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>Class</th><th>Average Score</th></tr>
    <?php foreach ($averages as $a): ?>
        <tr>
            <td><?php echo htmlspecialchars($a['class_name'] ?? '',ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars(round($a['avg_score'] ?? 0,2),ENT_QUOTES,'UTF-8'); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php require __DIR__ . '/../footer.php'; ?>