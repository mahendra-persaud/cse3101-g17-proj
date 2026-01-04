<?php
require_once __DIR__ . '/../header.php';
require_role(['office_admin']);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $year_id = (int)($_POST['year_id'] ?? 0);
    $term_label = trim($_POST['term_label'] ?? '');
    if ($year_id <= 0) $errors[] = 'Year required.';
    if ($term_label === '') $errors[] = 'Term label required.';
    if (empty($errors)){
        // TODO: insert term into DB using PDO (associate with school year)
        header('Location: term_manage.php?year_id=' . urlencode($year_id)); exit;
    }
}
// TODO: load school years
$years = [];
?>
<h2>Create Term</h2>
<?php if ($errors): ?><div style="color:red"><?php echo implode('<br>', array_map('htmlspecialchars',$errors)); ?></div><?php endif; ?>
<form method="post">
    <label>School Year: 
        <select name="year_id">
            <?php foreach ($years as $y): ?>
                <option value="<?php echo htmlspecialchars($y['id'] ?? '',ENT_QUOTES,'UTF-8'); ?>"><?php echo htmlspecialchars($y['label'] ?? '',ENT_QUOTES,'UTF-8'); ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>
    <label>Term Label (e.g., Term 1): <input name="term_label" value="<?php echo htmlspecialchars($_POST['term_label'] ?? '',ENT_QUOTES,'UTF-8'); ?>"></label><br>
    <button type="submit">Create Term</button>
</form>
<?php require __DIR__ . '/../footer.php'; ?>