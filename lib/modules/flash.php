<?php
/**
 * Flash messages module.
 * For showing alerts to user.
 */

/**
 * Save a message for the next page.
 *
 * @param string $type Type of message (error, success)
 * @param string $message Text to show
 * @return void
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $message];
}

/**
 * Get the message and clear it.
 *
 * @return array|null The message or null
 */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>