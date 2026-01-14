<?php
/**
 * Subject Controller
 * Handles all subject CRUD operations
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Subject.php';
require_once __DIR__ . '/../models/SchoolClass.php';

class SubjectController extends BaseController {
    
    private $subjectModel;
    private $classModel;

    public function __construct() {
        $this->subjectModel = new Subject();
        $this->classModel = new SchoolClass();
    }

    /**
     * Get all subjects
     * @return array
     */
    public function index() {
        return $this->subjectModel->getAll();
    }

    /**
     * Get single subject
     * @param int $id
     * @return array|null
     */
    public function show($id) {
        return $this->subjectModel->find($id);
    }

    /**
     * Get subjects by grade
     * @param int $gradeId
     * @return array
     */
    public function getByGrade($gradeId) {
        return $this->subjectModel->getByGrade($gradeId);
    }

    /**
     * Create new subject
     * @param array $data
     * @return array Result with success status
     */
    public function store($data) {
        $this->requireRole('office_admin');

        // Validate
        $errors = $this->subjectModel->validateSubject($data);
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        // Check for duplicates
        if ($this->subjectModel->nameExistsForGrade($data['subject_name'], $data['grade_id'])) {
            return [
                'success' => false,
                'message' => 'Subject already exists for this grade.'
            ];
        }

        // Create subject
        $id = $this->subjectModel->create([
            'subject_name' => $data['subject_name'],
            'grade_id' => $data['grade_id']
        ]);

        if ($id) {
            $this->setFlash('success', 'Subject created successfully.');
            return [
                'success' => true,
                'id' => $id
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to create subject.'
        ];
    }

    /**
     * Update subject
     * @param int $id
     * @param array $data
     * @return array Result with success status
     */
    public function update($id, $data) {
        $this->requireRole('office_admin');

        // Validate
        $errors = $this->subjectModel->validateSubject($data);
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        // Check for duplicates
        if ($this->subjectModel->nameExistsForGrade($data['subject_name'], $data['grade_id'], $id)) {
            return [
                'success' => false,
                'message' => 'Subject already exists for this grade.'
            ];
        }

        // Update subject
        $success = $this->subjectModel->update($id, [
            'subject_name' => $data['subject_name'],
            'grade_id' => $data['grade_id']
        ]);

        if ($success) {
            $this->setFlash('success', 'Subject updated successfully.');
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => 'Failed to update subject.'
        ];
    }

    /**
     * Delete subject
     * @param int $id
     * @return array Result with success status
     */
    public function destroy($id) {
        $this->requireRole('office_admin');

        $success = $this->subjectModel->delete($id);

        if ($success) {
            $this->setFlash('success', 'Subject deleted successfully.');
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => 'Failed to delete subject. It may have associated scores.'
        ];
    }

    /**
     * Get all grades for dropdowns
     * @return array
     */
    public function getGrades() {
        return $this->classModel->getGrades();
    }
}

// Handle direct requests
if (basename($_SERVER['PHP_SELF']) === 'SubjectController.php') {
    $controller = new SubjectController();
    $action = $_GET['action'] ?? '';
    $id = $_GET['id'] ?? null;

    switch ($action) {
        case 'list':
            $controller->json($controller->index());
            break;

        case 'show':
            if ($id) {
                $controller->json($controller->show($id));
            }
            break;

        case 'store':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $result = $controller->store($_POST);
                $controller->json($result);
            }
            break;

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
                $result = $controller->update($id, $_POST);
                $controller->json($result);
            }
            break;

        case 'delete':
            if ($id) {
                $result = $controller->destroy($id);
                $controller->json($result);
            }
            break;

        case 'by-grade':
            $gradeId = $_GET['grade_id'] ?? null;
            if ($gradeId) {
                $controller->json($controller->getByGrade($gradeId));
            }
            break;

        case 'grades':
            $controller->json($controller->getGrades());
            break;
    }
}
