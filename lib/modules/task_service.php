<?php
// logic for filtering and preparing tasks data

// filters tasks array based on user, status and subject
function get_filtered_tasks($all_tasks, $user_id, $status = '', $subject_id = '') {
    $filtered = [];
    foreach ($all_tasks as $t) {
        // checks if task belongs to user
        if ($t['user_id'] != $user_id) continue;
        
        // apply filters if they are set
        if ($status !== '' && $t['status'] !== $status) continue;
        if ($subject_id !== '' && $t['subject_id'] != $subject_id) continue;
        
        $filtered[] = $t;
    }
    
    // sort by due date, oldest first
    usort($filtered, function($a, $b) {
        return strtotime($a['due_date']) - strtotime($b['due_date']);
    });
    
    return $filtered;
}

// adds extra info to task for display (formatted dates, colors, images)
function enrich_task_data($task, $subject_map) {
    $today = new DateTime();
    $today->setTime(0,0,0); // reset time to midnight
    
    $due = new DateTime($task['due_date']);
    $due->setTime(0,0,0);
    
    $diff = $today->diff($due);
    
    // basic calculation
    $task['subject_name'] = $subject_map[$task['subject_id']] ?? '';
    $task['days_left'] = $diff->days;
    $task['is_past'] = $diff->invert; 
    $task['is_overdue'] = ($task['is_past'] && $task['status'] === 'pending');
    $task['due_date_formatted'] = date('d.m.Y', strtotime($task['due_date']));

    // set css classes and text labels
    if ($task['status'] === 'completed') {
        $task['status_text'] = 'Hotovo';
        $task['status_class'] = 'completed';
        $task['row_class'] = '';
        $task['days_text'] = '';
    } elseif ($task['is_overdue']) {
        $task['status_text'] = 'PO TERMÍNU';
        $task['status_class'] = 'overdue';
        $task['row_class'] = 'overdue';
        $task['days_text'] = "<span class='text-danger' style='font-weight:bold;'>({$diff->days} dny po termínu)</span>";
    } else {
        $task['status_text'] = 'Nevyřízeno';
        $task['status_class'] = 'pending';
        $task['row_class'] = '';
        
        if ($diff->days == 0) {
            $task['days_text'] = "<span style='color:#e0a800; font-weight:bold;'>(Dnes)</span>";
        } else {
            $task['days_text'] = "<span class='color-success'>(Zbývá {$diff->days} dní)</span>";
        }
    }

    // handle image paths
    $task['thumb_url'] = null;
    $task['full_url'] = null;
    
    if (!empty($task['image'])) {
        $upload_dir = __DIR__ . '/../../assets/uploads/';
        $web_dir = 'assets/uploads/';
        
        $thumb_name = 'thumb_' . $task['image'];
        
        // generate thumb if missing
        if (!file_exists($upload_dir . $thumb_name) && file_exists($upload_dir . $task['image'])) {
            make_thumb($upload_dir . $task['image'], $upload_dir . $thumb_name, 300);
        }

        if (file_exists($upload_dir . $thumb_name)) {
            $task['thumb_url'] = $web_dir . $thumb_name;
        } else {
            // fallback to original if thumb fails
            $task['thumb_url'] = $web_dir . $task['image'];
        }
        $task['full_url'] = $web_dir . $task['image'];
    }

    // escape output for xss safety
    $task['title'] = h($task['title']);
    $task['description'] = h($task['description'] ?? '');
    $task['subject_name'] = h($task['subject_name']);

    return $task;
}
?>