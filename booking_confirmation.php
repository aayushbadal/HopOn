<?php
require_once "./includes/header.php";

if(!isset($_GET['reference'])){
    echo "Invalid reference!";
    exit;
}
$ref = $_GET['reference'];

// Fetch booking details
$stmt = $conn->prepare("SELECT * FROM bookings WHERE booking_reference=?");
$stmt->bind_param("s",$ref);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
?>

<div class="confirmation-container">
  <div class="confirmation-card">
    <div class="confirmation-icon"><i class="fa-solid fa-circle-check"></i></div>
    <h1 class="confirmation-title">Booking Confirmed!</h1>
    <p style="color:black;"><strong>Booking Reference:</strong> <?= $booking['booking_reference'] ?></p>
    <p style="color:black;"><strong>Payment Method:</strong> <?= $booking['payment_method'] ?></p>
    <p style="color:black;"><strong>Payment Status:</strong> <?= $booking['payment_status'] ?></p>
    <a href="./index.php" class="confirmation-btn">Back to Home</a>
  </div>
</div>
