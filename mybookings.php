<?php
require_once "./includes/header.php";

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch bookings for user
$sql = "SELECT b.id, b.booking_reference, b.total_amount, b.booking_date,
               r.startin, r.destination, v.vehicle_number
        FROM bookings b
        JOIN routes r ON b.route_id = r.id
        JOIN vehicle_lists v ON b.vehicle_id = v.id
        WHERE b.user_id = ? 
        ORDER BY b.booking_date DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>

<div class="book-container">
  <h2>My Bookings</h2>

  <?php if(!$bookings || count($bookings) == 0): ?>
    <p>You have no bookings yet. <a href="routes.php">Book a ride now</a>.</p>
  <?php else: ?>
    <div class="bookings-list">
      <?php foreach($bookings as $b): ?>
        <?php
          // Fetch seats for this booking
          $seat_query = "SELECT seat_number FROM booking_seats WHERE booking_id = ?";
          $sstmt = mysqli_prepare($conn, $seat_query);
          mysqli_stmt_bind_param($sstmt, "i", $b['id']);
          mysqli_stmt_execute($sstmt);
          $sres = mysqli_stmt_get_result($sstmt);
          $seat_rows = mysqli_fetch_all($sres, MYSQLI_ASSOC);
          mysqli_stmt_close($sstmt);
          $seat_list = array_map(function($r){ return $r['seat_number']; }, $seat_rows);
          $seat_display = implode(", ", $seat_list);
        ?>
        <div class="booking-card">
          <div class="booking-row">
            <div class="booking-left">
              <div class="booking-ref">Reference: <strong><?= htmlspecialchars($b['booking_reference']) ?></strong></div>
              <div class="booking-route"><?= htmlspecialchars($b['startin']) ?> → <?= htmlspecialchars($b['destination']) ?></div>
              <div class="booking-vehicle"><?= htmlspecialchars($b['vehicle_number']) ?> — NPR <?= number_format($b['total_amount'],2) ?></div>
              <div class="booking-seats">Seats: <?= htmlspecialchars($seat_display) ?></div>
            </div>
            <div class="booking-right">
              <div class="booking-date"><?= date('F j, Y, g:i a', strtotime($b['booking_date'])) ?></div>
              <a class="b-btn view-btn" href="booking_view.php?id=<?= $b['id'] ?>">View</a>
              <a class="b-btn download-btn" href="booking_invoice.php?id=<?= $b['id'] ?>">Invoice</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php
require_once "./includes/footer.php";
?>