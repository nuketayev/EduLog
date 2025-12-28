<?php
// logout script
require 'lib/common.php';
session_destroy();
header("Location: login.php");
exit();
?>