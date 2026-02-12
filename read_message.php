<?php
include "db.php";
include "auth.php";

if ($_SESSION['role'] != 'admin') {
    die("Access Denied");
}

$id = $_GET['id'];

mysqli_query($conn,
    "UPDATE messages SET is_read=1 WHERE id=$id"
);

$res = mysqli_query($conn,
    "SELECT * FROM messages WHERE id=$id"
);
$msg = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html>
<head>
<title>Read Message</title>
<style>
body{
    font-family:Arial;
    background:#f4f6fb;
    padding:40px;
}
.card{
    background:#fff;
    padding:20px;
    border-radius:10px;
    box-shadow:0 10px 30px rgba(0,0,0,.2);
}
</style>
</head>
<body>

<div class="card">
<h3>From: <?= htmlspecialchars($msg['sender_name']); ?></h3>
<p><?= nl2br(htmlspecialchars($msg['message'])); ?></p>
<small><?= $msg['created_at']; ?></small>
<br><br>
<a href="admin_messages.php">← Back</a>
</div>

</body>
</html>
