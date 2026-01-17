<?php
// class controller
// manages classes (like grade 1, grade 2 etc)

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/SchoolClass.php';

class ClassController extends BaseController {
    
    private $classModel;

    public function __construct() {
        $this->classModel = new SchoolClass();
    }

    // get list of all classes
    public function index() {
        return $this->classModel->getAll();
    }

    // get one class by id
    public function show($id) {
        return $this->classModel->find($id);
    }

    // get classes for a certain grade
    public function getByGrade($gradeId) {
        return $this->classModel->getByGrade($gradeId);
    }

    // make a new class
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

        // make sure we don't have duplicates
        if ($this->classModel->nameExistsForGrade($data['class_name'], $data['grade_id'])) {
            return [
                'success' => false,
                'message' => 'Class already exists for this grade.'
            ];
        }

        // limit to 6 classes per grade (assignment rule)
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

    // update class info
    // cant change the grade though, only the name
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

        // stop them from changing the grade
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

        // fix the class name
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

    // delete a class
    // cant delete if it has students
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

    // info for dropdowns
    public function getGrades() {
        return $this->classModel->getGrades();
    }

    // formats the name nicely
    public function getDisplayName($classId) {
        return $this->classModel->getDisplayName($classId);
    }

    // assign a teacher to a class
    public function assignTeacher($classId, $teacherId) {
        $this->requireRole('office_admin');

        $success = $this->classModel->assignTeacher($classId, $teacherId);

        if ($success) {
            $this->setFlash('success', 'Teacher assigned successfully.');
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => 'Failed to assign teacher.'
        ];
    }

    // get teacher for a class
    public function getTeacher($classId) {
        return $this->classModel->getTeacher($classId);
    }

    // get all teachers
    public function getAllTeachers() {
        return $this->classModel->getAllTeachers();
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

        case 'assign-teacher':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $classId = $_POST['class_id'] ?? null;
                $teacherId = $_POST['teacher_id'] ?? null;
                if ($classId) {
                    $result = $controller->assignTeacher($classId, $teacherId);
                    $controller->json($result);
                }
            }
            break;

        case 'get-teacher':
            if ($id) {
                $controller->json($controller->getTeacher($id));
            }
            break;

        case 'teachers':
            $controller->json($controller->getAllTeachers());
            break;
    }
}
