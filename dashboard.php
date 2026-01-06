<?php
// Dashboard page — use shared includes and per-page stylesheet
$extra_head = '<link rel="stylesheet" href="assets/css/dashboard.css">';
require_once __DIR__ . '/includes/header.php';
require_role(['office_admin']);
?>
<body class="glass-bg">
 <!--  <div class="sidebar">
    <h2>School Management</h2>
    <ul>
      <li><a href="/"><span>🏠</span> Dashboard</a></li>
      <li><a href="/users"><span>👤</span> Users</a></li>
      <li><a href="/grades"><span>🎖️</span> Grades</a></li>
      <li><a href="/classes"><span>🧑‍🏫</span> Classes</a></li>
      <li><a href="/students"><span>🎓</span> Students</a></li>
      <li><a href="/subjects"><span>📘</span> Subjects</a></li>
      <li><a href="/years_terms"><span>📅</span> Years & Terms</a></li>
      <li><a href="/scores"><span>📝</span> Scores</a></li>
      <li><a href="/reports"><span>📊</span> Reports</a></li>
    </ul>
  </div>

  <div class="main">
    <header>
      <h1>Dashboard</h1>
      <div class="user-info">
        Logged in as: <strong>Admin</strong>
        <a href="/logout" class="logout-btn">Logout</a>
        <span class="active-year">Active Year: 2026</span>
      </div>
    </header>

    <section class="widgets">
      <div class="widget blue">
        <h3>Total Students</h3>
        <p><?= $studentCount ?></p>
      </div>
      <div class="widget green">
        <h3>Total Teachers</h3>
        <p><?= $teacherCount ?></p>
      </div>
      <div class="widget teal">
        <h3>Active School Year</h3>
        <p><?= $activeYear ?></p>
      </div>
      <div class="widget purple">
        <h3>Classes per Grade</h3>
        <canvas id="classChart"></canvas>
      </div>
    </section>

    <section class="quick-links">
      <h2>Quick Links</h2>
      <a href="/users/create" class="quick-btn">Add User</a>
      <a href="/classes/create" class="quick-btn">Add Class</a>
      <a href="/subjects/create" class="quick-btn">Add Subject</a>
    </section>
  </div> -->
  <div class = "Main-Container">
        <div class="sidebar">
            <h2>School Management</h2>
            <nav>
                <ul>
                    <li><a href="#"><span>🏠</span> Dashboard</a></li>
                    <li><a href="#"><span>👤</span> Users</a></li>
                    <li><a href="#"><span>🎖️</span> Grades</a></li>
                    <li><a href="#"><span>🧑‍🏫</span> Classes</a></li>
                    <li><a href="#"><span>🎓</span> Students</a></li>
                    <li><a href="#"><span>📘</span> Subjects</a></li>
                    <li><a href="#"><span>📅</span> Years & Terms</a></li>
                    <li><a href="#"><span>📝</span> Scores</a></li>
                    <li><a href="#"><span>📊</span> Reports</a></li>
                </ul>
            </nav>
        </div>

        <main class="main-dashboard">
        <div class="content">

            <div class = "header">
                <h2>Dashboard</h2>

                <div class="user-info">
                    <p>Logged in as: &nbsp<span><strong><?php echo e($_SESSION['username'] ?? 'Admin'); ?></strong></span></p>
                    <a class="logout-btn" href="logout.php">Logout</a>
                </div>
            </div>

            <h1>Dashboard Overview</h1>
            <section class="widgets">

                <div class="widget-blue">
                    <h3>Total <br> Students</h3>
                    <p>150</p>
                </div>
                <div class="widget-green">
                    <h3>Total <br> Teachers</h3>
                    <p>15</p>
                </div>
                <div class="widget-purple">
                    <h3>Classes per <br> Grade</h3>
                    <canvas id="classChart"></canvas>
                </div>
            </section>
            <section class = "other-widgets">
                    <div class="School-Year">
                        <h3>Active School Year</h3>
                        <p>2025 – 2026</p>
                    </div>

                    <div class="quick-links">
                        <h3>Quick Links</h3>
                        <ul>
                            <li>
                                <a href="#" class="quick-btn">Add User</a>
                            </li>
                            <li>
                                <a href="#" class="quick-btn">Add Class</a>
                            </li>
                            <li>
                                <a href="#" class="quick-btn">Add Subject</a>
                            </li>
                        </ul>
                    </div>
            </section>
        </div> <!-- .content -->
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('classChart').getContext('2d');
        new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6'],
            datasets: [{
            label: 'Classes',
            data: [1, 2, 3, 4, 5, 6],
            backgroundColor: 'rgba(128, 90, 213, 0.6)'
            }]
        },
        options: {
            responsive: true,
            maintainaspectRatio: true,
            scales: { y: { beginAtZero: true } }
        }
        });
    </script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>