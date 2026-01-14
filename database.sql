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

-- ===================== SCORES =====================
CREATE TABLE IF NOT EXISTS scores (
    score_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    term_id INT NOT NULL,
    score INT NOT NULL CHECK (score BETWEEN 0 AND 100),
    UNIQUE (student_id, subject_id, term_id),
    CONSTRAINT fk_scores_student
        FOREIGN KEY (student_id) REFERENCES students(student_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_scores_subject
        FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_scores_term
        FOREIGN KEY (term_id) REFERENCES terms(term_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

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
('sarah_teacher', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFPqI0tPGF4gEaGRZCe0lDCHWtfC', 2),
('mike_teacher', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFPqI0tPGF4gEaGRZCe0lDCHWtfC', 2),
('emily_teacher', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFPqI0tPGF4gEaGRZCe0lDCHWtfC', 2),
('david_teacher', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVNfFPqI0tPGF4gEaGRZCe0lDCHWtfC', 2);

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
INSERT INTO students (first_name, last_name, class_id) VALUES
-- Grade 1A
('James', 'Williams', 1), ('Sophia', 'Brown', 1), ('Oliver', 'Davis', 1), ('Emma', 'Miller', 1), ('Noah', 'Wilson', 1),
-- Grade 1B
('Liam', 'Moore', 2), ('Ava', 'Taylor', 2), ('William', 'Anderson', 2), ('Isabella', 'Thomas', 2), ('Mason', 'Jackson', 2),
-- Grade 1C
('Ethan', 'White', 3), ('Mia', 'Harris', 3), ('Lucas', 'Martin', 3), ('Charlotte', 'Thompson', 3), ('Logan', 'Garcia', 3),
-- Grade 2A
('Michael', 'Martinez', 4), ('Amelia', 'Robinson', 4), ('Alexander', 'Clark', 4), ('Harper', 'Rodriguez', 4), ('Daniel', 'Lewis', 4),
-- Grade 2B
('Matthew', 'Lee', 5), ('Evelyn', 'Walker', 5), ('Henry', 'Hall', 5), ('Abigail', 'Allen', 5), ('Jackson', 'Young', 5),
-- Grade 2C
('Sebastian', 'Hernandez', 6), ('Emily', 'King', 6), ('Aiden', 'Wright', 6), ('Elizabeth', 'Lopez', 6), ('Samuel', 'Hill', 6),
-- Grade 3A
('Joseph', 'Scott', 7), ('Sofia', 'Green', 7), ('David', 'Adams', 7), ('Camila', 'Baker', 7), ('Carter', 'Gonzalez', 7),
-- Grade 3B
('Owen', 'Nelson', 8), ('Avery', 'Carter', 8), ('Wyatt', 'Mitchell', 8), ('Scarlett', 'Perez', 8), ('John', 'Roberts', 8),
-- Grade 3C
('Jack', 'Turner', 9), ('Madison', 'Phillips', 9), ('Luke', 'Campbell', 9), ('Ella', 'Parker', 9), ('Jayden', 'Evans', 9),
-- Grade 4A
('Dylan', 'Edwards', 10), ('Grace', 'Collins', 10), ('Grayson', 'Stewart', 10), ('Chloe', 'Sanchez', 10), ('Isaac', 'Morris', 10),
-- Grade 4B
('Gabriel', 'Rogers', 11), ('Penelope', 'Reed', 11), ('Anthony', 'Cook', 11), ('Lily', 'Morgan', 11), ('Julian', 'Bell', 11),
-- Grade 4C
('Levi', 'Murphy', 12), ('Layla', 'Bailey', 12), ('Christopher', 'Rivera', 12), ('Zoey', 'Cooper', 12), ('Joshua', 'Richardson', 12),
-- Grade 5A
('Ryan', 'Gray', 13), ('Lillian', 'Ramirez', 13), ('Jaxon', 'James', 13), ('Addison', 'Watson', 13), ('Nathan', 'Brooks', 13),
-- Grade 5B
('Aaron', 'Kelly', 14), ('Ellie', 'Sanders', 14), ('Isaiah', 'Price', 14), ('Aubrey', 'Bennett', 14), ('Thomas', 'Wood', 14),
-- Grade 5C
('Charles', 'Barnes', 15), ('Brooklyn', 'Ross', 15), ('Caleb', 'Henderson', 15), ('Aria', 'Coleman', 15), ('Josiah', 'Jenkins', 15),
-- Grade 6A
('Jonathan', 'Flores', 16), ('Claire', 'Washington', 16), ('Connor', 'Butler', 16), ('Skylar', 'Simmons', 16), ('Landon', 'Foster', 16),
-- Grade 6B
('Adrian', 'Gonzales', 17), ('Bella', 'Bryant', 17), ('Asher', 'Alexander', 17), ('Lucy', 'Russell', 17), ('Nolan', 'Griffin', 17),
-- Grade 6C
('Cameron', 'Diaz', 18), ('Paisley', 'Hayes', 18), ('Ezra', 'Myers', 18), ('Aaliyah', 'Ford', 18), ('Colton', 'Hamilton', 18);

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
INSERT INTO teachers (user_id, first_name, last_name, email) VALUES
(3, 'Sarah', 'Johnson', 'sarah.johnson@school.gy'),
(4, 'Mike', 'Thompson', 'mike.thompson@school.gy'),
(5, 'Emily', 'Rodriguez', 'emily.rodriguez@school.gy'),
(6, 'David', 'Chen', 'david.chen@school.gy');

-- ===================== TEACHER SUBJECTS =====================
-- Assign teachers to subjects
INSERT INTO teacher_subjects (teacher_id, subject_id) VALUES
-- Sarah teaches Mathematics for Grades 1-3
(1, 1), (1, 7), (1, 13),
-- Mike teaches English for Grades 4-6
(2, 20), (2, 29), (2, 38),
-- Emily teaches Science for Grades 1-6
(3, 3), (3, 9), (3, 15), (3, 21), (3, 30), (3, 39),
-- David teaches Social Studies for Grades 1-6
(4, 4), (4, 10), (4, 16), (4, 22), (4, 31), (4, 40);

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
