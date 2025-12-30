<?php
require_once"./config/db_connect.php";
if(session_status() == PHP_SESSION_NONE){
    session_name('hopon');
    session_start();
}

function isLoggedIn(){
    return isset($_SESSION['user_id']);
}

function getUsername(){
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HopOn - Book Bus Ticket Online</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="icon" type="image/jpg" href="./assets/images/Logo.jpg">

</head>
<body>
    <button id="mode-toggle">
        <i class="fas fa-moon"></i>
    </button>
    <!--Header Section -->
    <header>
        <div class="container">
            <div class="navbar">
                <a href="index.php" class="logo">
                    <img src="./assets/images/Logo.jpg" alt="">
                    Hop <span> On</span>
                </a>
                <div class="navbar-link">
                    <a href="index.php">Home</a>
                    <a href="routes.php">Routes</a>
                    <i class="fas fa-search" id="search-icon"> </i>
                    <div class="user-menu">
                        <i class="fas fa-user"></i>
                        <div class="dropdown">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <span> <i class="fa-regular fa-user"></i><?= getUsername() ?></span>
                            <a href="mybookings.php">My Bookings</a>
                            <a href="logout.php">Logout</a>
                        <?php else: ?>
                            <a href="login.php">Login</a>
                            <a href="register.php">Register</a>
                        <?php endif; ?>
                        </div>
                    </div>
                    
                </div>
                
            </div>
        </div>
    </header>
    <div id="searchbar">
    <form action="search.php" method="get" class="search-bar">
    <div class="search-group">
        <label for="start-point">Start:</label>
        <input type="text" name="start-point" id="start-point" required />
    </div>

    <div class="search-group">
        <label for="end-point">Destination:</label>
        <input type="text" name="end-point" id="end-point" required />
    </div>

    <div class="search-group">
        <label for="travel-date">Date:</label>
        <input type="date" name="travel-date" id="travel-date" required />
    </div>

    <div class="search-group">
        <button type="submit" class="search-btn">Search</button>
    </div>
    </form>

</div>

<script src="./assets/js/theme.js"></script>
<script src="./assets/js/search.js"></script>


    