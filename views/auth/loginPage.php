<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — School Management System</title>
  <?php $parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/')); $projectRoot = (isset($parts[1]) ? '/' . $parts[0] : ''); ?>
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

    /* Navigation Bar */
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
      content: '•';
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

    /* main content area */
    .main-content {
      max-width: 1400px;
      margin: 0 auto;
      padding: 80px 60px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: start;
    }

    /* left side - cool icons */
    .benefits-section {
      /* No additional padding needed */
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

    /* Right Side - Login Form */
    .login-card {
      background: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 16px;
      padding: 48px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
    }

    .login-card h2 {
      font-size: 28px;
      font-weight: 700;
      color: #1a1a1a;
      margin-bottom: 8px;
    }

    .login-card .subtitle {
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
    .form-group input[type="password"] {
      width: 100%;
      padding: 12px 16px;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      font-size: 15px;
      transition: all 0.2s;
      background: #ffffff;
    }

    .form-group input:focus {
      outline: none;
      border-color: #0891b2;
      box-shadow: 0 0 0 3px rgba(8, 145, 178, 0.1);
    }

    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      font-size: 14px;
    }

    .remember-me {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .remember-me input[type="checkbox"] {
      width: 16px;
      height: 16px;
      cursor: pointer;
    }

    .remember-me label {
      color: #374151;
      cursor: pointer;
      user-select: none;
    }

    .forgot-link {
      color: #0891b2;
      text-decoration: none;
      font-weight: 500;
    }

    .forgot-link:hover {
      text-decoration: underline;
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

    .divider::before {
      left: 0;
    }

    .divider::after {
      right: 0;
    }

    .register-prompt {
      text-align: center;
      margin-top: 24px;
      font-size: 14px;
      color: #6b7280;
    }

    .register-prompt a {
      color: #0891b2;
      text-decoration: none;
      font-weight: 600;
    }

    .register-prompt a:hover {
      text-decoration: underline;
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

    /* Preview Section */
    .preview-section {
      max-width: 1200px;
      margin: 60px auto 0;
      padding: 0 60px;
    }

    .preview-image {
      width: 100%;
      border-radius: 12px;
      border: 1px solid #e5e7eb;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
      .main-content {
        grid-template-columns: 1fr;
        gap: 40px;
      }

      .benefits-section {
        order: 2;
      }

      .login-card {
        order: 1;
      }

      .hero-title {
        font-size: 42px;
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

      .login-card {
        padding: 32px 24px;
      }
    }
  </style>
</head>
<body>
  <!-- navbar stuff -->
  <nav class="navbar">
    <div class="logo">
      <img src="<?php echo $projectRoot; ?>/public/assets/img/logo.png" alt="Logo" class="logo-img">
      <span>SchoolStream</span>
    </div>
    <div class="nav-tagline">
      <span>Academic Excellence through Innovation</span>
    </div>
    <div class="nav-buttons">
      <a href="<?php echo $projectRoot; ?>/views/auth/register.php" class="btn-secondary">Sign Up</a>
      <a href="<?php echo $projectRoot; ?>/views/auth/loginPage.php" class="btn-primary">Log In</a>
    </div>
  </nav>

  <!-- login form and info -->
  <main class="main-content">
    <!-- left side -->
    <div class="benefits-section">
      <h2>Start managing your school efficiently today</h2>

      <div class="benefit-item">
        <div class="benefit-icon">👥</div>
        <div class="benefit-text">
          <h3>Student Management</h3>
          <p>Easily manage student records, enrollment, and academic progress all in one place.</p>
        </div>
      </div>

      <div class="benefit-item">
        <div class="benefit-icon">📊</div>
        <div class="benefit-text">
          <h3>Grade & Score Tracking</h3>
          <p>Track student performance across subjects with comprehensive grading and reporting tools.</p>
        </div>
      </div>

      <div class="benefit-item">
        <div class="benefit-icon">🏫</div>
        <div class="benefit-text">
          <h3>Class Organization</h3>
          <p>Organize classes, assign teachers, and manage school terms seamlessly.</p>
        </div>
      </div>

      <div class="benefit-item">
        <div class="benefit-icon">📈</div>
        <div class="benefit-text">
          <h3>Detailed Reports</h3>
          <p>Generate comprehensive report cards and performance analytics with just a few clicks.</p>
        </div>
      </div>
    </div>

    <!-- right side - the actual form -->
    <div class="login-card">
      <h2>Welcome back</h2>
      <p class="subtitle">Log in to your account to continue</p>

      <?php if (isset($_GET['error'])): ?>
        <div style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
          <?php if ($_GET['error'] === 'db'): ?>
            ⚠️ Database connection failed. Please check your database settings.
          <?php else: ?>
            ⚠️ Invalid username or password. Please try again.
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <form method="post" action="login.php">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Enter your username" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="password-wrapper">
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
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

        <div class="form-options">
          <div class="remember-me">
            <input type="checkbox" id="remember_me" name="remember_me">
            <label for="remember_me">Remember me</label>
          </div>
          <a href="#" class="forgot-link">Forgot password?</a>
        </div>

        <button type="submit" class="submit-btn">Log In</button>

        <div class="divider">or</div>

        <div class="register-prompt">
          Don't have an account? <a href="<?php echo $projectRoot; ?>/views/auth/register.php">Sign up for free</a>
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
