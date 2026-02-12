<?php
include "db.php";
include "auth.php";

if ($_SESSION['role'] != 'admin') {
    die("Access Denied");
}

/* =======================
   CSRF TOKEN
======================= */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* =======================
   FETCH USER
======================= */
$id = $_GET['id'];

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    die("User not found");
}

/* =======================
   UPDATE LOGIC (MOVED UP)
======================= */
if (isset($_POST['update'])) {

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $name  = $_POST['name'];
    $email = $_POST['email'];
    $role  = $_POST['role'];

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $role, $id);
    mysqli_stmt_execute($stmt);

    $_SESSION['success'] = "User updated successfully ✅";
    header("Location: edit.php?id=$id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit User</title>

<style>
:root{
    --primary:#667eea;
    --bg:#f4f6fb;
    --card:#fff;
    --text:#333;
}
body.dark{
    --bg:#121212;
    --card:#1e1e1e;
    --text:#f1f1f1;
}

*{box-sizing:border-box}
body{
    margin:0;
    font-family:"Segoe UI",Arial;
    background:linear-gradient(120deg,#667eea,#764ba2);
    min-height:100vh;
    color:var(--text);
    overflow-x:hidden;
}

/* ===== Animated Background ===== */
.bubble{
    position:fixed;
    bottom:-150px;
    background:rgba(255,255,255,.15);
    border-radius:50%;
    animation:float 18s infinite ease-in;
}
@keyframes float{
    to{
        transform:translateY(-120vh);
        opacity:0;
    }
}
.bubble:nth-child(1){width:120px;height:120px;left:10%;animation-duration:22s}
.bubble:nth-child(2){width:80px;height:80px;left:30%;animation-duration:18s}
.bubble:nth-child(3){width:150px;height:150px;left:60%;animation-duration:25s}
.bubble:nth-child(4){width:60px;height:60px;left:80%;animation-duration:16s}

/* Navbar */
.navbar{
    background:rgba(0,0,0,.25);
    backdrop-filter:blur(8px);
    color:#fff;
    padding:15px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.navbar a{
    color:#fff;
    text-decoration:none;
    margin-left:15px;
    font-weight:600;
}

/* Container */
.container{
    padding:40px 20px;
    display:flex;
    justify-content:center;
}

/* Card */
.card{
    background:var(--card);
    width:100%;
    max-width:420px;
    padding:28px;
    border-radius:14px;
    box-shadow:0 25px 50px rgba(0,0,0,.25);
    animation:fadeUp .8s ease;
}
@keyframes fadeUp{
    from{opacity:0;transform:translateY(25px)}
    to{opacity:1;transform:translateY(0)}
}

h2{text-align:center;margin-bottom:20px}

/* Form */
label{font-weight:600}
input,select{
    width:100%;
    padding:12px;
    margin-bottom:16px;
    border-radius:8px;
    border:1px solid #ccc;
}
button{
    width:100%;
    padding:13px;
    background:linear-gradient(135deg,#667eea,#764ba2);
    color:#fff;
    border:none;
    border-radius:8px;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
}

/* Success */
.msg{
    background:#d4edda;
    color:#155724;
    padding:10px;
    border-radius:6px;
    text-align:center;
    margin-bottom:15px;
}

/* Links */
.links{text-align:center;margin-top:15px}
.links a{color:var(--primary);text-decoration:none;font-weight:600}
</style>
</head>

<body>

<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>

<div class="navbar">
    <strong>Admin Panel</strong>
    <div>
        <a href="index.php">Users</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="#" onclick="toggleDark()">🌙</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
<div class="card">

<h2>Edit User</h2>

<?php
if (isset($_SESSION['success'])) {
    echo "<div class='msg'>{$_SESSION['success']}</div>";
    unset($_SESSION['success']);
}
?>

<form method="POST">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

<label>Name</label>
<input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>

<label>Email</label>
<input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>

<label>Role</label>
<select name="role">
    <option value="user" <?= $user['role']=='user'?'selected':''; ?>>User</option>
    <option value="admin" <?= $user['role']=='admin'?'selected':''; ?>>Admin</option>
</select>

<button name="update">Update User</button>
</form>

<div class="links">
    <a href="index.php">← Back to Users</a>
</div>

</div>
</div>

<script>
function toggleDark(){
    document.body.classList.toggle("dark");
}
</script>

</body>
</html>
