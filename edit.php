<?php
// edit.php
require 'lib/common.php';
require_auth();

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit(); }

$user_id = $_SESSION['user_id'];
$all_tasks = get_tasks();
$task_index = null;
$current_task = null;

// Find Task
foreach ($all_tasks as $key => $t) {
    if ($t['id'] == $id && $t['user_id'] == $user_id) {
        $task_index = $key;
        $current_task = $t;
        break;
    }
}

if ($current_task === null) {
    set_flash('error', 'Úkol nenalezen.');
    header("Location: index.php");
    exit();
}

// Load subjects
$subjects = [];
if (function_exists('get_subjects')) {
    $subjects = array_filter(get_subjects(), function($s) use ($user_id) {
        return $s['user_id'] == $user_id;
    });
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description'] ?? '');
    
    if ($title) {
        $all_tasks[$task_index]['title'] = $title;
        $all_tasks[$task_index]['description'] = $description;
        $all_tasks[$task_index]['due_date'] = $_POST['due_date'];
        $all_tasks[$task_index]['status'] = $_POST['status'];
        $all_tasks[$task_index]['subject_id'] = $_POST['subject_id'] ?? '';

        // Handle Image Update
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $new_name = uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/assets/uploads/' . $new_name);
                $all_tasks[$task_index]['image'] = $new_name; 
            }
        }

        save_tasks($all_tasks);
        set_flash('success', 'Úkol upraven.');
        header("Location: index.php");
        exit();
    }
}

include 'templates/header.php';
?>

<div class="card" style="max-width: 500px; margin: 0 auto;">
    <h2>Upravit úkol</h2>
    <form method="post" enctype="multipart/form-data">
        <?php if (!empty($subjects)): ?>
        <div class="form-group">
            <label>Předmět:</label>
            <select name="subject_id">
                <option value="">-- Bez předmětu --</option>
                <?php foreach($subjects as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ($current_task['subject_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                        <?= h($s['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label>Název:</label>
            <input type="text" name="title" value="<?= h($current_task['title']) ?>" required>
        </div>

        <div class="form-group">
            <label>Podrobný popis:</label>
            <textarea name="description" rows="4"><?= h($current_task['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Termín:</label>
            <input type="date" name="due_date" value="<?= h($current_task['due_date']) ?>" required>
        </div>

        <div class="form-group">
            <label>Stav:</label>
            <select name="status">
                <option value="pending" <?= $current_task['status'] == 'pending' ? 'selected' : '' ?>>Nevyřízeno</option>
                <option value="completed" <?= $current_task['status'] == 'completed' ? 'selected' : '' ?>>Hotovo</option>
            </select>
        </div>

        <div class="form-group">
            <label>Změnit obrázek:</label>
            <input type="file" name="image" accept="image/*">
            <?php if(!empty($current_task['image'])): ?>
                <small>Aktuální: <a href="assets/uploads/<?= h($current_task['image']) ?>" target="_blank">Zobrazit</a></small>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%">Uložit změny</button>
    </form>
</div>
<?php include 'templates/footer.php'; ?>