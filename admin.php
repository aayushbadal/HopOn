<?php
    require_once"./Admin/includes/db_header.php";

    if(isLoggedIn()){
        header('Location: ./Admin/dashboard.php');
        exit();
    }

    header('Location:./Admin/admin_login.php');
    exit();