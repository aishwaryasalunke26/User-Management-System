<?php
session_start();
include "db.php";
include "auth.php";

if ($_SESSION['role'] !== 'admin') {
    die("Access Denied");
}

$id = intval($_GET['id']);

$result = mysqli_query($conn,
    "SELECT * FROM messages WHERE id = $id"
);

$msg = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
<title>Read Message</title>
<style>
body {
    font-family: Arial;
    background: #f4f6f8;
    padding: 30px;
}
.box {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
}
</style>
</head>

<body>

<h2>📨 Message Detail</h2>

<div class="box">
    <p><strong>Sender ID:</strong> <?= $msg['sender_id'] ?></p>
    <p><strong>Role:</strong> <?= $msg['sender_role'] ?></p>
    <p><strong>Message:</strong><br><br>
        <?= nl2br(htmlspecialchars($msg['message'])) ?>
    </p>
    <small><?= $msg['created_at'] ?></small>
</div>

<br>
<a href="admin_messages.php">⬅ Back to Inbox</a>

</body>
</html>
