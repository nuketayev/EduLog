<?php
/**
 * Export CSV.
 * Download tasks as file.
 */

require 'lib/common.php';
require_auth();

$user_id = $_SESSION['user_id'];
$tasks = get_tasks();

$my_tasks = array_filter($tasks, function($t) use ($user_id) {
    return $t['user_id'] == $user_id;
});

// headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=ukoly.csv');

// open output stream
$output = fopen('php://output', 'w');

// --- THE FIX: Write Byte Order Mark (BOM) for Excel ---
// This tells Excel the file is UTF-8 encoded
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header row
fputcsv($output, ['ID', 'Název', 'Popis', 'Termín', 'Stav', 'Obrázek']);

// Data rows
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