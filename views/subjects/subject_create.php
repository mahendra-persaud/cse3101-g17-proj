<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_role(['office_admin']);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name'] ?? '');
    $grade = (int)($_POST['grade'] ?? 0);
    if ($name === '') $errors[] = 'Name is required.';
    if ($grade < 1 || $grade > 6) $errors[] = 'Invalid grade.';
    if (empty($errors)){
        // TODO: insert into DB using PDO prepared statements
        // Example: $stmt = $pdo->prepare('INSERT INTO subjects (name, grade) VALUES (?,?)'); $stmt->execute([$name,$grade]);
        header('Location: subjects_list.php'); exit;
    }
}
?>
<h2>Create Subject</h2>
<?php if ($errors): ?>
    <div style="color:red"><?php echo implode('<br>', array_map('htmlspecialchars',$errors)); ?></div>
<?php endif; ?>
<form method="post">
    <label>Name: <input name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES,'UTF-8'); ?>"></label><br>
    <label>Grade: 
        <select name="grade">
            <?php for ($g=1;$g<=6;$g++): ?>
                <option value="<?php echo $g; ?>" <?php echo ((int)($_POST['grade'] ?? 0) === $g)?'selected':''; ?>><?php echo $g; ?></option>
            <?php endfor; ?>
        </select>
    </label><br>
    <button type="submit">Create</button>
</form>
<?php require __DIR__ . '/../../includes/footer.php'; ?>