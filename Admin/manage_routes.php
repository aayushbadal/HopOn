<?php
  require_once"./includes/db_header.php";
  if(!isLoggedIn()){
        header('Location: ./admin_login.php');
        exit();
  }


//Fetch all routes
$query = "SELECT * FROM routes" ;
$result = mysqli_query($conn, $query);

$routes = mysqli_fetch_all($result, MYSQLI_ASSOC);



if($_SERVER['REQUEST_METHOD'] == "POST"){
    //Get form data
    $startin = trim($_POST['start-point']);
    $destin = trim($_POST['destination-point']);
    $duration = trim($_POST['duration']);
    $price = trim($_POST['price']);

    //Perform Addition Work
    $check_sql = "SELECT ID FROM routes Where startin = ? AND destination = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
            
    if($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "ss", $startin, $destin);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if(mysqli_stmt_num_rows($check_stmt) > 0){
            $errormessage = "Route already exists";
        } else{
            //Now insert data into database
            $insert_sql = "INSERT INTO routes(startin, destination, duration, price) VALUES(?, ?, ?, ?);";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);

            if($insert_stmt) {
                mysqli_stmt_bind_param($insert_stmt, "ssss", $startin, $destin, $duration, $price);
                if (mysqli_stmt_execute($insert_stmt)){
                    // Redirect to Route page
                    header('Location:./manage_routes.php');
                    exit();
                } else{
                    $errormessage = "Addition fail";
                }
            }
        }
            }
}

?>

    <section id="routes" class="routes-section">
        <div class="container">
            <h2 class="section-title">Available Routes</h2>
            <?php if(!empty($errormessage)):?>
                <div class="error-message">
                    <?=$errormessage ?>
                </div>
            <?php endif; ?>
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
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button id="route-add" class="add-btn"> Add</button>
        </div>
        <div id="hidden-form">
            <form action="manage_routes.php" method="POST" id="add-form">
            
            <div class="add-group">
                <label for="">Starting City</label>
                <input type="text" name="start-point" id="start-point" placeholder="Enter Starting City" required />
            </div>

            <div class="add-group">
                <label for="">Destination City</label>
                <input type="text" name="destination-point" id="destination-point" placeholder="Enter Destination City" required />
            </div>

            <div class="add-group">
                <label for="">Duration</label>
                <input type="text" name="duration" id="duration" placeholder="Enter the duration of ride" required />
            </div>

            <div class="add-group">
                <label for="">Price</label>
                <input type="text" name="price" id="price" placeholder="Enter the price" required />
            </div>

            <button type="submit" class="add-form-btn">Add</button>
        </form>
        </div>
        
    </section>

    <script src="./assets/js/add.js"></script>
        