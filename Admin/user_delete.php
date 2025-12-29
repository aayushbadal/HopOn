<?php
require_once "./includes/db.php";
session_start();

if (!isLoggedIn()) {
    header("Location: admin_login.php");
    exit();
}

$user_id = intval($_GET['id']);

$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

header("Location: manage_users.php");
exit();
