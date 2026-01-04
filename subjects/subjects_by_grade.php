<?php
require_once __DIR__ . '/../header.php';
require_role(['office_admin']);
$grade = isset($_GET['grade'])? (int)$_GET['grade'] : 0;
if ($grade < 1 || $grade > 6) { echo '<p>Invalid grade selected.</p>'; require 'footer.php'; exit; }
// TODO: Fetch subjects for the given grade via PDO prepared statements
$subjects = []; // e.g. $stmt = $pdo->prepare('SELECT id,name FROM subjects WHERE grade=?'); $stmt->execute([$grade]); $subjects = $stmt->fetchAll();
?>
<h2>Subjects for Grade <?php echo htmlspecialchars($grade,ENT_QUOTES,'UTF-8'); ?></h2>
<ul>
    <?php foreach ($subjects as $s): ?>
        <li><?php echo htmlspecialchars($s['name'] ?? '', ENT_QUOTES,'UTF-8'); ?></li>
    <?php endforeach; ?>
</ul>
<?php require __DIR__ . '/../footer.php'; ?>