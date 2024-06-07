document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("registerForm").addEventListener("submit", function(event) {
        var username = document.getElementById("username").value;
        var email = document.getElementById("email").value;
        var password = document.getElementById("password").value;
        var repassword = document.getElementById("repassword").value;

        var error_username = document.getElementById("error_username");
        var error_email = document.getElementById("error_email");
        var error_password = document.getElementById("error_password");
        var error_repassword = document.getElementById("error_repassword");

        var password_regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()\[\]\-_=+{};:,.])[\w\d!@#$%^&*()\[\]\-_=+{};:,.]{8,100}$/;
        var username_regex = /^[a-zA-Z0-9_]{3,20}$/;

        var valid = true;

        error_username.innerHTML = "";
        error_email.innerHTML = "";
        error_password.innerHTML = "";
        error_repassword.innerHTML = "";

        if (!username.match(username_regex) || username.length > 20 || username.length < 3) {
            error_username.innerHTML = "Username must be between 3 and 20 characters long and may only contain letters, numbers, and underscores.";
            valid = false;
        }

        if (!email.includes("@") || !email.includes(".")) {
            error_email.innerHTML = "Please enter a valid email address.";
            valid = false;
        }

        if (!password.match(password_regex) || password.length < 8) {
            error_password.innerHTML = "Password must be at least 8 characters long and equal or less than 100 characters and contain at least one uppercase letter, one lowercase letter, one digit and one special character";
            valid = false;
        }

        if (password !== repassword) {
            error_repassword.innerHTML = "Password and re-password do not match!";
            valid = false;
        }
        if (!valid) {
            event.preventDefault();
        }
    });
});