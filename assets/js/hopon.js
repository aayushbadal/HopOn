const bus = document.getElementById('bus');
const totalSeats = 33; // 7 rows of 4 + 1 row of 5
let selectedSeats = [];

// occupiedSeats is already from PHP
// Example for testing: const occupiedSeats = [2, 5, 28];

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
}
