<?php
require_once __DIR__ . '/Model.php';

/**
 * Student Model
 * Handles all student-related database operations
 */
class Student extends Model {
    protected $table = 'students';
    protected $primaryKey = 'student_id';

    /**
     * Get allowed fields for this model
     */
    protected function getAllowedFields() {
        return ['first_name', 'last_name', 'class_id'];
    }

    /**
     * Get all students with class and grade information
     * @return array
     */
    public function getAll() {
        $sql = "SELECT s.*, c.class_name, g.grade_name, g.grade_id,
                CONCAT(s.first_name, ' ', s.last_name) as full_name
                FROM students s 
                JOIN classes c ON s.class_id = c.class_id 
                JOIN grades g ON c.grade_id = g.grade_id 
                ORDER BY g.grade_id, c.class_name, s.last_name, s.first_name";
        return $this->query($sql);
    }

    /**
     * Find student by ID with class and grade info
     * @param int $id
     * @return array|null
     */
    public function find($id) {
        $sql = "SELECT s.*, c.class_name, g.grade_name, g.grade_id,
                CONCAT(s.first_name, ' ', s.last_name) as full_name
                FROM students s 
                JOIN classes c ON s.class_id = c.class_id 
                JOIN grades g ON c.grade_id = g.grade_id 
                WHERE s.student_id = ?";
        return $this->queryOne($sql, [$id]);
    }

    /**
     * Get students by grade
     * @param int $gradeId
     * @return array
     */
    public function getByGrade($gradeId) {
        $sql = "SELECT s.*, c.class_name, g.grade_name,
                CONCAT(s.first_name, ' ', s.last_name) as full_name
                FROM students s 
                JOIN classes c ON s.class_id = c.class_id 
                JOIN grades g ON c.grade_id = g.grade_id 
                WHERE g.grade_id = ? 
                ORDER BY c.class_name, s.last_name, s.first_name";
        return $this->query($sql, [$gradeId]);
    }

    /**
     * Get students by class
     * @param int $classId
     * @return array
     */
    public function getByClass($classId) {
        $sql = "SELECT s.*, c.class_name, g.grade_name,
                CONCAT(s.first_name, ' ', s.last_name) as full_name
                FROM students s 
                JOIN classes c ON s.class_id = c.class_id 
                JOIN grades g ON c.grade_id = g.grade_id 
                WHERE s.class_id = ? 
                ORDER BY s.last_name, s.first_name";
        return $this->query($sql, [$classId]);
    }

    /**
     * Get students count by class
     * @param int $classId
     * @return int
     */
    public function countByClass($classId) {
        return $this->count('class_id', $classId);
    }

    /**
     * Validate student data
     * @param array $data
     * @return array
     */
    public function validateStudent($data) {
        return $this->validate($data, [
            'first_name' => 'required|min:2|max:50',
            'last_name' => 'required|min:2|max:50',
            'class_id' => 'required|numeric'
        ]);
    }

    /**
     * Search students by name
     * @param string $search
     * @return array
     */
    public function search($search) {
        $sql = "SELECT s.*, c.class_name, g.grade_name,
                CONCAT(s.first_name, ' ', s.last_name) as full_name
                FROM students s 
                JOIN classes c ON s.class_id = c.class_id 
                JOIN grades g ON c.grade_id = g.grade_id 
                WHERE s.first_name LIKE ? OR s.last_name LIKE ? 
                ORDER BY s.last_name, s.first_name";
        $searchTerm = "%{$search}%";
        return $this->query($sql, [$searchTerm, $searchTerm]);
    }
}
