<?php
include "db.php";
if($_SESSION['role']!='admin') die("Denied");

$r = mysqli_query($conn,"
SELECT sender_id,sender_name,COUNT(*) unread
FROM messages
WHERE receiver_role='admin' AND is_read=0
GROUP BY sender_id
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Inbox</title>
<style>
body{background:#ece5dd;font-family:Poppins}
.box{max-width:500px;margin:20px auto;background:#fff;border-radius:15px}
.user{padding:15px;border-bottom:1px solid #eee}
.badge{background:red;color:#fff;padding:4px 8px;border-radius:50%}
a{text-decoration:none;color:#000}
</style>
</head>

<body>
<div class="box">
<?php while($u=mysqli_fetch_assoc($r)){ ?>
<a href="admin_chat.php?uid=<?= $u['sender_id'] ?>">
<div class="user">
<?= htmlspecialchars($u['sender_name']) ?>
<span class="badge"><?= $u['unread'] ?></span>
</div>
</a>
<?php } ?>
</div>
</body>
</html>
