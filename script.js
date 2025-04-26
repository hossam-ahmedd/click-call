function validateStep1() {
  const form = document.forms["signup-step1"];
  const password = form["password"].value;
  const confirmPassword = form["confirmPassword"].value;
  const errorDiv = document.getElementById("passwordError");

  if (password !== confirmPassword) {
    errorDiv.textContent = "Passwords do not match. Please confirm correctly.";
    errorDiv.style.color = "red";
    return false;
  }

  errorDiv.textContent = ""; // Clear message if correct
  return true;
}
