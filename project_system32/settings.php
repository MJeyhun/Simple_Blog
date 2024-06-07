<?php
session_start();

// Include your database connection file
include_once 'connecttodb.php';
$conn = mysqli_connect($server, $user, $password_db, $database);
// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
  // Fetch user's email from the database based on user id
  $user_id = $_SESSION['user_id'];
  $query = "SELECT email FROM users WHERE id = $user_id";
  $result = mysqli_query($conn, $query);
  $user_data = mysqli_fetch_assoc($result);
  $user_email = $user_data['email'];
} else {
  header("Location: login.php");
  exit(); // Make sure to exit after redirection
}

$error_current = '';
$error_new = '';
$password_regex = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()\-_=+{};:,<.>])(?=.*[^\w\d\s]).{8,100}$/';

function validatePassword($password)
{
  global $password_regex;
  return preg_match($password_regex, $password);
}

// Handle form submission
if (isset($_POST['submit'])) {
  // Get form data
  $current_password = $_POST['current'];
  $new_password = $_POST['new'];

  // Validations
  $error_current = empty($current_password) ? "Please enter your current password." : '';
  $error_new = validatePassword($new_password) ? '' : "Password must be at least 8 characters long and equal or less than 100 characters and contain at least one uppercase letter, one lowercase letter, one digit and one special character";

  if (empty($error_current) && empty($error_new)) {
    // Fetch user's current password from the database
    $user_id = $_SESSION['user_id'];
    $query = "SELECT password FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $query);
    $user_data = mysqli_fetch_assoc($result);
    $current_password_db = $user_data['password'];

    // Verify if the current password matches the one in the database
    if (password_verify($current_password, $current_password_db)) {
      // Hash and update the new password
      $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
      $update_query = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
      mysqli_query($conn, $update_query);
      $success_message = "Password updated successfully.";
    } else {
      $error_current = "Current password is incorrect.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="author" content="System32" />
  <link rel="stylesheet" href="./assets/styles/settings.css" />
  <title>Settings</title>
  <script src="./assets/js/main.js" defer></script>
  <script>
        document.addEventListener("DOMContentLoaded", function () {
            const menuIcon = document.querySelector(".menu-for-nav");
            const headerNav = document.querySelector(".header-navbar");

            menuIcon.addEventListener("click", function () {
                headerNav.classList.toggle("open");
            });
        });
  </script>
</head>
<body>
    <header>
        <img class="menu-for-nav" src="./assets/img/menu.png" alt="menu">
        <img id="logo-header" class="img" src="./assets/img/Skynet_Terminator_logo_1.png" alt="Logo">
        <nav class="header-navbar">
            <ul class="pages">
                <li class="page"><a href="index.php">Home</a></li>
                <li class="page"><a href="blogs.php">Blogs</a></li>
                <li class="page"><a href="about.php">About</a></li>
            </ul>
        </nav>
        <?php if(isset($_SESSION['user_id'])): ?>
            <button id="createButton" class="create-button btn">Create</button>
            <form method="post" action="logout.php" id="logoutForm">  
                <button type="submit" id="logoutButton" class="create-button log-out-button btn">Log out</button>
            </form>
            <div class="account-logo"><img class="logo-pfp" id="accountLogo" src="./assets/img/account.png" alt="Account logo"></div>
        <?php else: ?>
            <button class="signup-button btn" id="signUpButton">Sign up</button>
        <?php endif; ?>
    </header>
  <main>
    <section id="settings-section">
      <div class="center">
        <div class="settings-content">
          <input type="radio" name="slider" checked id="home" />
          <input type="radio" name="slider" id="blog" />

          <div class="list">
            <label for="home" class="home">
              <i class="fas fa-home"></i>
              <span class="title">Reset password</span>
            </label>
            <label for="blog" class="blog">
              <span class="title">Information</span>
            </label>
            <div class="slider"></div>
          </div>
          <div class="text-content">
            <div class="home text">
              <div class="title">Reset Password</div>
              <?php if (isset($success_message)) : ?>
                <p style="color: green;"><?php echo $success_message; ?></p>
              <?php endif; ?>
              <form method="post" action="settings.php" id="resetPasswordForm">
                <label for="current">Current Password:</label>
                <input type="password" id="current" name="current" />
                <br>
                <?php if ($error_current) : ?>
                  <span class="error" style="color: red;"><?php echo $error_current; ?></span><br>
                <?php endif; ?>
                <br>
                <label for="new">New Password:</label>
                <input type="password" id="new" name="new" />
                <br>
                <?php if ($error_new) : ?>
                  <span class="error" style="color: red;"><?php echo $error_new; ?></span>
                <?php endif; ?>
                <div class="button">
                  <input type="submit" id="button" name="submit" value="Change Password">
                </div>
              </form>
            </div>
            <div class="blog text">
              <div class="title">Personal Information</div>
              <p>Username: <?php echo $_SESSION['username']; ?></p>
              <p>Mail: <?php echo $user_email; ?></p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <footer>
        <img class="logo-footer" src="./assets/img/Skynet_Terminator_logo_1.png" alt="">
        <div class="navbar">
            <nav class="footer-navbar">
                <ul class="pages">
                    <li class="page"><a href="index.php">Home</a></li>
                    <li class="page"><a href="blogs.php">Blogs</a></li>
                    <li class="page"><a href="about.php">About</a></li>
                </ul>
            </nav>
        </div>
        <div class="social-medias">
            <ul class="social-media-list">
                <li class="single-media" id="faceli"><img src="./assets/img/socialfacebook.png" id="facebook"></li>
                <li class="single-media"><img src="./assets/img/socialinstagram.png" id="instagram"></li>
                <li class="single-media"><img src="./assets/img/socialtelegram.png" id="telegram"></li>
            </ul>
        </div>
    </footer>
</body>

</html>