<?php 
require_once "./includes/header.php";

    $route_id = isset($_GET['routeId']) ? $_GET['routeId'] : 1;

     //fetch vehicle lists
    $vehicle_query = "select * from routes,vehicle_lists where route_id = $route_id and routes.id = vehicle_lists.route_id";
    $vehicle_result = mysqli_query($conn, $vehicle_query);
    $vehicle_list = mysqli_fetch_all($vehicle_result, MYSQLI_ASSOC);
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
                        <a href="booking.php?vehicleId=<?= $vehicle['id'] ?>" class="book-btn">Book</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>



<?php
require_once "./includes/footer.php";
?>