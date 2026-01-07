<?php
require_once __DIR__ . '/Model.php';

/**
 * Student Model
 * Handles all student-related database operations
 */
class Student extends Model {
    protected $table = 'students';

    /**
     * Get sample data for testing
     */
    protected function getSampleData() {
        if (isset($_SESSION[$this->table]) && !empty($_SESSION[$this->table])) {
            return $_SESSION[$this->table];
        }

        return [
            ['id' => 1, 'reg_number' => 'ST2023001', 'name' => 'Olivia Parker', 'grade' => '4', 'class' => 'Class A', 'email' => 'olivia.parker@example.com', 'phone' => '592-123-4567', 'dob' => '2012-05-15', 'gender' => 'Female', 'address' => '123 Main St, Georgetown'],
            ['id' => 2, 'reg_number' => 'ST2023002', 'name' => 'Liam Johnson', 'grade' => '5', 'class' => 'Class B', 'email' => 'liam.johnson@example.com', 'phone' => '592-234-5678', 'dob' => '2011-08-22', 'gender' => 'Male', 'address' => '456 Church St, Georgetown'],
            ['id' => 3, 'reg_number' => 'ST2023003', 'name' => 'Ava Williams', 'grade' => '6', 'class' => 'Class A', 'email' => 'ava.williams@example.com', 'phone' => '592-345-6789', 'dob' => '2010-11-10', 'gender' => 'Female', 'address' => '789 Water St, Georgetown'],
            ['id' => 4, 'reg_number' => 'ST2023004', 'name' => 'Noah Brown', 'grade' => '3', 'class' => 'Class C', 'email' => 'noah.brown@example.com', 'phone' => '592-456-7890', 'dob' => '2013-03-18', 'gender' => 'Male', 'address' => '321 High St, Georgetown'],
            ['id' => 5, 'reg_number' => 'ST2023005', 'name' => 'Emma Davis', 'grade' => '4', 'class' => 'Class B', 'email' => 'emma.davis@example.com', 'phone' => '592-567-8901', 'dob' => '2012-07-25', 'gender' => 'Female', 'address' => '654 Market St, Georgetown'],
        ];
    }

    /**
     * Get students by grade
     * @param string $grade
     * @return array
     */
    public function getByGrade($grade) {
        return $this->findBy('grade', $grade);
    }

    /**
     * Get students by class
     * @param string $class
     * @return array
     */
    public function getByClass($class) {
        return $this->findBy('class', $class);
    }

    /**
     * Get students by grade and class
     * @param string $grade
     * @param string $class
     * @return array
     */
    public function getByGradeAndClass($grade, $class) {
        $students = $this->getAll();
        $filtered = [];

        foreach ($students as $student) {
            if ($student['grade'] === $grade && $student['class'] === $class) {
                $filtered[] = $student;
            }
        }

        return $filtered;
    }

    /**
     * Validate student data
     * @param array $data
     * @return array
     */
    public function validateStudent($data) {
        return $this->validate($data, [
            'reg_number' => 'required|min:5|max:20',
            'name' => 'required|min:3|max:100',
            'grade' => 'required',
            'class' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'dob' => 'required',
            'gender' => 'required'
        ]);
    }

    /**
     * Check if registration number already exists
     * @param string $regNumber
     * @param int|null $excludeId
     * @return bool
     */
    public function regNumberExists($regNumber, $excludeId = null) {
        $students = $this->getAll();

        foreach ($students as $student) {
            if ($student['reg_number'] === $regNumber && $student['id'] != $excludeId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate next registration number
     * @return string
     */
    public function generateRegNumber() {
        $students = $this->getAll();
        $year = date('Y');
        $maxNumber = 0;

        foreach ($students as $student) {
            if (preg_match('/ST' . $year . '(\d{3})/', $student['reg_number'], $matches)) {
                $number = (int)$matches[1];
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }

        $nextNumber = $maxNumber + 1;
        return 'ST' . $year . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
