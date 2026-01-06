<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/assets/css/years.css">';
require_once $headerPath;
require_once dirname($headerPath) . '/sidebar.php';
?>

  <main class="dashboard">
    <!-- 🔹 Top Bar -->
    <div class = "header">
        <h2>Term Management</h2>

        <div class="user-info">
            <p>Logged in as: &nbsp<span><strong>Admin</strong></span></p>
            <button class="logout-btn">Logout</button>
        </div>
    </div>

    <section class="form-card">
      <h2>Add New Term</h2>
      <form>
        <label>Year</label>
        <input type="text" placeholder="e.g., 2026" required>

        <label>Term Name</label>
        <input type="text" placeholder="e.g., Term 1" required>

        <label>Start Date</label>
        <input type="date" required>

        <label>End Date</label>
        <input type="date" required>

        <button type="submit">Add Term</button>
      </form>
    </section>

    <section class="table-card">
      <h2>Years & Terms</h2>
      <table>
        <thead>
          <tr>
            <th>Select</th>
            <th>Year</th>
            <th>Term Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><input type="checkbox"></td>
            <td>2026</td>
            <td>Term 1</td>
            <td>2026-01-10</td>
            <td>2026-04-15</td>
            <td><a href="#">Edit</a> | <a href="#">Delete</a></td>
          </tr>
          <tr>
            <td><input type="checkbox"></td>
            <td>2026</td>
            <td>Term 2</td>
            <td>2026-05-01</td>
            <td>2026-08-15</td>
            <td><a href="#">Edit</a> | <a href="#">Delete</a></td>
          </tr>
        </tbody>
      </table>
      <button class="delete-btn">Delete Selected</button>
    </section>
  </main>
</body>
</html>