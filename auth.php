<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Login protection
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/*
 OPTIONAL USAGE:

 // For admin-only pages
 if ($_SESSION['role'] !== 'admin') {
     echo "Access denied";
     exit;
 }
*/
?>
