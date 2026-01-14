<?php
require_once __DIR__ . '/Model.php';

// subject model
// for subjects like math, english, etc
class Subject extends Model {
    protected $table = 'subjects';
    protected $primaryKey = 'subject_id';

    // allowed fields
    protected function getAllowedFields() {
        return ['subject_name', 'grade_id'];
    }

    // get all subjects with their grade level
    public function getAll() {
        $sql = "SELECT s.*, g.grade_name 
                FROM subjects s 
                JOIN grades g ON s.grade_id = g.grade_id 
                ORDER BY g.grade_id, s.subject_name";
        return $this->query($sql);
    }

    // get subjects for a specific grade
    public function getByGrade($gradeId) {
        $sql = "SELECT s.*, g.grade_name 
                FROM subjects s 
                JOIN grades g ON s.grade_id = g.grade_id 
                WHERE s.grade_id = ? 
                ORDER BY s.subject_name";
        return $this->query($sql, [$gradeId]);
    }

    // find subject by id
    public function find($id) {
        $sql = "SELECT s.*, g.grade_name 
                FROM subjects s 
                JOIN grades g ON s.grade_id = g.grade_id 
                WHERE s.subject_id = ?";
        return $this->queryOne($sql, [$id]);
    }

    // validates subject info
    public function validateSubject($data) {
        return $this->validate($data, [
            'subject_name' => 'required|min:3|max:50',
            'grade_id' => 'required|numeric'
        ]);
    }

    // checks if we already have this subject for this grade
    // avoids duplicates
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
