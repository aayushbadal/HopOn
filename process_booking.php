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
$selected_seats = $_POST['selected_seats']; // e.g., "1,2,5"
$booking_reference = "BK". time() . rand(1000,9999);

// ------------------------------
// 1️⃣ Fetch price per seat and route_id from database
// ------------------------------
$route_query = "
    SELECT routes.price, routes.id AS route_id
    FROM routes
    INNER JOIN vehicle_lists ON routes.id = vehicle_lists.route_id
    WHERE vehicle_lists.id = ?
";

$stmt_route = mysqli_prepare($conn, $route_query);
if (!$stmt_route) {
    die("Prepare failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt_route, "i", $vehicle_id);
mysqli_stmt_execute($stmt_route);
mysqli_stmt_bind_result($stmt_route, $pricePerSeat, $route_id);
mysqli_stmt_fetch($stmt_route);
mysqli_stmt_close($stmt_route);

// ------------------------------
// 2️⃣ Calculate total price
// ------------------------------
$seatCount = count(array_filter(explode(",", $selected_seats))); // safe in case of empty strings
$total_price = $seatCount * $pricePerSeat;

// ------------------------------
// 3️⃣ Insert into bookings table
// ------------------------------
$booking_query = "
    INSERT INTO bookings 
    (user_id, route_id, vehicle_id, route_date_id, booking_reference, total_amount, booking_date)
    VALUES (?, ?, ?, ?, ?, ?, NOW())
";

$stmt_booking = mysqli_prepare($conn, $booking_query);
if (!$stmt_booking) {
    die("Prepare failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt_booking, "iiiisi", $user_id, $route_id, $vehicle_id, $route_date_id, $booking_reference, $total_price);
mysqli_stmt_execute($stmt_booking);

$booking_id = mysqli_insert_id($conn);
mysqli_stmt_close($stmt_booking);

// ------------------------------
// 4️⃣ Insert each selected seat
// ------------------------------
$booking_seats = explode(",", $selected_seats);
foreach($booking_seats as $bookingSeat) {
    $bookingSeat = trim($bookingSeat);
    if ($bookingSeat === '') continue; // skip empty values

    $booking_seat_query = "INSERT INTO booking_seats (booking_id, seat_number) VALUES (?, ?)";
    $stmt_seat = mysqli_prepare($conn, $booking_seat_query);
    if (!$stmt_seat) {
        die("Prepare failed: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt_seat, "is", $booking_id, $bookingSeat);
    mysqli_stmt_execute($stmt_seat);
    mysqli_stmt_close($stmt_seat);
}
?>

<!-- ------------------------------ -->
<!--  Show checkout summary -->
<!-- ------------------------------ -->
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
