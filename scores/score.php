<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/assets/css/score.css">';
require_once $headerPath;
require_once dirname($headerPath) . '/sidebar.php';
?>

  <main class="dashboard">
    <!-- 🔹 Top Bar -->
    <div class = "header">
        <h2>Score Management</h2>

        <div class="user-info">
            <p>Logged in as: &nbsp<span><strong>Admin</strong></span></p>
            <button class="logout-btn">Logout</button>
        </div>
    </div>

     <section class="form-card">
      <h2>Add New Score</h2>
      <form>
        <label>Student</label>
        <select>
          <option>Olivia Parker</option>
          <option>Liam Johnson</option>
        </select>

        <label>Subject</label>
        <select>
          <option>Mathematics</option>
          <option>Science</option>
        </select>

        <label>Grade</label>
        <select>
          <option>Grade 1</option>
          <option>Grade 2</option>
        </select>

        <label>Term</label>
        <select>
          <option>Term 1</option>
          <option>Term 2</option>
        </select>

        <label>Score</label>
        <input type="number" placeholder="e.g., 95" required>

        <button type="submit">Add Score</button>
      </form>
    </section>

     <section class="table-card">
      <h2>Scores</h2>
      <table>
        <thead>
          <tr>
            <th>Select</th>
            <th>Student</th>
            <th>Subject</th>
            <th>Grade</th>
            <th>Term</th>
            <th>Score</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><input type="checkbox"></td>
            <td>Olivia Parker</td>
            <td>Mathematics</td>
            <td>Grade 1</td>
            <td>Term 1</td>
            <td>95</td>
            <td><a href="#">Edit</a> | <a href="#">Delete</a></td>
          </tr>
          <tr>
            <td><input type="checkbox"></td>
            <td>Liam Johnson</td>
            <td>Science</td>
            <td>Grade 2</td>
            <td>Term 2</td>
            <td>88</td>
            <td><a href="#">Edit</a> | <a href="#">Delete</a></td>
          </tr>
        </tbody>
      </table>
      <button class="delete-btn">Delete Selected</button>
    </section>
  </main>
</body>
</html>