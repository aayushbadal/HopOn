const searchIcon = document.getElementById('search-icon');
const searchBar = document.getElementById('searchbar');
const startPoint = document.getElementById('start-point'); // first input field

searchIcon.addEventListener('click', () => {
  searchBar.classList.toggle('show'); // toggle visibility
  if (searchBar.classList.contains('show')) {
    startPoint.focus(); // focus on first input when shown
  }
});
