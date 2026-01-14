<?php
/**
 * User Controller
 * Handles user management (Office Admin only)
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';

class UserController extends BaseController {
    
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Get all users
     * @return array
     */
    public function index() {
        $this->requireRole('office_admin');
        return $this->userModel->getAll();
    }

    /**
     * Get single user
     * @param int $id
     * @return array|null
     */
    public function show($id) {
        $this->requireRole('office_admin');
        return $this->userModel->find($id);
    }

    /**
     * Get users by role
     * @param int $roleId
     * @return array
     */
    public function getByRole($roleId) {
        $this->requireRole('office_admin');
        return $this->userModel->getByRole($roleId);
    }

    /**
     * Create new user
     * @param array $data
     * @return array Result with success status
     */
    public function store($data) {
        $this->requireRole('office_admin');

        // Validate
        $errors = $this->userModel->validateUser($data);
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        // Check for duplicate username
        if ($this->userModel->usernameExists($data['username'])) {
            return [
                'success' => false,
                'message' => 'Username already exists.'
            ];
        }

        // Create user
        $id = $this->userModel->createUser([
            'username' => $data['username'],
            'password' => $data['password'],
            'role_id' => $data['role_id']
        ]);

        if ($id) {
            $this->setFlash('success', 'User created successfully.');
            return [
                'success' => true,
                'id' => $id
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to create user.'
        ];
    }

    /**
     * Update user
     * @param int $id
     * @param array $data
     * @return array Result with success status
     */
    public function update($id, $data) {
        $this->requireRole('office_admin');

        // Validate
        $errors = $this->userModel->validateUser($data, true);
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        // Check for duplicate username
        if (isset($data['username']) && $this->userModel->usernameExists($data['username'], $id)) {
            return [
                'success' => false,
                'message' => 'Username already exists.'
            ];
        }

        // Build update data
        $updateData = [
            'username' => $data['username'],
            'role_id' => $data['role_id']
        ];

        // Update user
        $success = $this->userModel->update($id, $updateData);

        // Update password if provided
        if ($success && !empty($data['password'])) {
            $this->userModel->updatePassword($id, $data['password']);
        }

        if ($success) {
            $this->setFlash('success', 'User updated successfully.');
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => 'Failed to update user.'
        ];
    }

    /**
     * Delete user
     * @param int $id
     * @return array Result with success status
     */
    public function destroy($id) {
        $this->requireRole('office_admin');

        // Prevent deleting own account
        if ($id == $this->getUserId()) {
            return [
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ];
        }

        $success = $this->userModel->delete($id);

        if ($success) {
            $this->setFlash('success', 'User deleted successfully.');
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => 'Failed to delete user. User may have associated data.'
        ];
    }

    /**
     * Get all roles
     * @return array
     */
    public function getRoles() {
        return $this->userModel->getRoles();
    }

    /**
     * Reset user password
     * @param int $id
     * @param string $newPassword
     * @return array
     */
    public function resetPassword($id, $newPassword) {
        $this->requireRole('office_admin');

        if (strlen($newPassword) < 6) {
            return [
                'success' => false,
                'message' => 'Password must be at least 6 characters.'
            ];
        }

        $success = $this->userModel->updatePassword($id, $newPassword);

        if ($success) {
            $this->setFlash('success', 'Password reset successfully.');
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => 'Failed to reset password.'
        ];
    }
}

// Handle direct requests
if (basename($_SERVER['PHP_SELF']) === 'UserController.php') {
    $controller = new UserController();
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

        case 'by-role':
            $roleId = $_GET['role_id'] ?? null;
            if ($roleId) {
                $controller->json($controller->getByRole($roleId));
            }
            break;

        case 'roles':
            $controller->json($controller->getRoles());
            break;

        case 'reset-password':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
                $result = $controller->resetPassword($id, $_POST['password'] ?? '');
                $controller->json($result);
            }
            break;
    }
}
