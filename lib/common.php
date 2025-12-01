<?php
// lib/common.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/json_driver.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// XSS Protection
function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// Security: Login Check
function require_auth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

// Flash messages (Success/Error alerts)
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $message];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>