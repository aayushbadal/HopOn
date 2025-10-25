// Theme change:

const toggleBtn = document.getElementById("mode-toggle");
const body = document.body;

  // Check saved preference
  if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark");
    toggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
  }

  toggleBtn.addEventListener("click", () => {
    body.classList.toggle("dark");

    if (body.classList.contains("dark")) {
      toggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
      localStorage.setItem("theme", "dark");
    } else {
      toggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
      localStorage.setItem("theme", "light");
    }
  });
