<?php
include "auth.php";
include "db.php";

$user_id = $_SESSION['user_id'];

/* FETCH USER DATA */
$stmt = mysqli_prepare($conn,
    "SELECT name, email, profile_image FROM users WHERE user_id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

/* UPDATE PROFILE */
if (isset($_POST['update'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $imageName = $user['profile_image'];

    /* IMAGE UPLOAD */
    if (!empty($_FILES['image']['name'])) {

        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

        if (!in_array($file['type'], $allowedTypes)) {
            $error = "Only JPG & PNG allowed";
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $error = "Image size must be under 2MB";
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $imageName = time() . "_" . $user_id . "." . $ext;
            move_uploaded_file($file['tmp_name'], "uploads/" . $imageName);
        }
    }

    if (!isset($error)) {
        $stmt = mysqli_prepare($conn,
            "UPDATE users SET name=?, email=?, profile_image=? WHERE user_id=?"
        );
        mysqli_stmt_bind_param($stmt, "sssi",
            $name, $email, $imageName, $user_id
        );
        mysqli_stmt_execute($stmt);

        $_SESSION['name'] = $name;
        $success = "Profile updated successfully";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Profile</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Edit Profile</h2>

<form method="POST" enctype="multipart/form-data">

<img src="uploads/<?php echo $user['profile_image']; ?>"
     width="120" style="border-radius:50%;margin-bottom:10px;">

<input type="text" name="name"
       value="<?php echo htmlspecialchars($user['name']); ?>" required>

<input type="email" name="email"
       value="<?php echo htmlspecialchars($user['email']); ?>" required>

<input type="file" name="image">

<button name="update">Update Profile</button>
</form>

<?php
if (isset($error)) echo "<p class='error'>$error</p>";
if (isset($success)) echo "<p class='success'>$success</p>";
?>

<div class="center">
<a href="index.php">Back to Dashboard</a>
</div>

</body>
</html>
