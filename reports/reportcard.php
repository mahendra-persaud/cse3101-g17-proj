<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Report Card</title>
  <link rel="stylesheet" href="assets/reportcard.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <aside class="sidebar">
    <h2>School Management</h2>
    <nav>
      <ul>
        <li><a href="#">🏠 Dashboard</a></li>
        <li><a href="#">👤 Users</a></li>
        <li><a href="#">🧑‍🏫 Classes</a></li>
        <li class="active"><a href="#">🎓 Students</a></li>
        <li><a href="#">📘 Subjects</a></li>
        <li><a href="#">🎖️ Grades</a></li>
        <li><a href="#">📅 Years & Terms</a></li>
        <li><a href="#">📝 Scores</a></li>
        <li><a href="#">📊 Reports</a></li>
        <li><a href="#">⚙️ Settings</a></li>
      </ul>
    </nav>
  </aside>

  <main class="dashboard">
     <div class = "header">
        <h2>Student Report Card</h2>

        <div class="user-info">
            <p>Logged in as: &nbsp<span><strong>Admin</strong></span></p>
            <button class="logout-btn">Logout</button>
        </div>
    </div>

    <header>
      <h1>Report Card – Olivia Parker</h1>
      <p><strong>Student ID:</strong> STU-001 | <strong>Grade:</strong> Grade 1 | <strong>Class:</strong> Class A</p>
    </header>

    <section class="table-card">
      <h2>Academic Performance</h2>
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
          <tr><td>Mathematics</td><td>Term 1</td><td>95</td><td>A</td><td>Excellent</td></tr>
          <tr><td>Science</td><td>Term 1</td><td>88</td><td>B+</td><td>Well done</td></tr>
          <tr><td>English</td><td>Term 1</td><td>82</td><td>B</td><td>Good effort</td></tr>
        </tbody>
      </table>
      <canvas id="scoreChart" width="400" height="200"></canvas>
    </section>

    <section class="table-card">
      <h2>Attendance Summary</h2>
      <table>
        <thead>
          <tr><th>Total Days</th><th>Present</th><th>Absent</th><th>Late</th></tr>
        </thead>
        <tbody>
          <tr><td>90</td><td>85</td><td>3</td><td>2</td></tr>
        </tbody>
      </table>
    </section>

    <section class="form-card">
      <h2>Teacher Remarks</h2>
      <p>Olivia has shown excellent progress in Mathematics and Science. She is attentive in class and participates actively. Improvement is needed in English writing skills, but overall performance is commendable.</p>
    </section>

    <section class="report-actions">
      <button class="export-btn">Export PDF</button>
      <button class="export-btn">Export Excel</button>
      <button class="export-btn">Print</button>
    </section>
  </main>

  <script>
    const ctx = document.getElementById('scoreChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Mathematics', 'Science', 'English'],
        datasets: [{
          label: 'Scores',
          data: [95, 88, 82],
          backgroundColor: ['#3b82f6', '#8b5cf6', '#38bdf8']
        }]
      },
      options: {
        scales: {
          y: { beginAtZero: true, max: 100 }
        },
        plugins: {
          legend: { display: false }
        }
      }
    });
  </script>
</body>
</html>