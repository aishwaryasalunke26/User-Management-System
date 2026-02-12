<?php
session_start();
include "db.php";

// Access control
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    die("Access Denied");
}

// DELETE MESSAGE
if(isset($_GET['delete_msg'])){
    $msg_id = intval($_GET['delete_msg']);
    mysqli_query($conn, "DELETE FROM user_replies WHERE id=$msg_id");
    header("Location: view_messages.php");
    exit;
}

// SEND REPLY
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['reply_message'])){
    $user_id = intval($_POST['user_id']);
    $admin_id = $_SESSION['user_id'];
    $message = mysqli_real_escape_string($conn, $_POST['reply_message']);
    mysqli_query($conn, "INSERT INTO user_replies (user_id, admin_id, message, created_at)
        VALUES ($user_id, $admin_id, '$message', NOW())");
    header("Location: view_messages.php"); // reload page after sending
    exit;
}

// FETCH USERS
$users = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role='user' ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Chat</title>
<style>
body {
    margin:0;
    font-family:Poppins,Arial,sans-serif;
    background:linear-gradient(-45deg,#667eea,#f687b3,#63cdda,#fcd34d);
    background-size:400% 400%;
    animation:bg 15s ease infinite;
    padding-top:90px;
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
    width:100%;
    padding:16px 35px;
    display:flex;
    justify-content:space-between;
    background:rgba(0,0,0,.3);
    color:#fff;
    z-index:100;
}
.navbar a{color:#fff;margin-left:20px;text-decoration:none}

/* CARD */
.card{
    background:#fff;
    padding:22px;
    border-radius:18px;
    max-width:900px;
    margin:25px auto;
    box-shadow:0 20px 40px rgba(0,0,0,.25);
}

/* CHAT BOX */
.messages-container{
    background:#f4f6ff;
    padding:15px;
    border-radius:15px;
    max-height:400px;
    overflow-y:auto;
    display:flex;
    flex-direction:column;
    gap:12px;
}

/* MESSAGE */
.message{
    max-width:70%;
    padding:10px 14px;
    border-radius:16px;
    font-size:14px;
    line-height:1.4;
    position:relative;
}

/* NAME LABEL */
.sender{
    font-size:11px;
    font-weight:600;
    opacity:.8;
    margin-bottom:4px;
}

/* ADMIN → LEFT */
.admin-msg{
    align-self:flex-start;
    background:#e9e9ff;
    color:#333;
    border-top-left-radius:4px;
}

/* USER → RIGHT */
.user-msg{
    align-self:flex-end;
    background:#d1fae5;
    color:#064e3b;
    border-top-right-radius:4px;
}

/* DELETE */
.delete-btn{
    position:absolute;
    top:-6px;
    right:-6px;
    background:#dc3545;
    color:#fff;
    font-size:11px;
    padding:2px 6px;
    border-radius:50%;
    text-decoration:none;
}

/* FORM */
form{
    margin-top:12px;
    display:flex;
    gap:10px;
}
textarea{
    flex:1;
    height:45px;
    padding:10px;
    border-radius:20px;
    border:1px solid #ccc;
    resize:none;
}
button{
    padding:10px 22px;
    border:none;
    border-radius:25px;
    background:#667eea;
    color:#fff;
    font-weight:600;
    cursor:pointer;
}
</style>
</head>
<body>

<div class="navbar">
    <div>Admin – User Chats</div>
    <div>
        <a href="dashboard.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<?php while($user=mysqli_fetch_assoc($users)){ ?>
<div class="card">

<h3><?=htmlspecialchars($user['name'])?>
    <small>(<?=htmlspecialchars($user['email'])?>)</small>
</h3>

<div class="messages-container" id="chat-<?=$user['id']?>">

<?php
$replies = mysqli_query($conn, "
    SELECT r.*, 
    CASE WHEN r.admin_id>0 THEN 'Admin' ELSE u.name END AS sender_name
    FROM user_replies r
    JOIN users u ON u.id = r.user_id
    WHERE r.user_id=".$user['id']."
    ORDER BY r.created_at ASC
");

while($row=mysqli_fetch_assoc($replies)){
    $class = $row['admin_id']>0 ? 'admin-msg' : 'user-msg';
?>
    <div class="message <?=$class?>">
        <div class="sender"><?=htmlspecialchars($row['sender_name'])?></div>
        <?=htmlspecialchars($row['message'])?>
        <a class="delete-btn" href="?delete_msg=<?=$row['id']?>">×</a>
    </div>
<?php } ?>

</div>

<form method="POST">
    <input type="hidden" name="user_id" value="<?=$user['id']?>">
    <textarea name="reply_message" placeholder="Reply as Admin..." required></textarea>
    <button type="submit">Send</button>
</form>

</div>
<?php } ?>

<script>
// Auto-scroll each chat to bottom
document.querySelectorAll('.messages-container').forEach(container=>{
    container.scrollTop = container.scrollHeight;
});
</script>

</body>
</html>
