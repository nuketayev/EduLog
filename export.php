<?php
// export tasks to csv
require 'lib/common.php';
require_auth();

$user_id = $_SESSION['user_id'];
$tasks = get_tasks();

$my_tasks = array_filter($tasks, function($t) use ($user_id) {
    return $t['user_id'] == $user_id;
});

// force download headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=tasks.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Title', 'Description', 'Due Date', 'Status', 'Image']);

foreach ($my_tasks as $t) {
    fputcsv($output, [
        $t['id'],
        $t['title'],
        $t['description'] ?? '',
        $t['due_date'],
        $t['status'],
        $t['image'] ?? ''
    ]);
}

fclose($output);
exit();
?>