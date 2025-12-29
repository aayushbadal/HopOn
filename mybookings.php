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

<div class="bookings-section">
  <div class="container">
    <h2 class="section-title">My Bookings</h2>

    <?php if(empty($bookings)): ?>
      <p class="no-bookings">You have no bookings yet. <a href="routes.php">Book a ride now</a>.</p>
    <?php else: ?>
      <div class="bookings-grid">
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
            $seat_list = array_map(fn($r) => $r['seat_number'], $seat_rows);
            $seat_display = implode(", ", $seat_list);
          ?>
          <div class="booking-card">
            <div class="booking-header">
              <span class="booking-ref">Ref: <?= htmlspecialchars($b['booking_reference']) ?></span>
              <span class="booking-date"><?= date('M j, Y', strtotime($b['booking_date'])) ?></span>
            </div>
            <div class="booking-body">
              <div class="route-info">
                <strong><?= htmlspecialchars($b['startin']) ?> → <?= htmlspecialchars($b['destination']) ?></strong>
                <p>Vehicle: <?= htmlspecialchars($b['vehicle_number']) ?></p>
                <p>Seats: <?= htmlspecialchars($seat_display) ?></p>
              </div>
              <div class="amount">
                <p>Total: <strong>NPR <?= number_format($b['total_amount'], 2) ?></strong></p>
              </div>
            </div>
            <div class="booking-actions">
              <a class="view-btn" href="booking_view.php?id=<?= $b['id'] ?>">View</a>
              <!--
              <a class="invoice-btn" href="booking_invoice.php?id=<?= $b['id'] ?>">Invoice</a>
        -->
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php
require_once "./includes/footer.php";
?>
