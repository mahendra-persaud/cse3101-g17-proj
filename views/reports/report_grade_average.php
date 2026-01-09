<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
require_once $headerPath;
require_role(['office_admin', 'teacher']);

// Sample grade data
$grade_data = [
    1 => ['name' => 'Grade 1', 'classes' => ['Class A' => ['students' => 26, 'avg' => 87.5], 'Class B' => ['students' => 26, 'avg' => 85.2]], 'subjects' => ['Mathematics' => 86.8, 'English Language' => 88.3, 'Science' => 84.5, 'Social Studies' => 87.1, 'HFLE' => 89.2, 'Physical Education' => 91.5, 'Music' => 85.7, 'Arts and Crafts' => 88.9]],
    2 => ['name' => 'Grade 2', 'classes' => ['Class A' => ['students' => 24, 'avg' => 84.3], 'Class B' => ['students' => 24, 'avg' => 86.1]], 'subjects' => ['Mathematics' => 83.2, 'English Language' => 86.5, 'Science' => 82.7, 'Social Studies' => 85.9, 'HFLE' => 87.4, 'Physical Education' => 90.2, 'Music' => 84.1, 'Arts and Crafts' => 86.8]],
    3 => ['name' => 'Grade 3', 'classes' => ['Class A' => ['students' => 27, 'avg' => 79.5], 'Class B' => ['students' => 28, 'avg' => 78.3]], 'subjects' => ['Mathematics' => 77.8, 'English Language' => 81.2, 'Science' => 76.5, 'Social Studies' => 79.8, 'HFLE' => 82.3, 'Physical Education' => 88.7, 'Music' => 78.4, 'Arts and Crafts' => 80.6]],
    4 => ['name' => 'Grade 4', 'classes' => ['Class A' => ['students' => 25, 'avg' => 83.2], 'Class B' => ['students' => 25, 'avg' => 81.0]], 'subjects' => ['Mathematics' => 81.5, 'English Language' => 84.8, 'Science' => 79.3, 'Social Studies' => 82.7, 'HFLE' => 85.1, 'Physical Education' => 89.9, 'Music' => 80.2, 'Arts and Crafts' => 83.4, 'Spanish' => 78.9, 'Information Technology' => 86.2]],
    5 => ['name' => 'Grade 5', 'classes' => ['Class A' => ['students' => 29, 'avg' => 77.8], 'Class B' => ['students' => 29, 'avg' => 75.8]], 'subjects' => ['Mathematics' => 75.2, 'English Language' => 79.6, 'Science' => 74.1, 'Social Studies' => 77.5, 'HFLE' => 80.8, 'Physical Education' => 87.3, 'Music' => 76.7, 'Arts and Crafts' => 78.9, 'Spanish' => 73.4, 'Information Technology' => 82.1]],
    6 => ['name' => 'Grade 6', 'classes' => ['Class A' => ['students' => 28, 'avg' => 81.7], 'Class B' => ['students' => 29, 'avg' => 79.3]], 'subjects' => ['Mathematics' => 79.8, 'English Language' => 83.1, 'Science' => 77.4, 'Social Studies' => 80.6, 'HFLE' => 84.2, 'Physical Education' => 88.1, 'Music' => 78.5, 'Arts and Crafts' => 81.3, 'Spanish' => 76.8, 'Information Technology' => 85.7]],
];

$selected_grade = isset($_GET['grade']) ? (int)$_GET['grade'] : 0;
$term = isset($_GET['term']) ? (int)$_GET['term'] : 1;
$show_report = ($selected_grade > 0 && $selected_grade <= 6);
$grade_info = $show_report ? $grade_data[$selected_grade] : null;

function calculateOverallAverage($classes) {
    $total = 0; $sum = 0;
    foreach ($classes as $c) { $total += $c['students']; $sum += $c['students'] * $c['avg']; }
    return $total > 0 ? round($sum / $total, 2) : 0;
}

function getPerformanceRating($avg) {
    if ($avg >= 85) return ['label' => 'Excellent', 'color' => '#4ade80'];
    if ($avg >= 75) return ['label' => 'Good', 'color' => '#60a5fa'];
    if ($avg >= 65) return ['label' => 'Satisfactory', 'color' => '#fbbf24'];
    return ['label' => 'Needs Improvement', 'color' => '#f87171'];
}
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>
    <main class="main-dashboard">
        <div class="header">
            <h2>Grade Average Report</h2></div>

        <?php if (!$show_report): ?>
        <h1>Generate Grade Average Report</h1>
        <section class="form-card">
            <form method="GET">
                <label style="color: #fff; font-weight: 600;">Select Grade</label>
                <select name="grade" required style="grid-column: span 2;">
                    <option value="">-- Choose a grade --</option>
                    <?php for($i=1; $i<=6; $i++): ?><option value="<?php echo $i; ?>">Grade <?php echo $i; ?></option><?php endfor; ?>
                </select>
                <label style="color: #fff; font-weight: 600;">Select Term</label>
                <select name="term" required style="grid-column: span 2;">
                    <option value="1">Term 1 (Sept - Dec)</option>
                    <option value="2">Term 2 (Jan - Apr)</option>
                    <option value="3">Term 3 (May - Aug)</option>
                </select>
                <button type="submit" style="grid-column: span 2;">Generate Report</button>
            </form>
        </section>

        <?php else: $overall_avg = calculateOverallAverage($grade_info['classes']); $rating = getPerformanceRating($overall_avg); ?>
        <div style="margin: 30px; display: flex; gap: 15px;">
            <button onclick="window.print()" style="background: rgba(35, 140, 246, 0.8); color: white; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; font-weight: 500;">🖨️ Print Report</button>
            <a href="report_grade_average.php" style="background: rgba(107, 114, 128, 0.8); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500;">← Back</a>
        </div>

        <section class="table-card" style="margin: 30px;">
            <div style="text-align: center; border-bottom: 2px solid rgba(255, 255, 255, 0.2); padding-bottom: 20px; margin-bottom: 30px;">
                <h1 style="margin: 0 0 5px; font-size: 32px; color: #a5d8ff;">Grade Average Performance Report</h1>
                <h2 style="margin: 10px 0 0; font-size: 24px; color: #fff;"><?php echo e($grade_info['name']); ?></h2>
                <p style="margin: 5px 0; color: rgba(255, 255, 255, 0.8);">Academic Year 2025-2026 | Term <?php echo $term; ?></p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <div style="background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px; text-align: center;">
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">OVERALL AVERAGE</p>
                    <p style="margin: 5px 0 0; font-size: 32px; font-weight: 700; color: <?php echo $rating['color']; ?>;"><?php echo $overall_avg; ?>%</p>
                    <p style="margin: 5px 0 0; color: rgba(255, 255, 255, 0.8); font-size: 14px;"><?php echo $rating['label']; ?></p>
                </div>
                <div style="background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px; text-align: center;">
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">TOTAL STUDENTS</p>
                    <p style="margin: 5px 0 0; font-size: 32px; font-weight: 700; color: #fff;"><?php echo array_sum(array_column($grade_info['classes'], 'students')); ?></p>
                </div>
                <div style="background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px; text-align: center;">
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">NUMBER OF CLASSES</p>
                    <p style="margin: 5px 0 0; font-size: 32px; font-weight: 700; color: #fff;"><?php echo count($grade_info['classes']); ?></p>
                </div>
            </div>

            <h3 style="margin: 30px 0 20px; color: #a5d8ff; font-size: 20px;">Performance by Class</h3>
            <table style="margin-bottom: 40px;">
                <thead><tr><th>Class</th><th style="text-align: center;">Students</th><th style="text-align: center;">Average Score</th><th>Performance</th></tr></thead>
                <tbody>
                    <?php foreach ($grade_info['classes'] as $class_name => $class_data): $class_rating = getPerformanceRating($class_data['avg']); ?>
                    <tr>
                        <td><?php echo e($class_name); ?></td>
                        <td style="text-align: center;"><?php echo $class_data['students']; ?></td>
                        <td style="text-align: center; font-size: 18px; font-weight: 600; color: <?php echo $class_rating['color']; ?>;"><?php echo $class_data['avg']; ?>%</td>
                        <td style="color: <?php echo $class_rating['color']; ?>; font-weight: 500;"><?php echo $class_rating['label']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3 style="margin: 30px 0 20px; color: #a5d8ff; font-size: 20px;">Performance by Subject</h3>
            <table>
                <thead><tr><th>Subject</th><th style="text-align: center;">Average Score</th><th>Performance</th><th>Status</th></tr></thead>
                <tbody>
                    <?php $sorted = $grade_info['subjects']; arsort($sorted); $rank = 1;
                    foreach ($sorted as $subject => $avg): $subj_rating = getPerformanceRating($avg); ?>
                    <tr>
                        <td><?php if ($rank <= 3): ?><span style="color: #fbbf24; font-weight: 600;">#<?php echo $rank; ?></span> <?php endif; echo e($subject); ?></td>
                        <td style="text-align: center; font-size: 16px; font-weight: 600; color: <?php echo $subj_rating['color']; ?>;"><?php echo $avg; ?>%</td>
                        <td style="color: <?php echo $subj_rating['color']; ?>; font-weight: 500;"><?php echo $subj_rating['label']; ?></td>
                        <td><?php echo $rank <= 3 ? '⭐ Top Performer' : ($rank > count($sorted) - 2 ? '⚠️ Needs Focus' : '✓ On Track'); ?></td>
                    </tr>
                    <?php $rank++; endforeach; ?>
                </tbody>
            </table>

            <div style="background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px; margin-top: 30px;">
                <h3 style="margin: 0 0 15px; color: #a5d8ff; font-size: 18px;">Recommendations</h3>
                <ul style="margin: 0; padding-left: 20px; color: rgba(255, 255, 255, 0.9); line-height: 1.8;">
                    <?php if ($overall_avg >= 85): ?>
                        <li>Excellent overall performance. Continue implementing current teaching strategies.</li>
                    <?php elseif ($overall_avg >= 75): ?>
                        <li>Good performance overall. Focus on consistency across all subjects.</li>
                    <?php else: ?>
                        <li>Performance needs improvement. Implement targeted intervention programs.</li>
                    <?php endif; ?>
                    <li>Monitor struggling subjects and provide additional support resources.</li>
                    <li>Celebrate successes in top-performing subjects and share effective teaching methods.</li>
                </ul>
            </div>

            <p style="text-align: center; margin-top: 30px; color: rgba(255, 255, 255, 0.6); font-size: 12px;">Generated on <?php echo date('F j, Y'); ?> | St. Joseph Primary School</p>
        </section>
        <?php endif; ?>
    </main>
</div>
</body>
</html>