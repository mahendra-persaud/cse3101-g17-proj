<?php
require_once __DIR__ . '/../header.php';
require_role(['office_admin']);
// List school years
$years = []; // TODO: fetch from DB
?>
<h2>School Years</h2>
<p><a href="school_year_create.php">Create School Year</a></p>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>ID</th><th>Year</th><th>Actions</th></tr>
    <?php foreach ($years as $y): ?>
        <tr>
            <td><?php echo htmlspecialchars($y['id'] ?? '', ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($y['label'] ?? '', ENT_QUOTES,'UTF-8'); ?></td>
            <td><a href="term_manage.php?year_id=<?php echo urlencode($y['id'] ?? ''); ?>">Manage Terms</a></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php require __DIR__ . '/../footer.php'; ?>