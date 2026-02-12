<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role']!='admin'){
    die("Access Denied");
}

$admin_id = $_SESSION['user_id'];

/* SELECT USER TO CHAT WITH */
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
if($user_id==0){ die("No user selected"); }

/* POST MESSAGE */
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['message'])){
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    mysqli_query($conn,"INSERT INTO user_replies (user_id, admin_id, message) VALUES ($user_id,$admin_id,'$message')");
}

/* DELETE MESSAGE */
if(isset($_GET['delete_msg'])){
    $msg_id = intval($_GET['delete_msg']);
    mysqli_query($conn,"DELETE FROM user_replies WHERE id=$msg_id AND admin_id=$admin_id");
    header("Location: admin_chat.php?user_id=$user_id");
    exit;
}

/* GET CONVERSATION */
$conversation = mysqli_query($conn,"
SELECT r.*, IF(r.admin_id>0,'Admin',u.name) AS sender_name
FROM user_replies r
LEFT JOIN users u ON u.id=r.user_id
WHERE r.user_id=$user_id
ORDER BY r.created_at ASC
");

/* MARK USER MESSAGES AS READ */
mysqli_query($conn,"UPDATE user_replies SET is_read=1 WHERE user_id=$user_id AND admin_id=0 AND (is_read=0 OR is_read IS NULL)");

$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT name FROM users WHERE id=$user_id"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Chat with <?= htmlspecialchars($user['name']) ?></title>
<style>
body{margin:0;font-family:Poppins,Arial,sans-serif;background:linear-gradient(-45deg,#667eea,#f687b3,#63cdda,#fcd34d);padding-top:80px;display:flex;flex-direction:column;align-items:center;}
.navbar{position:fixed;top:0;width:100%;padding:15px 30px;display:flex;justify-content:space-between;background:rgba(0,0,0,.2);color:#fff;z-index:10;}
.navbar a{color:#fff;margin-left:15px;text-decoration:none;}
.card{background:#fff;padding:25px;border-radius:20px;width:100%;max-width:700px;box-shadow:0 20px 40px rgba(0,0,0,.2);margin-bottom:20px;}
textarea{width:100%;height:50px;padding:10px;border-radius:10px;border:1px solid #ccc;resize:none;}
button{margin-top:5px;padding:8px 20px;border:none;border-radius:30px;background:#34d399;color:#fff;cursor:pointer;}
.messages-container{display:flex;flex-direction:column;padding:10px;border-radius:15px;max-height:500px;overflow-y:auto;background:#f4f4f4;}
.message{padding:10px 15px;border-radius:15px;margin-bottom:10px;max-width:70%;word-wrap:break-word;position:relative;}
.sent{background:#34d399;color:#fff;align-self:flex-end;margin-left:auto;} /* green for admin */
.received{background:#fff;color:#000;border:1px solid #ccc;align-self:flex-start;} /* white for user */
.sender-name{font-size:12px;font-weight:bold;margin-bottom:2px;}
.delete-btn{position:absolute;top:5px;right:5px;background:red;color:#fff;font-size:10px;padding:2px 5px;border:none;border-radius:5px;cursor:pointer;text-decoration:none;}
.btn-nav{margin-top:15px;display:inline-block;padding:10px 20px;background:#667eea;color:#fff;text-decoration:none;border-radius:30px;}
</style>
</head>
<body>

<div class="navbar">
<div>Admin Chat with <?= htmlspecialchars($user['name']) ?></div>
<div><a href="dashboard.php">Home</a><a href="logout.php">Logout</a></div>
</div>

<h2 style="color:#fff;text-align:center;margin-bottom:20px;">Conversation with <?= htmlspecialchars($user['name']) ?></h2>

<div class="card">
<div class="messages-container">
<?php
while($row=mysqli_fetch_assoc($conversation)){
    if(!empty($row['admin_id']) && $row['admin_id']==$admin_id){
        // Sent by admin → green
        echo "<div class='message sent'><div class='sender-name'>Admin</div>".htmlspecialchars($row['message'])."
        <a href='?delete_msg=".$row['id']."' class='delete-btn'>X</a></div>";
    } else {
        // Received from user → white
        echo "<div class='message received'><div class='sender-name'>".$row['sender_name']."</div>".htmlspecialchars($row['message'])."</div>";
    }
}
?>
</div>

<form method="POST">
<textarea name="message" placeholder="Type your message..." required></textarea>
<button type="submit">Send</button>
</form>

<a href="dashboard.php" class="btn-nav">Back to Dashboard</a>
</div>

</body>
</html>
