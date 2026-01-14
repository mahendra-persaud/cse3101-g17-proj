<?php
require_once __DIR__ . '/config/database.php';

echo "<h2>Generating Classes for All Grades</h2>";

$pdo = getDBConnection();
if (!$pdo) {
    die("<p style='color: red;'>❌ Cannot connect to database!</p>");
}

try {
    // First, check what grades exist
    $stmt = $pdo->query("SELECT grade_id, grade_name FROM grades ORDER BY grade_id");
    $grades = $stmt->fetchAll();

    echo "<h3>Existing Grades:</h3>";
    echo "<ul>";
    foreach ($grades as $grade) {
        echo "<li>Grade {$grade['grade_id']}: {$grade['grade_name']}</li>";
    }
    echo "</ul>";

    // Define classes to create (A, B, C for each grade)
    $classNames = ['A', 'B', 'C'];
    $classesCreated = 0;
    $classesSkipped = 0;

    echo "<h3>Creating Classes:</h3>";
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr style='background: #0891b2; color: white;'><th>Grade</th><th>Class Name</th><th>Full Name</th><th>Status</th></tr>";

    foreach ($grades as $grade) {
        foreach ($classNames as $className) {
            $gradeNum = str_replace('Grade ', '', $grade['grade_name']);
            $fullName = "Grade {$gradeNum}{$className}";

            try {
                // Try to insert the class
                $stmt = $pdo->prepare("INSERT INTO classes (class_name, grade_id) VALUES (?, ?)");
                $stmt->execute([$className, $grade['grade_id']]);

                echo "<tr>";
                echo "<td>{$grade['grade_name']}</td>";
                echo "<td style='font-weight: bold; text-align: center;'>{$className}</td>";
                echo "<td>{$fullName}</td>";
                echo "<td style='color: green;'>✅ Created</td>";
                echo "</tr>";

                $classesCreated++;
            } catch (PDOException $e) {
                // Class already exists (duplicate key)
                if ($e->getCode() == 23000) {
                    echo "<tr style='background: #f9fafb;'>";
                    echo "<td>{$grade['grade_name']}</td>";
                    echo "<td style='font-weight: bold; text-align: center;'>{$className}</td>";
                    echo "<td>{$fullName}</td>";
                    echo "<td style='color: #6b7280;'>⏭️ Already exists</td>";
                    echo "</tr>";
                    $classesSkipped++;
                } else {
                    throw $e;
                }
            }
        }
    }

    echo "</table>";

    echo "<div style='background: #d1fae5; border: 1px solid #10b981; color: #065f46; padding: 16px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3 style='margin-top: 0;'>✅ Summary:</h3>";
    echo "<ul style='margin: 0;'>";
    echo "<li><strong>$classesCreated</strong> new classes created</li>";
    echo "<li><strong>$classesSkipped</strong> classes already existed</li>";
    echo "<li><strong>" . ($classesCreated + $classesSkipped) . "</strong> total classes (should be 18 for Grades 1-6 × A,B,C)</li>";
    echo "</ul>";
    echo "</div>";

    // Show final list of all classes
    $stmt = $pdo->query("
        SELECT c.class_id, c.class_name, g.grade_name
        FROM classes c
        INNER JOIN grades g ON c.grade_id = g.grade_id
        ORDER BY g.grade_id, c.class_name
    ");
    $allClasses = $stmt->fetchAll();

    echo "<h3>All Classes in Database:</h3>";
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
    echo "<tr style='background: #0891b2; color: white;'><th>Class ID</th><th>Grade</th><th>Class</th><th>Full Name</th></tr>";

    foreach ($allClasses as $class) {
        $gradeNum = str_replace('Grade ', '', $class['grade_name']);
        $fullName = "Grade {$gradeNum}{$class['class_name']}";
        echo "<tr>";
        echo "<td>{$class['class_id']}</td>";
        echo "<td>{$class['grade_name']}</td>";
        echo "<td style='font-weight: bold; text-align: center;'>{$class['class_name']}</td>";
        echo "<td>{$fullName}</td>";
        echo "</tr>";
    }

    echo "</table>";

    echo "<p style='margin-top: 30px;'><a href='views/auth/loginPage.php' style='display: inline-block; background: #0891b2; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;'>Continue to Login</a></p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
