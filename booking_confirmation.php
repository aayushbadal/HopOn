<?php
require_once "./includes/header.php";

/* =========================
   AUTH & REQUEST CHECK
========================= */
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

/* =========================
   INPUT DATA
========================= */
$user_id           = intval($_SESSION['user_id']);
$vehicle_id        = intval($_POST['vehicle_id']);
$route_id          = intval($_POST['route_id']);
$route_date_id     = intval($_POST['route_date_id']);
$selected_seats    = trim($_POST['selected_seats']);
$total_price       = floatval($_POST['total_price']);
$payment_method    = trim($_POST['payment_method']);
$booking_reference = trim($_POST['booking_reference']);

if (
    empty($selected_seats) ||
    empty($payment_method) ||
    empty($booking_reference)
) {
    die("Invalid booking data!");
}

/* =========================
   DATABASE TRANSACTION
========================= */
try {
    $conn->begin_transaction();

    /* ---- INSERT BOOKING ---- */
    $booking_sql = "
        INSERT INTO bookings
        (user_id, route_id, vehicle_id, route_date_id, booking_reference,
         total_amount, payment_method, booking_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ";

    $stmt = $conn->prepare($booking_sql);
    $stmt->bind_param(
        "iiiisds",
        $user_id,
        $route_id,
        $vehicle_id,
        $route_date_id,
        $booking_reference,
        $total_price,
        $payment_method
    );
    $stmt->execute();
    $booking_id = $conn->insert_id;
    $stmt->close();

    /* ---- INSERT SEATS ---- */
    $seats = explode(",", $selected_seats);

    $seat_sql = "INSERT INTO booking_seats (booking_id, seat_number) VALUES (?, ?)";
    $stmt = $conn->prepare($seat_sql);

    foreach ($seats as $seat) {
        $seat = trim($seat);
        $stmt->bind_param("is", $booking_id, $seat);
        $stmt->execute();
    }
    $stmt->close();

    /* ---- COMMIT ---- */
    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
    die("Booking failed. Please try again.");
}
?>

<!-- =========================
     CONFIRMATION UI
========================= -->
<div class="confirmation-container">
    <div class="confirmation-card">
      <div class="confirmation-icon">
        <i class="fa-solid fa-circle-check"></i>
</div>
        <h1 class="confirmation-title">Booking Confirmed</h1>

        <p style="color:black;"><strong>Reference:</strong>
            <?= htmlspecialchars($booking_reference) ?>
        </p>

        <p style="color:black;"><strong>Payment Method:</strong>
            <?= htmlspecialchars($payment_method) ?>
        </p>

        <p style="color:black;"><strong>Total Amount:</strong>
            Rs. <?= number_format($total_price, 2) ?>
        </p>

        <a href="index.php" class="confirmation-btn">Back Home</a>
    </div>
</div>
