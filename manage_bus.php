<?php
require_once "./includes/db_header.php";

// Fetch all vehicles with routes
$query = "SELECT * FROM routes, vehicle_lists WHERE routes.id = vehicle_lists.route_id";
$result = mysqli_query($conn, $query);
$vehicles = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch all unique starting and destination cities
$r_query = "SELECT DISTINCT startin, destination FROM routes";

$r_result = mysqli_query($conn, $r_query);
$routes = mysqli_fetch_all($r_result, MYSQLI_ASSOC);



if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $startin = trim($_POST['startin']);
    $destination = trim($_POST['destination']);
    $starttime = trim($_POST['starting']);
    $endtime = trim($_POST['ending']);
    $vno = trim($_POST['v-no']);
    $facility = trim($_POST['facility']);
    $dname = trim($_POST['d-name']);
    $dno = trim($_POST['d-no']);
    $tseats = trim($_POST['seat-no']);

    //Find route ID for this start & destination
    $rid_query = "SELECT id FROM routes WHERE startin = ? AND destination = ?";
    $stmt = mysqli_prepare($conn, $rid_query);
    mysqli_stmt_bind_param($stmt, "ss", $startin, $destination);
    mysqli_stmt_execute($stmt);
    $rid_result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($rid_result) > 0) {
        $route = mysqli_fetch_assoc($rid_result);
        $rid = $route['id'];

        //Check if vehicle number already exists
        $check_sql = "SELECT id FROM vehicle_lists WHERE vehicle_number = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $vno);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $errormessage = "Vehicle number already exists.";
        } else {
            //Insert into database
            $insert_sql = "INSERT INTO vehicle_lists(route_id, starttime, endtime, vehicle_number, facilities, driver_name, driver_phone_number, total_seats) 
                           VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "isssssss", $rid, $starttime, $endtime, $vno, $facility, $dname, $dno, $tseats);

            if (mysqli_stmt_execute($insert_stmt)) {
                header('Location: ./manage_bus.php');
                exit();
            } else {
                $errormessage = "Error inserting record: " . mysqli_error($conn);
            }
        }
    } else {
        $errormessage = "No matching route found for these cities.";
    }
}
?>


    <section id="vehicle" class="vehicle-section">
        <div class="container">
            <h2 class="section-title">Available Vehicles</h2>
            <?php if(!empty($errormessage)):?>
                <div class="error-message">
                    <?=$errormessage ?>
                </div>
            <?php endif; ?>
            <div class="vehicle-grid">
                <!-- route -->
                <?php foreach($vehicles as $vehicle):?>
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
                            <div class="price">
                              <span>NPR <?= $vehicle['price'] ?></span>
                          </div>
                          <div class="veh-no">
                            <span> <?= $vehicle['vehicle_number'] ?></span>
                          </div>
                          <div class="facilits">
                            <span> <?= $vehicle['facilities'] ?></span>
                          </div>
                        </div>
                      </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button id="route-add" class="add-btn"> Add</button>
        </div>
        <div id="hidden-form">
            <form action="manage_bus.php" method="POST" id="add-form">
            
          <div class="add-group">
            <label for="">Starting City</label>
            <select name="startin" id="route-start">
            <?php 
              $startCities = array_unique(array_column($routes, 'startin'));
              foreach($startCities as $city): ?>
            <option value="<?= htmlspecialchars($city) ?>"><?= htmlspecialchars($city) ?></option>
            <?php endforeach; ?>
            </select>
          </div>

          <div class="add-group">
            <label for="">Destination City</label>
            <select name="destination" id="route-destination">
            <?php 
              $destCities = array_unique(array_column($routes, 'destination'));
              foreach($destCities as $city): ?>
            <option value="<?= htmlspecialchars($city) ?>"><?= htmlspecialchars($city) ?></option>
            <?php endforeach; ?>
            </select>
          </div>


            <div class="add-group">
                <label for="">Start Time</label>
                <input type="text" name="starting" id="starting" placeholder="Enter the Start time of the route" required />
            </div>

            <div class="add-group">
                <label for="">End Time</label>
                <input type="text" name="ending" id="ending" placeholder="Enter the End time of the route" required />
            </div>

            <div class="add-group">
                <label for="">Vehicle Number</label>
                <input type="text" name="v-no" id="v-no" placeholder="Enter the number of vehicle" />
            </div>

            <div class="add-group">
                <label for="">Facillities</label>
                <input type="text" name="facility" id="facility" placeholder="What are the facilities for the riders? " />
            </div>

            <div class="add-group">
                <label for="">Driver Name</label>
                <input type="text" name="d-name" id="d-name" placeholder="Enter the Name of the driver" required />
            </div>
            
            <div class="add-group">
                <label for="">Driver Phone Number</label>
                <input type="text" name="d-no" id="d-no" placeholder="Enter the Phone number of driver" required />
            </div>
            
            <div class="add-group">
                <label for="">Total Seats</label>
                <input type="text" name="seat-no" id="seat-no" placeholder="Enter the total number of seats in the bus" required />
            </div>

            <button type="submit" class="add-form-btn">Add</button>
        </form>
        </div>
        
    </section>

    <script src="./assets/js/add.js"></script>
        