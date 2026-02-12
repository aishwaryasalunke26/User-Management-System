<?php
session_start();
include "db.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!='admin') exit;

if($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['reply']) && !empty($_POST['message_id'])){
    $reply = trim($_POST['reply']);
    $message_id = intval($_POST['message_id']);
    $admin_name = $_SESSION['name'];

    $stmt = mysqli_prepare($conn,"INSERT INTO replies (message_id, admin_name, reply) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt,"iss",$message_id,$admin_name,$reply);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo "OK";
}
?>
