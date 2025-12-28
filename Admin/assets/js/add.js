const AddIcon = document.getElementById('route-add');
const HiddenForm = document.getElementById('hidden-form');

// Dynamically pick the first input/select in the form
const firstField = HiddenForm.querySelector('input, select, textarea');

AddIcon.addEventListener('click', () => {
  HiddenForm.classList.toggle('show'); // toggle visibility
  if (HiddenForm.classList.contains('show') && firstField) {
    firstField.focus(); // focus on first input/select/textarea
  }
});
