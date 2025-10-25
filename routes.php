<?php
require_once "./includes/header.php";

//Fetch all routes
$query = "SELECT * FROM routes" ;
$result = mysqli_query($conn, $query);

$routes = mysqli_fetch_all($result, MYSQLI_ASSOC);


?>

    <section id="routes" class="routes-section">
        <div class="container">
            <h2 class="section-title">Available Routes</h2>
            <div class="routes-grid">
                <!-- route -->
                <?php foreach($routes as $route):?>
                <div class="route-card">
                    <img src="./assets/images/Logo.jpg" class="route-poster" alt="">
                    <div class="route-details">
                        <div class="route-info">
                            <div class="start-to-end">
                                <span><strong><?= $route['startin'] ?>  To  </strong></span>
                                <span><strong><?= $route['destination'] ?></strong></span>
                            </div>
                            
                            <div class="duration">
                                <span> Duration: <?= $route['duration'] ?> Hrs</span>
                            </div>
                            <div class="price">
                                <span>NPR <?= $route['price'] ?></span>
                            </div>
                            
                        </div>
                        <a href="vehicle_list.php?routeId=<?= $route['id'] ?>" class="book-btn">View</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <?php
        require_once"./includes/footer.php";
    ?>