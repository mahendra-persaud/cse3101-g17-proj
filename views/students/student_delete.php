<?php
// Delete student
require_once __DIR__ . '/../../config/database.php';

$studentId = $_GET['id'] ?? '';

if (empty($studentId)) {
    header('Location: studentManagement.php?error=Invalid+student+ID');
    exit;
}

$pdo = getDBConnection();

try {
    // Check if student exists
    $stmt = $pdo->prepare("SELECT student_id FROM students WHERE student_id = ?");
    $stmt->execute([$studentId]);

    if (!$stmt->fetch()) {
        header('Location: studentManagement.php?error=Student+not+found');
        exit;
    }

    // Delete student (will cascade to scores and enrollments)
    $stmt = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->execute([$studentId]);

    header('Location: studentManagement.php?success=Student+deleted+successfully');
    exit;

} catch (PDOException $e) {
    header('Location: studentManagement.php?error=' . urlencode('Error deleting student: ' . $e->getMessage()));
    exit;
}
