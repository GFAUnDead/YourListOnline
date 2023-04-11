// Get the registration form element
const registrationForm = document.getElementById('registration-form');

// Add an event listener for when the form is submitted
registrationForm.addEventListener('submit', function(event) {
  // Prevent the default form submission behavior
  event.preventDefault();
  
  // Get the value of the username field
  const username = document.getElementById('username').value;
  
  // Get the value of the password field
  const password = document.getElementById('password').value;
  
  // Check if the registration is enabled
  if (registration_enabled) {
    // If registration is enabled, submit the form
    registrationForm.submit();
  } else {
    // If registration is disabled, display an error message
    const errorMessage = document.createElement('p');
    errorMessage.innerText = 'Registration is currently disabled.';
    registrationForm.appendChild(errorMessage);
  }
});
