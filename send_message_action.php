<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) exit;
if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;
if (empty($_POST['message'])) exit;

$sender_id   = $_SESSION['user_id'];
$sender_name = $_SESSION['name'];
$message     = trim($_POST['message']);

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO messages (sender_id, sender_name, message, is_read)
     VALUES (?, ?, ?, 0)"
);

mysqli_stmt_bind_param($stmt, "iss", $sender_id, $sender_name, $message);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo "OK";
