<?php
/**
 * API Endpoint.
 * Returns tasks in JSON for AJAX.
 */

require 'lib/common.php';
require_auth();

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$status_filter = $_GET['status'] ?? '';
$subject_filter = $_GET['subject_id'] ?? '';

// load
$all_tasks = get_tasks();
$all_subjects = get_subjects();

$subject_map = [];
foreach ($all_subjects as $s) {
    $subject_map[$s['id']] = $s['name'];
}

// process
$filtered_tasks = get_filtered_tasks($all_tasks, $user_id, $status_filter, $subject_filter);

foreach ($filtered_tasks as &$task) {
    $task = enrich_task_data($task, $subject_map);
}

echo json_encode(array_values($filtered_tasks));
exit();
?>