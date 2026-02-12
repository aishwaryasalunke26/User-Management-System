<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$uid  = $_SESSION['user_id'];
$name = $_SESSION['name'];
$role = $_SESSION['role'];

/* MESSAGE SEND – LOGIC SAME */
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['message'])){
    $message = mysqli_real_escape_string($conn,$_POST['message']);
    $admin = mysqli_fetch_assoc(
        mysqli_query($conn,"SELECT id FROM users WHERE role='admin' LIMIT 1")
    );
    mysqli_query(
        $conn,
        "INSERT INTO user_replies (user_id, admin_id, message)
         VALUES ($uid,".$admin['id'].",'$message')"
    );
}

$messages = mysqli_query(
    $conn,
    "SELECT * FROM user_replies WHERE user_id=$uid ORDER BY created_at ASC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Support Chat</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root{
--primary:#667eea;
--secondary:#764ba2;
--glass:rgba(255,255,255,.9);
--text:#222;
--muted:#777;
}
body.dark{
--glass:rgba(28,28,45,.95);
--text:#f1f1f1;
--muted:#aaa;
}

*{box-sizing:border-box}

body{
margin:0;
font-family:"Segoe UI",sans-serif;
background:linear-gradient(-45deg,#667eea,#f687b3,#63cdda,#fcd34d);
background-size:400% 400%;
animation:bg 15s ease infinite;
color:var(--text);
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
top:0;width:100%;
padding:18px 28px;
background:rgba(0,0,0,.3);
backdrop-filter:blur(14px);
color:#fff;
display:flex;
justify-content:space-between;
align-items:center;
z-index:10;
}
.navbar a{
color:#fff;
text-decoration:none;
margin-left:20px;
font-size:18px;
}

/* CONTAINER */
.container{
max-width:950px;
margin:auto;
padding:25px;
}

/* CARD */
.card{
background:var(--glass);
border-radius:28px;
padding:25px;
box-shadow:0 30px 60px rgba(0,0,0,.3);
display:flex;
flex-direction:column;
height:calc(100vh - 160px);
}

/* HEADER */
.chat-header{
text-align:center;
margin-bottom:10px;
}
.avatar{
width:90px;
height:90px;
border-radius:50%;
background:linear-gradient(135deg,var(--primary),var(--secondary));
color:#fff;
display:flex;
align-items:center;
justify-content:center;
font-size:38px;
margin:0 auto 8px;
font-weight:700;
}
.status{
font-size:13px;
color:var(--muted);
}

/* MESSAGE BOX */
.message-box{
flex:1;
overflow-y:auto;
padding:15px;
border-radius:20px;
background:rgba(0,0,0,.05);
display:flex;
flex-direction:column;
gap:12px;
scroll-behavior:smooth;
}

/* MESSAGE */
.message{
padding:12px 18px;
border-radius:18px;
max-width:78%;
animation:fadeUp .35s ease;
position:relative;
}
@keyframes fadeUp{
from{opacity:0;transform:translateY(12px)}
to{opacity:1;transform:none}
}

.admin-msg{
background:linear-gradient(135deg,#667eea,#764ba2);
color:#fff;
align-self:flex-start;
}
.user-msg{
background:linear-gradient(135deg,#63cdda,#38bdf8);
color:#fff;
align-self:flex-end;
margin-left:auto;
}

/* LABEL */
.label{
font-size:11px;
opacity:.85;
margin-bottom:4px;
}

/* TIME BADGE */
.time{
font-size:10px;
opacity:.7;
margin-top:4px;
text-align:right;
}

/* INPUT BAR */
.input-bar{
margin-top:12px;
background:rgba(0,0,0,.05);
border-radius:22px;
padding:12px;
display:flex;
gap:10px;
}
textarea{
flex:1;
height:55px;
padding:14px;
border-radius:16px;
border:1px solid #ccc;
resize:none;
outline:none;
}
textarea:focus{
border-color:var(--primary);
box-shadow:0 0 0 4px rgba(102,126,234,.25);
}
.send-btn{
border:none;
border-radius:50%;
width:55px;
height:55px;
background:linear-gradient(135deg,var(--primary),var(--secondary));
color:#fff;
font-size:18px;
cursor:pointer;
transition:.3s;
}
.send-btn:hover{transform:scale(1.08)}

/* FOOT */
.footer{
text-align:center;
margin-top:8px;
font-size:13px;
color:var(--muted);
}
</style>
</head>

<body>

<div class="navbar">
<strong><i class="fa fa-headset"></i> Support</strong>
<div>
<a href="dashboard.php"><i class="fa fa-home"></i></a>
<a href="#" onclick="toggleDark()">🌙</a>
<a href="logout.php"><i class="fa fa-sign-out-alt"></i></a>
</div>
</div>

<div class="container">
<div class="card">

<div class="chat-header">
<div class="avatar"><?= strtoupper(substr($name,0,1)) ?></div>
<h3><?= htmlspecialchars($name) ?></h3>
<div class="status">Admin usually replies within 24 hours</div>
</div>

<div class="message-box" id="chatBox">
<?php
$count=0;
while($row=mysqli_fetch_assoc($messages)){
$count++;
if($row['admin_id']>0){ ?>
<div class="message admin-msg">
<div class="label">Admin</div>
<?= htmlspecialchars($row['message']) ?>
<div class="time">✓ delivered</div>
</div>
<?php }else{ ?>
<div class="message user-msg">
<div class="label">You</div>
<?= htmlspecialchars($row['message']) ?>
<div class="time">✓ sent</div>
</div>
<?php }} ?>
</div>

<form method="POST" class="input-bar">
<textarea name="message" placeholder="Type your message..." required></textarea>
<button class="send-btn" type="submit">
<i class="fa fa-paper-plane"></i>
</button>
</form>

<div class="footer">
Total messages: <?= $count ?>
</div>

</div>
</div>

<script>
function toggleDark(){
document.body.classList.toggle("dark");
}

/* AUTO SCROLL */
const chat=document.getElementById("chatBox");
chat.scrollTop=chat.scrollHeight;
</script>

</body>
</html>
