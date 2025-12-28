<?php
// main dashboard page
require 'lib/common.php';
require_auth();

$user_id = $_SESSION['user_id'];
// get filters from url
$status_filter = $_GET['status'] ?? 'pending';
$subject_filter = $_GET['subject_id'] ?? '';

// load data from json
$all_tasks = get_tasks();
$all_subjects = get_subjects();

// map subjects to ids
$subject_map = [];
$my_subjects = [];
foreach ($all_subjects as $s) {
    $subject_map[$s['id']] = $s['name'];
    if ($s['user_id'] == $user_id) $my_subjects[] = $s;
}

// get filtered lists using helper function
$my_tasks_all = get_filtered_tasks($all_tasks, $user_id); // needed for stats count
$filtered_tasks = get_filtered_tasks($all_tasks, $user_id, $status_filter, $subject_filter); // needed for view

// prepare data for display (dates, images, etc)
foreach ($filtered_tasks as &$task) {
    $task = enrich_task_data($task, $subject_map);
}
unset($task);

// calc stats
$total = count($my_tasks_all);
$pending = count(array_filter($my_tasks_all, fn($t) => $t['status'] === 'pending'));
$completed = $total - $pending;

// pagination logic
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 5; 
$total_visible = count($filtered_tasks);
$max_pages = ceil($total_visible / $per_page);
// slice array for current page
$paged_tasks = array_slice($filtered_tasks, ($page - 1) * $per_page, $per_page);

include 'templates/header.php';
?>

<div class="flex-between mb-20">
    <h1>Můj Přehled</h1>
    <div>
        <a href="export.php" class="btn btn-info mr-5">Export CSV</a>
        <a href="subjects.php" class="btn btn-secondary mr-5">Předměty</a>
        <a href="create.php" class="btn btn-success">+ Nový úkol</a>
    </div>
</div>

<div class="flex-gap-15 mb-30">
    <div class="stat-card">
        <h3 id="stat-total" class="stat-number color-primary"><?= $total ?></h3>
        <small class="text-muted">Celkem</small>
    </div>
    <div class="stat-card">
        <h3 id="stat-pending" class="stat-number color-danger"><?= $pending ?></h3>
        <small class="text-muted">Nevyřízeno</small>
    </div>
    <div class="stat-card">
        <h3 id="stat-completed" class="stat-number color-success"><?= $completed ?></h3>
        <small class="text-muted">Hotovo</small>
    </div>
</div>

<div class="card p-15 mb-20 bg-light">
    <form id="filterForm" class="flex-gap-15 items-center">
        <strong>Filtrovat:</strong>
        <select name="status" class="p-5" aria-label="Stav">
            <option value="" <?= $status_filter === '' ? 'selected' : '' ?>>Všechny stavy</option>
            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Nevyřízeno</option>
            <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Hotovo</option>
        </select>
        <select name="subject_id" class="p-5" aria-label="Předmět">
            <option value="">Všechny předměty</option>
            <?php foreach($my_subjects as $s): ?>
                <option value="<?= $s['id'] ?>" <?= $subject_filter == $s['id'] ? 'selected' : '' ?>>
                    <?= h($s['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="task-list">
    <?php if (empty($paged_tasks)): ?>
        <div class="card text-center text-muted">
            <p>Žádné úkoly k zobrazení.</p>
        </div>
    <?php else: ?>
        <?php foreach ($paged_tasks as $task): ?>
            <div class="task-item <?= $task['row_class'] ?>">
                <div class="flex-1">
                    <?php if($task['subject_name']): ?>
                        <span class="task-badge"><?= $task['subject_name'] ?></span>
                    <?php endif; ?>

                    <strong class="task-title"><?= $task['title'] ?></strong>
                    
                    <?php if($task['description']): ?>
                        <p class="task-desc"><?= $task['description'] ?></p>
                    <?php endif; ?>
                    
                    <small class="text-muted">
                        Termín: <?= $task['due_date_formatted'] ?> <?= $task['days_text'] ?>
                    </small>
                    
                    <?php if($task['thumb_url']): ?>
                        <img 
                            src="<?= $task['thumb_url'] ?>" 
                            class="task-thumb js-open-modal" 
                            alt="Příloha" 
                            loading="lazy"
                            data-src="<?= $task['full_url'] ?>"
                        >
                    <?php endif; ?>
                </div>

                <div class="text-right">
                    <span class="status-badge status-<?= $task['status_class'] ?> mr-10">
                        <?= $task['status_text'] ?>
                    </span>
                    <br><br>
                    <?php if($task['status'] !== 'completed'): ?>
                        <button class="btn btn-success btn-sm mr-5 js-mark-complete" data-id="<?= $task['id'] ?>">Hotovo</button>
                    <?php endif; ?>
                    <a href="edit.php?id=<?= $task['id'] ?>" class="btn-sm mr-5">Upravit</a>
                    
                    <a href="delete.php?type=task&id=<?= $task['id'] ?>&token=<?= generate_csrf() ?>" 
                       class="text-danger btn-sm js-confirm" 
                       data-confirm="Smazat?">Smazat</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if ($max_pages > 1): ?>
<div id="pagination" class="text-center mt-20">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>" class="btn btn-pagination">&laquo; Předchozí</a>
    <?php endif; ?>
    <span class="mx-10">Strana <?= $page ?> z <?= $max_pages ?></span>
    <?php if ($page < $max_pages): ?>
        <a href="?page=<?= $page + 1 ?>" class="btn btn-pagination">Další &raquo;</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php include 'templates/footer.php'; ?>