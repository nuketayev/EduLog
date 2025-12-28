<?php
// handles deletion logic
require 'lib/common.php';
require_auth();

$id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? ''; 
$token = $_GET['token'] ?? '';
$user_id = $_SESSION['user_id'];

if ($id && $type) {
    // security check against fake links
    verify_csrf_token($token);

    if ($type === 'task') {
        $data = get_tasks();
        $file_func = 'save_tasks';
        
        $to_delete = null;
        foreach ($data as $item) {
            if ($item['id'] == $id && $item['user_id'] == $user_id) {
                $to_delete = $item;
                break;
            }
        }
        
        // delete files too
        if ($to_delete && !empty($to_delete['image'])) {
            $path = __DIR__ . '/assets/uploads/' . $to_delete['image'];
            $thumb_path = __DIR__ . '/assets/uploads/thumb_' . $to_delete['image'];
            if (file_exists($path)) @unlink($path);
            if (file_exists($thumb_path)) @unlink($thumb_path);
        }

    } elseif ($type === 'subject') {
        $data = get_subjects();
        $file_func = 'save_subjects';
    } else {
        die("Invalid type");
    }

    $new_data = array_filter($data, function($item) use ($id, $user_id) {
        return !($item['id'] == $id && $item['user_id'] == $user_id);
    });

    $new_data = array_values($new_data);
    $file_func($new_data);
    set_flash('success', 'Item deleted.');
}

if ($type === 'subject') {
    header("Location: subjects.php");
} else {
    header("Location: index.php");
}
exit();
?>