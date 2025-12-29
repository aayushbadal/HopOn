<?php
require_once "./includes/header.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$vehicle_id = $_POST['vehicle_id'];
$route_date_id = $_POST['route_date_id'];
$selected_seats = $_POST['selected_seats'];

/* Get route + price */
$stmt = $conn->prepare("
SELECT routes.id, routes.price
FROM routes
JOIN vehicle_lists ON routes.id = vehicle_lists.route_id
WHERE vehicle_lists.id = ?
");
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$stmt->bind_result($route_id, $price);
$stmt->fetch();
$stmt->close();

$seatCount = count(explode(",", $selected_seats));
$total_price = $seatCount * $price;   
$booking_reference = "BK" . time() . rand(1000, 9999);
?>

<div class="checkout-container">
  <div class="checkout-card">
    <h2 class="checkout-title">Checkout</h2>

    <p class="booked-seats">Selected Seats: <span><?= htmlspecialchars($selected_seats) ?></span></p>
    <p class="total-price">Total: <span>Rs. <?= htmlspecialchars($total_price) ?></span></p>


    <form method="POST" action="booking_confirmation.php">
        <input type="hidden" name="vehicle_id" value="<?= $vehicle_id ?>">
        <input type="hidden" name="route_id" value="<?= $route_id ?>">
        <input type="hidden" name="route_date_id" value="<?= $route_date_id ?>">
        <input type="hidden" name="selected_seats" value="<?= $selected_seats ?>">
        <input type="hidden" name="total_price" value="<?= $total_price ?>">
        <input type="hidden" name="booking_reference" value="<?= $booking_reference ?>">

        <label for="payment" class="payment-label">Select Payment Method</label>
        <select name="payment_method" required>
          <option value="eSewa">eSewa</option>
          <option value="Khalti">Khalti</option>
        </select>

      <button class="confirm-btn">Confirm & Pay</button>
    </form>
  </div>
</div>
