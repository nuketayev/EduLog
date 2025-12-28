<?php
// ajax handler to complete tasks
require 'lib/common.php';
require_auth();

$id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if ($id) {
    $tasks = get_tasks();
    $found_index = -1;

    foreach ($tasks as $key => $t) {
        if ($t['id'] == $id && $t['user_id'] == $user_id) {
            $found_index = $key;
            break;
        }
    }

    if ($found_index !== -1) {
        $tasks[$found_index]['status'] = 'completed';
        save_tasks($tasks);
        
        // if ajax request, return json stats
        if (isset($_GET['ajax'])) {
            $my_tasks = array_filter($tasks, fn($t) => $t['user_id'] == $user_id);
            
            $total = count($my_tasks);
            $pending = count(array_filter($my_tasks, fn($t) => $t['status'] === 'pending'));
            $completed = $total - $pending;

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'stats' => [
                    'total' => $total,
                    'pending' => $pending,
                    'completed' => $completed
                ]
            ]);
            exit;
        }
        
        set_flash('success', 'Task done.');
    }
}

header("Location: index.php");
exit();
?>