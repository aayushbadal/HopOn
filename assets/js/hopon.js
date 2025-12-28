const bus = document.getElementById('bus');
const totalSeats = 33; // Example: 7 rows of 4 + 1 row of 5
let selectedSeats = [];

// Hidden inputs in booking.php
const selectedSeatsInput = document.getElementById('selected-seats');
const totalPriceInput = document.getElementById('total-price');

// busTicketPrice is passed from PHP
// const busTicketPrice = <?= $vehicle_list[0]['price']; ?>; 

// occupiedSeats is passed from PHP
// Example: const occupiedSeats = [2,5,28];

for (let i = 1; i <= totalSeats; i++) {
  const seat = document.createElement('div');
  seat.classList.add('seat');
  seat.innerHTML = `<i class="fas fa-chair"></i>`;
  seat.title = `Seat ${i}`;

  if (occupiedSeats.includes(i)) {
    seat.classList.add('occupied');
  }

  seat.addEventListener('click', () => {
    if (!seat.classList.contains('occupied')) {
      seat.classList.toggle('selected');
      if (seat.classList.contains('selected')) {
        selectedSeats.push(i);
      } else {
        selectedSeats = selectedSeats.filter(s => s !== i);
      }
      updateBookingSummary();
    }
  });

  bus.appendChild(seat);
}

// Update hidden inputs and dynamic total price display
function updateBookingSummary() {
  selectedSeatsInput.value = selectedSeats.join(",");

  // Update totalPrice input for display only
  // The PHP will recalculate final total when form is submitted
  totalPriceInput.value = selectedSeats.length * busTicketPrice;

  // Optional: Update dynamic display on page
  const totalDisplay = document.querySelector('.total-price span');
  if (totalDisplay) {
    totalDisplay.textContent = "Rs. " + (selectedSeats.length * busTicketPrice);
  }
}
