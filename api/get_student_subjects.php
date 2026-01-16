<?php
// api/get_student_subjects.php
// Returns subjects for a student based on their grade

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

$studentId = $_GET['student_id'] ?? null;

if (!$studentId) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing student_id']);
    exit;
}

try {
    $pdo = getDBConnection();

    // Get subjects for the student's grade (via their class)
    $stmt = $pdo->prepare("
        SELECT sub.subject_id, sub.subject_name
        FROM students s
        JOIN classes c ON s.class_id = c.class_id
        JOIN subjects sub ON c.grade_id = sub.grade_id
        WHERE s.student_id = ?
        ORDER BY sub.subject_name
    ");
    $stmt->execute([$studentId]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($subjects);

} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error']);
}
