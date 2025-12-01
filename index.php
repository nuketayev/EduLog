<?php
// index.php
require 'lib/common.php';
require_auth();

$user_id = $_SESSION['user_id'];
$all_tasks = get_tasks();

// Load Subjects (if exists)
$subject_map = [];
if (function_exists('get_subjects')) {
    foreach (get_subjects() as $s) {
        $subject_map[$s['id']] = $s['name'];
    }
}

// Filter My Tasks
$my_tasks = array_filter($all_tasks, function($t) use ($user_id) {
    return $t['user_id'] == $user_id;
});

// Stats
$total = count($my_tasks);
$pending = count(array_filter($my_tasks, fn($t) => $t['status'] === 'pending'));
$completed = $total - $pending;

include 'templates/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h1>Můj Přehled</h1>
    <div>
        <a href="subjects.php" class="btn" style="background:#6c757d; color:white; margin-right:5px;">Spravovat předměty</a>
        <a href="create.php" class="btn btn-success">+ Nový úkol</a>
    </div>
</div>

<div style="display:flex; gap:15px; margin-bottom:30px;">
    <div class="card" style="flex:1; text-align:center; padding:15px;">
        <h3 style="margin:0; font-size:2rem; color:#007bff;"><?= $total ?></h3>
        <small>Celkem úkolů</small>
    </div>
    <div class="card" style="flex:1; text-align:center; padding:15px;">
        <h3 style="margin:0; font-size:2rem; color:#dc3545;"><?= $pending ?></h3>
        <small>Nevyřízeno</small>
    </div>
    <div class="card" style="flex:1; text-align:center; padding:15px;">
        <h3 style="margin:0; font-size:2rem; color:#28a745;"><?= $completed ?></h3>
        <small>Hotovo</small>
    </div>
</div>

<?php if (empty($my_tasks)): ?>
    <div class="card" style="text-align:center; color:#777;">
        <p>Nemáte žádné úkoly.</p>
    </div>
<?php else: ?>
    <div class="task-list">
        <?php foreach ($my_tasks as $task): ?>
            <div class="task-item">
                <div style="flex:1;">
                    <?php if(!empty($task['subject_id']) && isset($subject_map[$task['subject_id']])): ?>
                        <span style="background:#e9ecef; padding:2px 6px; border-radius:4px; font-size:0.8em; color:#555;">
                            <?= h($subject_map[$task['subject_id']]) ?>
                        </span>
                    <?php endif; ?>

                    <strong style="font-size:1.1em; display:block; margin-top:4px;"><?= h($task['title']) ?></strong>
                    
                    <?php if(!empty($task['description'])): ?>
                        <p style="color:#555; font-size:0.95em; margin:5px 0 10px 0; white-space: pre-wrap;"><?= h($task['description']) ?></p>
                    <?php endif; ?>
                    
                    <small style="color:#777">Termín: <?= h($task['due_date']) ?></small>
                    
                    <?php if(!empty($task['image'])): ?>
                        <br><a href="assets/uploads/<?= h($task['image']) ?>" target="_blank" style="font-size:0.85em; color:#007bff;">📎 Zobrazit přílohu</a>
                    <?php endif; ?>
                </div>

                <div style="text-align:right;">
                    <span class="status-badge status-<?= h($task['status']) ?>" style="margin-right:10px;">
                        <?= $task['status'] === 'pending' ? 'Nevyřízeno' : 'Hotovo' ?>
                    </span>
                    <br><br>
                    <a href="edit.php?id=<?= $task['id'] ?>" style="font-size:0.9em; margin-right:5px;">Upravit</a>
                    <a href="delete.php?type=task&id=<?= $task['id'] ?>" onclick="return confirm('Smazat?')" style="color:red; font-size:0.9em;">Smazat</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'templates/footer.php'; ?>