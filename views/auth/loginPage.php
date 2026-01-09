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
      gap: 8px;
      font-size: 24px;
      font-weight: 700;
      color: #1a1a1a;
    }

    .logo-icon {
      width: 32px;
      height: 32px;
      background: linear-gradient(135deg, #06b6d4, #0891b2);
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 700;
    }

    .nav-links {
      display: flex;
      gap: 40px;
      align-items: center;
    }

    .nav-links a {
      text-decoration: none;
      color: #6b7280;
      font-size: 15px;
      font-weight: 500;
      transition: color 0.2s;
    }

    .nav-links a:hover {
      color: #0891b2;
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

    /* Hero Section */
    .hero-section {
      text-align: center;
      padding: 80px 60px 60px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .hero-title {
      font-size: 56px;
      font-weight: 700;
      line-height: 1.2;
      color: #1a1a1a;
      margin-bottom: 16px;
    }

    .hero-brand {
      color: #0891b2;
    }

    .hero-subtitle {
      font-size: 18px;
      color: #6b7280;
      max-width: 700px;
      margin: 0 auto 40px;
      line-height: 1.6;
    }

    .hero-buttons {
      display: flex;
      gap: 16px;
      justify-content: center;
      margin-bottom: 60px;
    }

    .btn-large {
      padding: 14px 32px;
      font-size: 16px;
      border-radius: 10px;
      font-weight: 600;
    }

    /* Main Content - Login Section */
    .main-content {
      max-width: 1400px;
      margin: 0 auto;
      padding: 40px 60px 80px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: start;
    }

    /* Left Side - Benefits */
    .benefits-section {
      padding-top: 40px;
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

      .nav-links {
        display: none;
      }

      .hero-section {
        padding: 60px 30px 40px;
      }

      .hero-title {
        font-size: 36px;
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
  <!-- Navigation -->
  <nav class="navbar">
    <div class="logo">
      <div class="logo-icon">S</div>
      <span>SMS</span>
    </div>
    <div class="nav-links">
      <a href="#about">About</a>
      <a href="#features">Features</a>
      <a href="#contact">Contact</a>
    </div>
    <div class="nav-buttons">
      <a href="<?php echo $projectRoot; ?>/views/auth/register.php" class="btn-secondary">Sign Up</a>
      <a href="<?php echo $projectRoot; ?>/views/auth/loginPage.php" class="btn-primary">Log In</a>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero-section">
    <h1 class="hero-title">
      Revolutionizing School Activities &<br>
      Management with <span class="hero-brand">SMS</span>
    </h1>
    <p class="hero-subtitle">
      The ultimate school management solution designed to streamline your institution's operations,
      managing students, teachers, classes, and academic performance efficiently.
    </p>
    <div class="hero-buttons">
      <a href="#login" class="btn-primary btn-large">Get Started</a>
      <a href="#features" class="btn-secondary btn-large">Learn More</a>
    </div>
  </section>

  <!-- Main Content - Login -->
  <main class="main-content" id="login">
    <!-- Left Side - Benefits -->
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

    <!-- Right Side - Login Form -->
    <div class="login-card">
      <h2>Welcome back</h2>
      <p class="subtitle">Log in to your account to continue</p>

      <form method="post" action="login.php">
        <div class="form-group">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" placeholder="you@example.com" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter your password" required>
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
</body>
</html>
