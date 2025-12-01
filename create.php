<?php
// create.php
require 'lib/common.php';
require_auth();

$user_id = $_SESSION['user_id'];
$subjects = []; 
if (function_exists('get_subjects')) {
    $subjects = array_filter(get_subjects(), function($s) use ($user_id) {
        return $s['user_id'] == $user_id;
    });
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description'] ?? '');
    $date = $_POST['due_date'];
    $subject_id = $_POST['subject_id'] ?? '';
    
    if ($title && $date) {
        $tasks = get_tasks();
        
        // Image Upload Logic
        $image_file = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $new_name = uniqid() . '.' . $ext;
                $dest = __DIR__ . '/assets/uploads/' . $new_name;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $image_file = $new_name;
                }
            }
        }

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
        set_flash('success', 'Úkol byl přidán.');
        header("Location: index.php");
        exit();
    } else {
        set_flash('error', 'Vyplňte povinná pole.');
    }
}

include 'templates/header.php';
?>

<div class="card" style="max-width: 500px; margin: 0 auto;">
    <h2>Nový úkol</h2>
    <form method="post" enctype="multipart/form-data">
        <?php if (!empty($subjects)): ?>
        <div class="form-group">
            <label>Předmět:</label>
            <select name="subject_id">
                <option value="">-- Bez předmětu --</option>
                <?php foreach($subjects as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= h($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label>Název úkolu: *</label>
            <input type="text" name="title" required placeholder="Např. Koupit skripta">
        </div>

        <div class="form-group">
            <label>Podrobný popis:</label>
            <textarea name="description" rows="4" placeholder="Zde můžete rozepsat detaily úkolu..."></textarea>
        </div>

        <div class="form-group">
            <label>Termín: *</label>
            <input type="date" name="due_date" required>
        </div>

        <div class="form-group">
            <label>Obrázek (volitelné):</label>
            <input type="file" name="image" accept="image/*">
        </div>

        <button type="submit" class="btn btn-success" style="width:100%">Uložit úkol</button>
    </form>
    <div style="text-align:center; margin-top:15px;">
        <a href="index.php" style="color:#777;">Zrušit</a>
    </div>
</div>

<?php include 'templates/footer.php'; ?>