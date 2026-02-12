<?php
session_start();
include "db.php";
if(!isset($_SESSION['user_id']) || $_SESSION['role']!='admin'){ die("Access Denied"); }

$users = mysqli_query($conn,"SELECT u.id,
       SUM(CASE WHEN r.admin_id=0 AND r.is_read=0 THEN 1 ELSE 0 END) AS unread_count
FROM users u 
LEFT JOIN user_replies r ON u.id = r.user_id 
WHERE u.role='user'
GROUP BY u.id");

$result = [];
while($row = mysqli_fetch_assoc($users)){
    $result[$row['id']] = intval($row['unread_count']);
}

header('Content-Type: application/json');
echo json_encode($result);
