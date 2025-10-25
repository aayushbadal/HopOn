<?php 
require_once "./includes/header.php";

session_destroy();
header('Location: login.php');

exit();
?>