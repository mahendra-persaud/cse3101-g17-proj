<?php
// student controller
// crud for students

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

    // gets all students
    public function index() {
        $this->requireRole('office_admin');
        return $this->studentModel->getAll();
    }

    // gets one student
    public function show($id) {
        $this->requireRole('office_admin');
        return $this->studentModel->find($id);
    }

    // finds students in a class
    public function getByClass($classId) {
        return $this->studentModel->getByClass($classId);
    }

    // finds students in a grade
    public function getByGrade($gradeId) {
        return $this->studentModel->getByGrade($gradeId);
    }

    // adds a new student
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

    // updates student info
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

    // deletes a student
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

    // search function
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
