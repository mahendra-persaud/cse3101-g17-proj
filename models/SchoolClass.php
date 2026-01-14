<?php
require_once __DIR__ . '/Model.php';

/**
 * SchoolClass Model (named SchoolClass to avoid PHP reserved word 'Class')
 * Handles all class-related database operations
 */
class SchoolClass extends Model {
    protected $table = 'classes';
    protected $primaryKey = 'class_id';

    /**
     * Get allowed fields for this model
     */
    protected function getAllowedFields() {
        return ['class_name', 'grade_id'];
    }

    /**
     * Get all classes with grade information and student count
     * @return array
     */
    public function getAll() {
        $sql = "SELECT c.*, g.grade_name,
                (SELECT COUNT(*) FROM students s WHERE s.class_id = c.class_id) as student_count
                FROM classes c 
                JOIN grades g ON c.grade_id = g.grade_id 
                ORDER BY g.grade_id, c.class_name";
        return $this->query($sql);
    }

    /**
     * Find class by ID with grade info
     * @param int $id
     * @return array|null
     */
    public function find($id) {
        $sql = "SELECT c.*, g.grade_name,
                (SELECT COUNT(*) FROM students s WHERE s.class_id = c.class_id) as student_count
                FROM classes c 
                JOIN grades g ON c.grade_id = g.grade_id 
                WHERE c.class_id = ?";
        return $this->queryOne($sql, [$id]);
    }

    /**
     * Get classes by grade
     * @param int $gradeId
     * @return array
     */
    public function getByGrade($gradeId) {
        $sql = "SELECT c.*, g.grade_name,
                (SELECT COUNT(*) FROM students s WHERE s.class_id = c.class_id) as student_count
                FROM classes c 
                JOIN grades g ON c.grade_id = g.grade_id 
                WHERE c.grade_id = ? 
                ORDER BY c.class_name";
        return $this->query($sql, [$gradeId]);
    }

    /**
     * Get class by grade and name
     * @param int $gradeId
     * @param string $name
     * @return array|null
     */
    public function getByGradeAndName($gradeId, $name) {
        $sql = "SELECT c.*, g.grade_name 
                FROM classes c 
                JOIN grades g ON c.grade_id = g.grade_id 
                WHERE c.grade_id = ? AND c.class_name = ?";
        return $this->queryOne($sql, [$gradeId, $name]);
    }

    /**
     * Get all grades
     * @return array
     */
    public function getGrades() {
        $sql = "SELECT * FROM grades ORDER BY grade_id";
        return $this->query($sql);
    }

    /**
     * Check if class name exists for grade
     * @param string $name
     * @param int $gradeId
     * @param int|null $excludeId
     * @return bool
     */
    public function nameExistsForGrade($name, $gradeId, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM classes 
                WHERE class_name = ? AND grade_id = ?";
        $params = [$name, $gradeId];
        
        if ($excludeId !== null) {
            $sql .= " AND class_id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->queryOne($sql, $params);
        return ($result['count'] ?? 0) > 0;
    }

    /**
     * Validate class data
     * @param array $data
     * @return array
     */
    public function validateClass($data) {
        return $this->validate($data, [
            'class_name' => 'required|min:1|max:2',
            'grade_id' => 'required|numeric'
        ]);
    }

    /**
     * Get class display name (e.g., "Grade 1 - Class A")
     * @param int $classId
     * @return string
     */
    public function getDisplayName($classId) {
        $class = $this->find($classId);
        if ($class) {
            return $class['grade_name'] . ' - Class ' . $class['class_name'];
        }
        return '';
    }
}
