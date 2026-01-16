<?php
// report_grade_average.php - Grade Average Performance Report
require_once __DIR__ . '/../../controllers/ReportController.php';

$reportController = new ReportController();
$reportController->requireRole(['office_admin', 'teacher']);

// Get grades and terms from database
$grades = $reportController->getGrades();
$terms = $reportController->getTerms();

// Get selected values
$selected_grade = isset($_GET['grade']) ? (int)$_GET['grade'] : 0;
$selected_term = isset($_GET['term']) ? (int)$_GET['term'] : 0;

// Check if we should show report
$show_report = ($selected_grade > 0 && $selected_term > 0);

$grade_info = null;
$term_info = null;
$subject_report = [];
$class_stats = [];
$total_students = 0;
$overall_avg = 0;

if ($show_report) {
    // Get grade average report data
    $report = $reportController->gradeAverageReport($selected_grade, null, $selected_term);

    if ($report['success']) {
        $grade_info = $report['grade'];
        $term_info = $report['term'];
        $subject_report = $report['subjects'];
        $overall_avg = $report['overall_average'];
    }

    // Get class statistics
    $class_stats = $reportController->getClassStatsByGrade($selected_grade, $selected_term);
    $total_students = $reportController->getStudentCountByGrade($selected_grade);
}

function getPerformanceRating($avg) {
    if ($avg >= 85) return ['label' => 'Excellent', 'color' => '#4ade80'];
    if ($avg >= 75) return ['label' => 'Good', 'color' => '#60a5fa'];
    if ($avg >= 65) return ['label' => 'Satisfactory', 'color' => '#fbbf24'];
    return ['label' => 'Needs Improvement', 'color' => '#f87171'];
}

$projectRoot = '/cse3101-g17-proj';
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
$no_container = true;
$body_class = 'management-page';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="Main-Container">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="main-dashboard">
        <div class="header">
            <h2>Grade Average Report</h2>
        </div>

        <nav class="breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo $projectRoot; ?>/views/dashboard/index.php">Home</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><a href="index.php">Reports</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><span class="breadcrumb-current">Grade Average</span></div>
        </nav>

        <?php if (!$show_report): ?>
        <h1>Generate Grade Average Report</h1>
        <section class="form-card">
            <h2>Select Report Parameters</h2>
            <form method="GET" action="">
                <div class="form-group">
                    <label for="grade">Select Grade</label>
                    <select name="grade" id="grade" required>
                        <option value="">-- Choose a grade --</option>
                        <?php foreach ($grades as $g): ?>
                            <option value="<?php echo $g['grade_id']; ?>"><?php echo e($g['grade_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="term">Select Term</label>
                    <select name="term" id="term" required>
                        <option value="">-- Choose a term --</option>
                        <?php foreach ($terms as $t): ?>
                            <option value="<?php echo $t['term_id']; ?>">
                                <?php echo e($t['term_name'] . ' (' . $t['year_name'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="create-btn">Generate Report</button>
                </div>
            </form>
        </section>

        <?php else:
            $rating = getPerformanceRating($overall_avg);
        ?>

        <div style="margin: 20px 0; display: flex; gap: 15px;">
            <button onclick="window.print()" class="create-btn">Print Report</button>
            <a href="report_grade_average.php" class="btn" style="background: #6b7280; text-decoration: none;">Back</a>
        </div>

        <section class="table-card" style="margin: 20px 0;">
            <div style="text-align: center; border-bottom: 2px solid rgba(255, 255, 255, 0.2); padding-bottom: 20px; margin-bottom: 30px;">
                <h1 style="margin: 0 0 5px; font-size: 28px; color: #a5d8ff;">Grade Average Performance Report</h1>
                <h2 style="margin: 10px 0 0; font-size: 22px; color: #fff;"><?php echo e($grade_info['grade_name']); ?></h2>
                <p style="margin: 5px 0; color: rgba(255, 255, 255, 0.8);">
                    <?php echo e($term_info['term_name'] . ' - ' . $term_info['year_name']); ?>
                </p>
            </div>

            <!-- Stats Boxes -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <div style="background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px; text-align: center;">
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">OVERALL AVERAGE</p>
                    <p style="margin: 5px 0 0; font-size: 32px; font-weight: 700; color: <?php echo $rating['color']; ?>;">
                        <?php echo $overall_avg > 0 ? $overall_avg . '%' : 'N/A'; ?>
                    </p>
                    <p style="margin: 5px 0 0; color: rgba(255, 255, 255, 0.8); font-size: 14px;">
                        <?php echo $overall_avg > 0 ? $rating['label'] : ''; ?>
                    </p>
                </div>
                <div style="background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px; text-align: center;">
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">TOTAL STUDENTS</p>
                    <p style="margin: 5px 0 0; font-size: 32px; font-weight: 700; color: #fff;"><?php echo $total_students; ?></p>
                </div>
                <div style="background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px; text-align: center;">
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">NUMBER OF CLASSES</p>
                    <p style="margin: 5px 0 0; font-size: 32px; font-weight: 700; color: #fff;"><?php echo count($class_stats); ?></p>
                </div>
                <div style="background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px; text-align: center;">
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">SUBJECTS</p>
                    <p style="margin: 5px 0 0; font-size: 32px; font-weight: 700; color: #fff;"><?php echo count($subject_report); ?></p>
                </div>
            </div>

            <!-- Performance by Class -->
            <h3 style="margin: 30px 0 20px; color: #a5d8ff; font-size: 20px;">Performance by Class</h3>
            <?php if (!empty($class_stats)): ?>
            <table style="margin-bottom: 40px;">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th style="text-align: center;">Students</th>
                        <th style="text-align: center;">Average Score</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($class_stats as $class):
                        $class_rating = getPerformanceRating($class['class_avg']);
                    ?>
                    <tr>
                        <td>Class <?php echo e($class['class_name']); ?></td>
                        <td style="text-align: center;"><?php echo $class['student_count']; ?></td>
                        <td style="text-align: center; font-size: 18px; font-weight: 600; color: <?php echo $class_rating['color']; ?>;">
                            <?php echo $class['class_avg'] > 0 ? $class['class_avg'] . '%' : 'N/A'; ?>
                        </td>
                        <td style="color: <?php echo $class_rating['color']; ?>; font-weight: 500;">
                            <?php echo $class['class_avg'] > 0 ? $class_rating['label'] : '-'; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="color: rgba(255, 255, 255, 0.7); padding: 20px;">No class data available for this grade.</p>
            <?php endif; ?>

            <!-- Performance by Subject -->
            <h3 style="margin: 30px 0 20px; color: #a5d8ff; font-size: 20px;">Performance by Subject</h3>
            <?php if (!empty($subject_report)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th style="text-align: center;">Average Score</th>
                        <th style="text-align: center;">Grade</th>
                        <th>Performance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 1;
                    $total_subjects = count($subject_report);
                    foreach ($subject_report as $subject):
                        $subj_rating = getPerformanceRating($subject['average']);
                    ?>
                    <tr>
                        <td>
                            <?php if ($rank <= 3 && $subject['average'] > 0): ?>
                                <span style="color: #fbbf24; font-weight: 600;">#<?php echo $rank; ?></span>
                            <?php endif; ?>
                            <?php echo e($subject['subject_name']); ?>
                        </td>
                        <td style="text-align: center; font-size: 16px; font-weight: 600; color: <?php echo $subj_rating['color']; ?>;">
                            <?php echo $subject['average'] > 0 ? $subject['average'] . '%' : 'N/A'; ?>
                        </td>
                        <td style="text-align: center; font-weight: 600;">
                            <?php echo $subject['average'] > 0 ? $subject['letter_grade'] : '-'; ?>
                        </td>
                        <td style="color: <?php echo $subj_rating['color']; ?>; font-weight: 500;">
                            <?php echo $subject['average'] > 0 ? $subj_rating['label'] : '-'; ?>
                        </td>
                        <td>
                            <?php
                            if ($subject['average'] == 0) {
                                echo '<span style="color: #9ca3af;">No scores</span>';
                            } elseif ($rank <= 3) {
                                echo '<span style="color: #fbbf24;">Top Performer</span>';
                            } elseif ($rank > $total_subjects - 2) {
                                echo '<span style="color: #f87171;">Needs Focus</span>';
                            } else {
                                echo '<span style="color: #4ade80;">On Track</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php $rank++; endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="color: rgba(255, 255, 255, 0.7); padding: 20px;">No subjects found for this grade.</p>
            <?php endif; ?>

            <!-- Recommendations -->
            <div style="background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px; margin-top: 30px;">
                <h3 style="margin: 0 0 15px; color: #a5d8ff; font-size: 18px;">Recommendations</h3>
                <ul style="margin: 0; padding-left: 20px; color: rgba(255, 255, 255, 0.9); line-height: 1.8;">
                    <?php if ($overall_avg == 0): ?>
                        <li>No scores have been recorded for this term yet. Enter scores to see performance data.</li>
                    <?php elseif ($overall_avg >= 85): ?>
                        <li>Excellent overall performance. Continue implementing current teaching strategies.</li>
                        <li>Consider enrichment activities for high-performing students.</li>
                    <?php elseif ($overall_avg >= 75): ?>
                        <li>Good performance overall. Focus on consistency across all subjects.</li>
                        <li>Identify and support students who are slightly below average.</li>
                    <?php elseif ($overall_avg >= 65): ?>
                        <li>Satisfactory performance with room for improvement.</li>
                        <li>Review teaching methods for lower-performing subjects.</li>
                    <?php else: ?>
                        <li>Performance needs improvement. Implement targeted intervention programs.</li>
                        <li>Consider additional tutoring sessions for struggling students.</li>
                    <?php endif; ?>
                    <li>Monitor struggling subjects and provide additional support resources.</li>
                    <li>Celebrate successes in top-performing subjects and share effective teaching methods.</li>
                </ul>
            </div>

            <p style="text-align: center; margin-top: 30px; color: rgba(255, 255, 255, 0.6); font-size: 12px;">
                Generated on <?php echo date('F j, Y'); ?> | St. Joseph Primary School
            </p>
        </section>
        <?php endif; ?>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
