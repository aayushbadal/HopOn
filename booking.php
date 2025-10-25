<?php
require_once"./includes/header.php";


$vehicle_id = isset($_GET['vehicleId']) ? $_GET['vehicleId'] : 1;

     //fetch vehicle lists
    $vehicle_query = "select * from routes,vehicle_lists where vehicle_lists.id = $vehicle_id and routes.id = vehicle_lists.route_id";
    $vehicle_result = mysqli_query($conn, $vehicle_query);
    $vehicle_list = mysqli_fetch_all($vehicle_result, MYSQLI_ASSOC);

    //fetch routing dates
    $route_qurey = "select * from vehicle_lists,route_date where vehicle_lists.id = $vehicle_id and vehicle_lists.id = route_date.vehicle_id";
    $route_result = mysqli_query($conn, $route_qurey);
    $route_dates = mysqli_fetch_all($route_result, MYSQLI_ASSOC);


    $occupiedSeats = [];
    $sql = "SELECT seat_number FROM booking_seats,bookings WHERE vehicle_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $occupiedSeats[] = $row['seat_number'];
    }

    // Convert to JSON for JS
    $occupiedSeatsJson = json_encode($occupiedSeats);
?>


<section id="booking-section">
    <div class="container">
        <div class="booking-container">
            <div class="bus-detail">
                <?php foreach($vehicle_list as $vehicle) : ?>
                    <div class="booking-poster">
                        <img src="<?= $vehicle['image_url'] ?>"alt="">
                    </div>
                
                    <div class="booking-details">
                        <h2 class="booking-title"><?= $vehicle['startin'] ?> To <?= $vehicle['destination'] ?> </h2>
                        <div class="booking-info">
                            <p>
                                <strong>Boarding Time: </strong><?= $vehicle['starttime'] ?>
                            </p>
                            <p>
                                <strong>Dropping Time: </strong><?= $vehicle['endtime'] ?>
                            </p>
                            <p>
                                <strong>Facilities: </strong><?= $vehicle['facilities'] ?>
                            </p>
                            <p>
                                <strong>Total Seats: </strong><?= $vehicle['total_seats'] ?>
                            </p>
                            <p>
                                <strong>Available Seats: </strong><?= $vehicle['available_seats'] ?>
                            </p>
                            <p>
                                <strong>Bus Number: </strong><?= $vehicle['vehicle_number'] ?>
                            </p>
                            <p>
                                <strong>Driver Name: </strong><?= $vehicle['driver_name'] ?>
                            </p>
                            <p>
                                <strong>Driver Phone Number: </strong><?= $vehicle['driver_phone_number'] ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach;?>
            </div>

            <form action="./process_booking.php" method="POST" id="booking-form">
                <input type="hidden" name="vehicle_id" value="<?= $vehicle_id ?>" >
                <input type="hidden" name="selected_seats" id="selected-seats" value="">
                <input type="hidden" name="total_price" id="total-price" value="0">
                
                <div class="form-group">
                    <label for="">Select Date</label>
                    <select name="route_date_id" id="">
                        <?php foreach($route_dates as $routedate): ?>
                            <option value="<?= $routedate['id'] ?>"><?= date('d/m/Y',strtotime($routedate['routing_date'])) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="seat-details">
                    <h3>Select Your Seat</h3>
                    <div class="front">Front of Bus</div>
                    <div class="bus" id="bus">
                        <!-- Seats will be generated here by JavaScript -->
                    </div> 
                </div>
                <button type="submit" class="confirm-btn">Confirm Button</button>
            </form>
        </div>
    </div>
</section>

<script>
  const busTicketPrice = <?= $vehicle['price']; ?>;
  const occupiedSeats = <?php echo $occupiedSeatsJson; ?>.map(Number);
</script>


<?php
require_once"./includes/footer.php";
?>