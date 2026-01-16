<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

    /* Select2 Styling */
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--single {
        height: 48px !important;
        padding: 10px 16px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 8px !important;
        background: #ffffff !important;
        font-size: 15px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px !important;
        color: #1a1a1a !important;
        padding-left: 0 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
        right: 10px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #9ca3af !important;
    }
    .select2-dropdown {
        border: 1px solid #d1d5db !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1) !important;
    }
    .select2-search--dropdown .select2-search__field {
        padding: 10px 12px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 6px !important;
        font-size: 14px !important;
    }
    .select2-results__option {
        padding: 10px 12px !important;
        font-size: 14px !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #0d9488 !important;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #ccfbf1 !important;
        color: #0f766e !important;
    }
    .filter-note {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }
    .form-card label {
        color: #374151 !important;
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
    }
    .form-card .form-group {
        margin-bottom: 20px;
    }
</style>';
$no_container = true;
$body_class = 'management-page';
require_once $headerPath;
require_role(['office_admin', 'teacher']);

require_once __DIR__ . '/../../controllers/ReportController.php';
require_once __DIR__ . '/../../controllers/ScoreController.php';

$reportController = new ReportController();
$scoreController = new ScoreController();

$students = $reportController->getStudents();
$allTerms = $reportController->getTerms();
$classes = $scoreController->getClasses();

// get url params
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$term_id = isset($_GET['term_id']) ? (int)$_GET['term_id'] : (count($allTerms) > 0 ? $allTerms[0]['term_id'] : 0);

$reportData = null;
if ($student_id > 0 && $term_id > 0) {
    $reportData = $reportController->studentReportCard($student_id, $term_id);
}

// check if student exists
if ($reportData && $reportData['success']) {
    $student = $reportData['student'];
    $scores = $reportData['scores'];
    $termInfo = $reportData['term'];
} else {
    $student = null;
    $scores = [];
}
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

    <main class="main-dashboard">
        <div class="header no-print">
            <h2>Student Report Card</h2>
        </div>

        <?php if (!$student): ?>
        <!-- form to pick student -->
        <nav class="breadcrumb no-print">
            <div class="breadcrumb-item"><a href="<?php echo $projectRoot; ?>/views/dashboard/index.php">Home</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><span class="breadcrumb-current">Report Card</span></div>
        </nav>

        <h1 class="no-print">Generate Student Report Card</h1>

        <section class="form-card no-print">
            <form method="GET" action="report_student_card.php">
                <div class="form-group">
                    <label for="class_filter">Filter by Class</label>
                    <select id="class_filter" class="searchable-select">
                        <option value="">All Classes</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['class_id']; ?>">
                                <?php echo e($class['grade_name'] . ' - ' . $class['class_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="filter-note">Optional: Select a class to filter students</p>
                </div>

                <div class="form-group">
                    <label for="student_id">Select Student</label>
                    <select id="student_id" name="student_id" class="searchable-select" required>
                        <option value="">Search or select student...</option>
                        <?php foreach ($students as $s): ?>
                            <option value="<?php echo $s['student_id']; ?>" data-class-id="<?php echo $s['class_id']; ?>" <?php echo $student_id == $s['student_id'] ? 'selected' : ''; ?>>
                                <?php echo e($s['first_name'] . ' ' . $s['last_name']); ?> (Grade <?php echo e($s['grade_name']); ?> - <?php echo e($s['class_name']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="term_id">Select Term</label>
                    <select id="term_id" name="term_id" class="searchable-select" required>
                        <option value="">Search or select term...</option>
                        <?php foreach ($allTerms as $t): ?>
                            <option value="<?php echo $t['term_id']; ?>" <?php echo $term_id == $t['term_id'] ? 'selected' : ''; ?>>
                                <?php echo e($t['term_name']); ?> (<?php echo e($t['year_name']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="create-btn">Generate Report Card</button>
                </div>
            </form>
        </section>

        <?php else: ?>

        <!-- showing the actual card -->
        <div class="no-print" style="margin: 30px; display: flex; gap: 15px;">
            <button onclick="window.print()" style="background: rgba(35, 140, 246, 0.8); color: white; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; font-weight: 500;">Print Report Card</button>
            <button onclick="exportPDF()" style="background: rgba(220, 38, 38, 0.8); color: white; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; font-weight: 500;">Export as PDF</button>
            <a href="report_student_card.php" style="background: rgba(107, 114, 128, 0.8); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500;">Back to Selection</a>
        </div>

        <section class="table-card report-card" style="margin: 30px; max-width: 900px;">
            <!-- school name header -->
            <div style="text-align: center; border-bottom: 2px solid rgba(255, 255, 255, 0.2); padding-bottom: 20px; margin-bottom: 30px;">
                <h1 style="margin: 0 0 5px; font-size: 32px; color: #a5d8ff;">St. Joseph Primary School</h1>
                <p style="margin: 5px 0; color: rgba(255, 255, 255, 0.8); font-size: 14px;">123 Main Street, Georgetown, Guyana</p>
                <h2 style="margin: 15px 0 0; font-size: 24px; color: #fff;">Student Report Card</h2>
                <p style="margin: 5px 0; color: rgba(255, 255, 255, 0.8);">Academic Year <?php echo e($termInfo['year_name']); ?> | <?php echo e($termInfo['term_name']); ?></p>
            </div>

            <!-- student details -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px;">
                <div>
                    <p style="margin: 5px 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">STUDENT NAME</p>
                    <p style="margin: 0; font-size: 18px; font-weight: 600; color: #fff;"><?php echo e($student['full_name'] ?? ($student['first_name'] . ' ' . $student['last_name'])); ?></p>
                </div>
                <div>
                    <p style="margin: 5px 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">REGISTRATION NUMBER</p>
                    <p style="margin: 0; font-size: 18px; font-weight: 600; color: #fff;">#<?php echo str_pad($student['student_id'], 6, '0', STR_PAD_LEFT); ?></p>
                </div>
                <div>
                    <p style="margin: 5px 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">GRADE</p>
                    <p style="margin: 0; font-size: 18px; font-weight: 600; color: #fff;">Grade <?php echo e($student['grade_name'] ?? ''); ?></p>
                </div>
                <div>
                    <p style="margin: 5px 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">CLASS</p>
                    <p style="margin: 0; font-size: 18px; font-weight: 600; color: #fff;"><?php echo e($student['class_name'] ?? ''); ?></p>
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
                        <?php foreach ($scores as $score):
                            $grade = $score['letter_grade'];
                            $gradeClass = 'grade-' . strtolower($grade);

                            // Determine remarks
                            if ($score['score'] >= 90) $remark = 'Excellent performance';
                            elseif ($score['score'] >= 80) $remark = 'Very good work';
                            elseif ($score['score'] >= 70) $remark = 'Good effort';
                            elseif ($score['score'] >= 60) $remark = 'Satisfactory';
                            else $remark = 'Needs improvement';
                        ?>
                        <tr>
                            <td><?php echo e($score['subject_name']); ?></td>
                            <td style="text-align: center; font-size: 16px; font-weight: 600;"><?php echo $score['score']; ?>%</td>
                            <td style="text-align: center;" class="<?php echo $gradeClass; ?>"><?php echo $grade; ?></td>
                            <td><?php echo $remark; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- summary box -->
            <?php if ($reportData['subject_count'] > 0): ?>
            <div style="background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                <h3 style="margin: 0 0 15px; color: #a5d8ff; font-size: 18px;">Performance Summary</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                    <div>
                        <p style="margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">AVERAGE</p>
                        <p style="margin: 5px 0 0; font-size: 24px; font-weight: 600; color: #4ade80;"><?php echo $reportData['overall_average']; ?>%</p>
                    </div>
                    <div>
                        <p style="margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 13px;">TOTAL SUBJECTS</p>
                        <p style="margin: 5px 0 0; font-size: 24px; font-weight: 600; color: #fff;"><?php echo $reportData['subject_count']; ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- teacher says... -->
            <div style="background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                <h3 style="margin: 0 0 10px; color: #a5d8ff; font-size: 18px;">Class Teacher's Remarks</h3>
                <p style="margin: 0; color: rgba(255, 255, 255, 0.9); line-height: 1.6;">
                    <?php
                    $avg = $reportData['overall_average'];
                    $name = $student['first_name'];
                    if ($avg >= 85) {
                        echo $name . " has demonstrated exceptional academic performance this term. Consistent effort and excellent understanding across all subjects. Keep up the outstanding work!";
                    } elseif ($avg >= 75) {
                        echo $name . " has shown good academic progress this term. Continue to work hard and focus on areas that need improvement. Well done overall!";
                    } else {
                        echo $name . " is making satisfactory progress. Additional support and practice in weaker subjects would be beneficial. Keep working hard!";
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Store all students data for filtering
    const allStudents = [];
    $('#student_id option').each(function() {
        if (this.value) {
            allStudents.push({
                id: this.value,
                text: this.text,
                classId: $(this).data('class-id')
            });
        }
    });

    // Initialize Select2 on class filter
    $('#class_filter').select2({
        placeholder: 'All Classes',
        allowClear: true
    });

    // Initialize Select2 on student dropdown
    $('#student_id').select2({
        placeholder: 'Search or select student...',
        allowClear: true
    });

    $('#term_id').select2({
        placeholder: 'Search or select term...',
        allowClear: true
    });

    // Handle class filter change
    $('#class_filter').on('change', function() {
        const selectedClassId = this.value;
        const $studentSelect = $('#student_id');

        // Clear current selection
        $studentSelect.val(null).trigger('change');

        // Clear and rebuild student options
        $studentSelect.empty().append('<option value="">Search or select student...</option>');

        allStudents.forEach(student => {
            if (!selectedClassId || student.classId == selectedClassId) {
                const option = new Option(student.text, student.id, false, false);
                $(option).data('class-id', student.classId);
                $studentSelect.append(option);
            }
        });

        $studentSelect.trigger('change');
    });
});

function exportPDF() {
    alert('PDF export functionality would integrate with a library like jsPDF or server-side PDF generation (e.g., TCPDF, FPDF). For now, please use the Print function and save as PDF.');
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
