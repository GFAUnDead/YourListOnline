if (registration_enabled) {
  // Registration is enabled, do nothing.
} else {
  // Registration is disabled, display error message.
  var errorDiv = document.getElementById("error");
  errorDiv.innerHTML = "Registration is currently disabled.";
}
