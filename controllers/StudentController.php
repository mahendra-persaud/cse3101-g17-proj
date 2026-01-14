<?php
/**
 * Student Controller
 * Handles all student CRUD operations
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/SchoolClass.php';

class StudentController extends BaseController {
    
    private $studentModel;
    private $classModel;

    public function __construct() {
        $this->studentModel = new Student();
        $this->classModel = new SchoolClass();
    }

    /**
     * Get all students
     * @return array
     */
    public function index() {
        $this->requireRole('office_admin');
        return $this->studentModel->getAll();
    }

    /**
     * Get single student
     * @param int $id
     * @return array|null
     */
    public function show($id) {
        $this->requireRole('office_admin');
        return $this->studentModel->find($id);
    }

    /**
     * Get students by class
     * @param int $classId
     * @return array
     */
    public function getByClass($classId) {
        return $this->studentModel->getByClass($classId);
    }

    /**
     * Get students by grade
     * @param int $gradeId
     * @return array
     */
    public function getByGrade($gradeId) {
        return $this->studentModel->getByGrade($gradeId);
    }

    /**
     * Create new student
     * @param array $data
     * @return array Result with success status
     */
    public function store($data) {
        $this->requireRole('office_admin');

        // Validate
        $errors = $this->studentModel->validateStudent($data);
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        // Create student
        $id = $this->studentModel->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'class_id' => $data['class_id']
        ]);

        if ($id) {
            $this->setFlash('success', 'Student created successfully.');
            return [
                'success' => true,
                'id' => $id
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to create student.'
        ];
    }

    /**
     * Update student
     * @param int $id
     * @param array $data
     * @return array Result with success status
     */
    public function update($id, $data) {
        $this->requireRole('office_admin');

        // Validate
        $errors = $this->studentModel->validateStudent($data);
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        // Update student
        $success = $this->studentModel->update($id, [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'class_id' => $data['class_id']
        ]);

        if ($success) {
            $this->setFlash('success', 'Student updated successfully.');
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => 'Failed to update student.'
        ];
    }

    /**
     * Delete student
     * @param int $id
     * @return array Result with success status
     */
    public function destroy($id) {
        $this->requireRole('office_admin');

        $success = $this->studentModel->delete($id);

        if ($success) {
            $this->setFlash('success', 'Student deleted successfully.');
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => 'Failed to delete student.'
        ];
    }

    /**
     * Search students
     * @param string $query
     * @return array
     */
    public function search($query) {
        return $this->studentModel->search($query);
    }

    /**
     * Get all classes for dropdowns
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
}

// Handle direct requests
if (basename($_SERVER['PHP_SELF']) === 'StudentController.php') {
    $controller = new StudentController();
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

        case 'search':
            $query = $_GET['q'] ?? '';
            $controller->json($controller->search($query));
            break;

        case 'by-class':
            $classId = $_GET['class_id'] ?? null;
            if ($classId) {
                $controller->json($controller->getByClass($classId));
            }
            break;

        case 'by-grade':
            $gradeId = $_GET['grade_id'] ?? null;
            if ($gradeId) {
                $controller->json($controller->getByGrade($gradeId));
            }
            break;
    }
}
