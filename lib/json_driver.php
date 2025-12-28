<?php
// helper functions to work with json files
// basically acts like our database driver

// ensure data folder exists
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}

// ensure uploads folder exists
$upload_dir = __DIR__ . '/../assets/uploads';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// reads a json file and returns an array
// if file doesnt exist, it returns empty array
function load_json($filepath) {
    if (!file_exists($filepath)) {
        file_put_contents($filepath, json_encode([]));
        return [];
    }
    // decode json to associative array
    return json_decode(file_get_contents($filepath), true) ?? [];
}

// saves an array back to json file
// uses pretty print so we can read it easily
function save_json($filepath, $data) {
    file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

// finds the highest id and adds 1
// simple auto increment logic
function get_next_id($array) {
    if (empty($array)) return 1;
    $ids = array_column($array, 'id');
    return max($ids) + 1;
}

// shortcuts for specific files
function get_users() { return load_json(FILE_USERS); }
function save_users($data) { save_json(FILE_USERS, $data); }

function get_tasks() { return load_json(FILE_TASKS); }
function save_tasks($data) { save_json(FILE_TASKS, $data); }

function get_subjects() { return load_json(FILE_SUBJECTS); }
function save_subjects($data) { save_json(FILE_SUBJECTS, $data); }
?>