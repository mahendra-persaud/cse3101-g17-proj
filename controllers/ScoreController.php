<?php
// controls the scores page
// only teachers should be here

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Score.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Subject.php';
require_once __DIR__ . '/../models/SchoolClass.php';

class ScoreController extends BaseController {
    
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

    /**
     * Get all scores
     * @return array
     */
    public function index() {
        return $this->scoreModel->getAll();
    }

    /**
     * Get scores for a student
     * @param int $studentId
     * @param int|null $termId
     * @return array
     */
    public function getByStudent($studentId, $termId = null) {
        return $this->scoreModel->getByStudent($studentId, $termId);
    }

    /**
     * Get scores for a subject
     * @param int $subjectId
     * @param int|null $termId
     * @return array
     */
    public function getBySubject($subjectId, $termId = null) {
        return $this->scoreModel->getBySubject($subjectId, $termId);
    }

    // save a single score
    // checks security token and validation first
    public function store($data) {
        $this->requireRole(['teacher', 'office_admin']);

        // CSRF check
        if (!$this->validateCsrf()) {
            return [
                'success' => false,
                'message' => 'Invalid security token (CSRF).'
            ];
        }

        // Validate
        $errors = $this->scoreModel->validateScore($data);
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        // Save score with user ID for audit trail
        $success = $this->scoreModel->saveScore(
            $data['student_id'],
            $data['subject_id'],
            $data['term_id'],
            $data['score'],
            $this->getUserId()  // Pass current user ID for audit
        );

        if ($success) {
            return [
                'success' => true,
                'message' => 'Score saved successfully.'
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to save score.'
        ];
    }

    // save a bunch of scores at once
    // loops through them and saves one by one
    // this handles the whole class list form
    public function bulkSave($scores) {
        $this->requireRole(['teacher', 'office_admin']);

        $saved = 0;
        $errors = [];
        $userId = $this->getUserId();  // Get user ID once for all scores

        foreach ($scores as $index => $score) {
            $validationErrors = $this->scoreModel->validateScore($score);
            if (!empty($validationErrors)) {
                $errors[$index] = $validationErrors;
                continue;
            }

            $success = $this->scoreModel->saveScore(
                $score['student_id'],
                $score['subject_id'],
                $score['term_id'],
                $score['score'],
                $userId  // Pass current user ID for audit
            );

            if ($success) {
                $saved++;
            }
        }

        return [
            'success' => $saved > 0,
            'saved' => $saved,
            'total' => count($scores),
            'errors' => $errors
        ];
    }

    // delete a score
    // why would you delete a score though? maybe a mistake
    public function destroy($id) {
        $this->requireRole(['teacher', 'office_admin']);

        $success = $this->scoreModel->delete($id);

        if ($success) {
            $this->setFlash('success', 'Score deleted successfully.');
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => 'Failed to delete score.'
        ];
    }

    /**
     * Get student average
     * @param int $studentId
     * @param int|null $termId
     * @return float
     */
    public function getStudentAverage($studentId, $termId = null) {
        return $this->scoreModel->calculateStudentAverage($studentId, $termId);
    }

    /**
     * Get subject average
     * @param int $subjectId
     * @param int|null $termId
     * @return float
     */
    public function getSubjectAverage($subjectId, $termId = null) {
        return $this->scoreModel->calculateSubjectAverage($subjectId, $termId);
    }

    /**
     * Get grade/subject average for reports
     * @param int $gradeId
     * @param int $subjectId
     * @param int|null $termId
     * @return float
     */
    public function getGradeSubjectAverage($gradeId, $subjectId, $termId = null) {
        return $this->scoreModel->calculateGradeSubjectAverage($gradeId, $subjectId, $termId);
    }

    /**
     * Get all terms
     * @return array
     */
    public function getTerms() {
        return $this->scoreModel->getTerms();
    }

    /**
     * Get current term
     * @return array|null
     */
    public function getCurrentTerm() {
        return $this->scoreModel->getCurrentTerm();
    }

    /**
     * Get students for score entry
     * @param int $classId
     * @return array
     */
    public function getStudentsForEntry($classId) {
        return $this->studentModel->getByClass($classId);
    }

    /**
     * Get subjects for score entry (by grade)
     * @param int $gradeId
     * @return array
     */
    public function getSubjectsForEntry($gradeId) {
        return $this->subjectModel->getByGrade($gradeId);
    }

    /**
     * Get subjects specifically assigned to a student's class
     * @param int $studentId
     * @return array
     */
    public function getSubjectsForStudent($studentId) {
        return $this->scoreModel->getSubjectsForStudent($studentId);
    }

    /**
     * Get letter grade for score
     * @param int $score
     * @return string
     */
    public function getLetterGrade($score) {
        return $this->scoreModel->getLetterGrade($score);
    }

    /**
     * Get all classes
     * @return array
     */
    public function getClasses() {
        return $this->classModel->getAll();
    }

    /**
     * Get all grades
     * @return array
     */
    public function getGrades() {
        return $this->classModel->getGrades();
    }

    // shows who changed what
    // audit log for admins only
    public function getAuditLog($limit = 50) {
        $this->requireRole('office_admin');
        return $this->scoreModel->getRecentModifications($limit);
    }

    /**
     * Get scores modified by a specific user
     * @param int $userId
     * @return array
     */
    public function getByModifiedUser($userId) {
        $this->requireRole('office_admin');
        return $this->scoreModel->getByModifiedUser($userId);
    }
}

// Handle direct requests
if (basename($_SERVER['PHP_SELF']) === 'ScoreController.php') {
    $controller = new ScoreController();
    $action = $_GET['action'] ?? '';
    $id = $_GET['id'] ?? null;

    switch ($action) {
        case 'list':
            $controller->json($controller->index());
            break;

        case 'by-student':
            $studentId = $_GET['student_id'] ?? null;
            $termId = $_GET['term_id'] ?? null;
            if ($studentId) {
                $controller->json($controller->getByStudent($studentId, $termId));
            }
            break;

        case 'by-subject':
            $subjectId = $_GET['subject_id'] ?? null;
            $termId = $_GET['term_id'] ?? null;
            if ($subjectId) {
                $controller->json($controller->getBySubject($subjectId, $termId));
            }
            break;

        case 'store':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $result = $controller->store($_POST);
                $controller->json($result);
            }
            break;

        case 'bulk-save':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $scores = json_decode(file_get_contents('php://input'), true) ?? [];
                $result = $controller->bulkSave($scores);
                $controller->json($result);
            }
            break;

        case 'delete':
            if ($id) {
                $result = $controller->destroy($id);
                $controller->json($result);
            }
            break;

        case 'terms':
            $controller->json($controller->getTerms());
            break;

        case 'current-term':
            $controller->json($controller->getCurrentTerm());
            break;

        case 'student-average':
            $studentId = $_GET['student_id'] ?? null;
            $termId = $_GET['term_id'] ?? null;
            if ($studentId) {
                $controller->json(['average' => $controller->getStudentAverage($studentId, $termId)]);
            }
            break;

        case 'subject-average':
            $subjectId = $_GET['subject_id'] ?? null;
            $termId = $_GET['term_id'] ?? null;
            if ($subjectId) {
                $controller->json(['average' => $controller->getSubjectAverage($subjectId, $termId)]);
            }
            break;

        case 'grade-subject-average':
            $gradeId = $_GET['grade_id'] ?? null;
            $subjectId = $_GET['subject_id'] ?? null;
            $termId = $_GET['term_id'] ?? null;
            if ($gradeId && $subjectId) {
                $controller->json(['average' => $controller->getGradeSubjectAverage($gradeId, $subjectId, $termId)]);
            }
            break;

        case 'audit-log':
            $limit = $_GET['limit'] ?? 50;
            $controller->json($controller->getAuditLog($limit));
            break;

        case 'by-modified-user':
            $userId = $_GET['user_id'] ?? null;
            if ($userId) {
                $controller->json($controller->getByModifiedUser($userId));
            }
            break;
    }
}
