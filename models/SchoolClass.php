<?php
require_once __DIR__ . '/Model.php';

/**
 * SchoolClass Model (named SchoolClass to avoid PHP reserved word 'Class')
 * Handles all class-related database operations
 */
class SchoolClass extends Model {
    protected $table = 'classes';

    /**
     * Get sample data for testing
     */
    protected function getSampleData() {
        if (isset($_SESSION[$this->table]) && !empty($_SESSION[$this->table])) {
            return $_SESSION[$this->table];
        }

        return [
            ['id' => 1, 'name' => 'Class A', 'grade' => '1', 'teacher_id' => 2, 'teacher_name' => 'Ms. Johnson', 'room' => '101', 'capacity' => 30, 'current_students' => 26],
            ['id' => 2, 'name' => 'Class B', 'grade' => '1', 'teacher_id' => 3, 'teacher_name' => 'Mr. Williams', 'room' => '102', 'capacity' => 30, 'current_students' => 26],
            ['id' => 3, 'name' => 'Class A', 'grade' => '2', 'teacher_id' => 4, 'teacher_name' => 'Mrs. Brown', 'room' => '201', 'capacity' => 30, 'current_students' => 24],
            ['id' => 4, 'name' => 'Class B', 'grade' => '2', 'teacher_id' => 5, 'teacher_name' => 'Ms. Davis', 'room' => '202', 'capacity' => 30, 'current_students' => 24],
            ['id' => 5, 'name' => 'Class A', 'grade' => '3', 'teacher_id' => 6, 'teacher_name' => 'Mr. Miller', 'room' => '301', 'capacity' => 30, 'current_students' => 27],
            ['id' => 6, 'name' => 'Class B', 'grade' => '3', 'teacher_id' => 7, 'teacher_name' => 'Mrs. Wilson', 'room' => '302', 'capacity' => 30, 'current_students' => 28],
        ];
    }

    /**
     * Get classes by grade
     * @param string $grade
     * @return array
     */
    public function getByGrade($grade) {
        return $this->findBy('grade', $grade);
    }

    /**
     * Get classes by teacher
     * @param int $teacherId
     * @return array
     */
    public function getByTeacher($teacherId) {
        return $this->findBy('teacher_id', $teacherId);
    }

    /**
     * Check if class has available capacity
     * @param int $classId
     * @return bool
     */
    public function hasCapacity($classId) {
        $class = $this->find($classId);
        if (!$class) {
            return false;
        }

        return $class['current_students'] < $class['capacity'];
    }

    /**
     * Get class by grade and name
     * @param string $grade
     * @param string $name
     * @return array|null
     */
    public function getByGradeAndName($grade, $name) {
        $classes = $this->getAll();

        foreach ($classes as $class) {
            if ($class['grade'] === $grade && $class['name'] === $name) {
                return $class;
            }
        }

        return null;
    }

    /**
     * Validate class data
     * @param array $data
     * @return array
     */
    public function validateClass($data) {
        return $this->validate($data, [
            'name' => 'required|min:3|max:50',
            'grade' => 'required',
            'teacher_id' => 'required|numeric',
            'room' => 'required',
            'capacity' => 'required|numeric'
        ]);
    }
}
