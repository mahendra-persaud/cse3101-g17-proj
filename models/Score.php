<?php
require_once __DIR__ . '/Model.php';

/**
 * Score Model
 * Handles all score/grade-related database operations
 * Includes audit trail for tracking who modified scores
 */
class Score extends Model {
    protected $table = 'scores';
    protected $primaryKey = 'score_id';

    /**
     * Get allowed fields for this model (including audit fields)
     */
    protected function getAllowedFields() {
        return ['student_id', 'subject_id', 'term_id', 'score', 'created_by', 'modified_by'];
    }

    /**
     * Get all scores with related information (including audit info)
     * @return array
     */
    public function getAll() {
        $sql = "SELECT sc.*, 
                CONCAT(st.first_name, ' ', st.last_name) as student_name,
                sub.subject_name,
                t.term_name,
                sy.year_name,
                creator.username as created_by_username,
                modifier.username as modified_by_username
                FROM scores sc
                JOIN students st ON sc.student_id = st.student_id
                JOIN subjects sub ON sc.subject_id = sub.subject_id
                JOIN terms t ON sc.term_id = t.term_id
                JOIN school_years sy ON t.school_year_id = sy.school_year_id
                LEFT JOIN users creator ON sc.created_by = creator.user_id
                LEFT JOIN users modifier ON sc.modified_by = modifier.user_id
                ORDER BY sy.year_name DESC, t.term_name, st.last_name";
        return $this->query($sql);
    }

    /**
     * Get scores by student
     * @param int $studentId
     * @param int|null $termId
     * @return array
     */
    public function getByStudent($studentId, $termId = null) {
        $sql = "SELECT sc.*, 
                sub.subject_name,
                t.term_name,
                sy.year_name,
                modifier.username as modified_by_username,
                sc.modified_at
                FROM scores sc
                JOIN subjects sub ON sc.subject_id = sub.subject_id
                JOIN terms t ON sc.term_id = t.term_id
                JOIN school_years sy ON t.school_year_id = sy.school_year_id
                LEFT JOIN users modifier ON sc.modified_by = modifier.user_id
                WHERE sc.student_id = ?";
        $params = [$studentId];

        if ($termId !== null) {
            $sql .= " AND sc.term_id = ?";
            $params[] = $termId;
        }

        $sql .= " ORDER BY sub.subject_name";
        return $this->query($sql, $params);
    }

    /**
     * Get scores by subject and term
     * @param int $subjectId
     * @param int|null $termId
     * @return array
     */
    public function getBySubject($subjectId, $termId = null) {
        $sql = "SELECT sc.*, 
                CONCAT(st.first_name, ' ', st.last_name) as student_name,
                t.term_name,
                modifier.username as modified_by_username,
                sc.modified_at
                FROM scores sc
                JOIN students st ON sc.student_id = st.student_id
                JOIN terms t ON sc.term_id = t.term_id
                LEFT JOIN users modifier ON sc.modified_by = modifier.user_id
                WHERE sc.subject_id = ?";
        $params = [$subjectId];

        if ($termId !== null) {
            $sql .= " AND sc.term_id = ?";
            $params[] = $termId;
        }

        $sql .= " ORDER BY st.last_name, st.first_name";
        return $this->query($sql, $params);
    }

    /**
     * Get score for specific student, subject, and term
     * @param int $studentId
     * @param int $subjectId
     * @param int $termId
     * @return array|null
     */
    public function getByStudentSubjectTerm($studentId, $subjectId, $termId) {
        $sql = "SELECT * FROM scores 
                WHERE student_id = ? AND subject_id = ? AND term_id = ?";
        return $this->queryOne($sql, [$studentId, $subjectId, $termId]);
    }

    /**
     * Save or update score (upsert) with audit trail
     * @param int $studentId
     * @param int $subjectId
     * @param int $termId
     * @param int $score
     * @param int|null $userId The ID of the user making the change
     * @return bool
     */
    public function saveScore($studentId, $subjectId, $termId, $score, $userId = null) {
        $existing = $this->getByStudentSubjectTerm($studentId, $subjectId, $termId);
        
        if ($existing) {
            // Update existing score with audit info
            $data = ['score' => $score];
            if ($userId !== null) {
                $data['modified_by'] = $userId;
            }
            return $this->update($existing['score_id'], $data);
        } else {
            // Create new score with audit info
            $data = [
                'student_id' => $studentId,
                'subject_id' => $subjectId,
                'term_id' => $termId,
                'score' => $score
            ];
            if ($userId !== null) {
                $data['created_by'] = $userId;
                $data['modified_by'] = $userId;
            }
            return $this->create($data) !== false;
        }
    }

    /**
     * Get score audit history for a specific score
     * @param int $scoreId
     * @return array|null
     */
    public function getScoreAudit($scoreId) {
        $sql = "SELECT sc.*,
                CONCAT(st.first_name, ' ', st.last_name) as student_name,
                sub.subject_name,
                t.term_name,
                sy.year_name,
                creator.username as created_by_username,
                modifier.username as modified_by_username
                FROM scores sc
                JOIN students st ON sc.student_id = st.student_id
                JOIN subjects sub ON sc.subject_id = sub.subject_id
                JOIN terms t ON sc.term_id = t.term_id
                JOIN school_years sy ON t.school_year_id = sy.school_year_id
                LEFT JOIN users creator ON sc.created_by = creator.user_id
                LEFT JOIN users modifier ON sc.modified_by = modifier.user_id
                WHERE sc.score_id = ?";
        return $this->queryOne($sql, [$scoreId]);
    }

    /**
     * Get recent score modifications (for audit log)
     * @param int $limit
     * @return array
     */
    public function getRecentModifications($limit = 50) {
        $sql = "SELECT sc.*,
                CONCAT(st.first_name, ' ', st.last_name) as student_name,
                sub.subject_name,
                t.term_name,
                modifier.username as modified_by_username
                FROM scores sc
                JOIN students st ON sc.student_id = st.student_id
                JOIN subjects sub ON sc.subject_id = sub.subject_id
                JOIN terms t ON sc.term_id = t.term_id
                LEFT JOIN users modifier ON sc.modified_by = modifier.user_id
                WHERE sc.modified_at IS NOT NULL
                ORDER BY sc.modified_at DESC
                LIMIT ?";
        return $this->query($sql, [$limit]);
    }

    /**
     * Get scores modified by a specific user
     * @param int $userId
     * @return array
     */
    public function getByModifiedUser($userId) {
        $sql = "SELECT sc.*,
                CONCAT(st.first_name, ' ', st.last_name) as student_name,
                sub.subject_name,
                t.term_name
                FROM scores sc
                JOIN students st ON sc.student_id = st.student_id
                JOIN subjects sub ON sc.subject_id = sub.subject_id
                JOIN terms t ON sc.term_id = t.term_id
                WHERE sc.modified_by = ?
                ORDER BY sc.modified_at DESC";
        return $this->query($sql, [$userId]);
    }

    /**
     * Calculate average for student in a term
     * @param int $studentId
     * @param int|null $termId
     * @return float
     */
    public function calculateStudentAverage($studentId, $termId = null) {
        $sql = "SELECT AVG(score) as average FROM scores WHERE student_id = ?";
        $params = [$studentId];

        if ($termId !== null) {
            $sql .= " AND term_id = ?";
            $params[] = $termId;
        }

        $result = $this->queryOne($sql, $params);
        return round($result['average'] ?? 0, 2);
    }

    /**
     * Calculate average for subject in a term
     * @param int $subjectId
     * @param int|null $termId
     * @return float
     */
    public function calculateSubjectAverage($subjectId, $termId = null) {
        $sql = "SELECT AVG(score) as average FROM scores WHERE subject_id = ?";
        $params = [$subjectId];

        if ($termId !== null) {
            $sql .= " AND term_id = ?";
            $params[] = $termId;
        }

        $result = $this->queryOne($sql, $params);
        return round($result['average'] ?? 0, 2);
    }

    /**
     * Calculate average for a grade and subject
     * @param int $gradeId
     * @param int $subjectId
     * @param int|null $termId
     * @return float
     */
    public function calculateGradeSubjectAverage($gradeId, $subjectId, $termId = null) {
        $sql = "SELECT AVG(sc.score) as average 
                FROM scores sc
                JOIN students st ON sc.student_id = st.student_id
                JOIN classes c ON st.class_id = c.class_id
                WHERE c.grade_id = ? AND sc.subject_id = ?";
        $params = [$gradeId, $subjectId];

        if ($termId !== null) {
            $sql .= " AND sc.term_id = ?";
            $params[] = $termId;
        }

        $result = $this->queryOne($sql, $params);
        return round($result['average'] ?? 0, 2);
    }

    /**
     * Get student report card data
     * @param int $studentId
     * @param int $termId
     * @return array
     */
    public function getReportCard($studentId, $termId) {
        $sql = "SELECT sc.score, sub.subject_name, sub.subject_id
                FROM scores sc
                JOIN subjects sub ON sc.subject_id = sub.subject_id
                WHERE sc.student_id = ? AND sc.term_id = ?
                ORDER BY sub.subject_name";
        return $this->query($sql, [$studentId, $termId]);
    }

    /**
     * Convert numeric score to letter grade
     * @param int $score
     * @return string
     */
    public function getLetterGrade($score) {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    /**
     * Validate score data
     * @param array $data
     * @return array
     */
    public function validateScore($data) {
        $errors = $this->validate($data, [
            'student_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'term_id' => 'required|numeric',
            'score' => 'required|numeric'
        ]);

        // Additional validation for score range
        if (isset($data['score']) && ($data['score'] < 0 || $data['score'] > 100)) {
            $errors['score'] = 'Score must be between 0 and 100';
        }

        return $errors;
    }

    /**
     * Get all terms with school year info
     * @return array
     */
    public function getTerms() {
        $sql = "SELECT t.*, sy.year_name 
                FROM terms t 
                JOIN school_years sy ON t.school_year_id = sy.school_year_id 
                ORDER BY sy.year_name DESC, t.term_id";
        return $this->query($sql);
    }

    /**
     * Get current term (most recent)
     * @return array|null
     */
    public function getCurrentTerm() {
        $sql = "SELECT t.*, sy.year_name 
                FROM terms t 
                JOIN school_years sy ON t.school_year_id = sy.school_year_id 
                ORDER BY sy.school_year_id DESC, t.term_id DESC 
                LIMIT 1";
        return $this->queryOne($sql);
    }
}
