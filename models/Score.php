<?php
require_once __DIR__ . '/Model.php';

// score model
// handles the grades and stuff
// also tracks who changed what (audit trail)
class Score extends Model {
    protected $table = 'scores';
    protected $primaryKey = 'score_id';

    /**
     * Get allowed fields for this model (including audit fields)
     */
    protected function getAllowedFields() {
        return ['student_id', 'subject_id', 'term_id', 'score', 'created_by', 'modified_by'];
    }

    // get all the scores
    // took forever to write this join query...
    // it gets student name, subject, term, and who modified it
    public function getAll() {
        $sql = "SELECT sc.*, 
                CONCAT(st.first_name, ' ', st.last_name) as student_name,
                sub.subject_name,
                t.term_name,
                sy.year_name,
                g.grade_name,
                c.class_name,
                creator.username as created_by_username,
                modifier.username as modified_by_username
                FROM scores sc
                JOIN students st ON sc.student_id = st.student_id
                JOIN classes c ON st.class_id = c.class_id
                JOIN grades g ON c.grade_id = g.grade_id
                JOIN subjects sub ON sc.subject_id = sub.subject_id
                JOIN terms t ON sc.term_id = t.term_id
                JOIN school_years sy ON t.school_year_id = sy.school_year_id
                LEFT JOIN users creator ON sc.created_by = creator.user_id
                LEFT JOIN users modifier ON sc.modified_by = modifier.user_id
                ORDER BY sy.year_name DESC, t.term_name, st.last_name";
        return $this->query($sql);
    }

    // find scores for a specific student
    // useful for the student profile page
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

    // find scores for a subject (e.g. math)
    // teachers use this i think
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

    // this finds just one specific score
    // like "what did david get in math term 1?"
    public function getByStudentSubjectTerm($studentId, $subjectId, $termId) {
        $sql = "SELECT * FROM scores 
                WHERE student_id = ? AND subject_id = ? AND term_id = ?";
        return $this->queryOne($sql, [$studentId, $subjectId, $termId]);
    }

    // save or update score
    // if it exists update it, otherwise make a new one (upsert)
    // also saves who did it for the audit thing
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

    // calculates the average score for a student
    // i used the SQL AVG() function cause its easier
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

    // figures out the letter grade (A, B, C...)
    // just a bunch of if statements
    public function getLetterGrade($score) {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    // checks if the score is valid
    // makes sure its between 0 and 100
    // and ALSO makes sure the student's class actually takes that subject
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

        // Check if subject is valid for student's class
        if (empty($errors)) {
            if (!$this->isSubjectValidForStudent($data['student_id'], $data['subject_id'])) {
                $errors['subject_id'] = 'This subject is not assigned to the student\'s class.';
            }
        }

        return $errors;
    }

    // checks if a subject is assigned to a student's grade
    public function isSubjectValidForStudent($studentId, $subjectId) {
        $sql = "SELECT COUNT(*) as count
                FROM students s
                JOIN classes c ON s.class_id = c.class_id
                JOIN subjects sub ON c.grade_id = sub.grade_id
                WHERE s.student_id = ? AND sub.subject_id = ?";
        $result = $this->queryOne($sql, [$studentId, $subjectId]);
        return ($result['count'] ?? 0) > 0;
    }

    // gets all subjects for a student's grade (via their class)
    public function getSubjectsForStudent($studentId) {
        $sql = "SELECT sub.*
                FROM students s
                JOIN classes c ON s.class_id = c.class_id
                JOIN subjects sub ON c.grade_id = sub.grade_id
                WHERE s.student_id = ?
                ORDER BY sub.subject_name";
        return $this->query($sql, [$studentId]);
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

    /**
     * Get class statistics for a grade (student count and average per class)
     * @param int $gradeId
     * @param int|null $termId
     * @return array
     */
    public function getClassStatsByGrade($gradeId, $termId = null) {
        $params = [$gradeId];

        $sql = "SELECT c.class_id, c.class_name,
                COUNT(DISTINCT s.student_id) as student_count,
                COALESCE(ROUND(AVG(sc.score), 1), 0) as class_avg
                FROM classes c
                LEFT JOIN students s ON c.class_id = s.class_id
                LEFT JOIN scores sc ON s.student_id = sc.student_id";

        if ($termId !== null) {
            $sql .= " AND sc.term_id = ?";
            $params[] = $termId;
        }

        $sql .= " WHERE c.grade_id = ?
                GROUP BY c.class_id, c.class_name
                ORDER BY c.class_name";

        // Fix param order: termId first (if exists), then gradeId
        if ($termId !== null) {
            $params = [$termId, $gradeId];
        }

        return $this->query($sql, $params);
    }

    /**
     * Get total student count for a grade
     * @param int $gradeId
     * @return int
     */
    public function getStudentCountByGrade($gradeId) {
        $sql = "SELECT COUNT(*) as count
                FROM students s
                JOIN classes c ON s.class_id = c.class_id
                WHERE c.grade_id = ?";
        $result = $this->queryOne($sql, [$gradeId]);
        return (int)($result['count'] ?? 0);
    }
}
