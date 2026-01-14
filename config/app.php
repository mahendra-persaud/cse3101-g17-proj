<?php
// main settings for the app
// stuff like name and timezone

// name of the website
define('APP_NAME', 'School Management System');

// version number
define('APP_VERSION', '1.0.0');

// setting the time to guyana time
date_default_timezone_set('America/Guyana');

// show all errors (good for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// session settings
// makes cookies safer
if (session_status() === PHP_SESSION_NONE) {
    @ini_set('session.cookie_httponly', 1);
    @ini_set('session.use_only_cookies', 1);
    @ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    
    // session lasts for 1 day
    @ini_set('session.gc_maxlifetime', 86400);
}

// Grades available
define('AVAILABLE_GRADES', ['1', '2', '3', '4', '5', '6']);

// how many terms we have
define('TERMS_PER_YEAR', 3);

// names of the terms
define('TERM_NAMES', [
    1 => 'Term 1 (Sept - Dec)',
    2 => 'Term 2 (Jan - Apr)',
    3 => 'Term 3 (May - Aug)'
]);

// who can log in
define('USER_ROLES', [
    'office_admin' => 'Office Administrator',
    'teacher' => 'Teacher'
]);

// score to grade conversion
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
