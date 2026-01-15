<?php
$headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
$no_container = true;
require_once __DIR__ . '/../../models/School.php';
require_once $headerPath;
require_role(['office_admin']);

$schoolModel = new School();
$terms = $schoolModel->getTermsWithYears();
?>

<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

    <main class="main-dashboard">
        <div class="header">
            <h2>School Settings</h2>
        </div>

        <nav class="breadcrumb">
            <div class="breadcrumb-item">
                <a href="<?php echo $projectRoot; ?>/views/dashboard/index.php" class="breadcrumb-link">Home</a>
            </div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item">
                <span class="breadcrumb-current">Years & Terms</span>
            </div>
        </nav>

        <h1>Academic Years & Terms</h1>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <section style="padding: 0 40px 24px; display: flex; gap: 12px;">
            <a href="school_year_create.php" class="create-btn" style="text-decoration: none;">
                + Create School Year
            </a>
            <a href="term_create.php" class="create-btn" style="text-decoration: none;">
                + Create Term
            </a>
        </section>

        <section class="table-card">
            <h2>All Terms by Year</h2>
            <?php if (empty($terms)): ?>
                <div class="empty-state">
                    <div class="empty-state-content">
                        <svg class="empty-state-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <h3>No terms found</h3>
                        <p>Create a school year first, then add terms to organize the academic calendar.</p>
                        <a href="school_year_create.php" class="empty-state-btn">+ Create School Year</a>
                    </div>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Academic Year</th>
                            <th>Term Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($terms as $t): ?>
                            <tr>
                                <td><strong><?php echo e($t['year_name']); ?></strong></td>
                                <td><?php echo e($t['term_name']); ?></td>
                                <td><?php echo !empty($t['start_date']) ? date('M d, Y', strtotime($t['start_date'])) : '<span style="color: #9ca3af;">TBD</span>'; ?></td>
                                <td><?php echo !empty($t['end_date']) ? date('M d, Y', strtotime($t['end_date'])) : '<span style="color: #9ca3af;">TBD</span>'; ?></td>
                                <td>
                                    <a href="term_edit.php?id=<?php echo $t['term_id']; ?>" class="action-btn edit-btn">Edit</a>
                                    <a href="term_delete.php?id=<?php echo $t['term_id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this term?');">Delete</a>
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
