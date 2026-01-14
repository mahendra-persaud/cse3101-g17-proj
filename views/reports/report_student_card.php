<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">
<style>
    @media print {
        .no-print { display: none !important; }
        .Main-Container { display: block !important; }
        .sidebar { display: none !important; }
        .header { display: none !important; }
        .main-dashboard { padding: 20px !important; }
        body { background: white !important; color: black !important; }
        .report-card { background: white !important; color: black !important; border: 2px solid #000 !important; }
        table { color: black !important; }
        th { background: #f0f0f0 !important; color: black !important; }
    }
    .grade-a { color: #4ade80; font-weight: 600; }
    .grade-b { color: #60a5fa; font-weight: 600; }
    .grade-c { color: #fbbf24; font-weight: 600; }
    .grade-d { color: #fb923c; font-weight: 600; }
    .grade-f { color: #f87171; font-weight: 600; }
</style>';
require_once $headerPath;
require_role(['office_admin', 'teacher']);

// fake student data
$students = [
    1 => ['id' => 1, 'name' => 'Olivia Parker', 'grade' => '4', 'class' => 'Class A', 'reg_number' => 'ST2023001'],
    2 => ['id' => 2, 'name' => 'Liam Johnson', 'grade' => '5', 'class' => 'Class B', 'reg_number' => 'ST2023002'],
    3 => ['id' => 3, 'name' => 'Ava Williams', 'grade' => '6', 'class' => 'Class A', 'reg_number' => 'ST2023003'],
    4 => ['id' => 4, 'name' => 'Noah Brown', 'grade' => '3', 'class' => 'Class C', 'reg_number' => 'ST2023004'],
    5 => ['id' => 5, 'name' => 'Emma Davis', 'grade' => '4', 'class' => 'Class B', 'reg_number' => 'ST2023005'],
];

// fake scores data (student_id => [subject => [term => score]])
$sample_scores = [
    1 => [ // Olivia Parker - Grade 4
        'Mathematics' => [1 => 92, 2 => 88, 3 => 95],
        'English Language' => [1 => 89, 2 => 91, 3 => 90],
        'Science' => [1 => 85, 2 => 87, 3 => 89],
        'Social Studies' => [1 => 91, 2 => 89, 3 => 93],
        'HFLE' => [1 => 88, 2 => 90, 3 => 92],
        'Physical Education' => [1 => 95, 2 => 94, 3 => 96],
        'Music' => [1 => 87, 2 => 85, 3 => 88],
        'Arts and Crafts' => [1 => 90, 2 => 92, 3 => 91],
        'Spanish' => [1 => 84, 2 => 86, 3 => 88],
        'Information Technology' => [1 => 93, 2 => 95, 3 => 94],
    ],
    2 => [ // Liam Johnson - Grade 5
        'Mathematics' => [1 => 78, 2 => 82, 3 => 85],
        'English Language' => [1 => 88, 2 => 86, 3 => 89],
        'Science' => [1 => 81, 2 => 83, 3 => 82],
        'Social Studies' => [1 => 85, 2 => 87, 3 => 86],
        'HFLE' => [1 => 90, 2 => 88, 3 => 91],
        'Physical Education' => [1 => 92, 2 => 93, 3 => 94],
        'Music' => [1 => 79, 2 => 80, 3 => 82],
        'Arts and Crafts' => [1 => 84, 2 => 85, 3 => 86],
        'Spanish' => [1 => 76, 2 => 78, 3 => 80],
        'Information Technology' => [1 => 89, 2 => 91, 3 => 90],
    ],
];

// get url params
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$term = isset($_GET['term']) ? (int)$_GET['term'] : 1;

// check if student exists
if ($student_id > 0 && isset($students[$student_id])) {
    $student = $students[$student_id];
    $scores = isset($sample_scores[$student_id]) ? $sample_scores[$student_id] : [];
} else {
    $student = null;
    $scores = [];
}

// figure out the letter grade
function getGrade($score) {
    if ($score >= 90) return 'A';
    if ($score >= 80) return 'B';
    if ($score >= 70) return 'C';
    if ($score >= 60) return 'D';
    return 'F';
}

// css class for colors
function getGradeClass($grade) {
    return 'grade-' . strtolower($grade);
}

// do the math for averages
function calculateStats($scores, $term) {
    $term_scores = [];
    foreach ($scores as $subject => $terms) {
        if (isset($terms[$term])) {
            $term_scores[] = $terms[$term];
        }
    }

    if (empty($term_scores)) {
        return ['avg' => 0, 'total' => 0, 'count' => 0, 'highest' => 0, 'lowest' => 0];
    }

    return [
        'avg' => round(array_sum($term_scores) / count($term_scores), 2),
        'total' => array_sum($term_scores),
        'count' => count($term_scores),
        'highest' => max($term_scores),
        'lowest' => min($term_scores),
    ];
}
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

    <main class="main-dashboard">
        <div class="header no-print">
            <h2>Student Report Card</h2></div>

        <?php if (!$student): ?>
        <!-- form to pick student -->
        <h1 class="no-print">Generate Student Report Card</h1>

        <section class="form-card no-print">
            <form method="GET" action="report_student_card.php">
                <label style="color: #fff; font-weight: 600;">Select Student</label>
                <select name="student_id" required style="grid-column: span 2;">
                    <option value="">-- Choose a student --</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo e($s['name']); ?> (Grade <?php echo e($s['grade']); ?> - <?php echo e($s['class']); ?>)</option>
                    <?php endforeach; ?>
                </select>

                <label style="color: #fff; font-weight: 600;">Select Term</label>
                <select name="term" required style="grid-column: span 2;">
                    <option value="1">Term 1 (Sept - Dec)</option>
                    <option value="2">Term 2 (Jan - Apr)</option>
                    <option value="3">Term 3 (May - Aug)</option>
                </select>

                <button type="submit" style="grid-column: span 2;">Generate Report Card</button>
            </form>
        </section>

        <?php else:
            $stats = calculateStats($scores, $term);
        ?>

        <!-- showing the actual card -->
        <div class="no-print" style="margin: 30px; display: flex; gap: 15px;">
            <button onclick="window.print()" style="background: rgba(35, 140, 246, 0.8); color: white; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; font-weight: 500;">🖨️ Print Report Card</button>
            <button onclick="exportPDF()" style="background: rgba(220, 38, 38, 0.8); color: white; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; font-weight: 500;">📄 Export as PDF</button>
            <a href="report_student_card.php" style="background: rgba(107, 114, 128, 0.8); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500;">← Back to Selection</a>
        </div>

        <section class="table-card report-card" style="margin: 30px; max-width: 900px;">
            <!-- school name header -->
            <div style="text-align: center; border-bottom: 2px solid rgba(255, 255, 255, 0.2); padding-bottom: 20px; margin-bottom: 30px;">
                <h1 style="margin: 0 0 5px; font-size: 32px; color: #a5d8ff;">St. Joseph Primary School</h1>
                <p style="margin: 5px 0; color: rgba(255, 255, 255, 0.8); font-size: 14px;">123 Main Street, Georgetown, Guyana</p>
                <h2 style="margin: 15px 0 0; font-size: 24px; color: #fff;">Student Report Card</h2>
                <p style="margin: 5px 0; color: rgba(255, 255, 255, 0.8);">Academic Year 2025-2026 | Term <?php echo $term; ?></p>
            </div>

            <!-- student details -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px;">
                <div>
                    <p style="margin: 5px 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">STUDENT NAME</p>
                    <p style="margin: 0; font-size: 18px; font-weight: 600; color: #fff;"><?php echo e($student['name']); ?></p>
                </div>
                <div>
                    <p style="margin: 5px 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">REGISTRATION NUMBER</p>
                    <p style="margin: 0; font-size: 18px; font-weight: 600; color: #fff;"><?php echo e($student['reg_number']); ?></p>
                </div>
                <div>
                    <p style="margin: 5px 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">GRADE</p>
                    <p style="margin: 0; font-size: 18px; font-weight: 600; color: #fff;">Grade <?php echo e($student['grade']); ?></p>
                </div>
                <div>
                    <p style="margin: 5px 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">CLASS</p>
                    <p style="margin: 0; font-size: 18px; font-weight: 600; color: #fff;"><?php echo e($student['class']); ?></p>
                </div>
            </div>

            <!-- table of grades -->
            <table style="margin-bottom: 30px;">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th style="text-align: center;">Score</th>
                        <th style="text-align: center;">Grade</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($scores)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 30px; color: #999;">No scores recorded for this term.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($scores as $subject => $terms): ?>
                            <?php if (isset($terms[$term])):
                                $score = $terms[$term];
                                $grade = getGrade($score);
                                $gradeClass = getGradeClass($grade);

                                // Determine remarks
                                if ($score >= 90) $remark = 'Excellent performance';
                                elseif ($score >= 80) $remark = 'Very good work';
                                elseif ($score >= 70) $remark = 'Good effort';
                                elseif ($score >= 60) $remark = 'Satisfactory';
                                else $remark = 'Needs improvement';
                            ?>
                            <tr>
                                <td><?php echo e($subject); ?></td>
                                <td style="text-align: center; font-size: 16px; font-weight: 600;"><?php echo $score; ?></td>
                                <td style="text-align: center;" class="<?php echo $gradeClass; ?>"><?php echo $grade; ?></td>
                                <td><?php echo $remark; ?></td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- summary box -->
            <?php if ($stats['count'] > 0): ?>
            <div style="background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                <h3 style="margin: 0 0 15px; color: #a5d8ff; font-size: 18px;">Performance Summary</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                    <div>
                        <p style="margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">AVERAGE</p>
                        <p style="margin: 5px 0 0; font-size: 24px; font-weight: 600; color: #4ade80;"><?php echo $stats['avg']; ?>%</p>
                    </div>
                    <div>
                        <p style="margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">TOTAL SUBJECTS</p>
                        <p style="margin: 5px 0 0; font-size: 24px; font-weight: 600; color: #fff;"><?php echo $stats['count']; ?></p>
                    </div>
                    <div>
                        <p style="margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">HIGHEST SCORE</p>
                        <p style="margin: 5px 0 0; font-size: 24px; font-weight: 600; color: #4ade80;"><?php echo $stats['highest']; ?>%</p>
                    </div>
                    <div>
                        <p style="margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">LOWEST SCORE</p>
                        <p style="margin: 5px 0 0; font-size: 24px; font-weight: 600; color: #fbbf24;"><?php echo $stats['lowest']; ?>%</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- teacher says... -->
            <div style="background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                <h3 style="margin: 0 0 10px; color: #a5d8ff; font-size: 18px;">Class Teacher's Remarks</h3>
                <p style="margin: 0; color: rgba(255, 255, 255, 0.9); line-height: 1.6;">
                    <?php
                    if ($stats['avg'] >= 85) {
                        echo e($student['name']) . " has demonstrated exceptional academic performance this term. Consistent effort and excellent understanding across all subjects. Keep up the outstanding work!";
                    } elseif ($stats['avg'] >= 75) {
                        echo e($student['name']) . " has shown good academic progress this term. Continue to work hard and focus on areas that need improvement. Well done overall!";
                    } else {
                        echo e($student['name']) . " is making satisfactory progress. Additional support and practice in weaker subjects would be beneficial. Keep working hard!";
                    }
                    ?>
                </p>
            </div>

            <!-- sign here -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 30px; margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.2);">
                <div style="text-align: center;">
                    <div style="border-bottom: 1px solid rgba(255, 255, 255, 0.3); margin-bottom: 8px; height: 40px;"></div>
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 12px;">Class Teacher</p>
                </div>
                <div style="text-align: center;">
                    <div style="border-bottom: 1px solid rgba(255, 255, 255, 0.3); margin-bottom: 8px; height: 40px;"></div>
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 12px;">Head Teacher</p>
                </div>
                <div style="text-align: center;">
                    <div style="border-bottom: 1px solid rgba(255, 255, 255, 0.3); margin-bottom: 8px; height: 40px;"></div>
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 12px;">Parent/Guardian</p>
                </div>
            </div>

            <p style="text-align: center; margin-top: 30px; color: rgba(255, 255, 255, 0.6); font-size: 12px;">Generated on <?php echo date('F j, Y'); ?></p>
        </section>

        <?php endif; ?>

    </main>
</div>

<script>
function exportPDF() {
    alert('PDF export functionality would integrate with a library like jsPDF or server-side PDF generation (e.g., TCPDF, FPDF). For now, please use the Print function and save as PDF.');
}
</script>

</body>
</html>
