<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_role(['office_admin']);
$year_id = isset($_GET['year_id'])? (int)$_GET['year_id'] : 0;
if ($year_id <= 0) { echo '<p>Select a valid school year to manage terms.</p>'; require __DIR__ . '/../../includes/footer.php'; exit; }
// TODO: fetch terms for the year via PDO
$terms = []; // e.g. SELECT * FROM terms WHERE year_id = ?
?>
<h2>Manage Terms for Year <?php echo htmlspecialchars($year_id,ENT_QUOTES,'UTF-8'); ?></h2>
<p><a href="term_create.php">Create Term</a></p>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>ID</th><th>Label</th><th>Actions</th></tr>
    <?php foreach ($terms as $t): ?>
        <tr>
            <td><?php echo htmlspecialchars($t['id'] ?? '',ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($t['label'] ?? '',ENT_QUOTES,'UTF-8'); ?></td>
            <td><!-- actions: edit/delete if needed --></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php require __DIR__ . '/../../includes/footer.php'; ?>