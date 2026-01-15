<?php
// delete a term

require_once __DIR__ . '/../../models/School.php';
require_once __DIR__ . '/../../includes/header.php';
require_role(['office_admin']);

$termId = $_GET['id'] ?? null;

if (!$termId) {
    header('Location: years_terms.php?error=Invalid+term+ID');
    exit;
}

$schoolModel = new School();
$term = $schoolModel->getTerm($termId);

if (!$term) {
    header('Location: years_terms.php?error=Term+not+found');
    exit;
}

// Check if there are scores associated with this term
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM scores WHERE term_id = ?");
$stmt->execute([$termId]);
$result = $stmt->fetch();

if ($result['count'] > 0) {
    header('Location: years_terms.php?error=Cannot+delete+term+with+existing+scores.+Please+delete+scores+first.');
    exit;
}

// Delete the term
$schoolModel->deleteTerm($termId);
header('Location: years_terms.php?success=Term+deleted+successfully');
exit;
