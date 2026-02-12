<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    die("Access Denied");
}

$role = $_SESSION['role'];
$uid = $_SESSION['user_id'];

/* Select user if admin */
if($role == 'admin'){
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    if($user_id == 0) die("No user selected");
}

/* POST MESSAGE */
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['message'])){
    $message = mysqli_real_escape_string($conn,$_POST['message']);
    if($role=='user'){
        mysqli_query($conn,"INSERT INTO user_replies (user_id, admin_id, message) VALUES ($uid,0,'$message')");
    } else {
        mysqli_query($conn,"INSERT INTO user_replies (user_id, admin_id, message) VALUES ($user_id,$uid,'$message')");
    }
}

/* DELETE MESSAGE */
if(isset($_GET['delete_msg'])){
    $msg_id = intval($_GET['delete_msg']);
    if($role=='user'){
        mysqli_query($conn,"DELETE FROM user_replies WHERE id=$msg_id AND user_id=$uid AND admin_id=0");
        header("Location: ".$_SERVER['PHP_SELF']); exit;
    } else {
        mysqli_query($conn,"DELETE FROM user_replies WHERE id=$msg_id AND admin_id=$uid");
        header("Location: ".$_SERVER['PHP_SELF']."?user_id=$user_id"); exit;
    }
}

/* GET CONVERSATION */
if($role=='user'){
    $conversation = mysqli_query($conn,"SELECT * FROM user_replies WHERE user_id=$uid ORDER BY created_at ASC");
} else {
    $conversation = mysqli_query($conn,"SELECT * FROM user_replies WHERE user_id=$user_id ORDER BY created_at ASC");
    $user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT name FROM users WHERE id=$user_id"));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Chat</title>
<style>
body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#ece5dd;
    display:flex;
    justify-content:center;
    padding-top:80px;
}
.navbar{
    position:fixed;
    top:0;
    width:100%;
    background:#075e54;
    color:#fff;
    padding:15px 30px;
    display:flex;
    justify-content:space-between;
    z-index:10;
}
.navbar a{color:#fff;text-decoration:none;margin-left:15px;}
.chat-container{
    width:100%;
    max-width:600px;
    background:#fff;
    border-radius:10px;
    box-shadow:0 4px 10px rgba(0,0,0,0.2);
    display:flex;
    flex-direction:column;
}
.messages{
    flex:1;
    padding:15px;
    overflow-y:auto;
    display:flex;
    flex-direction:column;
    gap:8px;
    height:500px;
    background:#ece5dd;
}
.message{
    max-width:70%;
    padding:10px 15px;
    border-radius:10px;
    position:relative;
    word-wrap:break-word;
}
.user-msg{background:#fff; align-self:flex-start;} /* white left */
.admin-msg{background:#34b7f1; color:#fff; align-self:flex-end;} /* blue right */
.delete-btn{
    position:absolute;
    top:2px;
    right:5px;
    font-size:10px;
    background:red;
    color:#fff;
    border:none;
    border-radius:5px;
    cursor:pointer;
    padding:1px 4px;
    text-decoration:none;
}
form{display:flex;padding:10px;border-top:1px solid #ddd;background:#f0f0f0;}
form textarea{
    flex:1;
    border-radius:20px;
    border:1px solid #ccc;
    padding:10px;
    resize:none;
    height:40px;
}
form button{
    margin-left:5px;
    border:none;
    border-radius:20px;
    background:#075e54;
    color:#fff;
    padding:0 15px;
    cursor:pointer;
}
</style>
</head>
<body>

<div class="navbar">
    <div><?= $role=='admin' ? "Chat with ".$user['name'] : "Admin Chat" ?></div>
    <div><a href="dashboard.php">Home</a><a href="logout.php">Logout</a></div>
</div>

<div class="chat-container">
    <div class="messages" id="messages">
        <?php while($row=mysqli_fetch_assoc($conversation)):
            if(($role=='user' && $row['admin_id']==0) || ($role=='admin' && $row['admin_id']==$uid)){
                // sent message
                $cls = $role=='user' ? "user-msg" : "admin-msg";
                $show_delete = true;
            } else {
                // received message
                $cls = $role=='user' ? "admin-msg" : "user-msg";
                $show_delete = false;
            }
        ?>
        <div class="message <?= $cls ?>">
            <?= htmlspecialchars($row['message']) ?>
            <?php if($show_delete): ?>
            <a href="?delete_msg=<?= $row['id'] ?>" class="delete-btn">X</a>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>

    <form method="POST">
        <textarea name="message" placeholder="Type a message..." required></textarea>
        <button type="submit">Send</button>
    </form>
</div>

<script>
// auto scroll to bottom
var messagesDiv = document.getElementById("messages");
messagesDiv.scrollTop = messagesDiv.scrollHeight;
</script>

</body>
</html>
