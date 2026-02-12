<?php
// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "crud_db");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
