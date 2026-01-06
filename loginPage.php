<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login — School Management System</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="glass-bg">
  <div class="login-container">
    <div class = 'left-card'>
      <h2>Welcome to the School Management System</h2>
      <section class="info-text">
         <p>
            <span> Manage students, teachers, </span> 
         </p>
         <p>
            and classes efficiently.
         </p>
      </section>
    </div>

    <div class="glass-card">
      <?php
        $users_file = __DIR__ . '/temp/users.json';
        $has_dave = false;
        if (file_exists($users_file)) {
            $users = json_decode(file_get_contents($users_file), true) ?: [];
            foreach ($users as $u) {
                if (strcasecmp($u['username'], 'dave') === 0) { $has_dave = true; break; }
            }
        }
      ?>
      <?php if (!empty($has_dave)): ?>
        <div style="background:#e6ffea;padding:10px;border-radius:4px;margin-bottom:12px;color:#064">
            Test account: <strong>dave@example.com</strong> / <strong>password123</strong>
        </div>
      <?php endif; ?>
      <?php if (!empty($_GET['registered'])): ?>
        <div style="background:#e6ffea;padding:10px;border-radius:4px;margin-bottom:12px;color:#064">
            Registered successfully — you can now log in.
        </div>
      <?php endif; ?>
      <?php if (!empty($_GET['error'])): ?>
        <div style="background:#ffe6e6;padding:10px;border-radius:4px;margin-bottom:12px;color:#900">
            Invalid email or password.
        </div>
      <?php endif; ?>
      <form method="post" action="login.php">
        <h2>Login</h2>
        <label>Email</label>
        <input type="email" name="email" placeholder="someone@example.com" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <section class = "memory-section">
          <div class = "remember-me-checkbox">
             <input type="checkbox" name="remember_me" id="remember_me">
             <label for="remember_me">Remember Me</label>
          </div>

          <p class="forgot-password">
            Forgot Password?
          </p>
        </section>
        <button type="submit">Login</button>

        <p class="register-link">
          Don't have an account? &nbsp <a href="register.php">Register here</a>
        </p>

        <p class = "terms-conditions">
          Terms and Conditions &nbsp <a href="terms.php"> Privacy Policy</a>
        </p>
      </form>
    </div>
  </div>
</body>
</html>