<?php
require_once"./config/db_connect.php";
if(session_status() == PHP_SESSION_NONE){
    session_name('auth');
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
  <title>HopOn - Operator Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./assets/css/style.css">

  <link rel="icon" type="image/jpg" href="../assets/images/Logo.jpg">
</head>
<body>


<header>
  <div class="sidebar-container">
    <div class="nav-bar">
      <a href="../index.php" class="logo">
        <img src="./assets/images/Logo.jpg" alt="">
          Hop <span> On</span>
      </a>
      <div class="navbar-link">
        <?php if(isset($_SESSION['user_id'])): ?>
          <span> <i class="fa-etch fa-solid fa-user"></i> <?= getUsername() ?> </span>
        <?php endif; ?>
        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="manage_routes.php"><i class="fas fa-route"></i> Routes</a>
        <a href="manage_bus.php"><i class="fas fa-bus"></i> Buses</a>
        <a href="manage_schedules.php"><i class="fas fa-calendar-alt"></i> Schedules</a>
        <a href="manage_bookings.php"><i class="fas fa-ticket-alt"></i> Bookings</a>
        <a href="manage_users.php"><i class="fas fa-users"></i> Customers</a>
        <a href="manage_payments.php"><i class="fas fa-credit-card"></i> Payments</a>
        <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>
  </div>

</header>
