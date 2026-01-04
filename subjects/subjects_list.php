<?php
require_once __DIR__ . '/../header.php';
require_role(['office_admin']);
// Minimal subjects list view
// NOTE: Replace the following DB placeholder with your actual DB connection (PDO) and prepared statements.
$subjects = []; // e.g. $pdo->query('SELECT id, name, grade FROM subjects')->fetchAll();
?><h2>Subjects</h2>
<p><a href="subject_create.php">Create new subject</a></p>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>ID</th><th>Name</th><th>Grade</th><th>Actions</th></tr>
    <?php foreach ($subjects as $s): ?>
        <tr>
            <td><?php echo htmlspecialchars($s['id'] ?? '', ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($s['name'] ?? '', ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($s['grade'] ?? '', ENT_QUOTES,'UTF-8'); ?></td>
            <td>
                <a href="subject_edit.php?id=<?php echo urlencode($s['id'] ?? ''); ?>">Edit</a>
                <!-- Delete should be POST with CSRF token in real app -->
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php require __DIR__ . '/../footer.php'; ?>