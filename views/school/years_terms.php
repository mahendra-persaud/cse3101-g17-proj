<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/years.css">';
require_once __DIR__ . '/../../models/School.php';
require_once $headerPath;
require_once dirname($headerPath) . '/sidebar.php';
?>

  <main class="dashboard">
    <!-- 🔹 Top Bar -->
    <div class = "header">
        <h2>Term Management</h2></div>

    <section class="form-card">
      <h2>Add New Term</h2>
      <a href="term_create.php" class="create-btn" style="text-decoration: none; display: inline-block; background: #0891b2; color: white; padding: 10px 20px; border-radius: 6px; font-weight: 500;">+ Open Term Creation Form</a>
    </section>

    <section class="table-card">
      <h2>Years & Terms</h2>
      <?php
      $schoolModel = new School();
      $terms = $schoolModel->getTermsWithYears();
      ?>
      <table>
        <thead>
          <tr>
            <th>Year</th>
            <th>Term Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($terms)): ?>
            <tr><td colspan="5" style="text-align: center; padding: 20px;">No terms found.</td></tr>
          <?php else: ?>
            <?php foreach ($terms as $t): ?>
              <tr>
                <td><?php echo e($t['year_name']); ?></td>
                <td><strong><?php echo e($t['term_name']); ?></strong></td>
                <td><?php echo !empty($t['start_date']) ? date('M d, Y', strtotime($t['start_date'])) : 'TBD'; ?></td>
                <td><?php echo !empty($t['end_date']) ? date('M d, Y', strtotime($t['end_date'])) : 'TBD'; ?></td>
                <td>
                  <a href="term_edit.php?id=<?php echo $t['term_id']; ?>">Edit</a> | 
                  <a href="term_delete.php?id=<?php echo $t['term_id']; ?>" onclick="return confirm('Delete this term?');">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </main>
</body>
</html>