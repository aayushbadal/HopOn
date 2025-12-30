<?php
require_once "./includes/header.php";

$vehicle_id = isset($_GET['vehicleId']) ? intval($_GET['vehicleId']) : 0;
if ($vehicle_id <= 0) {
    echo "<p>Invalid vehicle.</p>";
    require_once "./includes/footer.php";
    exit;
}

/* Fetch vehicle + route info */
$vehicle_query = "
    SELECT routes.*, vehicle_lists.*
    FROM routes
    JOIN vehicle_lists ON routes.id = vehicle_lists.route_id
    WHERE vehicle_lists.id = ?
";
$stmt = $conn->prepare($vehicle_query);
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$result = $stmt->get_result();
$vehicle = $result->fetch_assoc();

if (!$vehicle) {
    echo "<p>No vehicle details found.</p>";
    require_once "./includes/footer.php";
    exit;
}

/* Fetch route dates */
$route_query = "SELECT * FROM route_date WHERE vehicle_id = ?";
$stmt2 = $conn->prepare($route_query);
$stmt2->bind_param("i", $vehicle_id);
$stmt2->execute();
$route_dates = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

$totalSeats = $vehicle['total_seats'] ?: 33;
?>

<section id="booking-section">
    <div class="container">
        <div class="booking-container">

            <!-- BUS DETAILS -->
            <div class="bus-detail">
                <div class="booking-poster">
                    <img src="./assets/images/Bus.png" alt="Bus">
                </div>

                <div class="booking-details">
                    <h2 class="booking-title">
                        <?= htmlspecialchars($vehicle['startin']) ?> →
                        <?= htmlspecialchars($vehicle['destination']) ?>
                    </h2>

                    <div class="booking-info">
                        <p><strong>Boarding Time:</strong> <?= htmlspecialchars($vehicle['starttime']) ?></p>
                        <p><strong>Dropping Time:</strong> <?= htmlspecialchars($vehicle['endtime']) ?></p>
                        <p><strong>Facilities:</strong> <?= htmlspecialchars($vehicle['facilities']) ?></p>
                        <p><strong>Total Seats:</strong> <?= $totalSeats ?></p>
                        <p><strong>Available Seats:</strong>
                            <span id="available-seats">Select a date</span>
                        </p>
                        <p><strong>Bus Number:</strong> <?= htmlspecialchars($vehicle['vehicle_number']) ?></p>
                        <p><strong>Driver:</strong> <?= htmlspecialchars($vehicle['driver_name']) ?></p>
                        <p><strong>Driver Phone:</strong> <?= htmlspecialchars($vehicle['driver_phone_number']) ?></p>
                    </div>
                </div>
            </div>

            <!-- BOOKING FORM -->
            <form action="./esewa_process_booking.php" method="POST" id="booking-form">
                <input type="hidden" name="vehicle_id" value="<?= $vehicle_id ?>">
                <input type="hidden" name="selected_seats" id="selected-seats">
                <input type="hidden" name="total_price" id="total-price">

                <div class="form-group">
                    <label>Select Date</label>
                    <select name="route_date_id" id="route-date" required>
                        <option value="" disabled selected hidden>Select Date</option>
                        <?php foreach ($route_dates as $date): ?>
                            <option value="<?= $date['id'] ?>">
                                <?= date('d/m/Y', strtotime($date['routing_date'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="seat-details">
                    <h3>Select Your Seat</h3>
                    <div class="front">Front of Bus</div>
                    <div class="bus" id="bus"></div>
                </div>

                <div class="form-group total-price">
                    <strong>Total Price:</strong>
                    <span>Rs. 0</span>
                </div>

                <button type="submit" id="pay-button" class="confirm-btn" disabled>
                    Confirm Booking
                </button>
            </form>

        </div>
    </div>
</section>

<script>
const totalSeats = <?= $totalSeats ?>;
const ticketPrice = <?= (int)$vehicle['price'] ?>;
const vehicleId = <?= $vehicle_id ?>;

const bus = document.getElementById('bus');
const dateSelect = document.getElementById('route-date');
const payButton = document.getElementById('pay-button');
const availableSeatsEl = document.getElementById('available-seats');

const selectedSeatsInput = document.getElementById('selected-seats');
const totalPriceInput = document.getElementById('total-price');

let occupiedSeats = [];
let selectedSeats = [];

/* Load seats when date changes */
dateSelect.addEventListener('change', function () {
    payButton.disabled = false;
    fetchOccupiedSeats(this.value);
});

function fetchOccupiedSeats(routeDateId) {
    fetch(`get_occupied_seats.php?vehicle_id=${vehicleId}&route_date_id=${routeDateId}`)
        .then(res => res.json())
        .then(data => {
            occupiedSeats = data;
            selectedSeats = [];
            renderSeats();
            updateSummary();
        });
}

function renderSeats() {
    bus.innerHTML = "";

    for (let i = 1; i <= totalSeats; i++) {
        const seat = document.createElement('div');
        seat.className = "seat";
        seat.innerHTML = `<i class="fas fa-chair"></i>`;
        seat.title = `Seat ${i}`;

        if (occupiedSeats.includes(i)) {
            seat.classList.add("occupied");
        }

        seat.addEventListener('click', () => {
            if (seat.classList.contains("occupied")) return;

            seat.classList.toggle("selected");

            if (seat.classList.contains("selected")) {
                selectedSeats.push(i);
            } else {
                selectedSeats = selectedSeats.filter(s => s !== i);
            }
            updateSummary();
        });

        bus.appendChild(seat);
    }
}

function updateSummary() {
    selectedSeatsInput.value = selectedSeats.join(",");
    totalPriceInput.value = selectedSeats.length * ticketPrice;

    const available = totalSeats - occupiedSeats.length - selectedSeats.length;
    availableSeatsEl.textContent = available;

    document.querySelector('.total-price span').textContent =
        "Rs. " + (selectedSeats.length * ticketPrice);
}

/* Prevent empty submission */
document.getElementById('booking-form').addEventListener('submit', function (e) {
    if (!dateSelect.value || selectedSeats.length === 0) {
        alert("Please select date and seats.");
        e.preventDefault();
    }
});
</script>

<?php require_once "./includes/footer.php"; ?>
