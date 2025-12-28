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
  <link rel="stylesheet" href="./assets/css/dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <link rel="icon" type="image/jpg" href="./assets/images/Logo.jpg">
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
        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="manage_routes.php"><i class="fas fa-route"></i> Routes</a>
        <a href="manage_bus.php"><i class="fas fa-bus"></i> Manage Buses</a>
        <a href="manage_schedules.php"><i class="fas fa-calendar-alt"></i> Schedules</a>
        <a href="#"><i class="fas fa-ticket-alt"></i> Bookings</a>
        <a href="#"><i class="fas fa-users"></i> Customers</a>
        <a href="#"><i class="fas fa-credit-card"></i> Payments</a>
        <a href="#"><i class="fas fa-cogs"></i> Settings</a>
        <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>
  </div>

</header>
