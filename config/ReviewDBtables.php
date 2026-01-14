<?php
$host     = "localhost";
$dbname   = "School_Management";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!<br>";
} catch (PDOException $e) {
    exit("Connection failed: " . $e->getMessage());
}

/* ===================== ROLES ===================== */
try{
    $sql="CREATE TABLE IF NOT EXISTS roles (
        role_id INT AUTO_INCREMENT PRIMARY KEY,
        role_name VARCHAR(30) NOT NULL UNIQUE
    ) ENGINE=InnoDB";

    $pdo->exec($sql);
    echo "Roles table created.<br><br>";
}catch(PDOException $e){    
    echo "Error creating roles table: " . $e->getMessage();
}

/* ===================== USERS ===================== */
try{
    $sql="CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role_id INT NOT NULL,
        /*link to roles table*/
        CONSTRAINT fk_users_role
            FOREIGN KEY (role_id) REFERENCES roles(role_id)
            ON DELETE RESTRICT
            ON UPDATE CASCADE
    ) ENGINE=InnoDB";

    $pdo->exec($sql);
    echo "Users table created.<br><br>";
}catch(PDOException $e){    
    echo "Error creating users table: " . $e->getMessage();
}

/* ===================== GRADES ===================== */
try{
    $sql="CREATE TABLE IF NOT EXISTS grades (
        grade_id INT AUTO_INCREMENT PRIMARY KEY,
        grade_name VARCHAR(20) NOT NULL UNIQUE
    ) ENGINE=InnoDB";

    $pdo->exec($sql);

    $sql="INSERT IGNORE INTO grades (grade_name) VALUES
        ('Grade 1'),('Grade 2'),('Grade 3'),
        ('Grade 4'),('Grade 5'),('Grade 6')";

    $pdo->exec($sql);
    echo "Grades table created and seeded.<br><br>";
}catch(PDOException $e){    
    echo "Error creating grades table: " . $e->getMessage();
}

/* ===================== CLASSES ===================== */
try{
    $sql="CREATE TABLE IF NOT EXISTS classes (
        class_id INT AUTO_INCREMENT PRIMARY KEY,
        class_name VARCHAR(2) NOT NULL,
        grade_id INT NOT NULL,
        UNIQUE (class_name, grade_id),
        /*link grade to this table*/
        CONSTRAINT fk_classes_grade
            FOREIGN KEY (grade_id) REFERENCES grades(grade_id)
            ON DELETE RESTRICT
            ON UPDATE CASCADE
    ) ENGINE=InnoDB";

    $pdo->exec($sql);
    echo "Classes table created.<br><br>";
}catch(PDOException $e){    
    echo "Error creating classes table: " . $e->getMessage();
}

/* ===================== STUDENTS ===================== */
try{
    $sql="CREATE TABLE IF NOT EXISTS students (
        student_id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        class_id INT NOT NULL,
        /* link to classes table */
        CONSTRAINT fk_students_class
            FOREIGN KEY (class_id) REFERENCES classes(class_id)
            ON DELETE RESTRICT
            ON UPDATE CASCADE
    ) ENGINE=InnoDB";

    $pdo->exec($sql);
    echo "Students table created.<br><br>";
}catch(PDOException $e){    
    echo "Error creating students table: " . $e->getMessage();
}

/* ===================== SCHOOL YEARS ===================== */
try{
    $sql="CREATE TABLE IF NOT EXISTS school_years (
        school_year_id INT AUTO_INCREMENT PRIMARY KEY,
        year_name VARCHAR(20) NOT NULL UNIQUE
    ) ENGINE=InnoDB";

    $pdo->exec($sql);
    echo "School years table created.<br><br>";
}catch(PDOException $e){    
    echo "Error creating school_years table: " . $e->getMessage();
}

/* ===================== TERMS ===================== */
try{
    $sql="CREATE TABLE IF NOT EXISTS terms (
        term_id INT AUTO_INCREMENT PRIMARY KEY,
        term_name VARCHAR(20) NOT NULL,
        school_year_id INT NOT NULL,
        UNIQUE (term_name, school_year_id),
        /*link to school_years table*/
        CONSTRAINT fk_terms_year
            FOREIGN KEY (school_year_id) REFERENCES school_years(school_year_id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    ) ENGINE=InnoDB";

    $pdo->exec($sql);
    echo "Terms table created.<br><br>";
}catch(PDOException $e){    
    echo "Error creating terms table: " . $e->getMessage();
}

/* ===================== SUBJECTS ===================== */
try{
    $sql="CREATE TABLE IF NOT EXISTS subjects (
        subject_id INT AUTO_INCREMENT PRIMARY KEY,
        subject_name VARCHAR(50) NOT NULL,
        grade_id INT NOT NULL,
        UNIQUE (subject_name, grade_id),
        /*link grade to this table*/
        CONSTRAINT fk_subjects_grade
            FOREIGN KEY (grade_id) REFERENCES grades(grade_id)
            ON DELETE RESTRICT
            ON UPDATE CASCADE
    ) ENGINE=InnoDB";

    $pdo->exec($sql);
    echo "Subjects table created.<br><br>";
}catch(PDOException $e){    
    echo "Error creating subjects table: " . $e->getMessage();
}

/* ===================== TEACHERS ===================== */
try{
    $sql="CREATE TABLE IF NOT EXISTS teachers (
        teacher_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL UNIQUE,
        first_name VARCHAR(20) NOT NULL,
        last_name VARCHAR(20) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        /*link to users table*/
        CONSTRAINT fk_teachers_user
            FOREIGN KEY (user_id) REFERENCES users(user_id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    ) ENGINE=InnoDB";

    $pdo->exec($sql);
    echo "Teachers table created.<br><br>";
}catch(PDOException $e){    
    echo "Error creating teachers table: " . $e->getMessage();
}

/* ===================== TEACHER SUBJECTS ===================== */
try{
    $sql="CREATE TABLE IF NOT EXISTS teacher_subjects (
        teacher_id INT NOT NULL,
        subject_id INT NOT NULL,
        PRIMARY KEY (teacher_id, subject_id),
        /*link teacher to subject*/
        CONSTRAINT fk_ts_teacher
            FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id)
            ON DELETE CASCADE,
            /*link subject to this table*/
        CONSTRAINT fk_ts_subject
            FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
            ON DELETE CASCADE
    ) ENGINE=InnoDB";

    $pdo->exec($sql);
    echo "Teacher_Subjects table created.<br><br>";
}catch(PDOException $e){    
    echo "Error creating teacher_subjects table: " . $e->getMessage();
}

/* ===================== SCORES ===================== */
try{
    $sql="CREATE TABLE IF NOT EXISTS scores (
        score_id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        subject_id INT NOT NULL,
        term_id INT NOT NULL,
        score INT NOT NULL CHECK (score BETWEEN 0 AND 100),
        UNIQUE (student_id, subject_id, term_id),
        /*link to students table*/
        CONSTRAINT fk_scores_student
            FOREIGN KEY (student_id) REFERENCES students(student_id)
            ON DELETE CASCADE,
            /*link to subjects table*/
        CONSTRAINT fk_scores_subject
            FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
            ON DELETE CASCADE,
        /*link to terms table*/
        CONSTRAINT fk_scores_term
            FOREIGN KEY (term_id) REFERENCES terms(term_id)
            ON DELETE CASCADE
    ) ENGINE=InnoDB";

    $pdo->exec($sql);
    echo "Scores table created.<br><br>";
}catch(PDOException $e){    
    echo "Error creating scores table: " . $e->getMessage();
}

/* ===================== STUDENT ENROLLMENTS ===================== */
try{
    $sql="CREATE TABLE IF NOT EXISTS student_enrollments (
        enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        class_id INT NOT NULL,
        school_year_id INT NOT NULL,
        UNIQUE (student_id, school_year_id),
        /*link to students table*/
        CONSTRAINT fk_enroll_student
            FOREIGN KEY (student_id) REFERENCES students(student_id)
            ON DELETE CASCADE,
            /*link to classes table*/
        CONSTRAINT fk_enroll_class
            FOREIGN KEY (class_id) REFERENCES classes(class_id)
            ON DELETE RESTRICT,
            /*link to school_years table*/
        CONSTRAINT fk_enroll_year
            FOREIGN KEY (school_year_id) REFERENCES school_years(school_year_id)
            ON DELETE CASCADE
    ) ENGINE=InnoDB";

    $pdo->exec($sql);
    echo "Student enrollments table created.<br><br>";
}catch(PDOException $e){    
    echo "Error creating student_enrollments table: " . $e->getMessage();
}

echo "Database setup completed successfully!";
?>
