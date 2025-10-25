<?php
    require_once"./includes/header.php";
// Check if the form parameters exist
if (isset($_GET['start-point']) && isset($_GET['end-point'])) {
    $start = trim($_GET['start-point']);
    $end = trim($_GET['end-point']);

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM routes,vehicle_lists WHERE routes.startin = ? AND routes.destination = ?");
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
            <div class="vehicles-grid">
                <!-- vehicles -->
                <?php foreach($vehicle_list as $vehicle):?>
                <div class="vehicle-card">
                    <img src="<?=$vehicle['image_url']?>" class="vehicle-poster" alt="">
                    <div class="vehicle-details">
                        <div class="vehicle-info">
                            <div class="start-to-end">
                                <span><strong><?= $vehicle['startin'] ?>  To  </strong></span>
                                <span><strong><?= $vehicle['destination'] ?></strong></span>
                            </div>
                            
                            
                                <div class="timing">
                                    
                                        <span> <?= $vehicle['starttime'] ?> - - To - - </span>
                                    
                                    
                                        <span> <?= $vehicle['endtime'] ?></span>
                                    
                                </div>
                                <div class="pricing">
                                    <div class="price">
                                        <span>NPR <?= $vehicle['price'] ?></span>
                                    </div>
                                </div>
                                    
                                
                        </div>
                        <a href="booking?vehicleId=<?= $vehicle['id'] ?>" class="book-btn">Book</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>



<?php
    require_once"./includes/footer.php"
?>