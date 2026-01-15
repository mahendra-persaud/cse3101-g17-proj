<?php
// page to edit a score

require_once __DIR__ . '/../../controllers/ScoreController.php';

$scoreController = new ScoreController();
$scoreController->requireRole(['teacher', 'office_admin']);

$scoreId = $_GET['id'] ?? null;
if (!$scoreId) {
    header('Location: scores_list.php');
    exit;
}

// getting the score with all the extra info like names
$score = (new Score())->getScoreAudit($scoreId);
if (!$score) {
    header('Location: scores_list.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'student_id' => $score['student_id'],
        'subject_id' => $score['subject_id'],
        'term_id' => $score['term_id'],
        'score' => $_POST['score'] ?? ''
    ];
    
    $result = $scoreController->store($data);
    if ($result['success']) {
        ScoreController::setFlash('success', 'Score updated successfully.');
        header('Location: scores_list.php');
        exit;
    } else {
        $errors = $result['errors'] ?? [$result['message'] ?? 'An error occurred.'];
    }
}

$projectRoot = '/cse3101-g17-proj';
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css?v=' . time() . '">';
$no_container = true;
$body_class = 'management-page';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="Main-Container">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="main-dashboard">
        <div class="header">
            <h2>Score Management</h2>
        </div>

        <nav class="breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo $projectRoot; ?>/views/dashboard/index.php">Home</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><a href="scores_list.php">Scores</a></div>
            <div class="breadcrumb-separator">></div>
            <div class="breadcrumb-item"><span class="breadcrumb-current">Edit Score</span></div>
        </nav>

        <h1>Edit Student Score</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <section class="form-card">
            <h2>Score Details for <?php echo e($score['student_name']); ?></h2>
            <div class="info-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; padding: 20px; background: rgba(0,0,0,0.02); border-radius: 8px;">
                <div>
                    <span style="color: #6b7280; font-size: 0.9em;">Subject:</span>
                    <div style="font-weight: 600;"><?php echo e($score['subject_name']); ?></div>
                </div>
                <div>
                    <span style="color: #6b7280; font-size: 0.9em;">Academic Context:</span>
                    <div style="font-weight: 600;"><?php echo e($score['term_name']); ?> (<?php echo e($score['year_name']); ?>)</div>
                </div>
                <div>
                    <span style="color: #6b7280; font-size: 0.9em;">Created By:</span>
                    <div style="font-weight: 500;"><?php echo e($score['created_by_username'] ?: 'System'); ?> at <?php echo date('M d, Y H:i', strtotime($score['created_at'])); ?></div>
                </div>
                <?php if ($score['modified_by']): ?>
                <div>
                    <span style="color: #6b7280; font-size: 0.9em;">Last Modified:</span>
                    <div style="font-weight: 500;"><?php echo e($score['modified_by_username']); ?> at <?php echo date('M d, Y H:i', strtotime($score['modified_at'])); ?></div>
                </div>
                <?php endif; ?>
            </div>

            <form method="POST" action="">
                <?php echo ScoreController::csrfField(); ?>
                
                <div class="form-group">
                    <label for="score">Score (0-100)</label>
                    <input type="number" id="score" name="score" value="<?php echo e($score['score']); ?>" min="0" max="100" step="1" required>
                    <p class="help-text" style="font-size: 0.8em; color: #6b7280; margin-top: 5px;">Enter the student's numeric grade for this subject and term.</p>
                </div>

                <div class="form-actions">
                    <button type="submit" class="create-btn">Update Score</button>
                    <a href="scores_list.php" class="btn" style="background: #9ca3af; text-decoration: none;">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
