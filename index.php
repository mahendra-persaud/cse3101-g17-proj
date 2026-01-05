<?php
// index.php — project entry point (themed dashboard)
require_once __DIR__ . '/includes/header.php';
// If not logged in, redirect to login page
if (empty($_SESSION['username'])) {
    header('Location: loginPage.php');
    exit;
}
?>
<div class="page-glass">
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
        <h2>Welcome, <?php echo e($_SESSION['username']); ?>!</h2>
        <p style="margin-top:12px;color:rgba(255,255,255,0.9)">You are signed in as <strong><?php echo e($_SESSION['role']); ?></strong>.</p>
        <div style="margin-top:24px;background:rgba(255,255,255,0.08);padding:12px;border-radius:8px;color:#fff">
            Use the navigation links above to access features available to your role.
        </div>
        <div style="margin-top:24px;text-align:center">
            <a href="logout.php" style="display:inline-block;padding:8px 16px;background:#fff;color:#7867f4;border-radius:8px;text-decoration:none;font-weight:bold">Logout</a>
        </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
