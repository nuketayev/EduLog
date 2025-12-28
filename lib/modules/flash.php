<?php
// handling flash messages (success/error alerts)

// save message to session to show it on next page load
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $message];
}

// get the message and clear it from session so it doesnt show again
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>