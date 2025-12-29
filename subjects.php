<?php
/**
 * Subjects Page.
 * Manage user subjects.
 */

require 'lib/common.php';
require_auth();

$user_id = $_SESSION['user_id'];
$all_subjects = get_subjects();

// add new
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

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

$my_subjects = array_filter($all_subjects, function($s) use ($user_id) {
    return $s['user_id'] == $user_id;
});

include 'templates/header.php';
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2>Správa předmětů</h2>
    
    <form method="post" class="flex-gap-10 mb-20">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
        <label for="name" class="sr-only">Název předmětu</label>
        <input type="text" name="name" id="name" required placeholder="Nový předmět (např. Matematika)" class="flex-1">
        <button type="submit" class="btn btn-success">Přidat</button>
    </form>

    <table class="table-full">
        <?php if(empty($my_subjects)): ?>
            <tr><td class="text-center text-muted">Zatím žádné předměty.</td></tr>
        <?php else: ?>
            <?php foreach($my_subjects as $s): ?>
            <tr>
                <td class="p-15"><strong><?= h($s['name']) ?></strong></td>
                <td class="text-right">
                    <a href="delete.php?type=subject&id=<?= $s['id'] ?>&token=<?= generate_csrf() ?>" 
                       class="text-danger js-confirm" 
                       data-confirm="Opravdu smazat předmět?">Smazat</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
    <div class="text-center mt-20">
        <a href="index.php" class="text-muted">Zpět</a>
    </div>
</div>

<?php include 'templates/footer.php'; ?>