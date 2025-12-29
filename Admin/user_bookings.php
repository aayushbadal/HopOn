<?php
require_once "./includes/header.php";

/* Admin authentication */
if (!isLoggedIn()) {
    header("Location: admin_login.php");
    exit();
}

/* Validate user id */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p class='empty-msg'>Invalid User ID</p>";
    exit();
}

$user_id = (int) $_GET['id'];

/* ============================
   FETCH USER BASIC INFO
============================ */
$user_sql = "SELECT username, email FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);

if (!$user_stmt) {
    die("SQL Error (User): " . $conn->error);
}

$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "<p class='empty-msg'>User not found</p>";
    exit();
}

/* ============================
   FETCH USER BOOKINGS
============================ */
$booking_sql = "
    SELECT
        b.id AS booking_id,
        b.booking_reference,
        b.total_amount,
        b.booking_date,
        b.payment_status,

        r.startin,
        r.destination,

        v.vehicle_number,

        rd.routing_date,

        GROUP_CONCAT(bs.seat_number ORDER BY bs.seat_number SEPARATOR ', ') AS seats
    FROM bookings b
    INNER JOIN routes r ON b.route_id = r.id
    INNER JOIN vehicle_lists v ON b.vehicle_id = v.id
    INNER JOIN route_date rd ON b.route_date_id = rd.id
    LEFT JOIN booking_seats bs ON bs.booking_id = b.id
    WHERE b.user_id = ?
    GROUP BY b.id
    ORDER BY b.booking_date DESC
";

$booking_stmt = $conn->prepare($booking_sql);

if (!$booking_stmt) {
    die("SQL Error (Bookings): " . $conn->error);
}

$booking_stmt->bind_param("i", $user_id);
$booking_stmt->execute();
$bookings = $booking_stmt->get_result();
?>

<section class="dashboard-section">
    <div class="container">

        <h2 class="section-title">
            Bookings of <?= htmlspecialchars($user['username']) ?>
        </h2>

        <?php if ($bookings->num_rows === 0): ?>
            <p class="empty-msg">No bookings found for this user.</p>
        <?php else: ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Booking Ref</th>
                    <th>Route</th>
                    <th>Vehicle</th>
                    <th>Travel Date</th>
                    <th>Seats</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Booked On</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($row = $bookings->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['booking_reference']) ?></td>
                    <td>
                        <?= htmlspecialchars($row['startin']) ?>
                        →
                        <?= htmlspecialchars($row['destination']) ?>
                    </td>
                    <td><?= htmlspecialchars($row['vehicle_number']) ?></td>
                    <td><?= htmlspecialchars($row['routing_date']) ?></td>
                    <td><?= htmlspecialchars($row['seats'] ?? 'N/A') ?></td>
                    <td>Rs. <?= number_format($row['total_amount'], 2) ?></td>
                    <td><?= htmlspecialchars($row['payment_status']) ?></td>
                    <td><?= htmlspecialchars($row['booking_date']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php endif; ?>

        <div style="margin-top:20px;">
            <a href="user_view.php?id=<?= $user_id ?>" class="view-btn">
                Back to User
            </a>
        </div>

    </div>
</section>
