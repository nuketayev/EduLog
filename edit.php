<?php
// edit task page
require 'lib/common.php';
require_auth();

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit(); }

$user_id = $_SESSION['user_id'];
$all_tasks = get_tasks();
$task_index = null;
$current_task = null;

// find task
foreach ($all_tasks as $key => $t) {
    if ($t['id'] == $id && $t['user_id'] == $user_id) {
        $task_index = $key;
        $current_task = $t;
        break;
    }
}

if ($current_task === null) {
    set_flash('error', 'Task not found.');
    header("Location: index.php");
    exit();
}

$subjects = [];
if (function_exists('get_subjects')) {
    $subjects = array_filter(get_subjects(), function($s) use ($user_id) {
        return $s['user_id'] == $user_id;
    });
}

// handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title = trim($_POST['title']);
    $description = trim($_POST['description'] ?? '');
    
    if ($title) {
        $all_tasks[$task_index]['title'] = $title;
        $all_tasks[$task_index]['description'] = $description;
        $all_tasks[$task_index]['due_date'] = $_POST['due_date'];
        $all_tasks[$task_index]['status'] = $_POST['status'];
        $all_tasks[$task_index]['subject_id'] = $_POST['subject_id'] ?? '';

        // image upload logic
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $new_name = uniqid() . '.' . $ext;
                $upload_dir = __DIR__ . '/assets/uploads/';
                $dest = $upload_dir . $new_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    // remove old files
                    if (!empty($current_task['image'])) {
                        @unlink($upload_dir . $current_task['image']);
                        @unlink($upload_dir . 'thumb_' . $current_task['image']);
                    }
                    
                    $all_tasks[$task_index]['image'] = $new_name; 
                    make_thumb($dest, $upload_dir . 'thumb_' . $new_name, 300);
                }
            }
        }

        save_tasks($all_tasks);
        set_flash('success', 'Task updated.');
        header("Location: index.php");
        exit();
    }
}

include 'templates/header.php';
?>

<div class="card card-narrow">
    <h2>Edit Task</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">

        <?php if (!empty($subjects)): ?>
        <div class="form-group">
            <label for="subject_id">Subject:</label>
            <select name="subject_id" id="subject_id">
                <option value="">-- None --</option>
                <?php foreach($subjects as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ($current_task['subject_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                        <?= h($s['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" value="<?= h($current_task['title']) ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" rows="4"><?= h($current_task['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="due_date">Due Date:</label>
            <input type="date" name="due_date" id="due_date" value="<?= h($current_task['due_date']) ?>" required>
        </div>

        <div class="form-group">
            <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="pending" <?= $current_task['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="completed" <?= $current_task['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>
        </div>

        <div class="form-group">
            <label for="image">Change Image:</label>
            <input type="file" name="image" id="image" accept="image/*">
            <?php if(!empty($current_task['image'])): ?>
                <small>Current: <a href="assets/uploads/<?= h($current_task['image']) ?>" target="_blank">View</a></small>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary w-100">Save Changes</button>
    </form>
    
    <div class="text-center mt-15">
        <a href="index.php" class="text-muted mr-10">Cancel</a>
        <a href="delete.php?type=task&id=<?= $current_task['id'] ?>&token=<?= generate_csrf() ?>" 
           class="text-danger js-confirm" 
           data-confirm="Delete this task?">Delete task</a>
    </div>
</div>
<?php include 'templates/footer.php'; ?>