<?php
require 'lib/common.php';
require_auth(); // Protect Page

$user_id = $_SESSION['user_id'];
$all_tasks = get_tasks();

// Filter: Get only MY tasks
$my_tasks = array_filter($all_tasks, function($t) use ($user_id) {
    return $t['user_id'] == $user_id;
});

include 'templates/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h1>Můj Přehled</h1>
    <a href="create.php" class="btn btn-success">Nový úkol</a>
</div>

<?php if (empty($my_tasks)): ?>
    <div class="card" style="text-align:center; color:#777;">
        <p>Nemáte žádné úkoly. Začněte tím, že nějaký přidáte!</p>
    </div>
<?php else: ?>
    <div class="task-list">
        <?php foreach ($my_tasks as $task): ?>
            <div class="task-item">
                <div>
                    <strong><?= h($task['title']) ?></strong><br>
                    <small style="color:#777">Termín: <?= h($task['due_date']) ?></small>
                </div>
                <span class="status-badge status-<?= h($task['status']) ?>">
                    <?= $task['status'] === 'pending' ? 'Nevyřízeno' : 'Hotovo' ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'templates/footer.php'; ?>