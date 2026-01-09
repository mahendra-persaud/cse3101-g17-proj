<?php
$headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
require_once $headerPath;
require_role(['office_admin']);

// Sample school years for Guyanese primary school
// TODO: Replace with actual database queries
$years = [
    ['id' => '3', 'label' => '2025-2026', 'start_date' => '2025-09-01', 'end_date' => '2026-07-15', 'status' => 'Current', 'total_students' => 320, 'total_terms' => 3],
    ['id' => '2', 'label' => '2024-2025', 'start_date' => '2024-09-01', 'end_date' => '2025-07-15', 'status' => 'Completed', 'total_students' => 298, 'total_terms' => 3],
    ['id' => '1', 'label' => '2023-2024', 'start_date' => '2023-09-01', 'end_date' => '2024-07-15', 'status' => 'Archived', 'total_students' => 285, 'total_terms' => 3],
];
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

    <main class="main-dashboard">
        <div class="header">
            <h2>School Years</h2>
        </div>

        <h1>Academic Years Management</h1>

        <section style="padding: 0 40px 24px;">
            <a href="school_year_create.php" style="background: #0891b2; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block; font-weight: 500; transition: all 0.2s ease;">
                + Create New School Year
            </a>
        </section>

        <!-- Stats Cards -->
        <section style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 0 40px 32px;">
            <div class="card">
                <h3>Total School Years</h3>
                <p style="font-size: 32px; font-weight: 700; color: #1a1a1a; margin: 8px 0 0;"><?php echo count($years); ?></p>
            </div>
            <div class="card">
                <h3>Current Students</h3>
                <p style="font-size: 32px; font-weight: 700; color: #1a1a1a; margin: 8px 0 0;">320</p>
            </div>
            <div class="card">
                <h3>Active Terms</h3>
                <p style="font-size: 32px; font-weight: 700; color: #1a1a1a; margin: 8px 0 0;">3</p>
            </div>
        </section>

        <h2 class="heading users">All School Years</h2>

        <section class="table-card">
            <?php if (empty($years)): ?>
                <p style="text-align: center; color: #6b7280; padding: 40px;">No school years found. Create your first school year to get started.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Academic Year</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Students</th>
                            <th>Terms</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($years as $y): ?>
                            <tr>
                                <td><strong><?php echo e($y['label']); ?></strong></td>
                                <td><?php echo date('M d, Y', strtotime($y['start_date'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($y['end_date'])); ?></td>
                                <td>
                                    <?php
                                    $badgeClass = '';
                                    if ($y['status'] === 'Current') $badgeClass = 'badge-info';
                                    elseif ($y['status'] === 'Completed') $badgeClass = 'badge-success';
                                    else $badgeClass = 'badge-warning';
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo e($y['status']); ?></span>
                                </td>
                                <td><?php echo e($y['total_students']); ?></td>
                                <td><?php echo e($y['total_terms']); ?></td>
                                <td>
                                    <a href="term_manage.php?year_id=<?php echo urlencode($y['id']); ?>" class="action-btn edit-btn">Manage Terms</a>
                                    <a href="school_year_edit.php?id=<?php echo urlencode($y['id']); ?>" class="action-btn edit-btn">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
