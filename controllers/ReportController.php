<?php
// handles reports
// generates report cards and averages

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Score.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Subject.php';
require_once __DIR__ . '/../models/SchoolClass.php';

class ReportController extends BaseController {
    
    private $scoreModel;
    private $studentModel;
    private $subjectModel;
    private $classModel;

    public function __construct() {
        $this->scoreModel = new Score();
        $this->studentModel = new Student();
        $this->subjectModel = new Subject();
        $this->classModel = new SchoolClass();
    }

    // makes the report card for a student
    // calculates averages and stuff
    public function studentReportCard($studentId, $termId) {
        // Get student info
        $student = $this->studentModel->find($studentId);
        if (!$student) {
            return [
                'success' => false,
                'message' => 'Student not found.'
            ];
        }

        // Get term info
        $terms = $this->scoreModel->getTerms();
        $term = null;
        foreach ($terms as $t) {
            if ($t['term_id'] == $termId) {
                $term = $t;
                break;
            }
        }

        if (!$term) {
            return [
                'success' => false,
                'message' => 'Term not found.'
            ];
        }

        // Get scores
        $scores = $this->scoreModel->getReportCard($studentId, $termId);

        // Calculate overall average
        $totalScore = 0;
        $subjectCount = count($scores);
        
        foreach ($scores as &$score) {
            $score['letter_grade'] = $this->scoreModel->getLetterGrade($score['score']);
            $totalScore += $score['score'];
        }

        $overallAverage = $subjectCount > 0 ? round($totalScore / $subjectCount, 2) : 0;
        $overallGrade = $this->scoreModel->getLetterGrade($overallAverage);

        return [
            'success' => true,
            'student' => $student,
            'term' => $term,
            'scores' => $scores,
            'overall_average' => $overallAverage,
            'overall_grade' => $overallGrade,
            'subject_count' => $subjectCount
        ];
    }

    // shows how a grade is doing in a subject
    // e.g. how is grade 1 doing in math?
    public function gradeAverageReport($gradeId, $subjectId = null, $termId = null) {
        // Get grade info
        $grades = $this->classModel->getGrades();
        $grade = null;
        foreach ($grades as $g) {
            if ($g['grade_id'] == $gradeId) {
                $grade = $g;
                break;
            }
        }

        if (!$grade) {
            return [
                'success' => false,
                'message' => 'Grade not found.'
            ];
        }

        // Get subjects for this grade
        $subjects = $this->subjectModel->getByGrade($gradeId);

        // If specific subject requested, filter
        if ($subjectId) {
            $subjects = array_filter($subjects, function($s) use ($subjectId) {
                return $s['subject_id'] == $subjectId;
            });
            $subjects = array_values($subjects);
        }

        // Calculate averages for each subject
        $report = [];
        foreach ($subjects as $subject) {
            $average = $this->scoreModel->calculateGradeSubjectAverage(
                $gradeId, 
                $subject['subject_id'], 
                $termId
            );
            
            $report[] = [
                'subject_id' => $subject['subject_id'],
                'subject_name' => $subject['subject_name'],
                'average' => $average,
                'letter_grade' => $this->scoreModel->getLetterGrade($average)
            ];
        }

        // Sort by average descending
        usort($report, function($a, $b) {
            return $b['average'] <=> $a['average'];
        });

        // Get term info if specified
        $term = null;
        if ($termId) {
            $terms = $this->scoreModel->getTerms();
            foreach ($terms as $t) {
                if ($t['term_id'] == $termId) {
                    $term = $t;
                    break;
                }
            }
        }

        return [
            'success' => true,
            'grade' => $grade,
            'term' => $term,
            'subjects' => $report,
            'overall_average' => count($report) > 0 
                ? round(array_sum(array_column($report, 'average')) / count($report), 2) 
                : 0
        ];
    }

    // shows how a class is doing
    // ranks students by average
    public function classPerformanceReport($classId, $termId = null) {
        // Get class info
        $class = $this->classModel->find($classId);
        if (!$class) {
            return [
                'success' => false,
                'message' => 'Class not found.'
            ];
        }

        // Get students in class
        $students = $this->studentModel->getByClass($classId);

        // Calculate average for each student
        $report = [];
        foreach ($students as $student) {
            $average = $this->scoreModel->calculateStudentAverage(
                $student['student_id'], 
                $termId
            );
            
            $report[] = [
                'student_id' => $student['student_id'],
                'student_name' => $student['full_name'],
                'average' => $average,
                'letter_grade' => $this->scoreModel->getLetterGrade($average)
            ];
        }

        // sort best to worst
        usort($report, function($a, $b) {
            return $b['average'] <=> $a['average'];
        });

        // Add rank
        foreach ($report as $index => &$r) {
            $r['rank'] = $index + 1;
        }

        return [
            'success' => true,
            'class' => $class,
            'students' => $report,
            'class_average' => count($report) > 0 
                ? round(array_sum(array_column($report, 'average')) / count($report), 2) 
                : 0
        ];
    }

    // get list of students for the report form
    public function getStudents() {
        return $this->studentModel->getAll();
    }

    // get terms for dropdown
    public function getTerms() {
        return $this->scoreModel->getTerms();
    }

    /**
     * Get all grades for report selection
     * @return array
     */
    public function getGrades() {
        return $this->classModel->getGrades();
    }

    /**
     * Get all classes for report selection
     * @return array
     */
    public function getClasses() {
        return $this->classModel->getAll();
    }

    /**
     * Get subjects by grade for report selection
     * @param int $gradeId
     * @return array
     */
    public function getSubjectsByGrade($gradeId) {
        return $this->subjectModel->getByGrade($gradeId);
    }
}

// Handle direct requests
if (basename($_SERVER['PHP_SELF']) === 'ReportController.php') {
    $controller = new ReportController();
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'student-card':
            $studentId = $_GET['student_id'] ?? null;
            $termId = $_GET['term_id'] ?? null;
            if ($studentId && $termId) {
                $controller->json($controller->studentReportCard($studentId, $termId));
            }
            break;

        case 'grade-average':
            $gradeId = $_GET['grade_id'] ?? null;
            $subjectId = $_GET['subject_id'] ?? null;
            $termId = $_GET['term_id'] ?? null;
            if ($gradeId) {
                $controller->json($controller->gradeAverageReport($gradeId, $subjectId, $termId));
            }
            break;

        case 'class-performance':
            $classId = $_GET['class_id'] ?? null;
            $termId = $_GET['term_id'] ?? null;
            if ($classId) {
                $controller->json($controller->classPerformanceReport($classId, $termId));
            }
            break;

        case 'students':
            $controller->json($controller->getStudents());
            break;

        case 'terms':
            $controller->json($controller->getTerms());
            break;

        case 'grades':
            $controller->json($controller->getGrades());
            break;

        case 'classes':
            $controller->json($controller->getClasses());
            break;

        case 'subjects-by-grade':
            $gradeId = $_GET['grade_id'] ?? null;
            if ($gradeId) {
                $controller->json($controller->getSubjectsByGrade($gradeId));
            }
            break;
    }
}
