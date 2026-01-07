<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/studentProfile.css">';
require_once $headerPath;
require_once dirname($headerPath) . '/sidebar.php';
?>

  <main class="dashboard">
    <!-- 🔹 Top Bar -->
    <div class = "header">
        <h2>Individual Student Profile</h2></div>

    <!-- Student Info Card -->
    <section class="profile-card">
      <img src="student_photo.jpg" alt="Student Photo" class="student-photo">
      <div class="student-info">
        <h2>Olivia Parker</h2>
        <p><strong>ID:</strong> STU-001</p>
        <p><strong>Grade:</strong> Grade 1</p>
        <p><strong>Class:</strong> Class A</p>
        <p><strong>Date of Birth:</strong> 2015-03-12</p>
        <p><strong>Contact:</strong> olivia.parker@example.com</p>
      </div>
    </section>

    <!-- Academic Records -->
    <section class="table-card">
      <h2>Academic Records</h2>
      <table>
        <thead>
          <tr>
            <th>Subject</th>
            <th>Term</th>
            <th>Score</th>
            <th>Grade</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Mathematics</td>
            <td>Term 1</td>
            <td>95</td>
            <td>A</td>
            <td>Excellent</td>
          </tr>
          <tr>
            <td>Science</td>
            <td>Term 1</td>
            <td>88</td>
            <td>B+</td>
            <td>Well done</td>
          </tr>
        </tbody>
      </table>
    </section>

    <!-- Attendance Records -->
    <section class="table-card">
      <h2>Attendance Records</h2>
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>2026-01-05</td>
            <td>Present</td>
          </tr>
          <tr>
            <td>2026-01-06</td>
            <td>Absent</td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>
</body>
</html>