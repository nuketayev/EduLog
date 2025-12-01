<?php
// subjects.php
require 'lib/common.php';
require_auth();

$user_id = $_SESSION['user_id'];
$all_subjects = get_subjects();

// Handle Add Subject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    if ($name) {
        $all_subjects[] = [
            'id' => get_next_id($all_subjects),
            'user_id' => $user_id,
            'name' => $name
        ];
        save_subjects($all_subjects);
        set_flash('success', 'Předmět přidán.');
        header("Location: subjects.php");
        exit();
    }
}

// Filter MY subjects
$my_subjects = array_filter($all_subjects, function($s) use ($user_id) {
    return $s['user_id'] == $user_id;
});

include 'templates/header.php';
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2>Správa předmětů</h2>
    
    <form method="post" style="display:flex; gap:10px; margin-bottom:20px;">
        <input type="text" name="name" required placeholder="Nový předmět (např. Matematika)" style="flex:1;">
        <button type="submit" class="btn btn-success">Přidat</button>
    </form>

    <table style="width:100%; border-collapse:collapse;">
        <?php if(empty($my_subjects)): ?>
            <tr><td style="color:#777; text-align:center;">Zatím žádné předměty.</td></tr>
        <?php else: ?>
            <?php foreach($my_subjects as $s): ?>
            <tr style="border-bottom:1px solid #eee;">
                <td style="padding:10px;"><strong><?= h($s['name']) ?></strong></td>
                <td style="text-align:right;">
                    <a href="delete.php?type=subject&id=<?= $s['id'] ?>" 
                       onclick="return confirm('Opravdu smazat?');" 
                       style="color:red; text-decoration:none;">Smazat</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

<?php include 'templates/footer.php'; ?>