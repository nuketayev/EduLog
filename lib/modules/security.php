<?php
// security functions

// wrapper for htmlspecialchars to prevent XSS attacks
// makes it shorter to write h($str) instead of the long function
function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// generates a random token for csrf protection
function generate_csrf() {
    if (empty($_SESSION['csrf_token'])) {
        try {
            // try to generate secure random bytes
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            // fallback if random_bytes fails
            $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
        }
    }
    return $_SESSION['csrf_token'];
}

// checks if the form token matches the session token
function verify_csrf() {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security Error: CSRF token is invalid. Please refresh.");
    }
}

// check token from url link (get request)
function verify_csrf_token($token) {
    if (!$token || $token !== $_SESSION['csrf_token']) {
        die("Security Error: Invalid link token.");
    }
}
?>