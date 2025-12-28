<?php
require_once "./includes/header.php";
if(!isLoggedIn()){
    header('Location: ./admin_login.php');
    exit();
}

// Handle Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $del_sql = "DELETE FROM routes WHERE ID = ?";
    $del_stmt = mysqli_prepare($conn, $del_sql);
    if($del_stmt){
        mysqli_stmt_bind_param($del_stmt, "i", $delete_id);
        mysqli_stmt_execute($del_stmt);
        header('Location: ./manage_routes.php'); // Refresh page
        exit();
    }
}

// Fetch all routes
$query = "SELECT * FROM routes";
$result = mysqli_query($conn, $query);
$routes = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Handle Addition
if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['start-point'])){
    $startin = trim($_POST['start-point']);
    $destin = trim($_POST['destination-point']);
    $duration = trim($_POST['duration']);
    $price = trim($_POST['price']);

    $check_sql = "SELECT ID FROM routes WHERE startin = ? AND destination = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);

    if($check_stmt){
        mysqli_stmt_bind_param($check_stmt, "ss", $startin, $destin);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if(mysqli_stmt_num_rows($check_stmt) > 0){
            $errormessage = "Route already exists";
        } else{
            $insert_sql = "INSERT INTO routes(startin, destination, duration, price) VALUES(?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            if($insert_stmt){
                mysqli_stmt_bind_param($insert_stmt, "ssss", $startin, $destin, $duration, $price);
                if(mysqli_stmt_execute($insert_stmt)){
                    header('Location: ./manage_routes.php');
                    exit();
                } else{
                    $errormessage = "Addition failed";
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
            <div class="error-message"><?=$errormessage?></div>
        <?php endif; ?>

        <div class="routes-grid">
            <?php foreach($routes as $route): ?>
                <div class="route-card">
                    <img src="./assets/images/Logo.jpg" class="route-poster" alt="">
                    <div class="route-details">
                        <div class="route-info">
                            <div class="start-to-end">
                                <span><strong><?= $route['startin'] ?> To </strong></span>
                                <span><strong><?= $route['destination'] ?></strong></span>
                            </div>
                            <div class="duration">
                                <span>Duration: <?= $route['duration'] ?> Hrs</span>
                            </div>
                            <div class="price">
                                <span>NPR <?= $route['price'] ?></span>
                            </div>
                        </div>

                        <!-- DELETE BUTTON -->
                        <form action="manage_routes.php" method="POST" style="margin-top: 10px;">
                            <input type="hidden" name="delete_id" value="<?= $route['id'] ?>">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
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
