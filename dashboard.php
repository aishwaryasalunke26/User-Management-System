<?php
session_start();
include "db.php";

/* -------------------- AUTH CHECK -------------------- */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid  = $_SESSION['user_id'];
$role = $_SESSION['role'];
$name = $_SESSION['name'];

/* -------------------- TOTAL USERS -------------------- */
$user_count = $admin_count = $normal_count = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
if ($res) $user_count = mysqli_fetch_assoc($res)['total'];
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='admin'");
if ($res) $admin_count = mysqli_fetch_assoc($res)['total'];
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='user'");
if ($res) $normal_count = mysqli_fetch_assoc($res)['total'];

/* -------------------- UNREAD MESSAGES -------------------- */
$unread = 0;
$check = mysqli_query($conn, "SHOW TABLES LIKE 'user_replies'");
if ($check && mysqli_num_rows($check) > 0) {
    if ($role == 'admin') {
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM user_replies WHERE admin_id=0");
    } else {
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM user_replies WHERE admin_id>0 AND user_id=?");
        mysqli_stmt_bind_param($stmt, "i", $uid);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $unread);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

/* -------------------- TOTAL MESSAGES -------------------- */
$message_count = 0;
if ($check && mysqli_num_rows($check) > 0) {
    $res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user_replies");
    if ($res) $message_count = mysqli_fetch_assoc($res)['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root{
    --primary:#667eea;
    --secondary:#764ba2;
    --glass:rgba(255,255,255,.88);
    --text:#222;
}
body.dark{
    --glass:rgba(28,28,45,.96);
    --text:#f1f1f1;
}

*{box-sizing:border-box}

body{
    margin:0;
    font-family:"Segoe UI",sans-serif;
    background:linear-gradient(-45deg,#667eea,#f687b3,#63cdda,#fcd34d);
    background-size:400% 400%;
    animation:bg 15s ease infinite;
    color:var(--text);
    min-height:100vh;
}
@keyframes bg{
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}

/* NAVBAR */
.navbar{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    padding:18px 28px;
    background:rgba(0,0,0,.28);
    backdrop-filter:blur(12px);
    color:#fff;
    display:flex;
    justify-content:space-between;
    align-items:center;
    z-index:1000;
}
.navbar a{
    color:#fff;
    text-decoration:none;
    margin-left:20px;
    font-size:18px;
    transition:.25s;
}
.navbar a:hover{opacity:.8}

/* CONTAINER */
.container{
    padding:110px 28px 28px;
    max-width:1200px;
    margin:auto;
}

/* CARD */
.card{
    background:var(--glass);
    backdrop-filter:blur(14px);
    border-radius:26px;
    padding:34px;
    box-shadow:0 30px 60px rgba(0,0,0,.25);
    animation:fadeUp .6s ease;
    text-align:center;
}
@keyframes fadeUp{
    from{opacity:0;transform:translateY(30px)}
    to{opacity:1}
}

/* AVATAR */
.avatar{
    width:90px;
    height:90px;
    border-radius:50%;
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:40px;
    font-weight:bold;
    margin:0 auto 14px;
    box-shadow:0 12px 30px rgba(0,0,0,.25);
}

/* BUTTONS */
.btn{
    display:inline-block;
    margin:18px 10px 0;
    padding:13px 30px;
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    color:#fff;
    border-radius:30px;
    text-decoration:none;
    font-weight:600;
    transition:.3s;
    position:relative;
}
.btn:hover{
    transform:translateY(-3px);
    box-shadow:0 12px 30px rgba(0,0,0,.3);
}

/* BADGE */
.badge{
    position:absolute;
    top:-6px;
    right:-6px;
    background:#dc3545;
    color:#fff;
    font-size:12px;
    padding:3px 7px;
    border-radius:50%;
    font-weight:bold;
}

/* STATS */
.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:18px;
    margin-top:35px;
}
.stat{
    background:rgba(255,255,255,.65);
    border-radius:18px;
    padding:22px;
    box-shadow:0 12px 25px rgba(0,0,0,.15);
}
.stat h3{
    margin:0;
    font-size:30px;
    color:var(--primary);
}
.stat p{
    margin:6px 0 0;
    font-size:14px;
}

/* RESPONSIVE */
@media(max-width:700px){
    .container{padding:100px 18px 18px}
}
</style>
</head>

<body>

<div class="navbar">
    <strong><i class="fa-solid fa-gauge"></i> Dashboard</strong>
    <div>
        <a href="dashboard.php"><i class="fa fa-home"></i></a>
        <a href="logout.php"><i class="fa fa-sign-out-alt"></i></a>
    </div>
</div>

<div class="container">
<div class="card">

<div class="avatar"><?= strtoupper(substr($name,0,1)) ?></div>
<h2>Welcome <?= htmlspecialchars($name) ?></h2>

<?php if($role == 'admin'){ ?>
    <p><b>Administrator Access</b></p>
    <a href="index.php" class="btn">
        <i class="fa fa-users-cog"></i> Manage Users
    </a>
    <a href="view_messages.php" class="btn">
        <i class="fa fa-comments"></i> Messages
        <?php if($unread>0){ ?><span class="badge"><?= $unread ?></span><?php } ?>
    </a>
<?php } else { ?>
    <p><b>User Dashboard</b></p>
    <a href="send_message.php" class="btn">
        <i class="fa fa-comments"></i> Chat with Admin
        <?php if($unread>0){ ?><span class="badge"><?= $unread ?></span><?php } ?>
    </a>
<?php } ?>

<div class="stats">
    <div class="stat">
        <h3><?= $user_count ?></h3>
        <p>Total Users</p>
    </div>
    <div class="stat">
        <h3><?= $admin_count ?></h3>
        <p>Admins</p>
    </div>
    <div class="stat">
        <h3><?= $normal_count ?></h3>
        <p>Users</p>
    </div>
    <div class="stat">
        <h3><?= $message_count ?></h3>
        <p>Messages</p>
    </div>
</div>

</div>
</div>

</body>
</html>
