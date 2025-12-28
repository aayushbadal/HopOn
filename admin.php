<?php
    require_once"./Admin/includes/header.php";

    if(isLoggedIn()){
        header('Location: ./Admin/dashboard.php');
        exit();
    }

    header('Location:./Admin/admin_login.php');
    exit();