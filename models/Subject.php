<?php
require_once __DIR__ . '/Model.php';

/**
 * Subject Model
 * Handles all subject-related database operations
 */
class Subject extends Model {
    protected $table = 'subjects';

    /**
     * Get sample data for testing (will be replaced with database queries)
     */
    protected function getSampleData() {
        // Check if session data exists first
        if (isset($_SESSION[$this->table]) && !empty($_SESSION[$this->table])) {
            return $_SESSION[$this->table];
        }

        // Return default sample data
        return [
            ['id' => 1, 'code' => 'MATH', 'name' => 'Mathematics', 'grade' => 'All Grades', 'description' => 'Core mathematics curriculum'],
            ['id' => 2, 'code' => 'ENG', 'name' => 'English Language', 'grade' => 'All Grades', 'description' => 'Reading, writing, grammar and comprehension'],
            ['id' => 3, 'code' => 'SCI', 'name' => 'Science', 'grade' => 'All Grades', 'description' => 'General science and scientific inquiry'],
            ['id' => 4, 'code' => 'SOC', 'name' => 'Social Studies', 'grade' => 'All Grades', 'description' => 'History, geography, and civics'],
            ['id' => 5, 'code' => 'HFLE', 'name' => 'Health and Family Life Education', 'grade' => 'All Grades', 'description' => 'Health, wellness, and life skills'],
            ['id' => 6, 'code' => 'PE', 'name' => 'Physical Education', 'grade' => 'All Grades', 'description' => 'Sports, fitness, and physical activities'],
            ['id' => 7, 'code' => 'MUS', 'name' => 'Music', 'grade' => 'All Grades', 'description' => 'Music theory and practice'],
            ['id' => 8, 'code' => 'ART', 'name' => 'Arts and Crafts', 'grade' => 'All Grades', 'description' => 'Visual arts, drawing, and crafts'],
            ['id' => 9, 'code' => 'SPAN', 'name' => 'Spanish', 'grade' => 'Grades 4-6', 'description' => 'Spanish language instruction'],
            ['id' => 10, 'code' => 'ICT', 'name' => 'Information Technology', 'grade' => 'Grades 4-6', 'description' => 'Computer literacy and technology'],
            ['id' => 11, 'code' => 'REL', 'name' => 'Religious Education', 'grade' => 'All Grades', 'description' => 'Moral and religious studies'],
        ];
    }

    /**
     * Get subjects by grade
     * @param string $grade
     * @return array
     */
    public function getByGrade($grade) {
        $subjects = $this->getAll();
        $filtered = [];

        foreach ($subjects as $subject) {
            if ($subject['grade'] === 'All Grades' || $subject['grade'] === $grade) {
                $filtered[] = $subject;
            }
        }

        return $filtered;
    }

    /**
     * Validate subject data
     * @param array $data
     * @return array
     */
    public function validateSubject($data) {
        return $this->validate($data, [
            'code' => 'required|min:2|max:10',
            'name' => 'required|min:3|max:100',
            'grade' => 'required',
            'description' => 'required|min:10|max:255'
        ]);
    }

    /**
     * Check if subject code already exists
     * @param string $code
     * @param int|null $excludeId
     * @return bool
     */
    public function codeExists($code, $excludeId = null) {
        $subjects = $this->getAll();

        foreach ($subjects as $subject) {
            if ($subject['code'] === $code && $subject['id'] != $excludeId) {
                return true;
            }
        }

        return false;
    }
}
