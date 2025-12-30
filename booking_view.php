<?php
require_once "./includes/header.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

/* Fetch booking + user + route + vehicle */
$sql = "
SELECT 
    b.*, 
    u.full_name, u.email,
    r.startin, r.destination, r.price,
    v.vehicle_number, v.driver_name, v.driver_phone_number,
    rd.routing_date
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN routes r ON b.route_id = r.id
JOIN vehicle_lists v ON b.vehicle_id = v.id
JOIN route_date rd ON b.route_date_id = rd.id
WHERE b.id = ? AND b.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    echo "<p style='text-align:center;color:red;'>Booking not found.</p>";
    exit();
}

/* Fetch seats */
$seat_sql = "SELECT seat_number FROM booking_seats WHERE booking_id = ?";
$sstmt = $conn->prepare($seat_sql);
$sstmt->bind_param("i", $booking_id);
$sstmt->execute();
$sres = $sstmt->get_result();
$seats = [];

while ($row = $sres->fetch_assoc()) {
    $seats[] = $row['seat_number'];
}

$seat_display = implode(", ", $seats);
?>

<div class="booking-details-page">
    <div class="container">
        <h2 class="section-title">Booking Details</h2>

        <div class="booking-card">
            <h3>Passenger Information</h3>
            <p><strong>Name:</strong> <?= htmlspecialchars($booking['full_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($booking['email']) ?></p>

            <hr>

            <h3>Trip Information</h3>
            <p><strong>Route:</strong> <?= htmlspecialchars($booking['startin']) ?> → <?= htmlspecialchars($booking['destination']) ?></p>
            <p><strong>Date:</strong> <?= date("M j, Y", strtotime($booking['routing_date'])) ?></p>
            <p><strong>Seats Booked:</strong> <?= htmlspecialchars($seat_display) ?></p>

            <hr>

            <h3>Vehicle Information</h3>
            <p><strong>Vehicle No:</strong> <?= htmlspecialchars($booking['vehicle_number']) ?></p>
            <p><strong>Driver:</strong> <?= htmlspecialchars($booking['driver_name']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($booking['driver_phone_number']) ?></p>

            <hr>

            <h3>Payment Information</h3>
            <p><strong>Booking Ref:</strong> <?= htmlspecialchars($booking['booking_reference']) ?></p>
            <p><strong>Total Amount:</strong> NPR <?= number_format($booking['total_amount'], 2) ?></p>
            <p><strong>Payment Method:</strong> <?= htmlspecialchars($booking['payment_method']) ?></p>
            <p><strong>Payment Reference:</strong> <?= htmlspecialchars($booking['payment_ref']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($booking['payment_status']) ?></p>
    </div>
</div>

<?php require_once "./includes/footer.php"; ?>
