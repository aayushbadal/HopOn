const bus = document.getElementById('bus');
const totalSeats = 33; // default 33
let selectedSeats = [];
const selectedSeatsInput = document.getElementById('selected-seats');
const totalPriceInput = document.getElementById('total-price');
const availableSeatsDisplay = document.getElementById('available-seats');

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

function updateBookingSummary() {
  selectedSeatsInput.value = selectedSeats.join(",");
  totalPriceInput.value = selectedSeats.length * busTicketPrice;

  // Update Available Seats dynamically
  const availableSeats = totalSeats - occupiedSeats.length - selectedSeats.length;
  if (availableSeatsDisplay) {
    availableSeatsDisplay.textContent = availableSeats;
  }

  // Optional: Update dynamic total price display on page
  const totalDisplay = document.querySelector('.total-price span');
  if (totalDisplay) {
    totalDisplay.textContent = "Rs. " + (selectedSeats.length * busTicketPrice);
  }
}
