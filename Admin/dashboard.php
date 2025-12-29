<?php
require_once "./includes/header.php";
if (!isLoggedIn()) {
    header('Location: ./admin_login.php');
    exit();
}
?>

<section class="dashboard-section">
    <div class="container">
        <h1 class="section-title">Admin Dashboard</h1>

        <div class="dashboard-grid">
            <!-- Routes Card -->
            <div class="dashboard-card">
                <div class="card-icon"><i class="fas fa-route"></i></div>
                <h3>Routes</h3>
                <p>Manage all available routes</p>
                <a href="./manage_routes.php" class="card-btn">Manage Routes</a>
            </div>

            <!-- Vehicles Card -->
            <div class="dashboard-card">
                <div class="card-icon"><i class="fas fa-bus"></i></div>
                <h3>Buses</h3>
                <p>Manage all buses and vehicles</p>
                <a href="./manage_bus.php" class="card-btn">Manage Buses</a>
            </div>

            <!-- Schedules Card -->
            <div class="dashboard-card">
                <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
                <h3>Schedules</h3>
                <p>Manage bus schedules and dates</p>
                <a href="./manage_schedules.php" class="card-btn">Manage Schedules</a>
            </div>

            <!-- Users Card -->
            <div class="dashboard-card">
                <div class="card-icon"><i class="fas fa-users"></i></div>
                <h3>Users</h3>
                <p>View and manage users</p>
                <a href="manage_users.php" class="card-btn">Manage Users</a>
            </div>

            <!-- Bookings Card -->
            <div class="dashboard-card">
                <div class="card-icon"><i class="fas fa-ticket-alt"></i></div>
                <h3>Bookings</h3>
                <p>View and manage bookings</p>
                <a href="manage_bookings.php" class="card-btn">Manage Bookings</a>
            </div>

            <!-- LogOut Card -->
             <div class="dashboard-card">
                <div class="card-icon"><i class="fas fa-credit-card"></i></div>
                <h3>Payment</h3>
                <p>View and manage payments</p>
                <a href="manage_payments.php" class="card-btn">Manage Payments</a>
            </div>
        </div>
    </div>
</section>