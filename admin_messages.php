<?php
session_start();
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access Denied");
}

mysqli_query($conn, "UPDATE messages SET is_read=1 WHERE receiver_role='admin'");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Chat</title>
<style>
body{margin:0;font-family:Poppins;background:#ece5dd}
.chat{max-width:600px;margin:20px auto;background:#fff;border-radius:15px;display:flex;flex-direction:column;height:85vh}
.header{background:#075e54;color:#fff;padding:15px}
.messages{flex:1;padding:15px;overflow-y:auto;background:#ece5dd}
.me{background:#dcf8c6;padding:10px;border-radius:10px;margin:8px 0 8px auto;max-width:75%}
.other{background:#fff;padding:10px;border-radius:10px;margin:8px 0;max-width:75%}
.replyForm{display:flex;margin-top:5px}
.replyForm input{flex:1;padding:8px;border:1px solid #ccc;border-radius:5px}
.replyForm button{padding:8px 15px;background:#075e54;color:#fff;border:none;margin-left:5px;border-radius:5px}
</style>
</head>

<body>
<div class="chat">
<div class="header">Admin Messages</div>
<div class="messages" id="box"></div>
</div>

<script>
const box = document.getElementById("box");

function load(){
    fetch("fetch_messages.php")
        .then(r => r.text())
        .then(d => {
            box.innerHTML = d;
            box.scrollTop = box.scrollHeight;

            // Attach reply form submit
            document.querySelectorAll('.replyForm').forEach(form=>{
                form.onsubmit = e=>{
                    e.preventDefault();
                    let id = form.dataset.id;
                    let reply = form.querySelector('input[name="reply"]').value;

                    fetch("send_reply.php", {
                        method:"POST",
                        headers:{"Content-Type":"application/x-www-form-urlencoded"},
                        body:"reply="+encodeURIComponent(reply)+"&message_id="+id
                    }).then(()=>{ form.querySelector('input[name="reply"]').value=''; load(); });
                }
            });
        });
}

load();
setInterval(load,1500);
// Attach submit handler to each reply form
function attachReplyForms(){
    document.querySelectorAll('.replyForm').forEach(form=>{
        form.onsubmit = e=>{
            e.preventDefault();
            let id = form.dataset.id;
            let reply = form.querySelector('input[name="reply"]').value;

            fetch("send_reply.php", {
                method:"POST",
                headers:{"Content-Type":"application/x-www-form-urlencoded"},
                body:"reply="+encodeURIComponent(reply)+"&message_id="+id
            }).then(()=>{
                form.querySelector('input[name="reply"]').value='';
                load(); // reload messages after reply
            });
        };
    });
}

// Call attachReplyForms() inside load()
function load(){
    fetch("fetch_messages.php")
        .then(r => r.text())
        .then(d => {
            box.innerHTML = d;
            box.scrollTop = box.scrollHeight;
            attachReplyForms(); // important
        });
}

load();
setInterval(load,1500);

</script>
</body>
</html>
