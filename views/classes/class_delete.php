<?php
// Delete class
require_once __DIR__ . '/../../config/database.php';

$classId = $_GET['id'] ?? '';

if (empty($classId)) {
    header('Location: classManagement.php?error=Invalid+class+ID');
    exit;
}

$pdo = getDBConnection();

try {
    // Check if class exists
    $stmt = $pdo->prepare("SELECT class_id FROM classes WHERE class_id = ?");
    $stmt->execute([$classId]);

    if (!$stmt->fetch()) {
        header('Location: classManagement.php?error=Class+not+found');
        exit;
    }

    // Check if there are students in this class
    $stmt = $pdo->prepare("SELECT COUNT(*) as student_count FROM students WHERE class_id = ?");
    $stmt->execute([$classId]);
    $result = $stmt->fetch();

    if ($result['student_count'] > 0) {
        header('Location: classManagement.php?error=Cannot+delete+class+with+enrolled+students.+Please+reassign+students+first.');
        exit;
    }

    // Delete class
    $stmt = $pdo->prepare("DELETE FROM classes WHERE class_id = ?");
    $stmt->execute([$classId]);

    header('Location: classManagement.php?success=Class+deleted+successfully');
    exit;

} catch (PDOException $e) {
    header('Location: classManagement.php?error=' . urlencode('Error deleting class: ' . $e->getMessage()));
    exit;
}
