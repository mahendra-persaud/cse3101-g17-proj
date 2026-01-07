<?php
require_once __DIR__ . '/Model.php';

/**
 * Score Model
 * Handles all score/grade-related database operations
 */
class Score extends Model {
    protected $table = 'scores';

    /**
     * Get sample data for testing
     */
    protected function getSampleData() {
        if (isset($_SESSION[$this->table]) && !empty($_SESSION[$this->table])) {
            return $_SESSION[$this->table];
        }

        return [
            ['id' => 1, 'student_id' => 1, 'subject_id' => 1, 'term' => 1, 'score' => 92, 'grade' => 'A', 'remarks' => 'Excellent performance'],
            ['id' => 2, 'student_id' => 1, 'subject_id' => 2, 'term' => 1, 'score' => 89, 'grade' => 'B+', 'remarks' => 'Very good work'],
            ['id' => 3, 'student_id' => 2, 'subject_id' => 1, 'term' => 1, 'score' => 78, 'grade' => 'C+', 'remarks' => 'Good effort'],
            ['id' => 4, 'student_id' => 2, 'subject_id' => 2, 'term' => 1, 'score' => 88, 'grade' => 'B+', 'remarks' => 'Well done'],
        ];
    }

    /**
     * Get scores by student
     * @param int $studentId
     * @param int|null $term
     * @return array
     */
    public function getByStudent($studentId, $term = null) {
        $scores = $this->findBy('student_id', $studentId);

        if ($term !== null) {
            $scores = array_filter($scores, function($score) use ($term) {
                return $score['term'] == $term;
            });
        }

        return array_values($scores);
    }

    /**
     * Get scores by subject
     * @param int $subjectId
     * @param int|null $term
     * @return array
     */
    public function getBySubject($subjectId, $term = null) {
        $scores = $this->findBy('subject_id', $subjectId);

        if ($term !== null) {
            $scores = array_filter($scores, function($score) use ($term) {
                return $score['term'] == $term;
            });
        }

        return array_values($scores);
    }

    /**
     * Get score for specific student, subject, and term
     * @param int $studentId
     * @param int $subjectId
     * @param int $term
     * @return array|null
     */
    public function getByStudentSubjectTerm($studentId, $subjectId, $term) {
        $scores = $this->getAll();

        foreach ($scores as $score) {
            if ($score['student_id'] == $studentId &&
                $score['subject_id'] == $subjectId &&
                $score['term'] == $term) {
                return $score;
            }
        }

        return null;
    }

    /**
     * Calculate average for student
     * @param int $studentId
     * @param int|null $term
     * @return float
     */
    public function calculateStudentAverage($studentId, $term = null) {
        $scores = $this->getByStudent($studentId, $term);

        if (empty($scores)) {
            return 0;
        }

        $total = array_sum(array_column($scores, 'score'));
        return round($total / count($scores), 2);
    }

    /**
     * Calculate average for subject
     * @param int $subjectId
     * @param int|null $term
     * @return float
     */
    public function calculateSubjectAverage($subjectId, $term = null) {
        $scores = $this->getBySubject($subjectId, $term);

        if (empty($scores)) {
            return 0;
        }

        $total = array_sum(array_column($scores, 'score'));
        return round($total / count($scores), 2);
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
            'term' => 'required|numeric',
            'score' => 'required|numeric'
        ]);

        // Additional validation for score range
        if (isset($data['score']) && ($data['score'] < 0 || $data['score'] > 100)) {
            $errors['score'] = 'Score must be between 0 and 100';
        }

        return $errors;
    }
}
