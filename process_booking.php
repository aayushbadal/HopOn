<?php
require_once "./includes/header.php";

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

if($_SERVER["REQUEST_METHOD"] != "POST"){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$vehicle_id = $_POST['vehicle_id'];
$route_date_id = $_POST['route_date_id'];
$selected_seats = $_POST['selected_seats'];
$total_price = $_POST['total_price'];
$booking_reference = "BK". time() .rand(1000,9999);

// Fetch route_id from vehicle_lists using vehicle_id
$route_query = "SELECT route_id FROM vehicle_lists WHERE id = ?";
$stmt_route = mysqli_prepare($conn, $route_query);
mysqli_stmt_bind_param($stmt_route, "i", $vehicle_id);
mysqli_stmt_execute($stmt_route);
mysqli_stmt_bind_result($stmt_route, $route_id);
mysqli_stmt_fetch($stmt_route);
mysqli_stmt_close($stmt_route);

// Now insert into bookings
$booking_query = "INSERT INTO bookings 
(user_id, route_id, vehicle_id, route_date_id, booking_reference, total_amount, booking_date) 
VALUES (?, ?, ?, ?, ?, ?, NOW())";

$stmt = mysqli_prepare($conn, $booking_query);
mysqli_stmt_bind_param($stmt, "iiiisi", $user_id, $route_id, $vehicle_id, $route_date_id, $booking_reference, $total_price);
mysqli_stmt_execute($stmt);

$booking_id = mysqli_insert_id($conn);

// Insert each seat
$booking_seats = explode(",", $selected_seats);
foreach($booking_seats as $bookingSeat) {
    $booking_seat_query = "INSERT INTO booking_seats(booking_id, seat_number) VALUES(?,?)";
    $show_stmt = mysqli_prepare($conn, $booking_seat_query);
    mysqli_stmt_bind_param($show_stmt, "is", $booking_id, $bookingSeat);
    mysqli_stmt_execute($show_stmt);
}
?>
<div class="checkout-container">
  <div class="checkout-card">
    <h2 class="checkout-title">Checkout</h2>
    <p class="booked-seats">Selected Seats: <span><?= htmlspecialchars($selected_seats) ?></span></p>
    <p class="total-price">Total: <span>Rs. <?= htmlspecialchars($total_price) ?></span></p>

    <form method="POST" action="booking_confirmation.php?reference=<?= $booking_reference ?>">
      <label for="payment" class="payment-label">Select Payment Method</label>
      <select name="pay" id="payment" class="payment-select">
        <option value="esewa">eSewa</option>
        <option value="khalti">Khalti</option>
      </select>

      <button type="submit" class="confirm-btn">Confirm Booking</button>
    </form>
  </div>
</div>


