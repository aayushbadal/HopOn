<?php 
require_once "./includes/db_header.php";

session_destroy();
header('Location: admin_login.php');

exit();
?>