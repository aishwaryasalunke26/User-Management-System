<?php
include "db.php";
if(!isset($_SESSION['user_id'])) exit;
?>
<!DOCTYPE html>
<html>
<head>
<title>Chat</title>
<style>
body{
    margin:0;
    font-family:Poppins;
    background:#ece5dd;
}
.chat{
    max-width:500px;
    margin:auto;
    background:#fff;
    height:100vh;
    display:flex;
    flex-direction:column;
}
.messages{
    flex:1;
    padding:10px;
    overflow-y:auto;
}
.me{
    background:#dcf8c6;
    margin:5px;
    padding:8px;
    border-radius:10px;
    align-self:flex-end;
}
.admin{
    background:#f1f0f0;
    margin:5px;
    padding:8px;
    border-radius:10px;
    align-self:flex-start;
}
form{
    display:flex;
}
input{
    flex:1;
    padding:10px;
}
button{
    padding:10px;
}
</style>
</head>
<body>

<div class="chat">
    <div class="messages" id="chat"></div>

    <form id="form">
        <input type="text" id="msg" required placeholder="Type message">
        <button>Send</button>
    </form>
</div>

<script>
function load(){
    fetch("fetch_messages.php")
    .then(r=>r.text())
    .then(d=>chat.innerHTML=d);
}
setInterval(load,1000);
load();

form.onsubmit=e=>{
    e.preventDefault();
    fetch("send_message.php",{
        method:"POST",
        body:new URLSearchParams({message:msg.value})
    });
    msg.value="";
}
</script>

</body>
</html>
