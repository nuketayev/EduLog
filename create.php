<?php
/**
 * Create Task.
 * Form for adding new tasks.
 */

require 'lib/common.php';
require_auth();

$user_id = $_SESSION['user_id'];
$subjects = []; 
if (function_exists('get_subjects')) {
    $subjects = array_filter(get_subjects(), function($s) use ($user_id) {
        return $s['user_id'] == $user_id;
    });
}

// defaults
$title = '';
$description = '';
$date = '';
$subject_id = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title = trim($_POST['title']);
    $description = trim($_POST['description'] ?? '');
    $date = $_POST['due_date'];
    $subject_id = $_POST['subject_id'] ?? '';
    
    if ($title && $date) {
        $tasks = get_tasks();
        
        // upload image
        $image_file = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $new_name = uniqid() . '.' . $ext;
                $upload_dir = __DIR__ . '/assets/uploads/';
                $dest = $upload_dir . $new_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $image_file = $new_name;
                    make_thumb($dest, $upload_dir . 'thumb_' . $new_name, 300);
                }
            }
        }

        // save
        $tasks[] = [
            'id' => get_next_id($tasks),
            'user_id' => $user_id,
            'subject_id' => $subject_id,
            'title' => $title,
            'description' => $description,
            'due_date' => $date,
            'status' => 'pending',
            'image' => $image_file
        ];
        
        save_tasks($tasks);
        set_flash('success', 'Úkol vytvořen.');
        header("Location: index.php");
        exit();
    } else {
        set_flash('error', 'Vyplňte prosím povinná pole.');
    }
}

include 'templates/header.php';
?>

<div class="card card-narrow">
    <h2>Nový úkol</h2>
    <form method="post" enctype="multipart/form-data" id="createTaskForm">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
        
        <?php if (!empty($subjects)): ?>
        <div class="form-group">
            <label for="subject_id">Předmět:</label>
            <select name="subject_id" id="subject_id">
                <option value="">-- Žádný --</option>
                <?php foreach($subjects as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $subject_id == $s['id'] ? 'selected' : '' ?>>
                        <?= h($s['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="title">Název: *</label>
            <input type="text" name="title" id="title" required value="<?= h($title) ?>" placeholder="Např. Koupit skripta">
        </div>

        <div class="form-group">
            <label for="description">Popis:</label>
            <textarea name="description" id="description" rows="4" placeholder="Detaily..."><?= h($description) ?></textarea>
        </div>

        <div class="form-group">
            <label for="due_date">Termín: *</label>
            <input type="date" name="due_date" id="due_date" required value="<?= h($date) ?>">
        </div>

        <div class="form-group">
            <label for="image">Obrázek (volitelné):</label>
            <input type="file" name="image" id="image" accept="image/*">
        </div>

        <button type="submit" class="btn btn-success w-100">Uložit úkol</button>
    </form>
    <div class="text-center mt-15">
        <a href="index.php" class="text-muted">Zrušit</a>
    </div>
</div>
<?php include 'templates/footer.php'; ?>