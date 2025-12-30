<?php
require_once "./config/db_connect.php"; // same DB connection as others

if (!isset($_GET['vehicle_id'], $_GET['route_date_id'])) {
    echo json_encode([]);
    exit;
}

$vehicle_id = intval($_GET['vehicle_id']);
$route_date_id = intval($_GET['route_date_id']);

$sql = "SELECT seat_number
        FROM booking_seats
        JOIN bookings ON booking_seats.booking_id = bookings.id
        WHERE bookings.vehicle_id = ?
        AND bookings.route_date_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $vehicle_id, $route_date_id);
$stmt->execute();
$result = $stmt->get_result();

$occupiedSeats = [];
while ($row = $result->fetch_assoc()) {
    $occupiedSeats[] = (int)$row['seat_number'];
}

header('Content-Type: application/json');
echo json_encode($occupiedSeats);
