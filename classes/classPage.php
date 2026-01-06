<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/assets/css/classPage.css">';
require_once $headerPath;
require_once dirname($headerPath) . '/sidebar.php';
?>

  <main class="dashboard">
    <!-- 🔹 Top Bar -->
     <div class = "header">
         <header>
           <h1>Manage Classes</h1>
         </header>

        <div class="user-info">
            <p>Logged in as: &nbsp<span><strong>Admin</strong></span></p>
            <button class="logout-btn">Logout</button>
        </div>
    </div>

    <!-- Add Class Form -->
    <section class="form-card">
      <h2>Add New Class</h2>
      <form>
        <label>Class Name</label>
        <input type="text" placeholder="e.g., Class A" required>

        <label>Class ID</label>
        <input type="text" placeholder="e.g., CLS-001" required>

        <label>Grade</label>
        <select>
          <option>Grade 1</option>
          <option>Grade 2</option>
        </select>

        <label>Assigned Teacher</label>
        <input type="text" placeholder="e.g., Sarah Johnson">

        <label>Description</label>
        <textarea placeholder="Brief description of the class"></textarea>

        <button type="submit">Add Class</button>
      </form>
    </section>

    <!-- Classes Table -->
    <section class="table-card">
      <h2>All Classes</h2>
      <table>
        <thead>
          <tr>
            <th>Select</th>
            <th>Class ID</th>
            <th>Class Name</th>
            <th>Grade</th>
            <th>Teacher</th>
            <th>Description</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><input type="checkbox"></td>
            <td>CLS-001</td>
            <td>Class A</td>
            <td>Grade 1</td>
            <td>Sarah Johnson</td>
            <td>Foundational learning group</td>
            <td><a href="#">View</a> | <a href="#">Edit</a> | <a href="#">Delete</a></td>
          </tr>
          <tr>
            <td><input type="checkbox"></td>
            <td>CLS-002</td>
            <td>Class B</td>
            <td>Grade 2</td>
            <td>Michael Brown</td>
            <td>Intermediate learning group</td>
            <td><a href="#">View</a> | <a href="#">Edit</a> | <a href="#">Delete</a></td>
          </tr>
        </tbody>
      </table>
      <button class="delete-btn">Delete Selected</button>
    </section>
  </main>
</body>
</html>