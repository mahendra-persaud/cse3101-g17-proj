<?php
require_once __DIR__ . '/Model.php';

/**
 * Teacher Model
 * Handles teacher data and assignments (subjects and classes)
 */
class Teacher extends Model {
    protected $table = 'teachers';
    protected $primaryKey = 'teacher_id';

    protected function getAllowedFields() {
        return ['user_id', 'first_name', 'last_name', 'email'];
    }

    /**
     * Get all teachers with user info
     * @return array
     */
    public function getAll() {
        $sql = "SELECT t.*, u.username, r.role_name
                FROM teachers t
                JOIN users u ON t.user_id = u.user_id
                JOIN roles r ON u.role_id = r.role_id
                ORDER BY t.last_name, t.first_name";
        return $this->query($sql);
    }

    /**
     * Find teacher by ID with full details
     * @param int $id
     * @return array|null
     */
    public function find($id) {
        $sql = "SELECT t.*, u.username, u.user_id
                FROM teachers t
                JOIN users u ON t.user_id = u.user_id
                WHERE t.teacher_id = ?";
        return $this->queryOne($sql, [$id]);
    }

    /**
     * Find teacher by user ID
     * @param int $userId
     * @return array|null
     */
    public function findByUserId($userId) {
        $sql = "SELECT t.*, u.username
                FROM teachers t
                JOIN users u ON t.user_id = u.user_id
                WHERE t.user_id = ?";
        return $this->queryOne($sql, [$userId]);
    }

    /**
     * Get subjects assigned to a teacher
     * @param int $teacherId
     * @return array
     */
    public function getSubjects($teacherId) {
        $sql = "SELECT s.*, g.grade_name
                FROM subjects s
                JOIN teacher_subjects ts ON s.subject_id = ts.subject_id
                JOIN grades g ON s.grade_id = g.grade_id
                WHERE ts.teacher_id = ?
                ORDER BY g.grade_id, s.subject_name";
        return $this->query($sql, [$teacherId]);
    }

    /**
     * Get all available subjects (for assignment dropdown)
     * @return array
     */
    public function getAllSubjects() {
        $sql = "SELECT s.*, g.grade_name
                FROM subjects s
                JOIN grades g ON s.grade_id = g.grade_id
                ORDER BY g.grade_id, s.subject_name";
        return $this->query($sql);
    }

    /**
     * Assign subjects to a teacher (replace all existing assignments)
     * @param int $teacherId
     * @param array $subjectIds
     * @return bool
     */
    public function syncSubjects($teacherId, $subjectIds) {
        $pdo = $this->getConnection();
        $pdo->beginTransaction();

        try {
            // Remove existing assignments
            $sqlDelete = "DELETE FROM teacher_subjects WHERE teacher_id = ?";
            $stmtDelete = $pdo->prepare($sqlDelete);
            $stmtDelete->execute([$teacherId]);

            // Add new assignments
            if (!empty($subjectIds)) {
                $sqlInsert = "INSERT INTO teacher_subjects (teacher_id, subject_id) VALUES (?, ?)";
                $stmtInsert = $pdo->prepare($sqlInsert);

                foreach ($subjectIds as $subjectId) {
                    $stmtInsert->execute([$teacherId, $subjectId]);
                }
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    /**
     * Get classes assigned to a teacher
     * @param int $teacherId
     * @return array
     */
    public function getClasses($teacherId) {
        $sql = "SELECT c.*, g.grade_name
                FROM classes c
                JOIN teacher_classes tc ON c.class_id = tc.class_id
                JOIN grades g ON c.grade_id = g.grade_id
                WHERE tc.teacher_id = ?
                ORDER BY g.grade_id, c.class_name";
        return $this->query($sql, [$teacherId]);
    }

    /**
     * Get all classes (for assignment dropdown)
     * @return array
     */
    public function getAllClasses() {
        $sql = "SELECT c.*, g.grade_name,
                (SELECT COUNT(*) FROM students s WHERE s.class_id = c.class_id) as student_count
                FROM classes c
                JOIN grades g ON c.grade_id = g.grade_id
                ORDER BY g.grade_id, c.class_name";
        return $this->query($sql);
    }

    /**
     * Assign classes to a teacher (replace all existing assignments)
     * @param int $teacherId
     * @param array $classIds
     * @return bool
     */
    public function syncClasses($teacherId, $classIds) {
        $pdo = $this->getConnection();
        $pdo->beginTransaction();

        try {
            // Remove existing assignments
            $sqlDelete = "DELETE FROM teacher_classes WHERE teacher_id = ?";
            $stmtDelete = $pdo->prepare($sqlDelete);
            $stmtDelete->execute([$teacherId]);

            // Add new assignments
            if (!empty($classIds)) {
                $sqlInsert = "INSERT INTO teacher_classes (teacher_id, class_id) VALUES (?, ?)";
                $stmtInsert = $pdo->prepare($sqlInsert);

                foreach ($classIds as $classId) {
                    $stmtInsert->execute([$teacherId, $classId]);
                }
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    /**
     * Get teacher's full name
     * @param int $teacherId
     * @return string
     */
    public function getFullName($teacherId) {
        $teacher = $this->find($teacherId);
        if ($teacher) {
            return $teacher['first_name'] . ' ' . $teacher['last_name'];
        }
        return '';
    }

    /**
     * Get summary of teacher assignments
     * @param int $teacherId
     * @return array
     */
    public function getAssignmentSummary($teacherId) {
        $subjects = $this->getSubjects($teacherId);
        $classes = $this->getClasses($teacherId);

        return [
            'subject_count' => count($subjects),
            'class_count' => count($classes),
            'subjects' => $subjects,
            'classes' => $classes
        ];
    }

    /**
     * Check if teacher exists
     * @param int $teacherId
     * @return bool
     */
    public function exists($teacherId) {
        $sql = "SELECT COUNT(*) as count FROM teachers WHERE teacher_id = ?";
        $result = $this->queryOne($sql, [$teacherId]);
        return ($result['count'] ?? 0) > 0;
    }

    /**
     * Create a new teacher (also creates user account)
     * @param array $data
     * @param string $password
     * @return int|false Teacher ID on success, false on failure
     */
    public function createTeacher($data, $password) {
        $pdo = $this->getConnection();
        $pdo->beginTransaction();

        try {
            // Create user account first
            $username = strtolower($data['first_name']) . '_' . strtolower($data['last_name']);
            $username = preg_replace('/[^a-z_]/', '', $username);

            // Make username unique if needed
            $baseUsername = $username;
            $counter = 1;
            while ($this->usernameExists($username)) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            $sqlUser = "INSERT INTO users (username, password_hash, role_id) VALUES (?, ?, 2)";
            $stmtUser = $pdo->prepare($sqlUser);
            $stmtUser->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
            $userId = $pdo->lastInsertId();

            // Create teacher record
            $sqlTeacher = "INSERT INTO teachers (user_id, first_name, last_name, email) VALUES (?, ?, ?, ?)";
            $stmtTeacher = $pdo->prepare($sqlTeacher);
            $stmtTeacher->execute([$userId, $data['first_name'], $data['last_name'], $data['email']]);
            $teacherId = $pdo->lastInsertId();

            $pdo->commit();
            return $teacherId;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    /**
     * Check if username exists
     * @param string $username
     * @return bool
     */
    private function usernameExists($username) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = ?";
        $result = $this->queryOne($sql, [$username]);
        return ($result['count'] ?? 0) > 0;
    }

    /**
     * Check if email exists
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM teachers WHERE email = ?";
        $params = [$email];

        if ($excludeId !== null) {
            $sql .= " AND teacher_id != ?";
            $params[] = $excludeId;
        }

        $result = $this->queryOne($sql, $params);
        return ($result['count'] ?? 0) > 0;
    }

    /**
     * Update teacher info
     * @param int $teacherId
     * @param array $data
     * @return bool
     */
    public function updateTeacher($teacherId, $data) {
        $sql = "UPDATE teachers SET first_name = ?, last_name = ?, email = ? WHERE teacher_id = ?";
        try {
            $pdo = $this->getConnection();
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$data['first_name'], $data['last_name'], $data['email'], $teacherId]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Validate teacher data
     * @param array $data
     * @param int|null $excludeId
     * @return array Errors
     */
    public function validateTeacher($data, $excludeId = null) {
        $errors = [];

        if (empty($data['first_name'])) {
            $errors['first_name'] = 'First name is required';
        } elseif (strlen($data['first_name']) > 20) {
            $errors['first_name'] = 'First name must be 20 characters or less';
        }

        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Last name is required';
        } elseif (strlen($data['last_name']) > 20) {
            $errors['last_name'] = 'Last name must be 20 characters or less';
        }

        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } elseif ($this->emailExists($data['email'], $excludeId)) {
            $errors['email'] = 'This email is already in use';
        }

        return $errors;
    }
}
