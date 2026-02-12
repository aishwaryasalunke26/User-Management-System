<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Chat</title>

<style>
body{
    font-family:Arial;
    background:#ece5dd;
}
.chat{
    max-width:600px;
    margin:30px auto;
    background:#fff;
    border-radius:10px;
    display:flex;
    flex-direction:column;
    height:80vh;
}
.messages{
    flex:1;
    padding:15px;
    overflow-y:auto;
}
.me{
    background:#dcf8c6;
    padding:10px;
    margin:8px;
    border-radius:10px;
    margin-left:auto;
    max-width:70%;
}
.other{
    background:#fff;
    padding:10px;
    margin:8px;
    border-radius:10px;
    max-width:70%;
}
form{
    display:flex;
    border-top:1px solid #ccc;
}
input{
    flex:1;
    padding:12px;
    border:none;
}
button{
    padding:12px 18px;
    border:none;
    background:#128c7e;
    color:#fff;
}
</style>
</head>

<body>

<div class="chat">
    <div class="messages" id="chatBox"></div>

    <form id="chatForm">
        <input type="text" id="msg" placeholder="Type message..." required>
        <button>Send</button>
    </form>
</div>

<script>
const chatBox = document.getElementById("chatBox");
const form = document.getElementById("chatForm");
const msg  = document.getElementById("msg");

function loadChat(){
    fetch("fetch_messages.php")
        .then(r => r.text())
        .then(d => {
            chatBox.innerHTML = d;
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

loadChat();
setInterval(loadChat, 1500);

form.onsubmit = e => {
    e.preventDefault();
    fetch("send_message.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"message=" + encodeURIComponent(msg.value)
    }).then(()=>{
        msg.value="";
        loadChat();
    });
};
</script>

</body>
</html>
