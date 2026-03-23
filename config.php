<?php
$DB_HOST = "localhost";
$DB_NAME = "cropdb";
$DB_USER = "root";
$DB_PASS = "";

$ADMIN_USER = "prajjval";
$ADMIN_PASS = "password"; // change after setup

// Create connection
$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>
