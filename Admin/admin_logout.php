<?php 
require_once "./includes/header.php";

session_destroy();
header('Location: admin_login.php');

exit();
?>