<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/assets/css/dashboard2.css">';
require_once $headerPath;
require_once dirname($headerPath) . '/sidebar.php';
?>

  <main class="dashboard">
    <!-- 🔹 Top Bar -->
      <div class = "header">
        <h2>Dashboard</h2>

        <div class="user-info">
            <p>Logged in as: &nbsp<span><strong>Admin</strong></span></p>
            <button class="logout-btn">Logout</button>
        </div>
    </div>

    <header>
      <h1>System Overview</h1>
    </header>

    <!-- Quick Stats -->
    <section class="stats-grid">
      <div class="stat-card">
        <h2>👤 Users</h2>
        <p>25 Active</p>
      </div>
      <div class="stat-card">
        <h2>🎓 Students</h2>
        <p>320 Enrolled</p>
      </div>
      <div class="stat-card">
        <h2>🧑‍🏫 Classes</h2>
        <p>18 Running</p>
      </div>
      <div class="stat-card">
        <h2>📝 Scores</h2>
        <p>1,245 Recorded</p>
      </div>
    </section>

    <!-- Recent Activity -->
    <section class="table-card">
      <h2>Recent Activity</h2>
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>User</th>
            <th>Action</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>2026-01-05</td>
            <td>Sarah Johnson</td>
            <td>Added Student</td>
            <td>Enrolled Liam Johnson in Grade 2</td>
          </tr>
          <tr>
            <td>2026-01-06</td>
            <td>Michael Brown</td>
            <td>Generated Report</td>
            <td>Grade 1 Mathematics Term 1</td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>
</body>
</html>