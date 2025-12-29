<?php
/**
 * Authentication module.
 * Functions for login checks.
 */

/**
 * Check if user is logged in.
 * Redirects to login if not.
 *
 * @return void
 */
function require_auth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

/**
 * Check if current user is admin.
 *
 * @return bool True if admin
 */
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
?>