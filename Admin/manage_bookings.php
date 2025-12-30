<?php
require_once "./includes/header.php";

if (!isLoggedIn()) {
    header("Location: admin_login.php");
    exit();
}

/* ===== Handle Booking Status Toggle ===== */
if (isset($_POST['toggle_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $current_status = $_POST['current_status'];

    $new_status = ($current_status === 'PAID') ? 'PENDING' : 'PAID';

    $update = $conn->prepare("UPDATE bookings SET payment_status = ? WHERE id = ?");
    $update->bind_param("si", $new_status, $booking_id);
    $update->execute();
    $update->close();

    // Reload page after update
    header("Location: manage_bookings.php");
    exit();
}

/* ===== Fetch All Bookings ===== */
$sql = "
    SELECT
        b.id AS booking_id,
        b.booking_reference,
        b.total_amount,
        b.booking_date,
        b.payment_status,

        u.username,
        u.email,

        r.startin,
        r.destination,

        v.vehicle_number,

        rd.routing_date,

        GROUP_CONCAT(bs.seat_number ORDER BY bs.seat_number SEPARATOR ', ') AS seats
    FROM bookings b
    INNER JOIN users u ON b.user_id = u.id
    INNER JOIN routes r ON b.route_id = r.id
    INNER JOIN vehicle_lists v ON b.vehicle_id = v.id
    INNER JOIN route_date rd ON b.route_date_id = rd.id
    LEFT JOIN booking_seats bs ON bs.booking_id = b.id
    GROUP BY b.id
    ORDER BY b.booking_date DESC
";

$result = $conn->query($sql);
if (!$result) {
    die("SQL Error: " . $conn->error);
}
?>

<section class="dashboard-section">
    <div class="container">
        <h2 class="section-title">Manage Bookings</h2>

        <?php if ($result->num_rows === 0): ?>
            <p class="empty-msg">No bookings found.</p>
        <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Booking Ref</th>
                    <th>User</th>
                    <th>Route</th>
                    <th>Vehicle</th>
                    <th>Travel Date</th>
                    <th>Seats</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Booked On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['booking_reference']) ?></td>
                    <td>
                        <?= htmlspecialchars($row['username']) ?><br>
                        <small><?= htmlspecialchars($row['email']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($row['startin']) ?> → <?= htmlspecialchars($row['destination']) ?></td>
                    <td><?= htmlspecialchars($row['vehicle_number']) ?></td>
                    <td><?= htmlspecialchars($row['routing_date']) ?></td>
                    <td><?= htmlspecialchars($row['seats'] ?? 'N/A') ?></td>
                    <td>Rs. <?= number_format($row['total_amount'], 2) ?></td>
                    <td><?= htmlspecialchars($row['payment_status']) ?></td>
                    <td><?= htmlspecialchars($row['booking_date']) ?></td>
                    <td>
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                            <input type="hidden" name="current_status" value="<?= $row['payment_status'] ?>">
                            <?php if ($row['payment_status'] === 'PAID'): ?>
                                <button type="submit" name="toggle_status" class="btn-undo">Undo Approve</button>
                            <?php else: ?>
                                <button type="submit" name="toggle_status" class="btn-approve">Approve</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</section>
