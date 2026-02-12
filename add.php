<?php
include "db.php";
include "auth.php";

// Admin-only access
if ($_SESSION['role'] != 'admin') {
    die("Access Denied");
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ----------- FORM PROCESSING -----------
if (isset($_POST['submit'])) {

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $name  = $_POST['name'];
    $email = $_POST['email'];
    $role  = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email=?");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $_SESSION['success'] = "⚠ Email already exists!";
        header("Location: add.php");
        exit;
    }

    // Insert user
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $password, $role);
    mysqli_stmt_execute($stmt);

    $_SESSION['success'] = "✅ User added successfully!";
    header("Location: add.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* ---------- BODY & BACKGROUND ---------- */
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #667eea, #764ba2, #f78ca0);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        /* ---------- CARD ---------- */
        @keyframes slideDownFade {
            0% { transform: translateY(-30px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        .card {
            background: #ffffffee;
            padding: 35px 30px;
            width: 100%;
            max-width: 420px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            backdrop-filter: blur(5px);
            text-align: center;
            animation: slideDownFade 0.8s ease;
        }

        h2 {
            margin-bottom: 25px;
            color: #333;
        }

        input, select {
            width: 100%;
            padding: 14px 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 8px rgba(102,126,234,0.4);
        }

        .password-box {
            position: relative;
        }

        .toggle {
            position: absolute;
            right: 12px;
            top: 14px;
            cursor: pointer;
            font-size: 13px;
            color: #667eea;
            user-select: none;
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        button:hover {
            background: linear-gradient(135deg, #5a4bdc, #6b3ba0);
            transform: translateY(-2px);
        }

        .links {
            margin-top: 20px;
        }

        .links a {
            text-decoration: none;
            color: #764ba2;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .links a:hover {
            color: #667eea;
        }

        /* ---------- NAVBAR ---------- */
        .navbar {
            position: absolute;
            top: 0;
            width: 100%;
            background: #ffffff99;
            backdrop-filter: blur(5px);
            color: #333;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar a {
            color: #667eea;
            text-decoration: none;
            margin-left: 15px;
            font-weight: bold;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        /* ---------- ALERT POPUP ---------- */
        .alert-box {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.5);
            background-color: #4CAF50; /* green */
            color: white;
            padding: 18px 25px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            z-index: 1000;
            display: none;
            font-weight: bold;
            font-size: 16px;
            min-width: 220px;
            text-align: center;
            animation: alertIn 0.5s forwards;
        }

        @keyframes alertIn {
            0% { opacity: 0; transform: translate(-50%, -50%) scale(0.5); }
            60% { opacity: 1; transform: translate(-50%, -50%) scale(1.05); }
            100% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }

        @keyframes alertOut {
            0% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
            100% { opacity: 0; transform: translate(-50%, -50%) scale(0.5); }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div>Admin Panel</div>
    <div>
        <a href="index.php">Users</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="card">
    <h2>Add New User</h2>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>

        <div class="password-box">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <span class="toggle" onclick="togglePassword()">Show</span>
        </div>

        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <button name="submit">Add User</button>
    </form>

    <div class="links">
        <a href="index.php">← Back</a>
    </div>
</div>

<!-- Alert Box -->
<div class="alert-box" id="alertBox"></div>

<script>
function togglePassword() {
    const pwd = document.getElementById("password");
    pwd.type = pwd.type === "password" ? "text" : "password";
}

// ---------- SHOW ALERT IF SUCCESS EXISTS ----------
window.onload = function() {
    <?php if (isset($_SESSION['success'])): ?>
        const alertBox = document.getElementById('alertBox');
        alertBox.innerHTML = "<?= $_SESSION['success']; ?>";
        alertBox.style.display = 'block';

        // Auto-hide after 3 seconds with fade-out
        setTimeout(function() {
            alertBox.style.animation = 'alertOut 0.5s forwards';
        }, 3000);

        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
};
</script>

</body>
</html>
