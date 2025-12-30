<?php
    require_once"./includes/header.php";
// Check if the form parameters exist
if (
    isset($_GET['start-point']) &&
    isset($_GET['end-point']) &&
    isset($_GET['travel-date'])
) {

    $start = trim($_GET['start-point']);
    $end   = trim($_GET['end-point']);
    $date  = $_GET['travel-date'];


    // Prepare statement to prevent SQL injection
$stmt = $conn->prepare("
    SELECT 
        vl.id,
        vl.starttime,
        vl.endtime,
        r.startin,
        r.destination,
        r.price,
        rd.routing_date
    FROM routes r
    INNER JOIN vehicle_lists vl ON r.id = vl.route_id
    INNER JOIN route_date rd ON vl.id = rd.vehicle_id
    WHERE r.startin = ?
      AND r.destination = ?
      AND rd.routing_date = ?
");

$stmt->bind_param("sss", $start, $end, $date);
$stmt->execute();
$vehicle_list = $stmt->get_result();

} else {
    // Redirect back if parameters are missing
    header("Location: index.php");
    exit;
}

?>

    <section id="vehicles" class="vehicles-section">
    <div class="container">
        <h2 class="section-title">Available Vehicles</h2>

        <?php if ($vehicle_list->num_rows === 0): ?>
            <div class="no-vehicle-message">
                <p>Route vehicle not found</p>
                <span>Please try a different route or date</span>
            </div>

        <?php else: ?>
            <div class="vehicles-grid">
                <?php while ($vehicle = $vehicle_list->fetch_assoc()): ?>
                    <div class="vehicle-card">
                        <img src="./assets/images/Bus.png" class="vehicle-poster" alt="">
                        <div class="vehicle-details">
                            <div class="vehicle-info">
                                <div class="start-to-end">
                                    <strong><?= htmlspecialchars($vehicle['startin']) ?> to <?= htmlspecialchars($vehicle['destination']) ?></strong>
                                </div>

                                <div class="timing">
                                    <?= htmlspecialchars($vehicle['starttime']) ?> - <?= htmlspecialchars($vehicle['endtime']) ?>
                                </div>

                                <div class="pricing">
                                    NPR <?= htmlspecialchars($vehicle['price']) ?>
                                </div>
                            </div>

                            <a href="booking.php?vehicleId=<?= $vehicle['id'] ?>" class="book-btn">Book</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</section>




<?php
    require_once"./includes/footer.php"
?>