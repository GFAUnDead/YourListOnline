const registrationEnabled = false; // Replace this with the actual PHP code that checks whether registration is enabled or not

if (!registrationEnabled) {
  const errorMessage = document.getElementById('error-message');
  errorMessage.textContent = 'Registration is currently disabled.';
  errorMessage.style.color = 'red';
}
