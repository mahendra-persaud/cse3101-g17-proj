<?php
/**
 * TeacherController
 * Handles teacher management and assignments
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Teacher.php';

class TeacherController extends BaseController {

    private $teacherModel;

    public function __construct() {
        $this->teacherModel = new Teacher();
    }

    /**
     * Get all teachers
     * @return array
     */
    public function index() {
        return $this->teacherModel->getAll();
    }

    /**
     * Get single teacher with details
     * @param int $id
     * @return array|null
     */
    public function show($id) {
        return $this->teacherModel->find($id);
    }

    /**
     * Get teacher's assigned subjects
     * @param int $teacherId
     * @return array
     */
    public function getSubjects($teacherId) {
        return $this->teacherModel->getSubjects($teacherId);
    }

    /**
     * Get teacher's assigned classes
     * @param int $teacherId
     * @return array
     */
    public function getClasses($teacherId) {
        return $this->teacherModel->getClasses($teacherId);
    }

    /**
     * Get all available subjects for assignment
     * @return array
     */
    public function getAllSubjects() {
        return $this->teacherModel->getAllSubjects();
    }

    /**
     * Get all available classes for assignment
     * @return array
     */
    public function getAllClasses() {
        return $this->teacherModel->getAllClasses();
    }

    /**
     * Assign subjects to teacher
     * @param int $teacherId
     * @param array $subjectIds
     * @return array
     */
    public function assignSubjects($teacherId, $subjectIds) {
        $this->requireRole('office_admin');

        if (!$this->validateCsrf()) {
            return [
                'success' => false,
                'message' => 'Invalid security token.'
            ];
        }

        if (!$this->teacherModel->exists($teacherId)) {
            return [
                'success' => false,
                'message' => 'Teacher not found.'
            ];
        }

        $success = $this->teacherModel->syncSubjects($teacherId, $subjectIds);

        if ($success) {
            return [
                'success' => true,
                'message' => 'Subjects assigned successfully.'
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to assign subjects.'
        ];
    }

    /**
     * Assign classes to teacher
     * @param int $teacherId
     * @param array $classIds
     * @return array
     */
    public function assignClasses($teacherId, $classIds) {
        $this->requireRole('office_admin');

        if (!$this->validateCsrf()) {
            return [
                'success' => false,
                'message' => 'Invalid security token.'
            ];
        }

        if (!$this->teacherModel->exists($teacherId)) {
            return [
                'success' => false,
                'message' => 'Teacher not found.'
            ];
        }

        $success = $this->teacherModel->syncClasses($teacherId, $classIds);

        if ($success) {
            return [
                'success' => true,
                'message' => 'Classes assigned successfully.'
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to assign classes.'
        ];
    }

    /**
     * Get assignment summary for a teacher
     * @param int $teacherId
     * @return array
     */
    public function getAssignmentSummary($teacherId) {
        return $this->teacherModel->getAssignmentSummary($teacherId);
    }

    /**
     * Create new teacher
     * @param array $data
     * @return array
     */
    public function store($data) {
        $this->requireRole('office_admin');

        if (!$this->validateCsrf()) {
            return [
                'success' => false,
                'message' => 'Invalid security token.'
            ];
        }

        $errors = $this->teacherModel->validateTeacher($data);
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        // Default password
        $password = $data['password'] ?? 'password123';

        $teacherId = $this->teacherModel->createTeacher($data, $password);

        if ($teacherId) {
            return [
                'success' => true,
                'message' => 'Teacher created successfully.',
                'teacher_id' => $teacherId
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to create teacher.'
        ];
    }

    /**
     * Update teacher info
     * @param int $teacherId
     * @param array $data
     * @return array
     */
    public function update($teacherId, $data) {
        $this->requireRole('office_admin');

        if (!$this->validateCsrf()) {
            return [
                'success' => false,
                'message' => 'Invalid security token.'
            ];
        }

        $errors = $this->teacherModel->validateTeacher($data, $teacherId);
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        $success = $this->teacherModel->updateTeacher($teacherId, $data);

        if ($success) {
            return [
                'success' => true,
                'message' => 'Teacher updated successfully.'
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to update teacher.'
        ];
    }
}

// Handle direct requests
if (basename($_SERVER['PHP_SELF']) === 'TeacherController.php') {
    $controller = new TeacherController();
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

        case 'subjects':
            if ($id) {
                $controller->json($controller->getSubjects($id));
            }
            break;

        case 'classes':
            if ($id) {
                $controller->json($controller->getClasses($id));
            }
            break;

        case 'all-subjects':
            $controller->json($controller->getAllSubjects());
            break;

        case 'all-classes':
            $controller->json($controller->getAllClasses());
            break;

        case 'summary':
            if ($id) {
                $controller->json($controller->getAssignmentSummary($id));
            }
            break;

        case 'assign-subjects':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
                $subjectIds = $_POST['subject_ids'] ?? [];
                $controller->json($controller->assignSubjects($id, $subjectIds));
            }
            break;

        case 'assign-classes':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
                $classIds = $_POST['class_ids'] ?? [];
                $controller->json($controller->assignClasses($id, $classIds));
            }
            break;
    }
}
