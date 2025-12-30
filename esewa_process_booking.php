<?php
require_once "./includes/header.php";

/* =========================
   AUTH & REQUEST CHECK
========================= */
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ./login.php");
    exit();
}

/* =========================
   INPUT DATA
========================= */
$vehicle_id     = intval($_POST['vehicle_id']);
$route_date_id  = intval($_POST['route_date_id']);
$selected_seats = trim($_POST['selected_seats']);

if (empty($selected_seats)) {
    die("No seats selected!");
}

/* =========================
   GET ROUTE & PRICE
========================= */
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

if (!$route_id || !$price) {
    die("Invalid route data!");
}

/* =========================
   CALCULATE TOTAL
========================= */
$seats       = array_filter(array_map('trim', explode(",", $selected_seats)));
$seat_count  = count($seats);
$total_price = $seat_count * $price;

if ($seat_count <= 0) {
    die("Invalid seat selection!");
}

$session_id = session_id();

/* Clear expired locks */
$conn->query("DELETE FROM seat_locks WHERE expires_at < NOW()");

/* Lock seats (5 minutes) */
$lock_stmt = $conn->prepare("
    INSERT INTO seat_locks
    (vehicle_id, route_date_id, seat_number, session_id, expires_at)
    VALUES (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 5 MINUTE))
");

foreach ($seats as $seat) {

    // Check if seat already booked
    $checkBooked = $conn->prepare("
        SELECT 1 FROM booking_seats bs
        JOIN bookings b ON bs.booking_id = b.id
        WHERE b.vehicle_id = ? AND b.route_date_id = ? AND bs.seat_number = ?
    ");
    $checkBooked->bind_param("iii", $vehicle_id, $route_date_id, $seat);
    $checkBooked->execute();
    if ($checkBooked->get_result()->num_rows > 0) {
        die("Seat $seat already booked.");
    }

    // Check if seat already locked
    $checkLocked = $conn->prepare("
        SELECT 1 FROM seat_locks
        WHERE vehicle_id = ? AND route_date_id = ? AND seat_number = ?
        AND expires_at > NOW()
    ");
    $checkLocked->bind_param("iii", $vehicle_id, $route_date_id, $seat);
    $checkLocked->execute();
    if ($checkLocked->get_result()->num_rows > 0) {
        die("Seat $seat is temporarily locked.");
    }

    // Lock seat
    $lock_stmt->bind_param("iiis", $vehicle_id, $route_date_id, $seat, $session_id);
    $lock_stmt->execute();
}


/* =========================
   BOOKING REFERENCE
========================= */
$booking_reference = "BK" . time() . rand(1000, 9999);

/* =========================
   STORE TEMP DATA (SESSION)
========================= */
$_SESSION['vehicle_id']     = $vehicle_id;
$_SESSION['route_id']       = $route_id;
$_SESSION['route_date_id']  = $route_date_id;
$_SESSION['selected_seats'] = implode(",", $seats);
$_SESSION['total_price']    = $total_price;
$_SESSION['booking_ref']    = $booking_reference;

/* =========================
   eSewa SIGNATURE (REQUIRED)
========================= */
$secret_key = "8gBm/:&EnhH.1/q"; // eSewa TEST secret key

$signed_field_names = "total_amount,transaction_uuid,product_code";

$signature_string =
    "total_amount={$total_price}," .
    "transaction_uuid={$booking_reference}," .
    "product_code=EPAYTEST";

$signature = base64_encode(
    hash_hmac("sha256", $signature_string, $secret_key, true)
);
?>

<!-- =========================
     CHECKOUT UI
========================= -->
<div class="checkout-container">
    <div class="checkout-card">
        <h2 class="checkout-title">Checkout</h2>

        <p class="booked-seats"><strong>Selected Seats:</strong>
            <?= htmlspecialchars($_SESSION['selected_seats']) ?>
        </p>

        <p class="total-price"><strong>Total Amount:</strong>
            Rs. <?= number_format($total_price, 2) ?>
        </p>

        <!-- Hidden fields for JS -->
        <input type="hidden" id="total_price" value="<?= $total_price ?>">
        <input type="hidden" id="booking_ref" value="<?= $booking_reference ?>">
        <input type="hidden" id="signature" value="<?= $signature ?>">
        <input type="hidden" id="signed_field_names" value="<?= $signed_field_names ?>">

        <button id="esewaPayBtn" class="confirm-btn">
            Pay with eSewa
        </button>
    </div>
</div>

<script>
document.getElementById("esewaPayBtn").addEventListener("click", function (e) {
    e.preventDefault();

    const amount       = document.getElementById("total_price").value;
    const bookingRef  = document.getElementById("booking_ref").value;
    const signature   = document.getElementById("signature").value;
    const signedNames = document.getElementById("signed_field_names").value;

    const path = "https://rc-epay.esewa.com.np/api/epay/main/v2/form";

    const params = {
        amount: amount,
        tax_amount: 0,
        total_amount: amount,
        transaction_uuid: bookingRef,
        product_code: "EPAYTEST",
        product_service_charge: 0,
        product_delivery_charge: 0,
        signed_field_names: signedNames,
        signature: signature,
        success_url: "http://localhost/HopOn/esewa_success.php",
        failure_url: "http://localhost/HopOn/esewa_failure.php"
    };

    const form = document.createElement("form");
    form.method = "POST";
    form.action = path;

    Object.keys(params).forEach(key => {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = key;
        input.value = params[key];
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
});
</script>
