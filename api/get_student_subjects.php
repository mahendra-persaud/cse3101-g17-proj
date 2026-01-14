<?php
// api/get_student_subjects.php
require_once __DIR__ . '/../controllers/ScoreController.php';

$scoreController = new ScoreController();
$studentId = $_GET['student_id'] ?? null;

if ($studentId) {
    header('Content-Type: application/json');
    echo json_encode($scoreController->getSubjectsForStudent($studentId));
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Missing student_id']);
}
?>
