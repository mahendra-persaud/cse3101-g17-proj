<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/subject.css">';
require_once $headerPath;
require_once dirname($headerPath) . '/sidebar.php';
?>

  <main class="dashboard">
    <!-- 🔹 header bar -->
     <div class = "header">
         <header>
           <h1>Manage Subjects</h1>
         </header></div>

    <!-- form to add subjects -->
    <section class="form-card">
      <h2>Add New Subject</h2>
      <form>
        <label>Subject Name</label>
        <input type="text" placeholder="e.g., Mathematics" required>

        <label>Subject Code</label>
        <input type="text" placeholder="e.g., MATH-101" required>

        <label>Description</label>
        <textarea placeholder="Brief description of the subject"></textarea>

        <button type="submit">Add Subject</button>
      </form>
    </section>

    <!-- table of all subjects -->
    <section class="table-card">
      <h2>All Subjects</h2>
      <table>
        <thead>
          <tr>
            <th>Select</th>
            <th>Subject Code</th>
            <th>Subject Name</th>
            <th>Description</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><input type="checkbox"></td>
            <td>MATH-101</td>
            <td>Mathematics</td>
            <td>Core subject covering arithmetic, algebra, and geometry</td>
            <td><a href="#">Edit</a> | <a href="#">Delete</a></td>
          </tr>
          <tr>
            <td><input type="checkbox"></td>
            <td>SCI-102</td>
            <td>Science</td>
            <td>Introduction to biology, chemistry, and physics</td>
            <td><a href="#">Edit</a> | <a href="#">Delete</a></td>
          </tr>
        </tbody>
      </table>
      <button class="delete-btn">Delete Selected</button>
    </section>
  </main>
</body>
</html>