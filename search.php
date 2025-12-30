<?php
    require_once"./includes/header.php";
// Check if the form parameters exist
if (isset($_GET['start-point']) && isset($_GET['end-point'])) {
    $start = trim($_GET['start-point']);
    $end = trim($_GET['end-point']);

    // Prepare statement to prevent SQL injection
$stmt = $conn->prepare("
    SELECT 
        vehicle_lists.id,
        vehicle_lists.starttime,
        vehicle_lists.endtime,
        routes.startin,
        routes.destination,
        routes.price 
    FROM routes
    INNER JOIN vehicle_lists 
        ON routes.id = vehicle_lists.route_id
    WHERE routes.startin = ? 
      AND routes.destination = ?
");



    $stmt->bind_param("ss", $start, $end);
    $stmt->execute();

    $vehicle_list = $stmt->get_result();
} else {
    // Redirect back if parameters are missing
    header("Location: index");
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