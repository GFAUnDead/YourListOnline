// Get the registration error element
const registrationError = document.getElementById("registration-error");

// Check if registration is disabled
if (registration_enabled === false) {
  // If registration is disabled, display the error message
  registrationError.style.display = "block";
} else {
  // If registration is enabled, hide the error message
  registrationError.style.display = "none";
}
