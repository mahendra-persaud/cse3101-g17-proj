<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login — School Management System</title>
  <link rel="stylesheet" href="style.css">
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
      <form method="post" action="/login">
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
          Don't have an account? &nbsp <a href="/register.php">Register here</a>
        </p>

        <p class = "terms-conditions">
          Terms and Conditions &nbsp <a href="/terms.php"> Privacy Policy</a>
        </p>
      </form>
    </div>
  </div>
</body>
</html>