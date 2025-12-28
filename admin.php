<?php
// admin panel for managing users
require 'lib/common.php';
require_auth();

// check role
if ($_SESSION['role'] !== 'admin') {
    set_flash('error', 'Access denied. Admins only.');
    header("Location: index.php");
    exit();
}

$users = get_users();
$all_tasks = get_tasks();
$all_subjects = get_subjects();

// delete user logic
if (isset($_GET['delete_user'])) {
    verify_csrf_token($_GET['token'] ?? '');

    $del_id = $_GET['delete_user'];
    
    if ($del_id == $_SESSION['user_id']) {
        set_flash('error', 'You cannot delete yourself.');
    } else {
        // remove user
        $users = array_filter($users, fn($u) => $u['id'] != $del_id);
        
        // cleanup images
        $upload_dir = __DIR__ . '/assets/uploads/';
        foreach ($all_tasks as $t) {
            if ($t['user_id'] == $del_id && !empty($t['image'])) {
                if (file_exists($upload_dir . $t['image'])) {
                    @unlink($upload_dir . $t['image']);
                }
                if (file_exists($upload_dir . 'thumb_' . $t['image'])) {
                    @unlink($upload_dir . 'thumb_' . $t['image']);
                }
            }
        }
        
        // remove their tasks and subjects
        $all_tasks = array_filter($all_tasks, fn($t) => $t['user_id'] != $del_id);
        $all_subjects = array_filter($all_subjects, fn($s) => $s['user_id'] != $del_id);
        
        save_users(array_values($users));
        save_tasks(array_values($all_tasks));
        save_subjects(array_values($all_subjects));
        
        set_flash('success', 'User and all data deleted.');
    }
    header("Location: admin.php");
    exit();
}

// promote/demote user
if (isset($_GET['toggle_role'])) {
    verify_csrf_token($_GET['token'] ?? '');

    $uid = $_GET['toggle_role'];
    foreach ($users as &$u) {
        if ($u['id'] == $uid) {
            $u['role'] = ($u['role'] === 'admin') ? 'user' : 'admin';
            break;
        }
    }
    save_users($users);
    set_flash('success', 'User role updated.');
    header("Location: admin.php");
    exit();
}

include 'templates/header.php';
?>

<div class="card">
    <h2>User Administration</h2>
    <table class="table-full mt-20">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Role</th>
                <th>Data</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $u): 
                $task_count = count(array_filter($all_tasks, fn($t) => $t['user_id'] == $u['id']));
            ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><strong><?= h($u['email']) ?></strong></td>
                <td>
                    <?php if($u['role'] === 'admin'): ?>
                        <span class="status-badge badge-admin">admin</span>
                    <?php else: ?>
                        <span class="status-badge badge-user">user</span>
                    <?php endif; ?>
                </td>
                <td class="text-muted">
                    <?= $task_count ?> tasks
                </td>
                <td class="text-right">
                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                        <a href="?toggle_role=<?= $u['id'] ?>&token=<?= generate_csrf() ?>" class="btn-sm mr-10">Toggle Role</a>
                        <a href="?delete_user=<?= $u['id'] ?>&token=<?= generate_csrf() ?>" class="text-danger btn-sm js-confirm" data-confirm="Really delete user?">Delete</a>
                    <?php else: ?>
                        <span class="text-muted btn-sm">(You)</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'templates/footer.php'; ?>