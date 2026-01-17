-- =====================================================
-- School Management System Database
-- CSE3101-G17 Project
-- =====================================================

-- Create database
CREATE DATABASE IF NOT EXISTS School_Management;
USE School_Management;

-- Set charset
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- =====================================================
-- TABLE CREATION
-- =====================================================

-- ===================== ROLES =====================
CREATE TABLE IF NOT EXISTS roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ===================== USERS =====================
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    CONSTRAINT fk_users_role
        FOREIGN KEY (role_id) REFERENCES roles(role_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ===================== GRADES =====================
CREATE TABLE IF NOT EXISTS grades (
    grade_id INT AUTO_INCREMENT PRIMARY KEY,
    grade_name VARCHAR(20) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ===================== CLASSES =====================
CREATE TABLE IF NOT EXISTS classes (
    class_id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(2) NOT NULL,
    grade_id INT NOT NULL,
    UNIQUE (class_name, grade_id),
    CONSTRAINT fk_classes_grade
        FOREIGN KEY (grade_id) REFERENCES grades(grade_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ===================== STUDENTS =====================
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE DEFAULT NULL,
    gender ENUM('Male', 'Female') DEFAULT NULL,
    class_id INT NOT NULL,
    CONSTRAINT fk_students_class
        FOREIGN KEY (class_id) REFERENCES classes(class_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ===================== SCHOOL YEARS =====================
CREATE TABLE IF NOT EXISTS school_years (
    school_year_id INT AUTO_INCREMENT PRIMARY KEY,
    year_name VARCHAR(20) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ===================== TERMS =====================
CREATE TABLE IF NOT EXISTS terms (
    term_id INT AUTO_INCREMENT PRIMARY KEY,
    term_name VARCHAR(20) NOT NULL,
    school_year_id INT NOT NULL,
    UNIQUE (term_name, school_year_id),
    CONSTRAINT fk_terms_year
        FOREIGN KEY (school_year_id) REFERENCES school_years(school_year_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ===================== SUBJECTS =====================
CREATE TABLE IF NOT EXISTS subjects (
    subject_id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(50) NOT NULL,
    grade_id INT NOT NULL,
    UNIQUE (subject_name, grade_id),
    CONSTRAINT fk_subjects_grade
        FOREIGN KEY (grade_id) REFERENCES grades(grade_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ===================== TEACHERS =====================
CREATE TABLE IF NOT EXISTS teachers (
    teacher_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    first_name VARCHAR(20) NOT NULL,
    last_name VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    CONSTRAINT fk_teachers_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ===================== TEACHER SUBJECTS =====================
CREATE TABLE IF NOT EXISTS teacher_subjects (
    teacher_id INT NOT NULL,
    subject_id INT NOT NULL,
    PRIMARY KEY (teacher_id, subject_id),
    CONSTRAINT fk_ts_teacher
        FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_ts_subject
        FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===================== TEACHER CLASSES =====================
CREATE TABLE IF NOT EXISTS teacher_classes (
    teacher_id INT NOT NULL,
    class_id INT NOT NULL,
    PRIMARY KEY (teacher_id, class_id),
    CONSTRAINT fk_tc_teacher
        FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_tc_class
        FOREIGN KEY (class_id) REFERENCES classes(class_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===================== SCORES =====================
-- Includes audit columns for tracking who modified scores
CREATE TABLE IF NOT EXISTS scores (
    score_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    term_id INT NOT NULL,
    score INT NOT NULL CHECK (score BETWEEN 0 AND 100),
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified_by INT NULL,
    modified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE (student_id, subject_id, term_id),
    CONSTRAINT fk_scores_student
        FOREIGN KEY (student_id) REFERENCES students(student_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_scores_subject
        FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_scores_term
        FOREIGN KEY (term_id) REFERENCES terms(term_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_scores_created_by
        FOREIGN KEY (created_by) REFERENCES users(user_id)
        ON DELETE SET NULL,
    CONSTRAINT fk_scores_modified_by
        FOREIGN KEY (modified_by) REFERENCES users(user_id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- Create index for faster audit queries
CREATE INDEX idx_scores_modified_by ON scores(modified_by);
CREATE INDEX idx_scores_modified_at ON scores(modified_at);

-- ===================== STUDENT ENROLLMENTS =====================
CREATE TABLE IF NOT EXISTS student_enrollments (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    school_year_id INT NOT NULL,
    UNIQUE (student_id, school_year_id),
    CONSTRAINT fk_enroll_student
        FOREIGN KEY (student_id) REFERENCES students(student_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_enroll_class
        FOREIGN KEY (class_id) REFERENCES classes(class_id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_enroll_year
        FOREIGN KEY (school_year_id) REFERENCES school_years(school_year_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- DATA POPULATION
-- =====================================================

-- ===================== ROLES =====================
INSERT INTO roles (role_name) VALUES
('office_admin'),
('teacher');

-- ===================== USERS =====================
-- Password for all users: 'password123' (hashed with bcrypt)
-- Generated with: password_hash('password123', PASSWORD_DEFAULT)
INSERT INTO users (username, password_hash, role_id) VALUES
('admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFPqI0tPGF4gEaGRZCe0lDCHWtfC', 1),
('john_admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFPqI0tPGF4gEaGRZCe0lDCHWtfC', 1),
-- Teachers (user_id 3-12)
('sarah_teacher', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFPqI0tPGF4gEaGRZCe0lDCHWtfC', 2),
('mike_teacher', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFPqI0tPGF4gEaGRZCe0lDCHWtfC', 2),
('emily_teacher', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFPqI0tPGF4gEaGRZCe0lDCHWtfC', 2),
('david_teacher', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFPqI0tPGF4gEaGRZCe0lDCHWtfC', 2),
('lisa_teacher', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFPqI0tPGF4gEaGRZCe0lDCHWtfC', 2),
('james_teacher', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFPqI0tPGF4gEaGRZCe0lDCHWtfC', 2),
('anna_teacher', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFPqI0tPGF4gEaGRZCe0lDCHWtfC', 2),
('robert_teacher', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFPqI0tPGF4gEaGRZCe0lDCHWtfC', 2),
('maria_teacher', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFPqI0tPGF4gEaGRZCe0lDCHWtfC', 2),
('kevin_teacher', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFPqI0tPGF4gEaGRZCe0lDCHWtfC', 2);

-- ===================== GRADES =====================
INSERT INTO grades (grade_name) VALUES
('Grade 1'),
('Grade 2'),
('Grade 3'),
('Grade 4'),
('Grade 5'),
('Grade 6');

-- ===================== CLASSES =====================
INSERT INTO classes (class_name, grade_id) VALUES
-- Grade 1
('A', 1), ('B', 1), ('C', 1),
-- Grade 2
('A', 2), ('B', 2), ('C', 2),
-- Grade 3
('A', 3), ('B', 3), ('C', 3),
-- Grade 4
('A', 4), ('B', 4), ('C', 4),
-- Grade 5
('A', 5), ('B', 5), ('C', 5),
-- Grade 6
('A', 6), ('B', 6), ('C', 6);

-- ===================== STUDENTS =====================
-- 25 students in A classes, 24 in B classes, 23 in C classes (Total: 432 students)
INSERT INTO students (first_name, last_name, date_of_birth, gender, class_id) VALUES
-- Grade 1A (25 students, born 2018)
('James', 'Williams', '2018-03-15', 'Male', 1), ('Sophia', 'Brown', '2018-05-22', 'Female', 1), ('Oliver', 'Davis', '2018-01-08', 'Male', 1), ('Emma', 'Miller', '2018-07-30', 'Female', 1), ('Noah', 'Wilson', '2018-02-14', 'Male', 1),
('Ava', 'Martinez', '2018-09-05', 'Female', 1), ('Liam', 'Garcia', '2018-04-18', 'Male', 1), ('Isabella', 'Rodriguez', '2018-11-25', 'Female', 1), ('Mason', 'Lopez', '2018-06-12', 'Male', 1), ('Mia', 'Hernandez', '2018-08-03', 'Female', 1),
('Ethan', 'Moore', '2018-10-21', 'Male', 1), ('Charlotte', 'Taylor', '2018-12-09', 'Female', 1), ('Lucas', 'Anderson', '2018-03-27', 'Male', 1), ('Amelia', 'Thomas', '2018-05-14', 'Female', 1), ('Alexander', 'Jackson', '2018-07-08', 'Male', 1),
('Harper', 'White', '2018-09-19', 'Female', 1), ('Benjamin', 'Harris', '2018-01-30', 'Male', 1), ('Evelyn', 'Martin', '2018-04-05', 'Female', 1), ('Henry', 'Thompson', '2018-06-22', 'Male', 1), ('Abigail', 'Garcia', '2018-08-17', 'Female', 1),
('Sebastian', 'Martinez', '2018-10-03', 'Male', 1), ('Emily', 'Robinson', '2018-12-28', 'Female', 1), ('Jack', 'Clark', '2018-02-11', 'Male', 1), ('Elizabeth', 'Lewis', '2018-04-26', 'Female', 1), ('Aiden', 'Lee', '2018-07-15', 'Male', 1),

-- Grade 1B (24 students, born 2018)
('Michael', 'Walker', '2018-01-19', 'Male', 2), ('Sofia', 'Hall', '2018-03-08', 'Female', 2), ('Daniel', 'Allen', '2018-05-25', 'Male', 2), ('Avery', 'Young', '2018-07-12', 'Female', 2), ('Matthew', 'King', '2018-09-30', 'Male', 2),
('Scarlett', 'Wright', '2018-11-17', 'Female', 2), ('Joseph', 'Scott', '2018-02-04', 'Male', 2), ('Victoria', 'Green', '2018-04-21', 'Female', 2), ('David', 'Adams', '2018-06-08', 'Male', 2), ('Grace', 'Baker', '2018-08-26', 'Female', 2),
('Carter', 'Nelson', '2018-10-13', 'Male', 2), ('Chloe', 'Carter', '2018-12-01', 'Female', 2), ('Owen', 'Mitchell', '2018-01-28', 'Male', 2), ('Penelope', 'Perez', '2018-03-15', 'Female', 2), ('Wyatt', 'Roberts', '2018-05-02', 'Male', 2),
('Layla', 'Turner', '2018-07-20', 'Female', 2), ('John', 'Phillips', '2018-09-07', 'Male', 2), ('Riley', 'Campbell', '2018-11-24', 'Female', 2), ('Luke', 'Parker', '2018-02-18', 'Male', 2), ('Zoey', 'Evans', '2018-04-05', 'Female', 2),
('Jayden', 'Edwards', '2018-06-23', 'Male', 2), ('Nora', 'Collins', '2018-08-10', 'Female', 2), ('Dylan', 'Stewart', '2018-10-28', 'Male', 2), ('Lily', 'Sanchez', '2018-12-15', 'Female', 2),

-- Grade 1C (23 students, born 2018)
('Grayson', 'Morris', '2018-01-05', 'Male', 3), ('Hannah', 'Rogers', '2018-03-22', 'Female', 3), ('Isaac', 'Reed', '2018-05-09', 'Male', 3), ('Addison', 'Cook', '2018-07-27', 'Female', 3), ('Gabriel', 'Morgan', '2018-09-14', 'Male', 3),
('Ellie', 'Bell', '2018-11-01', 'Female', 3), ('Julian', 'Murphy', '2018-02-25', 'Male', 3), ('Aubrey', 'Bailey', '2018-04-12', 'Female', 3), ('Levi', 'Rivera', '2018-06-30', 'Male', 3), ('Natalie', 'Cooper', '2018-08-17', 'Female', 3),
('Lincoln', 'Richardson', '2018-10-04', 'Male', 3), ('Hazel', 'Cox', '2018-12-22', 'Female', 3), ('Ryan', 'Howard', '2018-01-16', 'Male', 3), ('Stella', 'Ward', '2018-03-03', 'Female', 3), ('Nathan', 'Torres', '2018-05-21', 'Male', 3),
('Zoe', 'Peterson', '2018-07-08', 'Female', 3), ('Aaron', 'Gray', '2018-09-26', 'Male', 3), ('Leah', 'Ramirez', '2018-11-13', 'Female', 3), ('Caleb', 'James', '2018-02-07', 'Male', 3), ('Savannah', 'Watson', '2018-04-24', 'Female', 3),
('Joshua', 'Brooks', '2018-06-11', 'Male', 3), ('Aria', 'Kelly', '2018-08-29', 'Female', 3), ('Isaiah', 'Sanders', '2018-10-16', 'Male', 3),

-- Grade 2A (25 students, born 2017)
('Thomas', 'Price', '2017-01-12', 'Male', 4), ('Bella', 'Bennett', '2017-03-29', 'Female', 4), ('Charles', 'Wood', '2017-05-16', 'Male', 4), ('Lucy', 'Barnes', '2017-07-04', 'Female', 4), ('Christopher', 'Ross', '2017-09-21', 'Male', 4),
('Anna', 'Henderson', '2017-11-08', 'Female', 4), ('Ezra', 'Coleman', '2017-02-02', 'Male', 4), ('Samantha', 'Jenkins', '2017-04-19', 'Female', 4), ('Andrew', 'Perry', '2017-06-06', 'Male', 4), ('Brooklyn', 'Powell', '2017-08-24', 'Female', 4),
('Josiah', 'Long', '2017-10-11', 'Male', 4), ('Claire', 'Patterson', '2017-12-28', 'Female', 4), ('Christian', 'Hughes', '2017-01-22', 'Male', 4), ('Skylar', 'Flores', '2017-03-09', 'Female', 4), ('Hunter', 'Washington', '2017-05-27', 'Male', 4),
('Paisley', 'Butler', '2017-07-14', 'Female', 4), ('Landon', 'Simmons', '2017-09-01', 'Male', 4), ('Madelyn', 'Foster', '2017-11-18', 'Female', 4), ('Jonathan', 'Gonzales', '2017-02-12', 'Male', 4), ('Genesis', 'Bryant', '2017-04-29', 'Female', 4),
('Adrian', 'Alexander', '2017-06-16', 'Male', 4), ('Naomi', 'Russell', '2017-08-03', 'Female', 4), ('Asher', 'Griffin', '2017-10-21', 'Male', 4), ('Aaliyah', 'Diaz', '2017-12-08', 'Female', 4), ('Nolan', 'Hayes', '2017-02-26', 'Male', 4),

-- Grade 2B (24 students, born 2017)
('Cameron', 'Myers', '2017-01-03', 'Male', 5), ('Kinsley', 'Ford', '2017-03-20', 'Female', 5), ('Colton', 'Hamilton', '2017-05-07', 'Male', 5), ('Sarah', 'Graham', '2017-07-25', 'Female', 5), ('Jordan', 'Sullivan', '2017-09-12', 'Male', 5),
('Allison', 'Wallace', '2017-10-30', 'Female', 5), ('Robert', 'Woods', '2017-01-24', 'Male', 5), ('Maya', 'Cole', '2017-03-11', 'Female', 5), ('Evan', 'West', '2017-05-28', 'Male', 5), ('Valentina', 'Jordan', '2017-07-15', 'Female', 5),
('Cooper', 'Owens', '2017-09-02', 'Male', 5), ('Caroline', 'Reynolds', '2017-11-19', 'Female', 5), ('Dominic', 'Fisher', '2017-02-13', 'Male', 5), ('Ruby', 'Ellis', '2017-04-30', 'Female', 5), ('Jace', 'Harrison', '2017-06-17', 'Male', 5),
('Piper', 'Gibson', '2017-08-04', 'Female', 5), ('Kevin', 'McDonald', '2017-10-22', 'Male', 5), ('Quinn', 'Cruz', '2017-12-09', 'Female', 5), ('Gavin', 'Marshall', '2017-01-26', 'Male', 5), ('Katherine', 'Ortiz', '2017-03-13', 'Female', 5),
('Austin', 'Gomez', '2017-05-01', 'Male', 5), ('Reagan', 'Murray', '2017-07-18', 'Female', 5), ('Jason', 'Freeman', '2017-09-05', 'Male', 5), ('Willow', 'Wells', '2017-11-22', 'Female', 5),

-- Grade 2C (23 students, born 2017)
('Chase', 'Webb', '2017-02-08', 'Male', 6), ('Autumn', 'Simpson', '2017-04-25', 'Female', 6), ('Ian', 'Stevens', '2017-06-12', 'Male', 6), ('Faith', 'Tucker', '2017-08-30', 'Female', 6), ('Xavier', 'Porter', '2017-10-17', 'Male', 6),
('Sydney', 'Hunter', '2017-12-04', 'Female', 6), ('Eli', 'Hicks', '2017-01-28', 'Male', 6), ('Peyton', 'Crawford', '2017-03-15', 'Female', 6), ('Brayden', 'Henry', '2017-05-02', 'Male', 6), ('Isabelle', 'Boyd', '2017-07-20', 'Female', 6),
('Kayden', 'Mason', '2017-09-07', 'Male', 6), ('Gabriella', 'Morales', '2017-11-24', 'Female', 6), ('Adam', 'Kennedy', '2017-02-18', 'Male', 6), ('Molly', 'Warren', '2017-04-05', 'Female', 6), ('Brandon', 'Dixon', '2017-06-23', 'Male', 6),
('Brielle', 'Ramos', '2017-08-10', 'Female', 6), ('Tyler', 'Reyes', '2017-10-28', 'Male', 6), ('Emilia', 'Burns', '2017-12-15', 'Female', 6), ('Blake', 'Gordon', '2017-01-09', 'Male', 6), ('Taylor', 'Shaw', '2017-03-26', 'Female', 6),
('Carson', 'Holmes', '2017-05-13', 'Male', 6), ('Jade', 'Rice', '2017-07-31', 'Female', 6), ('Nicholas', 'Robertson', '2017-09-18', 'Male', 6),

-- Grade 3A (25 students, born 2016)
('Parker', 'Hunt', '2016-01-14', 'Male', 7), ('Morgan', 'Black', '2016-03-31', 'Female', 7), ('Zachary', 'Daniels', '2016-05-18', 'Male', 7), ('Jasmine', 'Palmer', '2016-07-05', 'Female', 7), ('Connor', 'Mills', '2016-09-23', 'Male', 7),
('Alexandra', 'Nichols', '2016-11-10', 'Female', 7), ('Bryson', 'Grant', '2016-02-04', 'Male', 7), ('Eva', 'Knight', '2016-04-21', 'Female', 7), ('Easton', 'Ferguson', '2016-06-08', 'Male', 7), ('Mackenzie', 'Rose', '2016-08-26', 'Female', 7),
('Miles', 'Stone', '2016-10-13', 'Male', 7), ('Kimberly', 'Hawkins', '2016-12-30', 'Female', 7), ('Micah', 'Dunn', '2016-01-24', 'Male', 7), ('Lauren', 'Perkins', '2016-03-11', 'Female', 7), ('Tristan', 'Hudson', '2016-05-29', 'Male', 7),
('Kylie', 'Spencer', '2016-07-16', 'Female', 7), ('George', 'Gardner', '2016-09-03', 'Male', 7), ('Juliana', 'Stephens', '2016-11-20', 'Female', 7), ('Vincent', 'Payne', '2016-02-14', 'Male', 7), ('Kayla', 'Pierce', '2016-05-01', 'Female', 7),
('Max', 'Berry', '2016-06-18', 'Male', 7), ('Melanie', 'Matthews', '2016-08-05', 'Female', 7), ('Leo', 'Arnold', '2016-10-23', 'Male', 7), ('Destiny', 'Wagner', '2016-12-10', 'Female', 7), ('Cole', 'Willis', '2016-02-28', 'Male', 7),

-- Grade 3B (24 students, born 2016)
('Harrison', 'Ray', '2016-01-05', 'Male', 8), ('Alyssa', 'Ruiz', '2016-03-22', 'Female', 8), ('Bentley', 'Harper', '2016-05-09', 'Male', 8), ('Paige', 'Fox', '2016-07-27', 'Female', 8), ('Ryder', 'Boyd', '2016-09-14', 'Male', 8),
('Gianna', 'Rose', '2016-11-01', 'Female', 8), ('Maverick', 'Hunter', '2016-02-25', 'Male', 8), ('Diana', 'Mills', '2016-04-12', 'Female', 8), ('Roman', 'Ryan', '2016-06-30', 'Male', 8), ('Catherine', 'Lawrence', '2016-08-17', 'Female', 8),
('Elliot', 'Stone', '2016-10-04', 'Male', 8), ('Brooke', 'Murray', '2016-12-22', 'Female', 8), ('Timothy', 'Freeman', '2016-01-16', 'Male', 8), ('Vivian', 'Webb', '2016-03-03', 'Female', 8), ('Patrick', 'Simpson', '2016-05-21', 'Male', 8),
('Alexa', 'Stevens', '2016-07-08', 'Female', 8), ('Jeremy', 'Tucker', '2016-09-26', 'Male', 8), ('Eliana', 'Porter', '2016-11-13', 'Female', 8), ('Jesse', 'Crawford', '2016-02-07', 'Male', 8), ('Alina', 'Boyd', '2016-04-24', 'Female', 8),
('Preston', 'Mason', '2016-06-11', 'Male', 8), ('Arabella', 'Kennedy', '2016-08-29', 'Female', 8), ('Marcus', 'Warren', '2016-10-16', 'Male', 8), ('Bailey', 'Dixon', '2016-12-03', 'Female', 8),

-- Grade 3C (23 students, born 2016)
('Graham', 'Ramos', '2016-01-20', 'Male', 9), ('Clara', 'Burns', '2016-04-06', 'Female', 9), ('Derek', 'Gordon', '2016-06-24', 'Male', 9), ('Alana', 'Shaw', '2016-08-11', 'Female', 9), ('Kenneth', 'Holmes', '2016-10-29', 'Male', 9),
('Fiona', 'Rice', '2016-12-16', 'Female', 9), ('Grant', 'Robertson', '2016-02-10', 'Male', 9), ('Lyla', 'Hunt', '2016-04-27', 'Female', 9), ('Richard', 'Black', '2016-06-14', 'Male', 9), ('Camille', 'Daniels', '2016-09-01', 'Female', 9),
('Bradley', 'Palmer', '2016-10-19', 'Male', 9), ('Rose', 'Mills', '2016-01-13', 'Female', 9), ('Dean', 'Nichols', '2016-03-30', 'Male', 9), ('Daisy', 'Grant', '2016-05-17', 'Female', 9), ('Spencer', 'Knight', '2016-08-04', 'Male', 9),
('Olive', 'Ferguson', '2016-09-22', 'Female', 9), ('Peter', 'Rose', '2016-11-09', 'Male', 9), ('Iris', 'Stone', '2016-01-03', 'Female', 9), ('Francis', 'Hawkins', '2016-03-21', 'Male', 9), ('Violet', 'Dunn', '2016-05-08', 'Female', 9),
('Edward', 'Perkins', '2016-07-26', 'Male', 9), ('Ivy', 'Hudson', '2016-09-13', 'Female', 9), ('Simon', 'Spencer', '2016-11-30', 'Male', 9),

-- Grade 4A (25 students, born 2015)
('Walter', 'Gardner', '2015-01-17', 'Male', 10), ('Ada', 'Stephens', '2015-04-03', 'Female', 10), ('Hugo', 'Payne', '2015-06-21', 'Male', 10), ('Vera', 'Pierce', '2015-08-08', 'Female', 10), ('Felix', 'Berry', '2015-10-26', 'Male', 10),
('Esther', 'Matthews', '2015-12-13', 'Female', 10), ('Louis', 'Arnold', '2015-02-07', 'Male', 10), ('Margot', 'Wagner', '2015-04-24', 'Female', 10), ('Oscar', 'Willis', '2015-06-11', 'Male', 10), ('Helena', 'Ray', '2015-08-29', 'Female', 10),
('Theodore', 'Ruiz', '2015-10-16', 'Male', 10), ('Lydia', 'Harper', '2015-01-10', 'Female', 10), ('Arthur', 'Fox', '2015-03-27', 'Male', 10), ('Josephine', 'Boyd', '2015-05-14', 'Female', 10), ('Albert', 'Rose', '2015-08-01', 'Male', 10),
('Eloise', 'Hunter', '2015-09-18', 'Female', 10), ('Harvey', 'Mills', '2015-11-05', 'Male', 10), ('Thea', 'Ryan', '2015-01-29', 'Female', 10), ('Frederick', 'Lawrence', '2015-03-16', 'Male', 10), ('Matilda', 'Stone', '2015-05-03', 'Female', 10),
('Alfred', 'Murray', '2015-07-21', 'Male', 10), ('Rosalie', 'Freeman', '2015-09-08', 'Female', 10), ('Cecil', 'Webb', '2015-11-25', 'Male', 10), ('Beatrice', 'Simpson', '2015-02-19', 'Female', 10), ('Ralph', 'Stevens', '2015-04-06', 'Male', 10),

-- Grade 4B (24 students, born 2015)
('Stanley', 'Tucker', '2015-01-23', 'Male', 11), ('Agnes', 'Porter', '2015-04-10', 'Female', 11), ('Leonard', 'Crawford', '2015-06-27', 'Male', 11), ('Frances', 'Boyd', '2015-08-14', 'Female', 11), ('Ernest', 'Mason', '2015-10-02', 'Male', 11),
('Dorothy', 'Kennedy', '2015-12-19', 'Female', 11), ('Raymond', 'Warren', '2015-02-13', 'Male', 11), ('Margaret', 'Dixon', '2015-04-30', 'Female', 11), ('Eugene', 'Ramos', '2015-06-17', 'Male', 11), ('Lillian', 'Burns', '2015-09-04', 'Female', 11),
('Clifford', 'Gordon', '2015-10-22', 'Male', 11), ('Mildred', 'Shaw', '2015-01-16', 'Female', 11), ('Lawrence', 'Holmes', '2015-03-03', 'Male', 11), ('Edith', 'Rice', '2015-05-21', 'Female', 11), ('Warren', 'Robertson', '2015-07-08', 'Male', 11),
('Pearl', 'Hunt', '2015-09-25', 'Female', 11), ('Bernard', 'Black', '2015-11-12', 'Male', 11), ('Ruth', 'Daniels', '2015-02-06', 'Female', 11), ('Howard', 'Palmer', '2015-04-23', 'Male', 11), ('Virginia', 'Mills', '2015-06-10', 'Female', 11),
('Norman', 'Nichols', '2015-08-28', 'Male', 11), ('Helen', 'Grant', '2015-10-15', 'Female', 11), ('Gerald', 'Knight', '2015-12-02', 'Male', 11), ('Irene', 'Ferguson', '2015-02-26', 'Female', 11),

-- Grade 4C (23 students, born 2015)
('Harold', 'Rose', '2015-01-09', 'Male', 12), ('Gladys', 'Stone', '2015-03-26', 'Female', 12), ('Clarence', 'Hawkins', '2015-05-13', 'Male', 12), ('Ethel', 'Dunn', '2015-08-01', 'Female', 12), ('Floyd', 'Perkins', '2015-09-18', 'Male', 12),
('Hazel', 'Hudson', '2015-11-05', 'Female', 12), ('Russell', 'Spencer', '2015-01-30', 'Male', 12), ('Thelma', 'Gardner', '2015-03-17', 'Female', 12), ('Roy', 'Stephens', '2015-05-04', 'Male', 12), ('Lucille', 'Payne', '2015-07-22', 'Female', 12),
('Franklin', 'Pierce', '2015-09-09', 'Male', 12), ('Alma', 'Berry', '2015-11-26', 'Female', 12), ('Gordon', 'Matthews', '2015-02-20', 'Male', 12), ('Bessie', 'Arnold', '2015-04-07', 'Female', 12), ('Leslie', 'Wagner', '2015-06-25', 'Male', 12),
('Minnie', 'Willis', '2015-08-12', 'Female', 12), ('Glen', 'Ray', '2015-10-30', 'Male', 12), ('Clarice', 'Ruiz', '2015-01-24', 'Female', 12), ('Vernon', 'Harper', '2015-03-11', 'Male', 12), ('Marie', 'Fox', '2015-05-29', 'Female', 12),
('Arnold', 'Boyd', '2015-07-16', 'Male', 12), ('Mabel', 'Rose', '2015-10-03', 'Female', 12), ('Chester', 'Hunter', '2015-12-20', 'Male', 12),

-- Grade 5A (25 students, born 2014)
('Clyde', 'Mills', '2014-01-11', 'Male', 13), ('Edna', 'Ryan', '2014-03-28', 'Female', 13), ('Milton', 'Lawrence', '2014-05-15', 'Male', 13), ('Annie', 'Stone', '2014-08-02', 'Female', 13), ('Wilbur', 'Murray', '2014-09-19', 'Male', 13),
('Bertha', 'Freeman', '2014-11-06', 'Female', 13), ('Edgar', 'Webb', '2014-02-01', 'Male', 13), ('Flora', 'Simpson', '2014-04-18', 'Female', 13), ('Lester', 'Stevens', '2014-06-05', 'Male', 13), ('Stella', 'Tucker', '2014-08-23', 'Female', 13),
('Wayne', 'Porter', '2014-10-10', 'Male', 13), ('Nellie', 'Crawford', '2014-12-27', 'Female', 13), ('Homer', 'Boyd', '2014-01-21', 'Male', 13), ('Mattie', 'Mason', '2014-03-08', 'Female', 13), ('Morris', 'Kennedy', '2014-05-26', 'Male', 13),
('Carrie', 'Warren', '2014-07-13', 'Female', 13), ('Luther', 'Dixon', '2014-09-30', 'Male', 13), ('Fannie', 'Ramos', '2014-11-17', 'Female', 13), ('Alvin', 'Burns', '2014-02-11', 'Male', 13), ('Mamie', 'Gordon', '2014-04-28', 'Female', 13),
('Willis', 'Shaw', '2014-06-15', 'Male', 13), ('Jennie', 'Holmes', '2014-09-02', 'Female', 13), ('Marshall', 'Rice', '2014-10-20', 'Male', 13), ('Maude', 'Robertson', '2014-01-14', 'Female', 13), ('Irving', 'Hunt', '2014-03-31', 'Male', 13),

-- Grade 5B (24 students, born 2014)
('Hugh', 'Black', '2014-01-07', 'Male', 14), ('Lena', 'Daniels', '2014-03-24', 'Female', 14), ('Sidney', 'Palmer', '2014-05-11', 'Male', 14), ('Rosa', 'Mills', '2014-07-29', 'Female', 14), ('Edwin', 'Nichols', '2014-09-16', 'Male', 14),
('Nettie', 'Grant', '2014-11-03', 'Female', 14), ('Claude', 'Knight', '2014-01-28', 'Male', 14), ('Maggie', 'Ferguson', '2014-04-14', 'Female', 14), ('Elmer', 'Rose', '2014-06-01', 'Male', 14), ('Della', 'Stone', '2014-08-19', 'Female', 14),
('Archie', 'Hawkins', '2014-10-06', 'Male', 14), ('Lizzie', 'Dunn', '2014-12-23', 'Female', 14), ('Rufus', 'Perkins', '2014-02-17', 'Male', 14), ('Cora', 'Hudson', '2014-05-04', 'Female', 14), ('Amos', 'Spencer', '2014-06-21', 'Male', 14),
('Emmaline', 'Gardner', '2014-09-08', 'Female', 14), ('Virgil', 'Stephens', '2014-10-26', 'Male', 14), ('Sallie', 'Payne', '2014-01-20', 'Female', 14), ('Otis', 'Pierce', '2014-03-07', 'Male', 14), ('Hattie', 'Berry', '2014-05-25', 'Female', 14),
('Horace', 'Matthews', '2014-07-12', 'Male', 14), ('Adeline', 'Arnold', '2014-09-29', 'Female', 14), ('Percy', 'Wagner', '2014-11-16', 'Male', 14), ('Nancy', 'Willis', '2014-02-10', 'Female', 14),

-- Grade 5C (23 students, born 2014)
('Nathaniel', 'Ray', '2014-01-04', 'Male', 15), ('Ora', 'Ruiz', '2014-03-21', 'Female', 15), ('Waldo', 'Harper', '2014-05-08', 'Male', 15), ('Lula', 'Fox', '2014-07-26', 'Female', 15), ('Jasper', 'Boyd', '2014-09-13', 'Male', 15),
('Georgia', 'Rose', '2014-10-31', 'Female', 15), ('Martin', 'Hunter', '2014-01-25', 'Male', 15), ('Daisy', 'Mills', '2014-04-11', 'Female', 15), ('Gilbert', 'Ryan', '2014-05-29', 'Male', 15), ('Julia', 'Lawrence', '2014-08-16', 'Female', 15),
('Nelson', 'Stone', '2014-10-03', 'Male', 15), ('Kate', 'Murray', '2014-12-20', 'Female', 15), ('Owen', 'Freeman', '2014-02-14', 'Male', 15), ('Lottie', 'Webb', '2014-05-01', 'Female', 15), ('Edmund', 'Simpson', '2014-06-18', 'Male', 15),
('Louisa', 'Stevens', '2014-09-05', 'Female', 15), ('Marion', 'Tucker', '2014-10-23', 'Male', 15), ('Adelaide', 'Porter', '2014-01-17', 'Female', 15), ('Wesley', 'Crawford', '2014-03-04', 'Male', 15), ('Ellen', 'Boyd', '2014-05-22', 'Female', 15),
('Hiram', 'Mason', '2014-07-09', 'Male', 15), ('Carolyn', 'Kennedy', '2014-09-26', 'Female', 15), ('Cyrus', 'Warren', '2014-11-13', 'Male', 15),

-- Grade 6A (25 students, born 2013)
('Silas', 'Dixon', '2013-01-08', 'Male', 16), ('Augusta', 'Ramos', '2013-03-25', 'Female', 16), ('Ira', 'Burns', '2013-05-12', 'Male', 16), ('Estelle', 'Gordon', '2013-07-30', 'Female', 16), ('Perry', 'Shaw', '2013-09-17', 'Male', 16),
('Effie', 'Holmes', '2013-11-04', 'Female', 16), ('Abel', 'Rice', '2013-01-29', 'Male', 16), ('Nannie', 'Robertson', '2013-04-15', 'Female', 16), ('Felix', 'Hunt', '2013-06-02', 'Male', 16), ('Ollie', 'Black', '2013-08-20', 'Female', 16),
('Ivan', 'Daniels', '2013-10-07', 'Male', 16), ('Josie', 'Palmer', '2013-12-24', 'Female', 16), ('Otto', 'Mills', '2013-02-18', 'Male', 16), ('Birdie', 'Nichols', '2013-05-05', 'Female', 16), ('Grover', 'Grant', '2013-06-22', 'Male', 16),
('Dora', 'Knight', '2013-09-09', 'Female', 16), ('Jess', 'Ferguson', '2013-10-27', 'Male', 16), ('Norah', 'Rose', '2013-01-21', 'Female', 16), ('Barney', 'Stone', '2013-03-08', 'Male', 16), ('Hulda', 'Hawkins', '2013-05-26', 'Female', 16),
('Bert', 'Dunn', '2013-07-13', 'Male', 16), ('Etta', 'Perkins', '2013-09-30', 'Female', 16), ('Harvey', 'Hudson', '2013-11-17', 'Male', 16), ('Winnie', 'Spencer', '2013-02-11', 'Female', 16), ('Levi', 'Gardner', '2013-04-28', 'Male', 16),

-- Grade 6B (24 students, born 2013)
('Dewey', 'Stephens', '2013-01-15', 'Male', 17), ('Myrtle', 'Payne', '2013-04-01', 'Female', 17), ('Owen', 'Pierce', '2013-05-19', 'Male', 17), ('Verna', 'Berry', '2013-08-06', 'Female', 17), ('Roscoe', 'Matthews', '2013-09-23', 'Male', 17),
('May', 'Arnold', '2013-11-10', 'Female', 17), ('Cornelius', 'Wagner', '2013-02-04', 'Male', 17), ('Lydia', 'Willis', '2013-04-21', 'Female', 17), ('Dave', 'Ray', '2013-06-08', 'Male', 17), ('Mollie', 'Ruiz', '2013-08-26', 'Female', 17),
('Sam', 'Harper', '2013-10-13', 'Male', 17), ('Lola', 'Fox', '2013-12-30', 'Female', 17), ('Dan', 'Boyd', '2013-01-24', 'Male', 17), ('Hilda', 'Rose', '2013-03-11', 'Female', 17), ('Jake', 'Hunter', '2013-05-29', 'Male', 17),
('Iva', 'Mills', '2013-07-16', 'Female', 17), ('Tony', 'Ryan', '2013-10-03', 'Male', 17), ('Beulah', 'Lawrence', '2013-11-20', 'Female', 17), ('Will', 'Stone', '2013-02-14', 'Male', 17), ('Ola', 'Murray', '2013-05-01', 'Female', 17),
('Lee', 'Freeman', '2013-06-18', 'Male', 17), ('Rena', 'Webb', '2013-09-05', 'Female', 17), ('Ben', 'Simpson', '2013-10-23', 'Male', 17), ('Lela', 'Stevens', '2013-01-17', 'Female', 17),

-- Grade 6C (23 students, born 2013)
('Jay', 'Tucker', '2013-01-01', 'Male', 18), ('Una', 'Porter', '2013-03-18', 'Female', 18), ('Gus', 'Crawford', '2013-05-05', 'Male', 18), ('Celia', 'Boyd', '2013-07-23', 'Female', 18), ('Andy', 'Mason', '2013-09-10', 'Male', 18),
('Leila', 'Kennedy', '2013-10-28', 'Female', 18), ('Ross', 'Warren', '2013-01-22', 'Male', 18), ('Adele', 'Dixon', '2013-04-08', 'Female', 18), ('Joe', 'Ramos', '2013-05-26', 'Male', 18), ('Dollie', 'Burns', '2013-08-13', 'Female', 18),
('Lou', 'Gordon', '2013-09-30', 'Male', 18), ('Ellie', 'Shaw', '2013-12-17', 'Female', 18), ('Bill', 'Holmes', '2013-02-11', 'Male', 18), ('Maud', 'Rice', '2013-04-28', 'Female', 18), ('Tom', 'Robertson', '2013-06-15', 'Male', 18),
('Bell', 'Hunt', '2013-09-02', 'Female', 18), ('Jim', 'Black', '2013-10-20', 'Male', 18), ('Dona', 'Daniels', '2013-01-14', 'Female', 18), ('Cal', 'Palmer', '2013-03-31', 'Male', 18), ('Nan', 'Mills', '2013-05-18', 'Female', 18),
('Ned', 'Nichols', '2013-08-05', 'Male', 18), ('Sue', 'Grant', '2013-09-22', 'Female', 18), ('Rex', 'Knight', '2013-12-09', 'Male', 18);

-- ===================== SCHOOL YEARS =====================
INSERT INTO school_years (year_name) VALUES
('2023-2024'),
('2024-2025'),
('2025-2026');

-- ===================== TERMS =====================
INSERT INTO terms (term_name, school_year_id) VALUES
('Term 1', 1), ('Term 2', 1), ('Term 3', 1),
('Term 1', 2), ('Term 2', 2), ('Term 3', 2),
('Term 1', 3), ('Term 2', 3), ('Term 3', 3);

-- ===================== SUBJECTS =====================
-- Core subjects for all grades
INSERT INTO subjects (subject_name, grade_id) VALUES
-- Grade 1
('Mathematics', 1), ('English Language', 1), ('Science', 1), ('Social Studies', 1),
('Physical Education', 1), ('Arts and Crafts', 1),
-- Grade 2
('Mathematics', 2), ('English Language', 2), ('Science', 2), ('Social Studies', 2),
('Physical Education', 2), ('Arts and Crafts', 2),
-- Grade 3
('Mathematics', 3), ('English Language', 3), ('Science', 3), ('Social Studies', 3),
('Physical Education', 3), ('Arts and Crafts', 3), ('Music', 3),
-- Grade 4
('Mathematics', 4), ('English Language', 4), ('Science', 4), ('Social Studies', 4),
('Physical Education', 4), ('Arts and Crafts', 4), ('Music', 4), ('Spanish', 4),
-- Grade 5
('Mathematics', 5), ('English Language', 5), ('Science', 5), ('Social Studies', 5),
('Physical Education', 5), ('Arts and Crafts', 5), ('Music', 5), ('Spanish', 5), ('Information Technology', 5),
-- Grade 6
('Mathematics', 6), ('English Language', 6), ('Science', 6), ('Social Studies', 6),
('Physical Education', 6), ('Arts and Crafts', 6), ('Music', 6), ('Spanish', 6), ('Information Technology', 6);

-- ===================== TEACHERS =====================
-- teacher_id maps to user_id: 1->3, 2->4, 3->5, etc.
INSERT INTO teachers (user_id, first_name, last_name, email) VALUES
(3, 'Sarah', 'Johnson', 'sarah.johnson@school.gy'),
(4, 'Mike', 'Thompson', 'mike.thompson@school.gy'),
(5, 'Emily', 'Rodriguez', 'emily.rodriguez@school.gy'),
(6, 'David', 'Chen', 'david.chen@school.gy'),
(7, 'Lisa', 'Williams', 'lisa.williams@school.gy'),
(8, 'James', 'Brown', 'james.brown@school.gy'),
(9, 'Anna', 'Martinez', 'anna.martinez@school.gy'),
(10, 'Robert', 'Taylor', 'robert.taylor@school.gy'),
(11, 'Maria', 'Garcia', 'maria.garcia@school.gy'),
(12, 'Kevin', 'Anderson', 'kevin.anderson@school.gy');

-- ===================== TEACHER SUBJECTS =====================
-- Assign teachers to subjects
INSERT INTO teacher_subjects (teacher_id, subject_id) VALUES
-- Sarah teaches Mathematics for Grades 1-3 (subject_id 1, 7, 13)
(1, 1), (1, 7), (1, 13),
-- Mike teaches English for Grades 4-6 (subject_id 21, 30, 39)
(2, 21), (2, 30), (2, 39),
-- Emily teaches Science for Grades 1-6 (subject_id 3, 9, 15, 22, 31, 40)
(3, 3), (3, 9), (3, 15), (3, 22), (3, 31), (3, 40),
-- David teaches Social Studies for Grades 1-6 (subject_id 4, 10, 16, 23, 32, 41)
(4, 4), (4, 10), (4, 16), (4, 23), (4, 32), (4, 41),
-- Lisa teaches Physical Education for all grades (subject_id 5, 11, 17, 24, 33, 42)
(5, 5), (5, 11), (5, 17), (5, 24), (5, 33), (5, 42),
-- James teaches Arts and Crafts for all grades (subject_id 6, 12, 18, 25, 34, 43)
(6, 6), (6, 12), (6, 18), (6, 25), (6, 34), (6, 43),
-- Anna teaches Music for Grades 3-6 (subject_id 19, 26, 35, 44)
(7, 19), (7, 26), (7, 35), (7, 44),
-- Robert teaches Mathematics for Grades 4-6 (subject_id 20, 29, 38)
(8, 20), (8, 29), (8, 38),
-- Maria teaches Spanish for Grades 4-6 (subject_id 27, 36, 45)
(9, 27), (9, 36), (9, 45),
-- Kevin teaches IT for Grades 5-6 (subject_id 37, 46)
(10, 37), (10, 46);

-- ===================== TEACHER CLASSES =====================
-- Assign teachers to classes
INSERT INTO teacher_classes (teacher_id, class_id) VALUES
-- Sarah assigned to Grade 1 classes (class_id 1-3)
(1, 1), (1, 2), (1, 3),
-- Mike assigned to Grade 4-6 classes (class_id 10-18)
(2, 10), (2, 11), (2, 12), (2, 13), (2, 14), (2, 15), (2, 16), (2, 17), (2, 18),
-- Emily assigned to Grade 2-3 classes (class_id 4-9)
(3, 4), (3, 5), (3, 6), (3, 7), (3, 8), (3, 9),
-- David assigned to Grade 1-2 classes (class_id 1-6)
(4, 1), (4, 2), (4, 3), (4, 4), (4, 5), (4, 6),
-- Lisa (PE) assigned to all classes (class_id 1-18)
(5, 1), (5, 2), (5, 3), (5, 4), (5, 5), (5, 6), (5, 7), (5, 8), (5, 9), (5, 10), (5, 11), (5, 12), (5, 13), (5, 14), (5, 15), (5, 16), (5, 17), (5, 18),
-- James (Arts) assigned to all classes (class_id 1-18)
(6, 1), (6, 2), (6, 3), (6, 4), (6, 5), (6, 6), (6, 7), (6, 8), (6, 9), (6, 10), (6, 11), (6, 12), (6, 13), (6, 14), (6, 15), (6, 16), (6, 17), (6, 18),
-- Anna (Music) assigned to Grade 3-6 classes (class_id 7-18)
(7, 7), (7, 8), (7, 9), (7, 10), (7, 11), (7, 12), (7, 13), (7, 14), (7, 15), (7, 16), (7, 17), (7, 18),
-- Robert (Math 4-6) assigned to Grade 4-6 classes (class_id 10-18)
(8, 10), (8, 11), (8, 12), (8, 13), (8, 14), (8, 15), (8, 16), (8, 17), (8, 18),
-- Maria (Spanish) assigned to Grade 4-6 classes (class_id 10-18)
(9, 10), (9, 11), (9, 12), (9, 13), (9, 14), (9, 15), (9, 16), (9, 17), (9, 18),
-- Kevin (IT) assigned to Grade 5-6 classes (class_id 13-18)
(10, 13), (10, 14), (10, 15), (10, 16), (10, 17), (10, 18);

-- ===================== SCORES =====================
-- Sample scores for Term 2 of 2024-2025 (term_id = 5)
-- Grade 1A students
INSERT INTO scores (student_id, subject_id, term_id, score) VALUES
(1, 1, 5, 85), (1, 2, 5, 78), (1, 3, 5, 92), (1, 4, 5, 88),
(2, 1, 5, 92), (2, 2, 5, 88), (2, 3, 5, 95), (2, 4, 5, 90),
(3, 1, 5, 76), (3, 2, 5, 82), (3, 3, 5, 79), (3, 4, 5, 85),
(4, 1, 5, 88), (4, 2, 5, 91), (4, 3, 5, 87), (4, 4, 5, 89),
(5, 1, 5, 94), (5, 2, 5, 89), (5, 3, 5, 93), (5, 4, 5, 91),
-- Grade 2A students
(16, 7, 5, 82), (16, 8, 5, 85), (16, 9, 5, 88), (16, 10, 5, 84),
(17, 7, 5, 90), (17, 8, 5, 87), (17, 9, 5, 92), (17, 10, 5, 89),
(18, 7, 5, 78), (18, 8, 5, 81), (18, 9, 5, 76), (18, 10, 5, 80),
(19, 7, 5, 86), (19, 8, 5, 88), (19, 9, 5, 85), (19, 10, 5, 87),
(20, 7, 5, 91), (20, 8, 5, 93), (20, 9, 5, 90), (20, 10, 5, 92),
-- Grade 3A students
(31, 13, 5, 84), (31, 14, 5, 86), (31, 15, 5, 89), (31, 16, 5, 85),
(32, 13, 5, 91), (32, 14, 5, 88), (32, 15, 5, 93), (32, 16, 5, 90),
(33, 13, 5, 77), (33, 14, 5, 80), (33, 15, 5, 78), (33, 16, 5, 82),
(34, 13, 5, 89), (34, 14, 5, 92), (34, 15, 5, 87), (34, 16, 5, 88),
(35, 13, 5, 95), (35, 14, 5, 91), (35, 15, 5, 94), (35, 16, 5, 93),
-- Grade 4A students
(46, 19, 5, 83), (46, 20, 5, 85), (46, 21, 5, 88), (46, 22, 5, 84),
(47, 19, 5, 90), (47, 20, 5, 87), (47, 21, 5, 91), (47, 22, 5, 89),
(48, 19, 5, 79), (48, 20, 5, 82), (48, 21, 5, 77), (48, 22, 5, 81),
(49, 19, 5, 87), (49, 20, 5, 89), (49, 21, 5, 86), (49, 22, 5, 88),
(50, 19, 5, 92), (50, 20, 5, 94), (50, 21, 5, 91), (50, 22, 5, 93),
-- Grade 5A students
(56, 28, 5, 85), (56, 29, 5, 87), (56, 30, 5, 90), (56, 31, 5, 86),
(57, 28, 5, 91), (57, 29, 5, 89), (57, 30, 5, 93), (57, 31, 5, 92),
(58, 28, 5, 78), (58, 29, 5, 81), (58, 30, 5, 79), (58, 31, 5, 83),
(59, 28, 5, 88), (59, 29, 5, 91), (59, 30, 5, 87), (59, 31, 5, 89),
(60, 28, 5, 94), (60, 29, 5, 92), (60, 30, 5, 95), (60, 31, 5, 93),
-- Grade 6A students
(71, 37, 5, 86), (71, 38, 5, 88), (71, 39, 5, 91), (71, 40, 5, 87),
(72, 37, 5, 92), (72, 38, 5, 90), (72, 39, 5, 94), (72, 40, 5, 93),
(73, 37, 5, 80), (73, 38, 5, 83), (73, 39, 5, 81), (73, 40, 5, 84),
(74, 37, 5, 89), (74, 38, 5, 92), (74, 39, 5, 88), (74, 40, 5, 90),
(75, 37, 5, 95), (75, 38, 5, 93), (75, 39, 5, 96), (75, 40, 5, 94);

-- ===================== STUDENT ENROLLMENTS =====================
-- Enroll all students for 2024-2025 school year
INSERT INTO student_enrollments (student_id, class_id, school_year_id) VALUES
-- Grade 1A
(1, 1, 2), (2, 1, 2), (3, 1, 2), (4, 1, 2), (5, 1, 2),
-- Grade 1B
(6, 2, 2), (7, 2, 2), (8, 2, 2), (9, 2, 2), (10, 2, 2),
-- Grade 1C
(11, 3, 2), (12, 3, 2), (13, 3, 2), (14, 3, 2), (15, 3, 2),
-- Grade 2A
(16, 4, 2), (17, 4, 2), (18, 4, 2), (19, 4, 2), (20, 4, 2),
-- Grade 2B
(21, 5, 2), (22, 5, 2), (23, 5, 2), (24, 5, 2), (25, 5, 2),
-- Grade 2C
(26, 6, 2), (27, 6, 2), (28, 6, 2), (29, 6, 2), (30, 6, 2),
-- Grade 3A
(31, 7, 2), (32, 7, 2), (33, 7, 2), (34, 7, 2), (35, 7, 2),
-- Grade 3B
(36, 8, 2), (37, 8, 2), (38, 8, 2), (39, 8, 2), (40, 8, 2),
-- Grade 3C
(41, 9, 2), (42, 9, 2), (43, 9, 2), (44, 9, 2), (45, 9, 2),
-- Grade 4A
(46, 10, 2), (47, 10, 2), (48, 10, 2), (49, 10, 2), (50, 10, 2),
-- Grade 4B
(51, 11, 2), (52, 11, 2), (53, 11, 2), (54, 11, 2), (55, 11, 2),
-- Grade 4C
(56, 12, 2), (57, 12, 2), (58, 12, 2), (59, 12, 2), (60, 12, 2),
-- Grade 5A
(61, 13, 2), (62, 13, 2), (63, 13, 2), (64, 13, 2), (65, 13, 2),
-- Grade 5B
(66, 14, 2), (67, 14, 2), (68, 14, 2), (69, 14, 2), (70, 14, 2),
-- Grade 5C
(71, 15, 2), (72, 15, 2), (73, 15, 2), (74, 15, 2), (75, 15, 2),
-- Grade 6A
(76, 16, 2), (77, 16, 2), (78, 16, 2), (79, 16, 2), (80, 16, 2),
-- Grade 6B
(81, 17, 2), (82, 17, 2), (83, 17, 2), (84, 17, 2), (85, 17, 2),
-- Grade 6C
(86, 18, 2), (87, 18, 2), (88, 18, 2), (89, 18, 2), (90, 18, 2);

-- =====================================================
-- SUMMARY
-- =====================================================
-- Database setup completed successfully!
--
-- Default login credentials:
-- Admin: username='admin', password='password123'
-- Teacher: username='sarah_teacher', password='password123'
--
-- Contains:
-- - 2 roles (office_admin, teacher)
-- - 6 users (2 admins, 4 teachers)
-- - 6 grades (Grade 1-6)
-- - 18 classes (Grades 1-6, Classes A, B, C each)
-- - 90 students (5 students per class)
-- - 3 school years (2023-2024, 2024-2025, 2025-2026)
-- - 9 terms (3 per year)
-- - 46 subjects (distributed across grades)
-- - 4 teachers
-- - 120 score records (for current term)
-- - 90 student enrollments
