# MVC Reorganization Plan

## Current vs Proposed Structure

### ❌ Current Structure (Mixed)
```
cse3101-g17-proj/
├── auth/               ← Authentication pages (views)
├── classes/            ← Class management pages (views)
├── dashboard/          ← Dashboard pages (views)
├── reports/            ← Report pages (views)
├── school/             ← School year/term pages (views)
├── scores/             ← Score pages (views)
├── students/           ← Student pages (views)
├── subjects/           ← Subject pages (views)
├── users/              ← User pages (views)
├── assets/             ← CSS, JS, icons
├── includes/           ← Shared components (header, sidebar, footer)
├── temp/               ← Temporary files
└── models/             ← ✅ Model classes (NEW)
```

### ✅ Proposed MVC Structure
```
cse3101-g17-proj/
├── models/                     ← Data layer (DONE ✅)
│   ├── Model.php
│   ├── Subject.php
│   ├── Student.php
│   ├── User.php
│   ├── SchoolClass.php
│   └── Score.php
│
├── views/                      ← Presentation layer (TO ORGANIZE)
│   ├── auth/                   ← Authentication views
│   │   ├── login.php
│   │   ├── register.php
│   │   └── logout.php
│   │
│   ├── subjects/               ← Subject views
│   │   ├── subjects_list.php
│   │   ├── subject_create.php
│   │   ├── subject_edit.php
│   │   ├── subjectPage.php
│   │   └── subjectManagement.php
│   │
│   ├── students/               ← Student views
│   │   ├── studentManagement.php
│   │   ├── studentPage.php
│   │   └── studentProfile.php
│   │
│   ├── users/                  ← User views
│   │   ├── userManagement.php
│   │   ├── userPage.php
│   │   └── userProfile.php
│   │
│   ├── classes/                ← Class views
│   │   ├── classManagement.php
│   │   └── classPage.php
│   │
│   ├── scores/                 ← Score views
│   │   ├── scores_list.php
│   │   ├── scores_entry.php
│   │   └── score.php
│   │
│   ├── reports/                ← Report views
│   │   ├── reports_main.php
│   │   ├── report_student_card.php
│   │   └── report_grade_average.php
│   │
│   ├── dashboard/              ← Dashboard views
│   │   └── index.php
│   │
│   └── school/                 ← School year/term views
│       ├── school_years_list.php
│       ├── school_year_create.php
│       ├── term_create.php
│       ├── term_manage.php
│       ├── terms.php
│       └── years_terms.php
│
├── controllers/                ← Business logic (TO CREATE)
│   ├── SubjectController.php
│   ├── StudentController.php
│   ├── UserController.php
│   ├── ClassController.php
│   ├── ScoreController.php
│   └── ReportController.php
│
├── config/                     ← Configuration (DONE ✅)
│   ├── database.php
│   └── app.php
│
├── helpers/                    ← Helper functions (TO CREATE)
│   ├── auth_helper.php
│   ├── validation_helper.php
│   └── utility_helper.php
│
├── public/                     ← Public accessible files
│   ├── index.php              ← Main entry point (router)
│   ├── assets/                ← CSS, JS, images
│   │   ├── css/
│   │   └── icons/
│   └── uploads/               ← User uploaded files
│
├── includes/                   ← Shared components (KEEP HERE)
│   ├── header.php
│   ├── sidebar.php
│   └── footer.php
│
├── temp/                       ← Temporary files (KEEP)
│   ├── users.json
│   └── seed_user.php
│
├── .htaccess                   ← URL rewriting (TO CREATE)
├── index.php                   ← Root router (TO CREATE)
└── MVC_GUIDE.md               ← Documentation (DONE ✅)
```

---

## What Goes Where?

### 📂 `/models/` - Data Layer
**What:** Classes that interact with data (database/session)
**Examples:** Subject.php, Student.php, User.php
**Status:** ✅ COMPLETED

**Contains:**
- Database queries (when DB is setup)
- Data validation logic
- Business rules for data
- CRUD operations

**Files:**
- ✅ Model.php (base class)
- ✅ Subject.php
- ✅ Student.php
- ✅ User.php
- ✅ SchoolClass.php
- ✅ Score.php

---

### 📂 `/views/` - Presentation Layer
**What:** HTML pages that display data to users
**Examples:** subjects_list.php, studentManagement.php
**Status:** ⚠️ NEEDS REORGANIZATION

**Should contain:**
- HTML markup
- Minimal PHP for display logic (loops, if statements)
- No database queries
- No business logic

**Files to move:**
- `/auth/*.php` → `/views/auth/`
- `/subjects/*.php` → `/views/subjects/`
- `/students/*.php` → `/views/students/`
- `/users/*.php` → `/views/users/`
- `/classes/*.php` → `/views/classes/`
- `/scores/*.php` → `/views/scores/`
- `/reports/*.php` → `/views/reports/`
- `/dashboard/*.php` → `/views/dashboard/`
- `/school/*.php` → `/views/school/`

---

### 📂 `/controllers/` - Business Logic Layer
**What:** Classes that handle user requests and coordinate between models and views
**Examples:** SubjectController.php, StudentController.php
**Status:** ❌ NOT CREATED YET (Optional for now)

**Will contain:**
- Request handling (GET, POST)
- Calling model methods
- Data preparation for views
- Redirects and responses

**Example structure:**
```php
class SubjectController {
    private $subjectModel;

    public function __construct() {
        $this->subjectModel = new Subject();
    }

    public function index() {
        // Show all subjects
        $subjects = $this->subjectModel->getAll();
        require __DIR__ . '/../views/subjects/subjects_list.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle form submission
            $data = $_POST;
            $errors = $this->subjectModel->validateSubject($data);

            if (empty($errors)) {
                $this->subjectModel->create($data);
                header('Location: /subjects');
                exit;
            }
        }
        require __DIR__ . '/../views/subjects/subject_create.php';
    }
}
```

---

### 📂 `/config/` - Configuration
**What:** Application settings and database config
**Status:** ✅ COMPLETED

**Contains:**
- database.php - Database connection settings
- app.php - Application constants and settings

---

### 📂 `/helpers/` - Utility Functions
**What:** Reusable helper functions
**Status:** ❌ TO CREATE

**Should contain:**
```php
// helpers/auth_helper.php
function isLoggedIn() { ... }
function requireRole($roles) { ... }
function getCurrentUser() { ... }

// helpers/validation_helper.php
function validateEmail($email) { ... }
function validatePhone($phone) { ... }
function sanitizeInput($data) { ... }

// helpers/utility_helper.php
function redirect($url) { ... }
function formatDate($date) { ... }
function generateToken() { ... }
```

---

### 📂 `/public/` - Public Files
**What:** Files accessible via web browser
**Status:** ⚠️ NEEDS SETUP

**Should contain:**
- index.php (main entry point/router)
- /assets/ (CSS, JS, icons)
- /uploads/ (user uploaded files)

---

### 📂 `/includes/` - Shared Components
**What:** Reusable page components
**Status:** ✅ KEEP AS IS

**Contains:**
- header.php - Page header with navigation
- sidebar.php - Sidebar menu
- footer.php - Page footer

---

## Migration Steps

### Phase 1: Current Setup (Keep Working)
**Status:** Your current structure works fine for now!

**Keep using:**
- `/auth/`, `/subjects/`, `/students/`, etc. in current locations
- Models from `/models/` directory
- Everything works as-is

### Phase 2: Gradual Migration (When Ready)
**Do this later when you have time:**

1. **Create helper files**
   ```bash
   # Move auth functions from header.php to helpers
   cp includes/header.php helpers/auth_helper.php
   # Extract only auth functions
   ```

2. **Move views gradually**
   ```bash
   # Move one feature at a time
   mv subjects/*.php views/subjects/
   # Update paths in moved files
   ```

3. **Create controllers** (optional)
   ```bash
   # Create controller for each feature
   # Start with simplest feature (subjects)
   ```

4. **Setup public directory**
   ```bash
   # Move assets to public
   mv assets public/assets
   # Create main router
   ```

### Phase 3: Full MVC (Future)
**Do this when database is ready:**

1. Setup database connection
2. Update models to use PDO
3. Create all controllers
4. Setup URL routing
5. Move everything to proper MVC structure

---

## Recommended Approach

### ✅ DO NOW (Easy & Safe):
1. **Use models in existing pages**
   ```php
   // In subjects_list.php
   require_once __DIR__ . '/models/Subject.php';
   $subjectModel = new Subject();
   $subjects = $subjectModel->getAll();
   ```

2. **Create helper functions**
   - Extract common functions to helpers/
   - Make code more reusable

3. **Add comments and documentation**
   - Document what each file does
   - Add TODO comments for future improvements

### ⏰ DO LATER (When You Have Time):
1. **Reorganize file structure**
   - Move files to views/
   - Update all file paths
   - Test thoroughly

2. **Create controllers**
   - Start with one feature
   - Gradually convert all features

3. **Setup routing**
   - Create main index.php router
   - Use .htaccess for clean URLs

### 🚀 DO WHEN DATABASE READY:
1. **Update models**
   - Connect to MySQL
   - Replace session storage with database

2. **Add advanced features**
   - Pagination
   - Search/filter
   - Export functionality

---

## File Path Examples

### Current Paths (Working Now):
```php
// In subjects_list.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/models/Subject.php';
```

### Future Paths (After Reorganization):
```php
// In views/subjects/subjects_list.php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../models/Subject.php';
```

---

## Quick Reference

### Where Should Each File Type Go?

| File Type | Current Location | Proper MVC Location | Priority |
|-----------|-----------------|-------------------|----------|
| Subject.php, Student.php | `/models/` | ✅ Already correct | - |
| subjects_list.php | `/subjects/` | `/views/subjects/` | Low |
| SubjectController.php | Not created | `/controllers/` | Medium |
| database.php, app.php | `/config/` | ✅ Already correct | - |
| auth functions | `/includes/header.php` | `/helpers/auth_helper.php` | Medium |
| CSS, JS, icons | `/assets/` | `/public/assets/` | Low |
| header, sidebar, footer | `/includes/` | ✅ Keep as is | - |

---

## Summary

### ✅ What's Done:
- Models created and working
- Config files created
- MVC structure explained

### ⚠️ What's Partially Done:
- Views are in old locations (but working)
- No controllers yet (not required immediately)
- Assets not in public/ folder

### ❌ What's Not Done:
- Controllers not created (optional)
- Files not moved to views/
- Helpers not extracted
- Public directory not setup
- Routing not implemented

### 💡 Recommendation:
**Keep current structure working!** Use models in your existing pages. Move to full MVC structure later when you have time or when database is ready.

**The models are the most important part for OOP/MVC requirements, and those are done! ✅**
