<?php
require_once __DIR__ . '/Model.php';

// class model
// named SchoolClass because Class is a reserved word in php (annoying)
class SchoolClass extends Model {
    protected $table = 'classes';
    protected $primaryKey = 'class_id';

    // fields list
    protected function getAllowedFields() {
        return ['class_name', 'grade_id'];
    }

    // get all classes and count how many students are in them
    public function getAll() {
        $sql = "SELECT c.*, g.grade_name,
                (SELECT COUNT(*) FROM students s WHERE s.class_id = c.class_id) as student_count
                FROM classes c 
                JOIN grades g ON c.grade_id = g.grade_id 
                ORDER BY g.grade_id, c.class_name";
        return $this->query($sql);
    }

    // find class by id
    public function find($id) {
        $sql = "SELECT c.*, g.grade_name,
                (SELECT COUNT(*) FROM students s WHERE s.class_id = c.class_id) as student_count
                FROM classes c 
                JOIN grades g ON c.grade_id = g.grade_id 
                WHERE c.class_id = ?";
        return $this->queryOne($sql, [$id]);
    }

    // get classes for a specific grade
    public function getByGrade($gradeId) {
        $sql = "SELECT c.*, g.grade_name,
                (SELECT COUNT(*) FROM students s WHERE s.class_id = c.class_id) as student_count
                FROM classes c 
                JOIN grades g ON c.grade_id = g.grade_id 
                WHERE c.grade_id = ? 
                ORDER BY c.class_name";
        return $this->query($sql, [$gradeId]);
    }

    // helper to find class by name + grade
    public function getByGradeAndName($gradeId, $name) {
        $sql = "SELECT c.*, g.grade_name 
                FROM classes c 
                JOIN grades g ON c.grade_id = g.grade_id 
                WHERE c.grade_id = ? AND c.class_name = ?";
        return $this->queryOne($sql, [$gradeId, $name]);
    }

    // get list of all grades
    public function getGrades() {
        $sql = "SELECT * FROM grades ORDER BY grade_id";
        return $this->query($sql);
    }

    // checks for duplicate class names
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

    // validate class info
    public function validateClass($data) {
        return $this->validate($data, [
            'class_name' => 'required|min:1|max:2',
            'grade_id' => 'required|numeric'
        ]);
    }

    // formats a nice name for the class (e.g. Grade 1 - Class A)
    public function getDisplayName($classId) {
        $class = $this->find($classId);
        if ($class) {
            return $class['grade_name'] . ' - Class ' . $class['class_name'];
        }
        return '';
    }

    // gets all subjects assigned to this specific class
    public function getSubjects($classId) {
        $sql = "SELECT s.* 
                FROM subjects s 
                JOIN class_subjects cs ON s.subject_id = cs.subject_id 
                WHERE cs.class_id = ? 
                ORDER BY s.subject_name";
        return $this->query($sql, [$classId]);
    }

    // links a subject to a class
    public function assignSubject($classId, $subjectId) {
        $sql = "INSERT INTO class_subjects (class_id, subject_id) VALUES (?, ?)";
        try {
            $this->query($sql, [$classId, $subjectId]);
            return true;
        } catch (PDOException $e) {
            return false; // probably already exists
        }
    }

    // removes a subject from a class
    public function removeSubject($classId, $subjectId) {
        $sql = "DELETE FROM class_subjects WHERE class_id = ? AND subject_id = ?";
        $this->query($sql, [$classId, $subjectId]);
        return true;
    }

    // updates the list of subjects for a class
    // deletes old ones and adds new ones
    public function syncSubjects($classId, $subjectIds) {
        $pdo = $this->getPDO();
        $pdo->beginTransaction();

        try {
            // clear existing
            $sqlDelete = "DELETE FROM class_subjects WHERE class_id = ?";
            $stmtDelete = $pdo->prepare($sqlDelete);
            $stmtDelete->execute([$classId]);

            // add new ones
            if (!empty($subjectIds)) {
                $sqlInsert = "INSERT INTO class_subjects (class_id, subject_id) VALUES (?, ?)";
                $stmtInsert = $pdo->prepare($sqlInsert);
                
                foreach ($subjectIds as $subjectId) {
                    $stmtInsert->execute([$classId, $subjectId]);
                }
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
}
