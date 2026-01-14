<?php
require_once __DIR__ . '/Model.php';

/**
 * Subject Model
 * Handles all subject-related database operations
 */
class Subject extends Model {
    protected $table = 'subjects';
    protected $primaryKey = 'subject_id';

    /**
     * Get allowed fields for this model
     */
    protected function getAllowedFields() {
        return ['subject_name', 'grade_id'];
    }

    /**
     * Get all subjects with grade information
     * @return array
     */
    public function getAll() {
        $sql = "SELECT s.*, g.grade_name 
                FROM subjects s 
                JOIN grades g ON s.grade_id = g.grade_id 
                ORDER BY g.grade_id, s.subject_name";
        return $this->query($sql);
    }

    /**
     * Get subjects by grade ID
     * @param int $gradeId
     * @return array
     */
    public function getByGrade($gradeId) {
        $sql = "SELECT s.*, g.grade_name 
                FROM subjects s 
                JOIN grades g ON s.grade_id = g.grade_id 
                WHERE s.grade_id = ? 
                ORDER BY s.subject_name";
        return $this->query($sql, [$gradeId]);
    }

    /**
     * Find subject by ID with grade info
     * @param int $id
     * @return array|null
     */
    public function find($id) {
        $sql = "SELECT s.*, g.grade_name 
                FROM subjects s 
                JOIN grades g ON s.grade_id = g.grade_id 
                WHERE s.subject_id = ?";
        return $this->queryOne($sql, [$id]);
    }

    /**
     * Validate subject data
     * @param array $data
     * @return array
     */
    public function validateSubject($data) {
        return $this->validate($data, [
            'subject_name' => 'required|min:3|max:50',
            'grade_id' => 'required|numeric'
        ]);
    }

    /**
     * Check if subject name already exists for grade
     * @param string $name
     * @param int $gradeId
     * @param int|null $excludeId
     * @return bool
     */
    public function nameExistsForGrade($name, $gradeId, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM subjects 
                WHERE subject_name = ? AND grade_id = ?";
        $params = [$name, $gradeId];
        
        if ($excludeId !== null) {
            $sql .= " AND subject_id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->queryOne($sql, $params);
        return ($result['count'] ?? 0) > 0;
    }
}
