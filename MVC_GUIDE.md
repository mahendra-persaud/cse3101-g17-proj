# MVC Architecture Guide for School Management System

## Overview

Your project has been reorganized into a **Model-View-Controller (MVC)** architecture. This makes your code more organized, maintainable, and easier to understand.

## Directory Structure

```
cse3101-g17-proj/
├── models/              # Model classes (data layer)
│   ├── Model.php        # Base Model class
│   ├── Subject.php      # Subject Model
│   ├── Student.php      # Student Model
│   ├── User.php         # User Model
│   ├── SchoolClass.php  # Class Model
│   └── Score.php        # Score Model
│
├── controllers/         # Controller classes (business logic)
│   └── (to be created)
│
├── views/               # View files (presentation)
│   └── (existing PHP files will go here)
│
├── config/              # Configuration files
│   ├── database.php     # Database configuration
│   └── app.php          # Application settings
│
├── helpers/             # Helper functions
│   └── (utility functions)
│
├── includes/            # Shared components
│   ├── header.php
│   ├── sidebar.php
│   └── footer.php
│
├── assets/              # CSS, JS, images
│   ├── css/
│   └── icons/
│
└── (existing feature folders for now)
```

## What is MVC?

### Model (Data Layer)
- Handles all database operations (CRUD)
- Contains business logic for data validation
- Examples: `Subject.php`, `Student.php`, `User.php`

### View (Presentation Layer)
- Displays data to the user
- Contains HTML and minimal PHP for display logic
- Examples: Your existing PHP pages (subjects_list.php, studentManagement.php, etc.)

### Controller (Business Logic Layer)
- Receives user input from views
- Calls models to fetch/modify data
- Decides which view to display
- Examples: SubjectController, StudentController (to be created)

---

## How to Use the Models

### 1. Including Models

```php
<?php
require_once __DIR__ . '/models/Subject.php';
require_once __DIR__ . '/models/Student.php';
```

### 2. Using Subject Model

```php
// Create instance
$subjectModel = new Subject();

// Get all subjects
$subjects = $subjectModel->getAll();

// Find subject by ID
$subject = $subjectModel->find(1);

// Get subjects by grade
$grade4Subjects = $subjectModel->getByGrade('Grade 4');

// Create new subject
$newSubjectData = [
    'code' => 'GEOG',
    'name' => 'Geography',
    'grade' => 'All Grades',
    'description' => 'Study of earth and its features'
];

// Validate data first
$errors = $subjectModel->validateSubject($newSubjectData);
if (empty($errors)) {
    $id = $subjectModel->create($newSubjectData);
    echo "Subject created with ID: $id";
} else {
    print_r($errors);
}

// Update subject
$subjectModel->update(1, [
    'description' => 'Advanced mathematics curriculum'
]);

// Delete subject
$subjectModel->delete(1);

// Check if code exists
if ($subjectModel->codeExists('MATH')) {
    echo "Subject code already exists";
}
```

### 3. Using Student Model

```php
$studentModel = new Student();

// Get all students
$students = $studentModel->getAll();

// Get students by grade
$grade5Students = $studentModel->getByGrade('5');

// Get students by class
$classAStudents = $studentModel->getByClass('Class A');

// Get students by grade and class
$students = $studentModel->getByGradeAndClass('4', 'Class B');

// Create new student
$newStudent = [
    'reg_number' => 'ST2024001',
    'name' => 'John Doe',
    'grade' => '5',
    'class' => 'Class A',
    'email' => 'john.doe@example.com',
    'phone' => '592-123-4567',
    'dob' => '2011-06-15',
    'gender' => 'Male',
    'address' => '123 Main St'
];

$errors = $studentModel->validateStudent($newStudent);
if (empty($errors)) {
    $id = $studentModel->create($newStudent);
}

// Generate registration number
$nextRegNum = $studentModel->generateRegNumber(); // Returns ST2024006
```

### 4. Using User Model

```php
$userModel = new User();

// Authenticate user
$user = $userModel->authenticate('admin@school.com', 'password123');
if ($user) {
    $_SESSION['user'] = $user;
    header('Location: dashboard.php');
}

// Get users by role
$admins = $userModel->getByRole('office_admin');
$teachers = $userModel->getByRole('teacher');

// Check if email exists
if ($userModel->emailExists('test@example.com')) {
    echo "Email already registered";
}
```

### 5. Using SchoolClass Model

```php
$classModel = new SchoolClass();

// Get classes by grade
$grade3Classes = $classModel->getByGrade('3');

// Check if class has capacity
if ($classModel->hasCapacity(5)) {
    echo "Class has available spots";
}

// Get specific class
$class = $classModel->getByGradeAndName('4', 'Class A');
```

### 6. Using Score Model

```php
$scoreModel = new Score();

// Get scores for a student
$studentScores = $scoreModel->getByStudent(1, 1); // Student ID 1, Term 1

// Calculate student average
$average = $scoreModel->calculateStudentAverage(1, 1); // 90.5

// Get letter grade
$grade = $scoreModel->getLetterGrade(92); // Returns 'A'

// Create new score
$newScore = [
    'student_id' => 1,
    'subject_id' => 3,
    'term' => 1,
    'score' => 85
];

$errors = $scoreModel->validateScore($newScore);
if (empty($errors)) {
    $newScore['grade'] = $scoreModel->getLetterGrade($newScore['score']);
    $scoreModel->create($newScore);
}
```

---

## Updating Existing Pages to Use Models

### Before (subjects_list.php):
```php
<?php
// Hardcoded data
$subjects = [
    ['id' => '1', 'code' => 'MATH', 'name' => 'Mathematics', ...],
    ['id' => '2', 'code' => 'ENG', 'name' => 'English Language', ...],
    // ...
];
?>
```

### After (subjects_list.php):
```php
<?php
require_once __DIR__ . '/models/Subject.php';

// Use model to get data
$subjectModel = new Subject();
$subjects = $subjectModel->getAll();
?>
```

### Example: Subject Create Page with Model

```php
<?php
require_once __DIR__ . '/../models/Subject.php';
require_once __DIR__ . '/../includes/header.php';

$subjectModel = new Subject();
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'code' => $_POST['subject_code'] ?? '',
        'name' => $_POST['subject_name'] ?? '',
        'grade' => $_POST['grade'] ?? '',
        'description' => $_POST['description'] ?? ''
    ];

    // Validate
    $errors = $subjectModel->validateSubject($data);

    // Check for duplicate code
    if (empty($errors) && $subjectModel->codeExists($data['code'])) {
        $errors['code'] = 'Subject code already exists';
    }

    // Create if valid
    if (empty($errors)) {
        $id = $subjectModel->create($data);
        $success = true;
        header('Location: subjects_list.php?success=1');
        exit;
    }
}
?>

<!-- Form HTML -->
<form method="POST">
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <input type="text" name="subject_code" placeholder="Subject Code" required>
    <input type="text" name="subject_name" placeholder="Subject Name" required>
    <select name="grade" required>
        <option value="">Select Grade</option>
        <option value="Grade 1">Grade 1</option>
        <!-- ... -->
    </select>
    <textarea name="description" required></textarea>
    <button type="submit">Create Subject</button>
</form>
```

---

## Benefits of MVC Architecture

### 1. **Separation of Concerns**
- Data logic (Models) is separate from display logic (Views)
- Easy to update UI without touching database code
- Easy to change database without touching UI

### 2. **Reusability**
- Models can be used across multiple pages
- No need to duplicate database queries

### 3. **Maintainability**
- Easier to find and fix bugs
- Changes in one layer don't affect others

### 4. **Testability**
- Models can be tested independently
- Business logic is centralized

### 5. **Team Collaboration**
- Frontend devs work on views
- Backend devs work on models
- Less merge conflicts

---

## Next Steps

### Phase 1: Database Setup (When Ready)
1. Create MySQL database: `school_management`
2. Run SQL schema (will be provided)
3. Update `config/database.php` with credentials
4. Update Model.php to use actual PDO queries

### Phase 2: Replace Hardcoded Data
1. Update existing pages to use Models instead of arrays
2. Remove duplicate data definitions
3. Use model validation in forms

### Phase 3: Add Controllers (Optional but Recommended)
1. Create Controller classes
2. Move business logic from views to controllers
3. Make views purely presentational

---

## Model Methods Reference

### All Models Inherit:
- `getAll()` - Get all records
- `find($id)` - Find by ID
- `findBy($field, $value)` - Find by field
- `create($data)` - Create new record
- `update($id, $data)` - Update record
- `delete($id)` - Delete record
- `validate($data, $rules)` - Validate data

### Subject Model:
- `getByGrade($grade)` - Get subjects for grade
- `validateSubject($data)` - Validate subject data
- `codeExists($code, $excludeId)` - Check duplicate code

### Student Model:
- `getByGrade($grade)` - Get students by grade
- `getByClass($class)` - Get students by class
- `getByGradeAndClass($grade, $class)` - Combined filter
- `validateStudent($data)` - Validate student data
- `regNumberExists($regNumber, $excludeId)` - Check duplicate
- `generateRegNumber()` - Generate next reg number

### User Model:
- `findByEmail($email)` - Find user by email
- `findByUsername($username)` - Find by username
- `getByRole($role)` - Get users by role
- `authenticate($email, $password)` - Login validation
- `validateUser($data, $isUpdate)` - Validate user data
- `emailExists($email, $excludeId)` - Check duplicate email
- `usernameExists($username, $excludeId)` - Check duplicate username

### SchoolClass Model:
- `getByGrade($grade)` - Get classes by grade
- `getByTeacher($teacherId)` - Get teacher's classes
- `hasCapacity($classId)` - Check if class is full
- `getByGradeAndName($grade, $name)` - Find specific class
- `validateClass($data)` - Validate class data

### Score Model:
- `getByStudent($studentId, $term)` - Get student scores
- `getBySubject($subjectId, $term)` - Get subject scores
- `getByStudentSubjectTerm($studentId, $subjectId, $term)` - Specific score
- `calculateStudentAverage($studentId, $term)` - Calculate average
- `calculateSubjectAverage($subjectId, $term)` - Subject average
- `getLetterGrade($score)` - Convert to letter grade
- `validateScore($data)` - Validate score data

---

## Tips

1. **Always validate before creating/updating**:
   ```php
   $errors = $model->validateData($data);
   if (empty($errors)) {
       $model->create($data);
   }
   ```

2. **Use try-catch when database is added**:
   ```php
   try {
       $model->create($data);
   } catch (Exception $e) {
       $errors[] = 'Database error occurred';
   }
   ```

3. **Sanitize user input**:
   ```php
   $code = htmlspecialchars($_POST['code']);
   ```

4. **Check existence before operations**:
   ```php
   if ($model->codeExists($code)) {
       $errors[] = 'Code already exists';
   }
   ```

---

## Questions?

This architecture will make your project much easier to maintain and grade. The models are ready to use right now with session storage, and will seamlessly switch to database when you add MySQL!
