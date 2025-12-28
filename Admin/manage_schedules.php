<?php
require_once "./includes/header.php";

if (!isLoggedIn()) {
    header('Location: ./admin_login.php');
    exit();
}

// Fetch all schedules (route_date) with vehicles and routes
$query = "SELECT rd.id as schedule_id, v.vehicle_number, r.startin, r.destination, rd.routing_date, v.starttime, v.endtime 
          FROM route_date rd
          JOIN vehicle_lists v ON rd.vehicle_id = v.id
          JOIN routes r ON v.route_id = r.id";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("<div class='error-message'>Error fetching schedules: " . mysqli_error($conn) . "</div>");
}

$schedules = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch all vehicles for dropdown in add form
$vehicle_query = "SELECT id, vehicle_number FROM vehicle_lists";
$v_result = mysqli_query($conn, $vehicle_query);

$vehicles = [];
if ($v_result) {
    $vehicles = mysqli_fetch_all($v_result, MYSQLI_ASSOC);
}

// Handle form submission for adding new schedule
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_schedule'])) {
    $vehicle_id = trim($_POST['vehicle_id']);
    $routing_date = trim($_POST['routing_date']);

    // Insert new schedule into route_date
    $insert_sql = "INSERT INTO route_date(vehicle_id, routing_date) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($stmt, "is", $vehicle_id, $routing_date);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: ./manage_schedules.php');
        exit();
    } else {
        $errormessage = "Error adding schedule: " . mysqli_error($conn);
    }
}

// Handle deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_sql = "DELETE FROM route_date WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: ./manage_schedules.php');
        exit();
    } else {
        $errormessage = "Error deleting schedule: " . mysqli_error($conn);
    }
}

?>

<section class="routes-section">
    <div class="container">
        <h2 class="section-title">Bus Schedules</h2>

        <?php if (!empty($errormessage)): ?>
            <div class="error-message"><?= $errormessage ?></div>
        <?php endif; ?>

        <div class="routes-grid">
            <?php foreach ($schedules as $schedule): ?>
                <div class="route-card">
                    <div class="route-details">
                        <div class="route-info">
                            <div class="start-to-end">
                                <strong><?= $schedule['startin'] ?> To <?= $schedule['destination'] ?></strong>
                            </div>
                            <div class="timing">
                                Date: <?= $schedule['routing_date'] ?><br>
                                <?= $schedule['starttime'] ?> - <?= $schedule['endtime'] ?>
                            </div>
                            <div class="veh-no">
                                Vehicle: <?= $schedule['vehicle_number'] ?>
                            </div>
                            <div class="delete-btn-schedule">
                                <a href="?delete_id=<?= $schedule['schedule_id'] ?>" onclick="return confirm('Are you sure to delete this schedule?');">
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button id="schedule-add" class="add-btn">Add</button>

        <div id="hidden-form">
            <form action="manage_schedules.php" method="POST" id="add-form">
                <input type="hidden" name="add_schedule" value="1">

                <div class="add-group">
                    <label for="vehicle_id">Select Vehicle</label>
                    <select name="vehicle_id" id="vehicle_id" required>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <option value="<?= $vehicle['id'] ?>"><?= $vehicle['vehicle_number'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="add-group">
                    <label for="routing_date">Date</label>
                    <input type="date" name="routing_date" id="routing_date" required>
                </div>

                <button type="submit" class="add-form-btn">Add</button>
            </form>
        </div>
    </div>
</section>

<script>
const AddIcon = document.getElementById('schedule-add');
const HiddenForm = document.getElementById('hidden-form');

AddIcon.addEventListener('click', () => {
    HiddenForm.classList.toggle('show');
    if (HiddenForm.classList.contains('show')) {
        document.getElementById('vehicle_id').focus();
    }
});
</script>
