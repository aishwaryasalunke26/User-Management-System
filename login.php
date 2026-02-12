<?php
session_start();
include "db.php";

// ----------------- LOGIN LOGIC -----------------
$login_error = "";
$active_form = "login"; // default form

if (isset($_POST['login'])) {
    $active_form = "login"; // keep login form active
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit;
        } else {
            $login_error = "❌ Wrong password!";
        }
    } else {
        $login_error = "❌ User not found!";
    }
}

// ----------------- REGISTRATION LOGIC -----------------
$register_msg = "";
if (isset($_POST['register'])) {
    $active_form = "register"; // keep registration form active
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $register_msg = "⚠ Email already exists!";
    } else {
        mysqli_query($conn, "INSERT INTO users (name,email,password,role) VALUES ('$name','$email','$password','$role')");
        $register_msg = "✅ Registration successful! You can now login.";
        $active_form = "login"; // switch to login after successful registration
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login & Registration</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
    margin:0;
    font-family: Arial,sans-serif;
    background: linear-gradient(-45deg,#667eea,#f687b3,#63cdda,#fcd34d);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    min-height: 100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}
@keyframes gradientBG{0%{background-position:0% 50%;}50%{background-position:100% 50%;}100%{background-position:0% 50%;}}
.card { background: #fff; padding: 40px 35px; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); width: 100%; max-width: 400px; text-align: center; position: relative;}
h2 { margin-bottom:25px;color:#333; }
input, select { width:100%; padding:12px; margin-bottom:15px; border-radius:6px; border:1px solid #ccc; font-size:15px; }
button { width:100%; padding:12px; background:#667eea; color:#fff; border:none; border-radius:6px; font-size:16px; cursor:pointer; font-weight:bold; margin-bottom:10px; }
button:hover { background:#5566d6; }
.toggle { position:absolute; right:12px; top:12px; cursor:pointer; font-size:14px; color:#667eea; }
.toggle-btns { display:flex; justify-content:space-between; margin-bottom:20px; }
.toggle-btns button { width:48%; background:#f0f0f0; color:#333; font-weight:bold; border-radius:8px; border:1px solid #ccc; }
.toggle-btns button.active { background:#667eea; color:#fff; border-color:#667eea; }
form { display:none; }
form.active { display:block; }
.password-box { position:relative; }
.msg { padding:12px; margin-bottom:15px; border-radius:6px; font-weight:bold; text-align:center; }
.success { background:#d4edda; color:#155724; }
.error { background:#f8d7da; color:#721c24; }
.popup { position: fixed; top: 20px; left: 50%; transform: translateX(-50%); padding: 15px 25px; border-radius: 8px; font-weight: bold; color: #fff; z-index: 999; box-shadow:0 5px 15px rgba(0,0,0,0.3); opacity:0; animation: slideDownFade 0.5s forwards; }
.popup.success { background:#28a745; }
.popup.error { background:#dc3545; }
@keyframes slideDownFade {0% {opacity:0; transform:translate(-50%,-50px);}100% {opacity:1; transform:translate(-50%,0);}}
</style>
</head>
<body>

<div class="card">
    <div class="toggle-btns">
        <button id="loginToggle" class="<?= $active_form=='login'?'active':'' ?>" onclick="showForm('login')">Login</button>
        <button id="registerToggle" class="<?= $active_form=='register'?'active':'' ?>" onclick="showForm('register')">Register</button>
    </div>

    <!-- LOGIN FORM -->
    <form method="POST" id="loginForm" class="<?= $active_form=='login'?'active':'' ?>">
        <h2>Login</h2>
        <input type="email" name="email" placeholder="Email" required>
        <div class="password-box">
            <input type="password" name="password" id="loginPassword" placeholder="Password" required>
            <span class="toggle" onclick="togglePassword('loginPassword')">Show</span>
        </div>
        <button name="login">Login</button>
        <?php if(!empty($login_error)) echo "<div class='msg error'>{$login_error}</div>"; ?>
    </form>

    <!-- REGISTER FORM -->
    <form method="POST" id="registerForm" class="<?= $active_form=='register'?'active':'' ?>">
        <h2>Register</h2>
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <div class="password-box">
            <input type="password" name="password" id="registerPassword" placeholder="Password" required>
            <span class="toggle" onclick="togglePassword('registerPassword')">Show</span>
        </div>
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        <button name="register">Register</button>
        <?php if(!empty($register_msg)) {
            $cls = strpos($register_msg,'✅')!==false?'success':'error';
            echo "<div class='msg {$cls}'>{$register_msg}</div>";
        } ?>
    </form>
</div>

<?php
// Popup messages
if(!empty($login_error)) echo "<div class='popup error'>{$login_error}</div>";
if(!empty($register_msg)) {
    $cls = strpos($register_msg,'✅')!==false?'success':'error';
    echo "<div class='popup {$cls}'>{$register_msg}</div>";
}
?>

<script>
function togglePassword(id){
    const pwd=document.getElementById(id);
    pwd.type=pwd.type==='password'?'text':'password';
}

function showForm(formType){
    if(formType==='login'){
        document.getElementById('loginForm').classList.add('active');
        document.getElementById('registerForm').classList.remove('active');
        document.getElementById('loginToggle').classList.add('active');
        document.getElementById('registerToggle').classList.remove('active');
    } else {
        document.getElementById('registerForm').classList.add('active');
        document.getElementById('loginForm').classList.remove('active');
        document.getElementById('registerToggle').classList.add('active');
        document.getElementById('loginToggle').classList.remove('active');
    }
}

// Auto-hide popup after 3 seconds
window.addEventListener('DOMContentLoaded', ()=>{
    const popups = document.querySelectorAll('.popup');
    popups.forEach(p=>{
        setTimeout(()=>{
            p.style.transition='opacity 0.5s, transform 0.5s';
            p.style.opacity='0';
            p.style.transform='translate(-50%,-50px)';
            setTimeout(()=>p.remove(),500);
        },3000);
    });
});
</script>

</body>
</html>
