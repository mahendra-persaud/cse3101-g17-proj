<?php
/**
 * Class Controller
 * Handles all class CRUD operations
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/SchoolClass.php';

class ClassController extends BaseController {
    
    private $classModel;

    public function __construct() {
        $this->classModel = new SchoolClass();
    }

    /**
     * Get all classes
     * @return array
     */
    public function index() {
        return $this->classModel->getAll();
    }

    /**
     * Get single class
     * @param int $id
     * @return array|null
     */
    public function show($id) {
        return $this->classModel->find($id);
    }

    /**
     * Get classes by grade
     * @param int $gradeId
     * @return array
     */
    public function getByGrade($gradeId) {
        return $this->classModel->getByGrade($gradeId);
    }

    /**
     * Create new class
     * @param array $data
     * @return array Result with success status
     */
    public function store($data) {
        $this->requireRole('office_admin');

        // Validate
        $errors = $this->classModel->validateClass($data);
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        // Check for duplicates
        if ($this->classModel->nameExistsForGrade($data['class_name'], $data['grade_id'])) {
            return [
                'success' => false,
                'message' => 'Class already exists for this grade.'
            ];
        }

        // Check if grade already has 6 classes (max)
        $existingClasses = $this->classModel->getByGrade($data['grade_id']);
        if (count($existingClasses) >= 6) {
            return [
                'success' => false,
                'message' => 'Maximum of 6 classes per grade reached.'
            ];
        }

        // Create class
        $id = $this->classModel->create([
            'class_name' => $data['class_name'],
            'grade_id' => $data['grade_id']
        ]);

        if ($id) {
            $this->setFlash('success', 'Class created successfully.');
            return [
                'success' => true,
                'id' => $id
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to create class.'
        ];
    }

    /**
     * Update class
     * Note: grade_id cannot be changed per assignment requirements
     * @param int $id
     * @param array $data
     * @return array Result with success status
     */
    public function update($id, $data) {
        $this->requireRole('office_admin');

        // Get existing class
        $existing = $this->classModel->find($id);
        if (!$existing) {
            return [
                'success' => false,
                'message' => 'Class not found.'
            ];
        }

        // Prevent grade change (classes cannot switch grades per assignment)
        if (isset($data['grade_id']) && $data['grade_id'] != $existing['grade_id']) {
            return [
                'success' => false,
                'message' => 'Classes cannot switch grades.'
            ];
        }

        // Validate
        $errors = $this->classModel->validateClass([
            'class_name' => $data['class_name'] ?? $existing['class_name'],
            'grade_id' => $existing['grade_id']
        ]);
        
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        // Check for duplicates
        if (isset($data['class_name']) && 
            $this->classModel->nameExistsForGrade($data['class_name'], $existing['grade_id'], $id)) {
            return [
                'success' => false,
                'message' => 'Class name already exists for this grade.'
            ];
        }

        // Update class (only name can be changed)
        $success = $this->classModel->update($id, [
            'class_name' => $data['class_name'] ?? $existing['class_name']
        ]);

        if ($success) {
            $this->setFlash('success', 'Class updated successfully.');
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => 'Failed to update class.'
        ];
    }

    /**
     * Delete class
     * @param int $id
     * @return array Result with success status
     */
    public function destroy($id) {
        $this->requireRole('office_admin');

        // Check if class has students
        $class = $this->classModel->find($id);
        if ($class && $class['student_count'] > 0) {
            return [
                'success' => false,
                'message' => 'Cannot delete class with students. Move or delete students first.'
            ];
        }

        $success = $this->classModel->delete($id);

        if ($success) {
            $this->setFlash('success', 'Class deleted successfully.');
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => 'Failed to delete class.'
        ];
    }

    /**
     * Get all grades
     * @return array
     */
    public function getGrades() {
        return $this->classModel->getGrades();
    }

    /**
     * Get class display name
     * @param int $classId
     * @return string
     */
    public function getDisplayName($classId) {
        return $this->classModel->getDisplayName($classId);
    }
}

// Handle direct requests
if (basename($_SERVER['PHP_SELF']) === 'ClassController.php') {
    $controller = new ClassController();
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
