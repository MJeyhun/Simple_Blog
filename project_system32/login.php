<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitLoginForm'])) {
  // Retrieve login credentials
  $username = $_POST['username'];
  $password = trim($_POST['password']);

  // Database connection parameters
  include_once "connecttodb.php";
  // Create connection using PDO
  try {
    $conn = new PDO("mysql:host=$server;dbname=$database", $user, $password_db);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare SQL statement with parameterized query to fetch user data
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // Check if user exists
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      // Verify password
      if (password_verify($password, $row['password'])) {
        // Password is correct, redirect to index.php
        session_regenerate_id(true);
        // Store user data in session
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        header("Location: index.php");
        exit();
      } else {
        // Password is incorrect
        $login_error = "Incorrect username or password.";
      }
    } else {
      // Username doesn't exist
      $login_error = "Incorrect username or password.";
    }
  } catch (PDOException $e) {
    // Log connection error to console
    error_log("Connection failed: " . $e->getMessage());
    // Set generic error message
    $login_error = "An unexpected error occurred. Please try again later.";
  }

  // Close connection
  $conn = null;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Login and Register Page" />
  <meta name="author" content="System32" />
  <link rel="stylesheet" href="./assets/styles/login_style.css" />
  <script src="./assets/js/login.js" defer></script>
  <title>Login</title>
</head>

<body>
  <div class="center">
    <div class="home">
      <a href="index.php">Home</a>
    </div>
    <div class="homeimg">
      <a href="index.php" class="img"><img src="./assets/img/salamlar.png"></a>
    </div>
    <h1>Login to you account</h1>
    <form method="post" action="login.php" id="loginForm">
      <div class="text_field">
        <p>Username</p>
        <input type="text" id="username" name="username" placeholder="Enter your username" required />
        <?php if ($login_error) : ?><span style="color: red"><?php echo $login_error; ?></span><?php endif; ?>
      </div>
      <div class="text_field">
        <p>Password</p>
        <input type="password" id="password" name="password" placeholder="Enter your password" required />
        <?php if ($login_error) : ?><span style="color: red"><?php echo $login_error; ?></span><?php endif; ?>
      </div>
      <div class="button">
        <input type="submit" value="Login" name="submitLoginForm" id="submitLoginForm" />
      </div>
      <div class="Register">
        <label>Don't Have An Account? <a href="register.php">Register</a></label>
      </div>
    </form>
  </div>

</body>

</html>