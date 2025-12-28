const AddIcon = document.getElementById('route-add');
const HiddenForm = document.getElementById('hidden-form');
const startPoint = document.getElementById('start-point'); // first input field

AddIcon.addEventListener('click', () => {
  HiddenForm.classList.toggle('show'); // toggle visibility
  if (HiddenForm.classList.contains('show')) {
    startPoint.focus(); // focus on first input when shown
  }
});
