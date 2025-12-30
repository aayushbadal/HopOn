<?php
require_once "./config/db_connect.php"; // same DB connection as others

if (!isset($_GET['vehicle_id'], $_GET['route_date_id'])) {
    echo json_encode([]);
    exit;
}

$vehicle_id = intval($_GET['vehicle_id']);
$route_date_id = intval($_GET['route_date_id']);

$sql = "
SELECT seat_number FROM booking_seats bs
JOIN bookings b ON bs.booking_id = b.id
WHERE b.vehicle_id = ? AND b.route_date_id = ?

UNION

SELECT seat_number FROM seat_locks
WHERE vehicle_id = ? AND route_date_id = ? AND expires_at > NOW()
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii",
    $vehicle_id, $route_date_id,
    $vehicle_id, $route_date_id
);

$stmt->execute();
$result = $stmt->get_result();

$occupiedSeats = [];
while ($row = $result->fetch_assoc()) {
    $occupiedSeats[] = (int)$row['seat_number'];
}

header('Content-Type: application/json');
echo json_encode($occupiedSeats);
