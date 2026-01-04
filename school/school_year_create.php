<?php
require_once __DIR__ . '/../header.php';
require_role(['office_admin']);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $label = trim($_POST['label'] ?? '');
    if ($label === '') $errors[] = 'Label required.';
    if (empty($errors)){
        // TODO: insert school year into DB using PDO
        header('Location: school_years_list.php'); exit;
    }
}
?>
<h2>Create School Year</h2>
<?php if ($errors): ?><div style="color:red"><?php echo implode('<br>', array_map('htmlspecialchars',$errors)); ?></div><?php endif; ?>
<form method="post">
    <label>Label (e.g., 2025/2026): <input name="label" value="<?php echo htmlspecialchars($_POST['label'] ?? '',ENT_QUOTES,'UTF-8'); ?>"></label><br>
    <button type="submit">Create</button>
</form>
<?php require __DIR__ . '/../footer.php'; ?>