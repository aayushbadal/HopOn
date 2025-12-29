<?php
require_once "./includes/header.php";

if (!isLoggedIn()) {
    header("Location: ./admin_login.php");
    exit();
}

$user_id = intval($_GET['id']);

/* User info */
$user_sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "<p style='text-align:center;color:red;'>User not found</p>";
    exit();
}

/* Booking count */
$count_sql = "SELECT COUNT(*) AS total FROM bookings WHERE user_id = ?";
$cstmt = $conn->prepare($count_sql);
$cstmt->bind_param("i", $user_id);
$cstmt->execute();
$count = $cstmt->get_result()->fetch_assoc();
?>

<section class="dashboard-section">
    <div class="container">
        <h2 class="section-title">User Details</h2>

        <div class="booking-card">
            <p><strong>Full Name:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
            <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Total Bookings:</strong> <?= $count['total'] ?></p>

            <div class="booking-actions" style="margin-top:20px;">
    <a href="user_bookings.php?id=<?= $user_id ?>" class="view-btn">
        View Bookings
    </a>

    <a href="user_delete.php?id=<?= $user_id ?>"
       class="invoice-btn"
       onclick="return confirm('Are you sure you want to delete this user?');">
        Delete User
    </a>

    <a href="manage_users.php" class="view-btn">
        Back
    </a>
</div>

        </div>
    </div>
</section>