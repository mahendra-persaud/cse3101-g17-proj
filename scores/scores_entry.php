<?php
require_once __DIR__ . '/../includes/header.php';
require_role(['teacher']);
// Minimal score entry form. In a real app: validate, sanitize, check CSRF, use PDO prepared statements.
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $student_id = (int)($_POST['student_id'] ?? 0);
    $subject_id = (int)($_POST['subject_id'] ?? 0);
    $term = (int)($_POST['term'] ?? 0);
    $score = (float)($_POST['score'] ?? 0);
    if ($student_id <= 0) $errors[] = 'Student required.';
    if ($subject_id <= 0) $errors[] = 'Subject required.';
    if ($term < 1 || $term > 3) $errors[] = 'Invalid term.';
    if ($score < 0 || $score > 100) $errors[] = 'Score must be between 0 and 100.';
    if (empty($errors)){
        // TODO: INSERT or UPDATE score for student-term-subject using PDO
        // Example: $stmt = $pdo->prepare('REPLACE INTO scores (student_id, subject_id, term_id, score) VALUES (?,?,?,?)');
        header('Location: scores_list.php'); exit;
    }
}
// TODO: Populate students and subjects lists from DB
$students = [];
$subjects = [];
$terms = [1,2,3];
?>
<h2>Enter / Update Score</h2>
<?php if ($errors): ?><div style="color:red"><?php echo implode('<br>', array_map('htmlspecialchars',$errors)); ?></div><?php endif; ?>
<form method="post">
    <label>Student: 
        <select name="student_id">
            <option value="">Select</option>
            <?php foreach ($students as $st): ?>
                <option value="<?php echo htmlspecialchars($st['id'] ?? '',ENT_QUOTES,'UTF-8'); ?>"><?php echo htmlspecialchars($st['name'] ?? '',ENT_QUOTES,'UTF-8'); ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>
    <label>Subject: 
        <select name="subject_id">
            <option value="">Select</option>
            <?php foreach ($subjects as $s): ?>
                <option value="<?php echo htmlspecialchars($s['id'] ?? '',ENT_QUOTES,'UTF-8'); ?>"><?php echo htmlspecialchars($s['name'] ?? '',ENT_QUOTES,'UTF-8'); ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>
    <label>Term:
        <select name="term">
            <?php foreach ($terms as $t): ?><option value="<?php echo $t; ?>"><?php echo $t; ?></option><?php endforeach; ?>
        </select>
    </label><br>
    <label>Score: <input name="score" value="<?php echo htmlspecialchars($_POST['score'] ?? '',ENT_QUOTES,'UTF-8'); ?>"></label><br>
    <button type="submit">Save Score</button>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>