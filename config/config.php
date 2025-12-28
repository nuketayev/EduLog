<?php
// configuration file
// constants for paths and filenames

define('APP_NAME', 'EduLog');

// paths
define('BASE_DIR', __DIR__ . '/../');
define('DATA_DIR', BASE_DIR . 'data/');

// json files
define('FILE_USERS', DATA_DIR . 'users.json');
define('FILE_TASKS', DATA_DIR . 'tasks.json');
define('FILE_SUBJECTS', DATA_DIR . 'subjects.json');

// error reporting (hide errors in production)
ini_set('display_errors', 0);
error_reporting(E_ALL);
?>