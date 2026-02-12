<?php
session_start();
include "db.php";

// ----------------------
// Check Admin Access
// ----------------------
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Access Denied");
}

// ----------------------
// Handle Admin Reply Submission
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'])) {
    $user_id = intval($_POST['user_id']);
    $admin_id = $_SESSION['user_id'];
    $message = mysqli_real_escape_string($conn, $_POST['reply_message']);

    mysqli_query($conn, "INSERT INTO user_replies (user_id, admin_id, message) VALUES ($user_id, $admin_id, '$message')");
    $success = "Reply sent!";
}

// ----------------------
// Fetch Users
// ----------------------
$users = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role='user'");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Reply</title>
    <link rel="stylesheet" href="style.css"> <!-- your existing style -->
</head>
<body>

<h2>Admin Reply</h2>

<?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>

<table border="1" cellpadding="5">
    <tr>
        <th>User Name</th>
        <th>Email</th>
        <th>Reply</th>
        <th>View Replies</th>
    </tr>

    <?php while ($user = mysqli_fetch_assoc($users)) { ?>
    <tr>
        <td><?= htmlspecialchars($user['name']) ?></td>
        <td><?= htmlspecialchars($user['email']) ?></td>
        <td>
            <!-- Reply Form -->
            <form method="POST" style="margin:0;">
                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                <textarea name="reply_message" required placeholder="Type your reply..." style="width:100%;height:50px;"></textarea>
                <br>
                <button type="submit">Send</button>
            </form>

            <!-- Display Previous Replies -->
            <?php
            $replies = mysqli_query($conn, "SELECT * FROM user_replies WHERE user_id=".$user['id']." ORDER BY created_at DESC");
            while ($row = mysqli_fetch_assoc($replies)) {
                echo "<div style='margin-top:5px; font-size:14px;'><b>Admin:</b> ".htmlspecialchars($row['message'])." <small>(".$row['created_at'].")</small></div>";
            }
            ?>
        </td>
        <td>
            <a href="view_replies.php?user_id=<?= $user['id'] ?>">View All</a>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
