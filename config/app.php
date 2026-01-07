<?php
/**
 * Application Configuration
 *
 * General application settings and constants
 */

// Application name
define('APP_NAME', 'St. Joseph Primary School Management System');

// Application version
define('APP_VERSION', '1.0.0');

// Timezone
date_default_timezone_set('America/Guyana');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Session lifetime (24 hours)
ini_set('session.gc_maxlifetime', 86400);

// Grades available
define('AVAILABLE_GRADES', ['1', '2', '3', '4', '5', '6']);

// Terms per year
define('TERMS_PER_YEAR', 3);

// Term names
define('TERM_NAMES', [
    1 => 'Term 1 (Sept - Dec)',
    2 => 'Term 2 (Jan - Apr)',
    3 => 'Term 3 (May - Aug)'
]);

// User roles
define('USER_ROLES', [
    'office_admin' => 'Office Administrator',
    'teacher' => 'Teacher'
]);

// Grading scale
define('GRADING_SCALE', [
    'A' => ['min' => 90, 'max' => 100, 'description' => 'Excellent'],
    'B' => ['min' => 80, 'max' => 89, 'description' => 'Very Good'],
    'C' => ['min' => 70, 'max' => 79, 'description' => 'Good'],
    'D' => ['min' => 60, 'max' => 69, 'description' => 'Satisfactory'],
    'F' => ['min' => 0, 'max' => 59, 'description' => 'Needs Improvement']
]);

// Pagination settings
define('ITEMS_PER_PAGE', 20);

// File upload settings
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
