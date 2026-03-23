<?php
session_start();

$timeout_duration = 180; // 3 minutes

if (isset($_SESSION['last_activity']) && 
    (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../index.php"); // login page
    exit();
}

$_SESSION['last_activity'] = time();
?>
