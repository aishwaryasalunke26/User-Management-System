<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Access Denied");
}

$user_id = intval($_GET['user_id']);
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, email FROM users WHERE id=$user_id"));
$replies = mysqli_query($conn, "SELECT * FROM user_replies WHERE user_id=$user_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Replies</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Replies for <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>)</h2>

<table border="1" cellpadding="5">
    <tr>
        <th>Message</th>
        <th>Sent By</th>
        <th>Time</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($replies)) { ?>
    <tr>
        <td><?= htmlspecialchars($row['message']) ?></td>
        <td>Admin</td>
        <td><?= $row['created_at'] ?></td>
    </tr>
    <?php } ?>
</table>

<br>
<a href="admin_reply.php">Back</a>
</body>
</html>
