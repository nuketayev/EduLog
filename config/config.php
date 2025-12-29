<?php
/**
 * Configuration file.
 * Constants and settings.
 */

define('APP_NAME', 'EduLog');

// folders
define('BASE_DIR', __DIR__ . '/../');
define('DATA_DIR', BASE_DIR . 'data/');

// files
define('FILE_USERS', DATA_DIR . 'users.json');
define('FILE_TASKS', DATA_DIR . 'tasks.json');
define('FILE_SUBJECTS', DATA_DIR . 'subjects.json');

// settings
ini_set('display_errors', 0);
error_reporting(E_ALL);
?>