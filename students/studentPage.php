<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/assets/css/studentPage.css">';
require_once $headerPath;
require_once dirname($headerPath) . '/sidebar.php';
?>

  <main class="dashboard">
    <!-- 🔹 Top Bar -->
    <div class = "header">
         <header>
           <h1>Manage Students</h1>
         </header>

        <div class="user-info">
            <p>Logged in as: &nbsp<span><strong>Admin</strong></span></p>
            <button class="logout-btn">Logout</button>
        </div>
    </div>

    <!-- Add Student Form -->
    <section class="form-card">
  <h2>Add New Student</h2>
  <form class="two-column-form">
    <div>
      <label>Full Name</label>
      <input type="text" placeholder="e.g., Olivia Parker" required>
    </div>

    <div>
      <label>Student ID</label>
      <input type="text" placeholder="e.g., STU-001" required>
    </div>

    <div>
      <label>Date of Birth</label>
      <input type="date" required>
    </div>

    <div>
      <label>Email</label>
      <input type="email" placeholder="e.g., olivia@example.com">
    </div>

    <div>
      <label>Grade</label>
      <select>
        <option>Grade 1</option>
        <option>Grade 2</option>
        <option>Grade 3</option>
        <option>Grade 4</option>
        <option>Grade 5</option>
        <option>Grade 6</option>
      </select>
    </div>

    <div>
      <label>Class</label>
      <select>
        <option>Class A</option>
        <option>Class B</option>
        <option>Class C</option>
        <option>Class D</option>
      </select>
    </div>
  </form>
  <button class="addStudent" type="submit">Add Student</button>
</section>

    <!-- Students Table -->
    <section class="table-card">
      <h2>All Students</h2>
      <table>
        <thead>
          <tr>
            <th>Select</th>
            <th>Student ID</th>
            <th>Name</th>
            <th>Grade</th>
            <th>Class</th>
            <th>Email</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><input type="checkbox"></td>
            <td>STU-001</td>
            <td>Olivia Parker</td>
            <td>Grade 1</td>
            <td>Class A</td>
            <td>olivia.parker@example.com</td>
            <td><a href="#">View</a> | <a href="#">Edit</a> | <a href="#">Delete</a></td>
          </tr>
          <tr>
            <td><input type="checkbox"></td>
            <td>STU-002</td>
            <td>Liam Johnson</td>
            <td>Grade 2</td>
            <td>Class B</td>
            <td>liam.johnson@example.com</td>
            <td><a href="#">View</a> | <a href="#">Edit</a> | <a href="#">Delete</a></td>
          </tr>
        </tbody>
      </table>
      <button class="delete-btn">Delete Selected</button>
    </section>
  </main>
</body>
</html>