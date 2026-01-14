<?php
require_once __DIR__ . '/config/database.php';

echo "<h2>Generating Students for All Classes</h2>";

$pdo = getDBConnection();
if (!$pdo) {
    die("<p style='color: red;'>❌ Cannot connect to database!</p>");
}

// Guyanese-inspired first names
$firstNames = [
    'James', 'Sophia', 'Oliver', 'Emma', 'Liam', 'Ava', 'Noah', 'Isabella',
    'Ethan', 'Mia', 'Lucas', 'Charlotte', 'Mason', 'Amelia', 'Logan', 'Harper',
    'Alexander', 'Evelyn', 'Elijah', 'Abigail', 'Daniel', 'Emily', 'Matthew', 'Elizabeth',
    'Aiden', 'Sofia', 'Jackson', 'Avery', 'David', 'Ella', 'Joseph', 'Scarlett',
    'Samuel', 'Grace', 'Henry', 'Chloe', 'Owen', 'Victoria', 'Sebastian', 'Riley',
    'Gabriel', 'Aria', 'Carter', 'Lily', 'Jayden', 'Aubrey', 'John', 'Zoey',
    'Luke', 'Penelope', 'Anthony', 'Lillian', 'Isaac', 'Addison', 'Dylan', 'Layla',
    'Ryan', 'Natalie', 'Nathan', 'Camila', 'Zachary', 'Hannah', 'Christopher', 'Brooklyn',
    'Thomas', 'Zoe', 'Julian', 'Nora', 'Aaron', 'Leah', 'Charles', 'Savannah'
];

// Guyanese surnames
$lastNames = [
    'Singh', 'Khan', 'Mohammed', 'Ali', 'Persaud', 'Ramnarine', 'Ramjattan', 'Bissessar',
    'Williams', 'Brown', 'Davis', 'Miller', 'Wilson', 'Moore', 'Taylor', 'Anderson',
    'Thomas', 'Jackson', 'White', 'Harris', 'Martin', 'Thompson', 'Garcia', 'Martinez',
    'Robinson', 'Clark', 'Rodriguez', 'Lewis', 'Lee', 'Walker', 'Hall', 'Allen',
    'Young', 'King', 'Wright', 'Scott', 'Torres', 'Nguyen', 'Hill', 'Flores',
    'Green', 'Adams', 'Nelson', 'Baker', 'Ramirez', 'Campbell', 'Mitchell', 'Roberts',
    'Carter', 'Phillips', 'Evans', 'Turner', 'Diaz', 'Parker', 'Cruz', 'Edwards',
    'Collins', 'Reyes', 'Stewart', 'Morris', 'Morales', 'Murphy', 'Cook', 'Rogers'
];

try {
    // Get all classes
    $stmt = $pdo->query("
        SELECT c.class_id, c.class_name, g.grade_name, g.grade_id
        FROM classes c
        INNER JOIN grades g ON c.grade_id = g.grade_id
        ORDER BY g.grade_id, c.class_name
    ");
    $classes = $stmt->fetchAll();

    if (empty($classes)) {
        die("<p style='color: red;'>❌ No classes found! Run generate_classes.php first.</p>");
    }

    echo "<h3>Found " . count($classes) . " classes</h3>";

    // Get current school year (or create one if none exists)
    $stmt = $pdo->query("SELECT school_year_id FROM school_years ORDER BY school_year_id DESC LIMIT 1");
    $schoolYear = $stmt->fetch();

    if (!$schoolYear) {
        // Create a school year if none exists
        $pdo->exec("INSERT INTO school_years (year_name) VALUES ('2024-2025')");
        $schoolYearId = $pdo->lastInsertId();
        echo "<p>✅ Created school year 2024-2025</p>";
    } else {
        $schoolYearId = $schoolYear['school_year_id'];
    }

    $studentsPerClass = 15; // Number of students per class
    $totalStudentsCreated = 0;
    $studentsSkipped = 0;

    echo "<h3>Creating Students:</h3>";
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse; margin: 20px 0; width: 100%;'>";
    echo "<tr style='background: #0891b2; color: white;'><th>Class</th><th>Student Name</th><th>Status</th></tr>";

    foreach ($classes as $class) {
        $gradeNum = str_replace('Grade ', '', $class['grade_name']);
        $fullClassName = "Grade {$gradeNum}{$class['class_name']}";

        echo "<tr style='background: #e0f2fe;'><td colspan='3' style='font-weight: bold; padding: 12px;'>📚 {$fullClassName}</td></tr>";

        for ($i = 0; $i < $studentsPerClass; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $fullName = "{$firstName} {$lastName}";

            try {
                // Insert student
                $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, class_id) VALUES (?, ?, ?)");
                $stmt->execute([$firstName, $lastName, $class['class_id']]);
                $studentId = $pdo->lastInsertId();

                // Create enrollment for current school year
                $stmt = $pdo->prepare("INSERT INTO student_enrollments (student_id, class_id, school_year_id) VALUES (?, ?, ?)");
                $stmt->execute([$studentId, $class['class_id'], $schoolYearId]);

                echo "<tr>";
                echo "<td>{$fullClassName}</td>";
                echo "<td>{$fullName}</td>";
                echo "<td style='color: green;'>✅ Created</td>";
                echo "</tr>";

                $totalStudentsCreated++;
            } catch (PDOException $e) {
                echo "<tr>";
                echo "<td>{$fullClassName}</td>";
                echo "<td>{$fullName}</td>";
                echo "<td style='color: red;'>❌ Error: " . $e->getMessage() . "</td>";
                echo "</tr>";
                $studentsSkipped++;
            }
        }
    }

    echo "</table>";

    // Summary
    echo "<div style='background: #d1fae5; border: 1px solid #10b981; color: #065f46; padding: 16px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3 style='margin-top: 0;'>✅ Summary:</h3>";
    echo "<ul style='margin: 0;'>";
    echo "<li><strong>$totalStudentsCreated</strong> students created</li>";
    echo "<li><strong>" . count($classes) . "</strong> classes populated</li>";
    echo "<li><strong>$studentsPerClass</strong> students per class</li>";
    echo "<li><strong>Expected total:</strong> " . (count($classes) * $studentsPerClass) . " students</li>";
    echo "</ul>";
    echo "</div>";

    // Show breakdown by grade
    $stmt = $pdo->query("
        SELECT g.grade_name, COUNT(s.student_id) as student_count
        FROM grades g
        LEFT JOIN classes c ON g.grade_id = c.grade_id
        LEFT JOIN students s ON c.class_id = s.class_id
        GROUP BY g.grade_id, g.grade_name
        ORDER BY g.grade_id
    ");
    $gradeStats = $stmt->fetchAll();

    echo "<h3>Students per Grade:</h3>";
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
    echo "<tr style='background: #0891b2; color: white;'><th>Grade</th><th>Total Students</th></tr>";

    foreach ($gradeStats as $stat) {
        echo "<tr>";
        echo "<td>{$stat['grade_name']}</td>";
        echo "<td style='text-align: center; font-weight: bold;'>{$stat['student_count']}</td>";
        echo "</tr>";
    }

    echo "</table>";

    // Show breakdown by class
    $stmt = $pdo->query("
        SELECT
            c.class_id,
            g.grade_name,
            c.class_name,
            COUNT(s.student_id) as student_count
        FROM classes c
        INNER JOIN grades g ON c.grade_id = g.grade_id
        LEFT JOIN students s ON c.class_id = s.class_id
        GROUP BY c.class_id, g.grade_name, c.class_name
        ORDER BY g.grade_id, c.class_name
    ");
    $classStats = $stmt->fetchAll();

    echo "<h3>Students per Class:</h3>";
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
    echo "<tr style='background: #0891b2; color: white;'><th>Class ID</th><th>Class Name</th><th>Students</th></tr>";

    foreach ($classStats as $stat) {
        $gradeNum = str_replace('Grade ', '', $stat['grade_name']);
        $fullClassName = "Grade {$gradeNum}{$stat['class_name']}";
        echo "<tr>";
        echo "<td>{$stat['class_id']}</td>";
        echo "<td>{$fullClassName}</td>";
        echo "<td style='text-align: center; font-weight: bold;'>{$stat['student_count']}</td>";
        echo "</tr>";
    }

    echo "</table>";

    echo "<p style='margin-top: 30px;'><a href='views/auth/loginPage.php' style='display: inline-block; background: #0891b2; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;'>Continue to Login</a></p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
