<?php
/**
 * Security functions.
 * Helper functions for safety.
 */

/**
 * Make string safe for HTML.
 * Prevents XSS attacks.
 *
 * @param string|null $str Text to clean
 * @return string Safe text
 */
function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Create a new random token.
 * Used for CSRF protection.
 *
 * @return string The token
 */
function generate_csrf() {
    if (empty($_SESSION['csrf_token'])) {
        try {
            // try safe random
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            // fallback if fails
            $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
        }
    }
    return $_SESSION['csrf_token'];
}

/**
 * Check if form token is correct.
 * Stops the script if token is bad.
 *
 * @return void
 */
function verify_csrf() {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security Error: Bad token. Please refresh.");
    }
}

/**
 * Check if link token is correct.
 *
 * @param string $token Token from URL
 * @return void
 */
function verify_csrf_token($token) {
    if (!$token || $token !== $_SESSION['csrf_token']) {
        die("Security Error: Bad link token.");
    }
}
?>