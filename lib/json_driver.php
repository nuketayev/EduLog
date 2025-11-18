<?php
// lib/json_driver.php

// Ensure data folder exists
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}

// Generic Load
function load_json($filepath) {
    if (!file_exists($filepath)) {
        file_put_contents($filepath, json_encode([]));
        return [];
    }
    return json_decode(file_get_contents($filepath), true) ?? [];
}

// Generic Save
function save_json($filepath, $data) {
    file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
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
?>