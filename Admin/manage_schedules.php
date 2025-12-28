<?php
  require_once"./includes/db_header.php";
  if(!isLoggedIn()){
        header('Location: ./admin_login.php');
        exit();
  }


?>