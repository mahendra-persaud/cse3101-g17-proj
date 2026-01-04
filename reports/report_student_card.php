<?php
require_once __DIR__ . '/../header.php';
// Reports accessible to both roles — but may be restricted further in real app
// Minimal student report card by student and term
$student_id = isset($_GET['student_id'])? (int)$_GET['student_id'] : 0;
$term = isset($_GET['term'])? (int)$_GET['term'] : 0;
if ($student_id <= 0 || $term < 1 || $term > 3) { echo '<p>Select a valid student and term.</p>'; require 'footer.php'; exit; }
// TODO: Fetch student info and scores via PDO
$student = null; // e.g. SELECT * FROM students WHERE id=?
$scores = []; // e.g. SELECT subject_name, score FROM scores JOIN subjects ... WHERE student_id=? AND term=?
?>
<h2>Report Card for <?php echo htmlspecialchars($student['name'] ?? 'Unknown',ENT_QUOTES,'UTF-8'); ?> — Term <?php echo htmlspecialchars($term,ENT_QUOTES,'UTF-8'); ?></h2>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>Subject</th><th>Score</th></tr>
    <?php foreach ($scores as $sc): ?>
        <tr>
            <td><?php echo htmlspecialchars($sc['subject_name'] ?? '',ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($sc['score'] ?? '',ENT_QUOTES,'UTF-8'); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php require __DIR__ . '/../footer.php'; ?>