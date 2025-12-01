<?php
// delete.php
require 'lib/common.php';
require_auth();

$id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? ''; // 'task' or 'subject'
$user_id = $_SESSION['user_id'];

if ($id && $type) {
    if ($type === 'task') {
        $data = get_tasks();
        $file_func = 'save_tasks';
    } elseif ($type === 'subject') {
        $data = get_subjects();
        $file_func = 'save_subjects';
    } else {
        die("Invalid type");
    }

    // Filter out the item (Delete logic)
    $new_data = array_filter($data, function($item) use ($id, $user_id) {
        // Keep item if ID doesn't match OR User ID doesn't match (security)
        return !($item['id'] == $id && $item['user_id'] == $user_id);
    });

    // Re-index array to prevent JSON object conversion
    $new_data = array_values($new_data);

    // Save
    $file_func($new_data);
    set_flash('success', 'Položka smazána.');
}

// Redirect back
if ($type === 'subject') {
    header("Location: subjects.php");
} else {
    header("Location: index.php");
}
exit();
?>