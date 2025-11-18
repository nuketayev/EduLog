<?php
require 'lib/common.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $date = $_POST['due_date'];

    if ($title && $date) {
        $tasks = get_tasks();
        $tasks[] = [
            'id' => get_next_id($tasks),
            'user_id' => $_SESSION['user_id'],
            'title' => $title,
            'due_date' => $date,
            'status' => 'pending'
        ];
        save_tasks($tasks);
        set_flash('success', 'Úkol byl přidán.');
        header("Location: index.php");
        exit();
    } else {
        set_flash('error', 'Vyplňte prosím všechna pole.');
    }
}

include 'templates/header.php';
?>

<div class="card" style="max-width: 500px; margin: 0 auto;">
    <h2>Přidat nový úkol</h2>
    <form method="post">
        <div class="form-group">
            <label>Název úkolu:</label>
            <input type="text" name="title" required placeholder="Např. Koupit skripta">
        </div>
        <div class="form-group">
            <label>Termín splnění:</label>
            <input type="date" name="due_date" required>
        </div>
        <button type="submit" class="btn btn-success" style="width:100%">Uložit úkol</button>
    </form>
    <div style="text-align:center; margin-top:15px;">
        <a href="index.php" style="color:#777;">Zrušit</a>
    </div>
</div>

<?php include 'templates/footer.php'; ?>