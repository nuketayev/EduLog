<?php
/**
 * JSON Driver.
 * Functions to read and write JSON files.
 * Replaces database.
 */

// make sure folders exist
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}
$upload_dir = __DIR__ . '/../assets/uploads';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

/**
 * Load data from JSON file.
 *
 * @param string $filepath Path to file
 * @return array Data from file
 */
function load_json($filepath) {
    if (!file_exists($filepath)) {
        file_put_contents($filepath, json_encode([]));
        return [];
    }
    return json_decode(file_get_contents($filepath), true) ?? [];
}

/**
 * Save data to JSON file.
 *
 * @param string $filepath Path to file
 * @param array $data Data to save
 * @return void
 */
function save_json($filepath, $data) {
    file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

/**
 * Get next ID for new item.
 *
 * @param array $array List of items
 * @return int New ID
 */
function get_next_id($array) {
    if (empty($array)) return 1;
    $ids = array_column($array, 'id');
    return max($ids) + 1;
}

/**
 * Get all users.
 * @return array Users list
 */
function get_users() { return load_json(FILE_USERS); }

/**
 * Save users list.
 * @param array $data Users list
 * @return void
 */
function save_users($data) { save_json(FILE_USERS, $data); }

/**
 * Get all tasks.
 * @return array Tasks list
 */
function get_tasks() { return load_json(FILE_TASKS); }

/**
 * Save tasks list.
 * @param array $data Tasks list
 * @return void
 */
function save_tasks($data) { save_json(FILE_TASKS, $data); }

/**
 * Get all subjects.
 * @return array Subjects list
 */
function get_subjects() { return load_json(FILE_SUBJECTS); }

/**
 * Save subjects list.
 * @param array $data Subjects list
 * @return void
 */
function save_subjects($data) { save_json(FILE_SUBJECTS, $data); }
?>