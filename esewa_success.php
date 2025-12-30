<?php
require_once "./includes/header.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$data = json_decode(base64_decode($_GET['data']), true);

if (!$data || $data['status'] !== 'COMPLETE') {
    die("Invalid payment");
}

$booking_reference = $data['transaction_uuid'];
$payment_ref = $data['transaction_code'];

/* 
   Now fetch pending booking data
   (You should store booking info temporarily in session or a temp table)
*/

// Example (using session):
$user_id        = $_SESSION['user_id'];
$vehicle_id     = $_SESSION['vehicle_id'];
$route_id       = $_SESSION['route_id'];
$route_date_id  = $_SESSION['route_date_id'];
$selected_seats = $_SESSION['selected_seats'];
$total_price    = $_SESSION['total_price'];

$conn->begin_transaction();

try {

    /* INSERT BOOKING */
    $sql = "
    INSERT INTO bookings (
        user_id, route_id, vehicle_id, route_date_id,
        booking_reference, total_amount,
        payment_method, payment_status, payment_ref
    )
    VALUES (?, ?, ?, ?, ?, ?, 'ESEWA', 'PAID', ?)
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iiiisds",
        $user_id,
        $route_id,
        $vehicle_id,
        $route_date_id,
        $booking_reference,
        $total_price,
        $payment_ref
    );
    $stmt->execute();
    $booking_id = $conn->insert_id;
    $stmt->close();

    /* INSERT SEATS */
    $stmt = $conn->prepare(
        "INSERT INTO booking_seats (booking_id, seat_number) VALUES (?, ?)"
    );

    foreach (explode(",", $selected_seats) as $seat) {
        $seat = trim($seat);
        $stmt->bind_param("is", $booking_id, $seat);
        $stmt->execute();
    }

    $stmt->close();
    $conn->commit();

    header("Location: booking_confirmation.php?ref=$booking_reference");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Payment processed but booking failed.");
}
