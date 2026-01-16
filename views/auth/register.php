<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';

$errors = [];
$success = false;
$old = ['username'=>'','email'=>'','role'=>'teacher'];
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'teacher';
    $old = ['username'=>$username,'email'=>$email,'role'=>$role];

    // Validation
    if ($username === '') {
        $errors[] = 'Username is required.';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    // Map role name to role_id
    $role_id = ($role === 'office_admin') ? 1 : 2; // 1 = office_admin, 2 = teacher

    if (empty($errors)) {
        try {
            $userModel = new User();

            // Check if username already exists
            if ($userModel->usernameExists($username)) {
                $errors[] = 'Username is already taken.';
            } else {
                // Create the user
                $userId = $userModel->createUser([
                    'username' => $username,
                    'password' => $password,
                    'role_id' => $role_id
                ]);

                if ($userId) {
                    // If teacher, also create a teacher record
                    if ($role_id === 2) {
                        $pdo = getDBConnection();
                        $stmt = $pdo->prepare("INSERT INTO teachers (user_id, first_name, last_name, email) VALUES (?, ?, ?, ?)");
                        // Use username as placeholder for name, they can update later
                        $nameParts = explode('_', $username);
                        $firstName = ucfirst($nameParts[0] ?? $username);
                        $lastName = isset($nameParts[1]) ? ucfirst($nameParts[1]) : '';
                        $stmt->execute([$userId, $firstName, $lastName, $email]);
                    }

                    header('Location: ' . $projectRoot . '/views/auth/loginPage.php?registered=1');
                    exit;
                } else {
                    $errors[] = 'Failed to create account. Please try again.';
                }
            }
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            $errors[] = 'A database error occurred. Please try again.';
        }
    }
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - School Management System</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      background: #ffffff;
      color: #1a1a1a;
      overflow-x: hidden;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 60px;
      background: #ffffff;
      border-bottom: 1px solid #e5e7eb;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 24px;
      font-weight: 800;
      color: #1a1a1a;
      letter-spacing: -0.5px;
    }

    .logo-img {
      width: 40px;
      height: 40px;
      object-fit: contain;
    }

    .logo span {
      background: linear-gradient(135deg, #0891b2, #0e7490);
      background-clip: text;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .nav-tagline {
      display: flex;
      align-items: center;
      color: #9ca3af;
      font-size: 14px;
      font-weight: 500;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      font-style: italic;
    }

    .nav-tagline span::before,
    .nav-tagline span::after {
      content: '\2022';
      margin: 0 10px;
      color: #0891b2;
      font-style: normal;
    }

    .nav-buttons {
      display: flex;
      gap: 16px;
    }

    .btn-secondary {
      padding: 10px 24px;
      background: transparent;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      color: #1a1a1a;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
      display: inline-block;
    }

    .btn-secondary:hover {
      background: #f9fafb;
      border-color: #d1d5db;
    }

    .btn-primary {
      padding: 10px 24px;
      background: #0891b2;
      border: none;
      border-radius: 8px;
      color: white;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
      display: inline-block;
    }

    .btn-primary:hover {
      background: #0e7490;
      transform: translateY(-1px);
    }

    .main-content {
      max-width: 1400px;
      margin: 0 auto;
      padding: 80px 60px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: start;
    }

    .benefits-section h2 {
      font-size: 36px;
      font-weight: 700;
      color: #1a1a1a;
      margin-bottom: 24px;
      line-height: 1.3;
    }

    .benefit-item {
      display: flex;
      gap: 16px;
      margin-bottom: 24px;
      align-items: start;
    }

    .benefit-icon {
      width: 48px;
      height: 48px;
      min-width: 48px;
      background: linear-gradient(135deg, #e0f2fe, #bae6fd);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
    }

    .benefit-text h3 {
      font-size: 18px;
      font-weight: 600;
      color: #1a1a1a;
      margin-bottom: 6px;
    }

    .benefit-text p {
      font-size: 15px;
      color: #6b7280;
      line-height: 1.5;
    }

    .register-card {
      background: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 16px;
      padding: 48px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
    }

    .register-card h2 {
      font-size: 28px;
      font-weight: 700;
      color: #1a1a1a;
      margin-bottom: 8px;
    }

    .register-card .subtitle {
      font-size: 15px;
      color: #6b7280;
      margin-bottom: 32px;
    }

    .form-group {
      margin-bottom: 24px;
    }

    .form-group label {
      display: block;
      font-size: 14px;
      font-weight: 500;
      color: #374151;
      margin-bottom: 8px;
    }

    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="password"],
    .form-group select {
      width: 100%;
      padding: 12px 16px;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      font-size: 15px;
      transition: all 0.2s;
      background: #ffffff;
    }

    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: #0891b2;
      box-shadow: 0 0 0 3px rgba(8, 145, 178, 0.1);
    }

    .submit-btn {
      width: 100%;
      padding: 14px;
      background: #0891b2;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }

    .submit-btn:hover {
      background: #0e7490;
      transform: translateY(-1px);
    }

    .divider {
      margin: 24px 0;
      text-align: center;
      color: #9ca3af;
      font-size: 14px;
      position: relative;
    }

    .divider::before,
    .divider::after {
      content: '';
      position: absolute;
      top: 50%;
      width: 40%;
      height: 1px;
      background: #e5e7eb;
    }

    .divider::before { left: 0; }
    .divider::after { right: 0; }

    .login-prompt {
      text-align: center;
      margin-top: 24px;
      font-size: 14px;
      color: #6b7280;
    }

    .login-prompt a {
      color: #0891b2;
      text-decoration: none;
      font-weight: 600;
    }

    .login-prompt a:hover {
      text-decoration: underline;
    }

    .error-box {
      background: #fee2e2;
      border: 1px solid #ef4444;
      color: #991b1b;
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 14px;
    }

    .error-box ul {
      margin: 8px 0 0 20px;
      padding: 0;
    }

    .error-box li {
      margin-bottom: 4px;
    }

    .password-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .password-wrapper input {
      padding-right: 45px;
    }

    .toggle-password {
      position: absolute;
      right: 12px;
      background: none;
      border: none;
      cursor: pointer;
      padding: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #6b7280;
      transition: color 0.2s;
    }

    .toggle-password:hover {
      color: #0891b2;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    @media (max-width: 1024px) {
      .main-content {
        grid-template-columns: 1fr;
        gap: 40px;
      }

      .benefits-section {
        order: 2;
      }

      .register-card {
        order: 1;
      }
    }

    @media (max-width: 768px) {
      .navbar {
        padding: 20px 30px;
      }

      .nav-tagline {
        display: none;
      }

      .main-content {
        padding: 40px 30px;
      }

      .register-card {
        padding: 32px 24px;
      }

      .form-row {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="logo">
      <img src="<?php echo $projectRoot; ?>/public/assets/img/logo.png" alt="Logo" class="logo-img">
      <span>SchoolStream</span>
    </div>
    <div class="nav-tagline">
      <span>Academic Excellence through Innovation</span>
    </div>
    <div class="nav-buttons">
      <a href="<?php echo $projectRoot; ?>/views/auth/loginPage.php" class="btn-secondary">Log In</a>
      <a href="<?php echo $projectRoot; ?>/views/auth/register.php" class="btn-primary">Sign Up</a>
    </div>
  </nav>

  <main class="main-content">
    <div class="benefits-section">
      <h2>Join SchoolStream and transform your school management</h2>

      <div class="benefit-item">
        <div class="benefit-icon">&#128100;</div>
        <div class="benefit-text">
          <h3>Student Management</h3>
          <p>Easily manage student records, enrollment, and academic progress all in one place.</p>
        </div>
      </div>

      <div class="benefit-item">
        <div class="benefit-icon">&#128202;</div>
        <div class="benefit-text">
          <h3>Grade & Score Tracking</h3>
          <p>Track student performance across subjects with comprehensive grading and reporting tools.</p>
        </div>
      </div>

      <div class="benefit-item">
        <div class="benefit-icon">&#127979;</div>
        <div class="benefit-text">
          <h3>Class Organization</h3>
          <p>Organize classes, assign teachers, and manage school terms seamlessly.</p>
        </div>
      </div>

      <div class="benefit-item">
        <div class="benefit-icon">&#128200;</div>
        <div class="benefit-text">
          <h3>Detailed Reports</h3>
          <p>Generate comprehensive report cards and performance analytics with just a few clicks.</p>
        </div>
      </div>
    </div>

    <div class="register-card">
      <h2>Create an account</h2>
      <p class="subtitle">Get started with SchoolStream today</p>

      <?php if (!empty($errors)): ?>
        <div class="error-box">
          <strong>Please fix the following:</strong>
          <ul>
            <?php foreach ($errors as $err): ?>
              <li><?php echo e($err); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="register.php">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Choose a username" value="<?php echo e($old['username']); ?>" required>
        </div>

        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo e($old['email']); ?>" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="password-wrapper">
            <input type="password" id="password" name="password" placeholder="Create a password (min 6 characters)" required>
            <button type="button" class="toggle-password" onclick="togglePassword('password')">
              <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <svg class="eye-off-icon" style="display:none;" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
              </svg>
            </button>
          </div>
        </div>

        <div class="form-group">
          <label for="role">Account Type</label>
          <select id="role" name="role">
            <option value="teacher" <?php echo $old['role']==='teacher' ? 'selected' : ''; ?>>Teacher</option>
            <option value="office_admin" <?php echo $old['role']==='office_admin' ? 'selected' : ''; ?>>Office Administrator</option>
          </select>
        </div>

        <button type="submit" class="submit-btn">Create Account</button>

        <div class="divider">or</div>

        <div class="login-prompt">
          Already have an account? <a href="<?php echo $projectRoot; ?>/views/auth/loginPage.php">Log in</a>
        </div>
      </form>
    </div>
  </main>

  <script>
    function togglePassword(inputId) {
      const input = document.getElementById(inputId);
      const button = input.parentElement.querySelector('.toggle-password');
      const eyeIcon = button.querySelector('.eye-icon');
      const eyeOffIcon = button.querySelector('.eye-off-icon');

      if (input.type === 'password') {
        input.type = 'text';
        eyeIcon.style.display = 'none';
        eyeOffIcon.style.display = 'block';
      } else {
        input.type = 'password';
        eyeIcon.style.display = 'block';
        eyeOffIcon.style.display = 'none';
      }
    }
  </script>
</body>
</html>
