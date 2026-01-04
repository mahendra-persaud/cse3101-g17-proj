<?php
require_once __DIR__ . '/../header.php';
require_role(['teacher']);
// Minimal scores list view: filter by class/student/term
$filters = [
    'class_id' => $_GET['class_id'] ?? null,
    'student_id' => $_GET['student_id'] ?? null,
    'term' => $_GET['term'] ?? null,
];
// TODO: Build query with prepared statements and fetch scores from DB
$scores = [];
?>
<h2>Scores</h2>
<form method="get">
    <label>Class: <input name="class_id" value="<?php echo htmlspecialchars($filters['class_id'] ?? '',ENT_QUOTES,'UTF-8'); ?>"></label>
    <label>Student: <input name="student_id" value="<?php echo htmlspecialchars($filters['student_id'] ?? '',ENT_QUOTES,'UTF-8'); ?>"></label>
    <label>Term: <input name="term" value="<?php echo htmlspecialchars($filters['term'] ?? '',ENT_QUOTES,'UTF-8'); ?>"></label>
    <button type="submit">Filter</button>
</form>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>Student</th><th>Subject</th><th>Term</th><th>Score</th></tr>
    <?php foreach ($scores as $r): ?>
        <tr>
            <td><?php echo htmlspecialchars($r['student_name'] ?? '',ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['subject_name'] ?? '',ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['term'] ?? '',ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['score'] ?? '',ENT_QUOTES,'UTF-8'); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php require __DIR__ . '/../footer.php'; ?>