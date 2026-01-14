<?php
$headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
require_once $headerPath;
require_role(['office_admin']);

$year_id = isset($_GET['year_id']) ? (int)$_GET['year_id'] : 3; // Default to current year

// fake years data
$years_data = [
    1 => ['label' => '2023-2024'],
    2 => ['label' => '2024-2025'],
    3 => ['label' => '2025-2026'],
];

$current_year = isset($years_data[$year_id]) ? $years_data[$year_id]['label'] : 'Unknown';

// fake terms data
// TODO: hook this up to the db later
$terms = [
    ['id' => '1', 'label' => 'Term 1', 'start_date' => '2025-09-01', 'end_date' => '2025-12-20', 'status' => 'Completed', 'total_students' => 320, 'avg_attendance' => '94%'],
    ['id' => '2', 'label' => 'Term 2', 'start_date' => '2026-01-06', 'end_date' => '2026-04-15', 'status' => 'Current', 'total_students' => 320, 'avg_attendance' => '96%'],
    ['id' => '3', 'label' => 'Term 3', 'start_date' => '2026-04-20', 'end_date' => '2026-07-15', 'status' => 'Upcoming', 'total_students' => 320, 'avg_attendance' => 'N/A'],
];
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

    <main class="main-dashboard">
        <div class="header">
            <h2>Terms Management</h2>
        </div>

        <h1>Terms for <?php echo e($current_year); ?></h1>

        <div style="display: flex; gap: 16px; align-items: center; padding: 0 40px 24px;">
            <a href="school_years_list.php" style="background: #6b7280; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block; font-weight: 500; transition: all 0.2s ease;">
                ← Back to School Years
            </a>
            <a href="term_create.php?year_id=<?php echo $year_id; ?>" style="background: #0891b2; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block; font-weight: 500; transition: all 0.2s ease;">
                + Create New Term
            </a>
        </div>

        <!-- stats overview -->
        <section style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 0 40px 32px;">
            <div class="card">
                <h3>Total Terms</h3>
                <p style="font-size: 32px; font-weight: 700; color: #1a1a1a; margin: 8px 0 0;"><?php echo count($terms); ?></p>
            </div>
            <div class="card">
                <h3>Current Term</h3>
                <p style="font-size: 32px; font-weight: 700; color: #1a1a1a; margin: 8px 0 0;">Term 2</p>
            </div>
            <div class="card">
                <h3>Avg. Attendance</h3>
                <p style="font-size: 32px; font-weight: 700; color: #1a1a1a; margin: 8px 0 0;">95%</p>
            </div>
            <div class="card">
                <h3>Total Students</h3>
                <p style="font-size: 32px; font-weight: 700; color: #1a1a1a; margin: 8px 0 0;">320</p>
            </div>
        </section>

        <h2 class="heading users">All Terms for <?php echo e($current_year); ?></h2>

        <section class="table-card">
            <?php if (empty($terms)): ?>
                <p style="text-align: center; color: #6b7280; padding: 40px;">No terms found for this school year. Create your first term to get started.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Term</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Students</th>
                            <th>Avg. Attendance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($terms as $t): ?>
                            <?php
                            $start = new DateTime($t['start_date']);
                            $end = new DateTime($t['end_date']);
                            $duration = $start->diff($end)->days;
                            ?>
                            <tr>
                                <td><strong><?php echo e($t['label']); ?></strong></td>
                                <td><?php echo date('M d, Y', strtotime($t['start_date'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($t['end_date'])); ?></td>
                                <td><?php echo $duration; ?> days</td>
                                <td>
                                    <?php
                                    $badgeClass = '';
                                    if ($t['status'] === 'Current') $badgeClass = 'badge-info';
                                    elseif ($t['status'] === 'Completed') $badgeClass = 'badge-success';
                                    else $badgeClass = 'badge-warning';
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo e($t['status']); ?></span>
                                </td>
                                <td><?php echo e($t['total_students']); ?></td>
                                <td><?php echo e($t['avg_attendance']); ?></td>
                                <td>
                                    <a href="term_edit.php?id=<?php echo urlencode($t['id']); ?>&year_id=<?php echo $year_id; ?>" class="action-btn edit-btn">Edit</a>
                                    <a href="term_delete.php?id=<?php echo urlencode($t['id']); ?>&year_id=<?php echo $year_id; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this term?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <!-- info block -->
        <section class="table-card">
            <h2>About Academic Terms</h2>
            <p style="color: #6b7280; line-height: 1.6;">
                The academic year in Guyanese primary schools is divided into three terms:
            </p>
            <ul style="color: #6b7280; line-height: 1.8; margin-top: 16px;">
                <li><strong>Term 1:</strong> September to December (approximately 15 weeks)</li>
                <li><strong>Term 2:</strong> January to April (approximately 14 weeks)</li>
                <li><strong>Term 3:</strong> April to July (approximately 12 weeks)</li>
            </ul>
            <p style="color: #6b7280; line-height: 1.6; margin-top: 16px;">
                Each term includes regular assessments, parent-teacher meetings, and culminates in end-of-term examinations. Report cards are generated at the end of each term.
            </p>
        </section>
    </main>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
