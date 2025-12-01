<?php
// lib/json_driver.php

// Ensure data folder exists
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}

// Ensure Uploads folder exists
$upload_dir = __DIR__ . '/../assets/uploads';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generic Load
function load_json($filepath) {
    if (!file_exists($filepath)) {
        file_put_contents($filepath, json_encode([]));
        return [];
    }
    return json_decode(file_get_contents($filepath), true) ?? [];
}

// Generic Save (With Lock)
function save_json($filepath, $data) {
    file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

// Helper: Generate next ID
function get_next_id($array) {
    if (empty($array)) return 1;
    $ids = array_column($array, 'id');
    return max($ids) + 1;
}

// --- SPECIFIC HELPERS ---

function get_users() { return load_json(FILE_USERS); }
function save_users($data) { save_json(FILE_USERS, $data); }

function get_tasks() { return load_json(FILE_TASKS); }
function save_tasks($data) { save_json(FILE_TASKS, $data); }

// Subject Helpers
function get_subjects() { return load_json(FILE_SUBJECTS); }
function save_subjects($data) { save_json(FILE_SUBJECTS, $data); }
?>