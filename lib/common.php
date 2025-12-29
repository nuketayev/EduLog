<?php
/**
 * Common Library.
 * Main loader file for the application.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/json_driver.php';

// load modules
require_once __DIR__ . '/modules/security.php';
require_once __DIR__ . '/modules/auth.php';
require_once __DIR__ . '/modules/flash.php';
require_once __DIR__ . '/modules/image.php';
require_once __DIR__ . '/modules/task_service.php';

// start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>