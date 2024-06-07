
const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()\-_=+{};:,<.>])(?=.*[^\w\d\s]).{8,}$/;
const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
  
document.addEventListener('DOMContentLoaded', function() {
    function validateLoginForm() {
      var username = document.getElementById('username').value.trim();
      var password = document.getElementById('password').value.trim();

      // Get the error container (create it if it doesn't exist)
      var errorContainer = document.getElementById('error-messages');
      if (!errorContainer) {
        errorContainer = document.createElement('div');
        errorContainer.id = 'error-messages';
        document.getElementById('loginForm').insertBefore(errorContainer, document.getElementById('loginForm').firstChild);
      }

      // Clear any existing error messages (optional)
      errorContainer.innerHTML = '';

      // Validation checks
      if (username === '') {
        displayErrorMessage(errorContainer, 'Please enter your username.');
        return false;
      }

      if (password === '') {
        displayErrorMessage(errorContainer, 'Please enter your password.');
        return false;
      }

      if (!usernameRegex.test(username)) {
        displayErrorMessage(errorContainer, 'Incorrect username or password.');
        return false;
      }

      if (!passwordRegex.test(password)) {
        displayErrorMessage(errorContainer, 'Incorrect username or password.');
        return false;
      }

      return true; // If all validations pass, return true to allow submission
    }

    function displayErrorMessage(container, message) {
      var errorMessage = document.createElement('span');
      errorMessage.classList.add('error-message'); // Add a class for styling
      errorMessage.textContent = message;
      container.appendChild(errorMessage);

      // Add red color to the error container (optional)
      container.style.color = 'red';
    }

    // Submission event listener
    document.getElementById('loginForm').addEventListener('submit', function(event) {
      // Validate the form
      if (!validateLoginForm()) {
        // If validation fails, prevent form submission
        event.preventDefault();
      }
    });
});