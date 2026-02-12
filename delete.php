<?php
include "db.php"; // your connection file

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $query = "DELETE FROM users WHERE id = $id";
    mysqli_query($conn, $query);

    header("Location: index.php?msg=deleted");
    exit;
} else {
    echo "ID not found";
}
