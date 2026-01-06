<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/assets/css/userPage.css">';
require_once $headerPath;
require_once dirname($headerPath) . '/sidebar.php';
?>

  <main class="dashboard">
    <!-- 🔹 Top Bar -->
       <div class = "header">
         <header>
           <h1>Manage Users</h1>
         </header>

        <div class="user-info">
            <p>Logged in as: &nbsp<span><strong>Admin</strong></span></p>
            <button class="logout-btn">Logout</button>
        </div>
    </div>

    <!-- Add User Form -->
    <section class="form-card">
      <h2>Add New User</h2>
      <form>
        <label>Full Name</label>
        <input type="text" placeholder="e.g., Sarah Johnson" required>

        <label>Email</label>
        <input type="email" placeholder="e.g., sarah@example.com" required>

        <label>Role</label>
        <select>
          <option>Admin</option>
          <option>Teacher</option>
          <option>Parent</option>
        </select>

        <label>Status</label>
        <select>
          <option>Active</option>
          <option>Inactive</option>
        </select>

        <button type="submit">Add User</button>
      </form>
    </section>

    <!-- Users Table -->
    <section class="table-card">
      <h2>All Users</h2>
      <table>
        <thead>
          <tr>
            <th>Select</th>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><input type="checkbox"></td>
            <td>USR-101</td>
            <td>Sarah Johnson</td>
            <td>sarah.johnson@example.com</td>
            <td>Teacher</td>
            <td>Active</td>
            <td><a href="#">View</a> | <a href="#">Edit</a> | <a href="#">Delete</a></td>
          </tr>
          <tr>
            <td><input type="checkbox"></td>
            <td>USR-102</td>
            <td>Michael Brown</td>
            <td>michael.brown@example.com</td>
            <td>Admin</td>
            <td>Active</td>
            <td><a href="#">View</a> | <a href="#">Edit</a> | <a href="#">Delete</a></td>
          </tr>
        </tbody>
      </table>
      <button class="delete-btn">Delete Selected</button>
    </section>
  </main>
</body>
</html>