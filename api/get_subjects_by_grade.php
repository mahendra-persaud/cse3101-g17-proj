<?php
// api/get_subjects_by_grade.php
// Returns all subjects for a specific grade

require_once __DIR__ . '/../config/database.php';

// Start session to check authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$gradeId = $_GET['grade_id'] ?? null;

if (!$gradeId) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing grade_id']);
    exit;
}

try {
    $pdo = getDBConnection();

    // Get all subjects for the grade
    $stmt = $pdo->prepare("
        SELECT subject_id, subject_name
        FROM subjects
        WHERE grade_id = ?
        ORDER BY subject_name
    ");
    $stmt->execute([$gradeId]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($subjects);

} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error']);
}
