<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/userProfile.css">';
require_once $headerPath;
require_once dirname($headerPath) . '/sidebar.php';
?>

  <main class="dashboard">
    <!-- 🔹 Top Bar -->
    <div class = "header">
        <h2>Individual User Profile</h2></div>

    <!-- User Info Card -->
    <section class="profile-card">
      <img src="user_photo.jpg" alt="User Photo" class="user-photo"></section>

    <!-- Permissions -->
    <section class="table-card">
      <h2>Permissions</h2>
      <table>
        <thead>
          <tr>
            <th>Module</th>
            <th>Access Level</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Classes</td>
            <td>Read/Write</td>
          </tr>
          <tr>
            <td>Scores</td>
            <td>Read Only</td>
          </tr>
          <tr>
            <td>Reports</td>
            <td>Read/Write</td>
          </tr>
        </tbody>
      </table>
    </section>

    <!-- Activity Log -->
    <section class="table-card">
      <h2>Recent Activity</h2>
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Action</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>2026-01-05</td>
            <td>Edited Class</td>
            <td>Updated Class A roster</td>
          </tr>
          <tr>
            <td>2026-01-06</td>
            <td>Generated Report</td>
            <td>Grade 1 Mathematics Term 1</td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>
</body>
</html>