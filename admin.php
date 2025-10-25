<?php
    require_once"./includes/db_header.php";

    if(isLoggedIn()){
        header('Location: ./dashboard.php');
        exit();
    }

    header('Location:./admin_login.php');
    exit();