<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
require_once $headerPath;
require_role(['office_admin', 'teacher']);
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

    <main class="main-dashboard">
        <div class="header">
            <h2>Reports Management</h2></div>

        <h1>Academic Reports & Analytics</h1>

        <!-- Report Type Cards -->
        <section style="margin: 30px; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
            <a href="report_student_card.php" style="text-decoration: none; color: inherit;">
                <div style="background: rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 30px; backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; cursor: pointer;">
                    <h3 style="margin: 0 0 12px; font-size: 20px; color: #a5d8ff;">📋 Student Report Cards</h3>
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.8); font-size: 14px;">Generate detailed report cards for individual students by term</p>
                </div>
            </a>

            <a href="report_grade_average.php" style="text-decoration: none; color: inherit;">
                <div style="background: rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 30px; backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; cursor: pointer;">
                    <h3 style="margin: 0 0 12px; font-size: 20px; color: #a5d8ff;">📊 Grade Averages</h3>
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.8); font-size: 14px;">View average performance by grade, class, and subject</p>
                </div>
            </a>

            <a href="#" style="text-decoration: none; color: inherit; opacity: 0.6; pointer-events: none;">
                <div style="background: rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 30px; backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <h3 style="margin: 0 0 12px; font-size: 20px; color: #a5d8ff;">📈 Performance Trends</h3>
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.8); font-size: 14px;">Track student progress over multiple terms (Coming Soon)</p>
                </div>
            </a>

            <a href="#" style="text-decoration: none; color: inherit; opacity: 0.6; pointer-events: none;">
                <div style="background: rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 30px; backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <h3 style="margin: 0 0 12px; font-size: 20px; color: #a5d8ff;">🎯 Class Rankings</h3>
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.8); font-size: 14px;">View top performers and class rankings (Coming Soon)</p>
                </div>
            </a>
        </section>

        <!-- Quick Stats -->
        <h2 class='heading subjects' style="margin: 40px 0 20px 30px; font-size: 28px; color: #fff;">Quick Statistics</h2>

        <section class="table-card" style="margin: 30px;">
            <h3 style="margin: 0 0 20px; font-size: 20px; color: #fff;">Term Overview - Term 1 (2025-2026)</h3>

            <table>
                <thead>
                    <tr>
                        <th>Grade</th>
                        <th>Students</th>
                        <th>Average Score</th>
                        <th>Pass Rate</th>
                        <th>Top Subject</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Grade 1</td>
                        <td>52</td>
                        <td style="color: #4ade80; font-weight: 600;">87.5%</td>
                        <td style="color: #4ade80;">96%</td>
                        <td>Mathematics</td>
                    </tr>
                    <tr>
                        <td>Grade 2</td>
                        <td>48</td>
                        <td style="color: #4ade80; font-weight: 600;">85.2%</td>
                        <td style="color: #4ade80;">94%</td>
                        <td>English Language</td>
                    </tr>
                    <tr>
                        <td>Grade 3</td>
                        <td>55</td>
                        <td style="color: #fbbf24; font-weight: 600;">78.9%</td>
                        <td style="color: #fbbf24;">88%</td>
                        <td>Science</td>
                    </tr>
                    <tr>
                        <td>Grade 4</td>
                        <td>50</td>
                        <td style="color: #4ade80; font-weight: 600;">82.1%</td>
                        <td style="color: #4ade80;">91%</td>
                        <td>Social Studies</td>
                    </tr>
                    <tr>
                        <td>Grade 5</td>
                        <td>58</td>
                        <td style="color: #fbbf24; font-weight: 600;">76.8%</td>
                        <td style="color: #fbbf24;">85%</td>
                        <td>Mathematics</td>
                    </tr>
                    <tr>
                        <td>Grade 6</td>
                        <td>57</td>
                        <td style="color: #4ade80; font-weight: 600;">80.5%</td>
                        <td style="color: #4ade80;">89%</td>
                        <td>English Language</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Subject Performance -->
        <h2 class='heading subjects' style="margin: 40px 0 20px 30px; font-size: 28px; color: #fff;">Top Performing Subjects</h2>

        <section class="table-card" style="margin: 30px;">
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Grades Offered</th>
                        <th>Total Students</th>
                        <th>Average Score</th>
                        <th>Pass Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Mathematics</td>
                        <td>All Grades</td>
                        <td>320</td>
                        <td style="color: #4ade80; font-weight: 600;">84.3%</td>
                        <td style="color: #4ade80;">92%</td>
                    </tr>
                    <tr>
                        <td>English Language</td>
                        <td>All Grades</td>
                        <td>320</td>
                        <td style="color: #4ade80; font-weight: 600;">86.7%</td>
                        <td style="color: #4ade80;">95%</td>
                    </tr>
                    <tr>
                        <td>Science</td>
                        <td>All Grades</td>
                        <td>320</td>
                        <td style="color: #fbbf24; font-weight: 600;">79.2%</td>
                        <td style="color: #fbbf24;">87%</td>
                    </tr>
                    <tr>
                        <td>Social Studies</td>
                        <td>All Grades</td>
                        <td>320</td>
                        <td style="color: #4ade80; font-weight: 600;">81.5%</td>
                        <td style="color: #4ade80;">90%</td>
                    </tr>
                    <tr>
                        <td>Physical Education</td>
                        <td>All Grades</td>
                        <td>320</td>
                        <td style="color: #4ade80; font-weight: 600;">91.2%</td>
                        <td style="color: #4ade80;">98%</td>
                    </tr>
                </tbody>
            </table>
        </section>

    </main>
</div>
</body>
</html>
