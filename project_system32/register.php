<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
class User {
    public $id;
    public $username;
    public $email;
    public $password;

    function __construct($username, $email, $password) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }
}

$error_username = '';
$error_password = '';
$error_repassword = '';
$error_email = '';
$password_regex = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()\-_=+{};:,<.>])(?=.*[^\w\d\s]).{8,100}$/';
$username_regex = '/^[a-zA-Z0-9_]{3,20}$/';

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePassword($password) {
    global $password_regex;
    return preg_match($password_regex, $password);
}

function validateUsername($username) {
    global $username_regex;
    return preg_match($username_regex, $username);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitRegisterForm'])) {
    // Retrieve form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);

    // Validations
    $error_username = validateUsername($username) ? '' : "Username must be between 3 and 20 characters long and may only contain letters, numbers, and underscores.";
    $error_password = validatePassword($password) ? '' : "Password must be at least 8 characters long and equal or less than 100 characters and contain at least one uppercase letter, one lowercase letter, one digit and one special character";
    $error_repassword = ($password == $repassword) ? '' : "Password and re-password do not match!";
    $error_email = validateEmail($email) ? '' : "Please enter a valid email address!";

    if (!$error_password && !$error_repassword && !$error_username && !$error_email) {
        // Database connection parameters
        include_once "connecttodb.php";

        try {
            // Create connection using PDO
            $conn = new PDO("mysql:host=$server", $user, $password_db);
            // Set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Switch to the database
            $conn->exec("USE $database");

            // Check if the users table exists, if not, create it
            $stmt_check_table = $conn->query("SHOW TABLES LIKE 'users'");
            $table_exists = $stmt_check_table->fetchColumn();

            if (!$table_exists) {
                $conn->exec("CREATE TABLE IF NOT EXISTS users (
                            id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
                            username VARCHAR(255) NOT NULL,
                            email VARCHAR(255) NOT NULL,
                            password VARCHAR(255) NOT NULL
                        )");
            }

            // Check if username or email already exists
            $stmt_check_username = $conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt_check_username->bindParam(':username', $username);
            $stmt_check_username->execute();
            $result_username = $stmt_check_username->fetch(PDO::FETCH_ASSOC);

            $stmt_check_email = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt_check_email->bindParam(':email', $email);
            $stmt_check_email->execute();
            $result_email = $stmt_check_email->fetch(PDO::FETCH_ASSOC);

            if ($result_username) {
                $error_username .= "Username already exists.";
            }

            if ($result_email) {
                $error_email .= "Email already exists.";
            }

            if (!$error_username && !$error_email) {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert user data into the database
                $stmt_insert = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                $stmt_insert->bindParam(':username', $username);
                $stmt_insert->bindParam(':email', $email);
                $stmt_insert->bindParam(':password', $hashed_password);
                $stmt_insert->execute();

                // Redirect the user to the login page
                header("Location: login.php");
                exit();
            }
        } catch (PDOException $e) {
            // Log connection error to console
            error_log("Connection failed: " . $e->getMessage());
            // Set generic error message
            $error_username = "An unexpected error occurred. Please try again later.";
        }

        // Close connection
        $conn = null;
    }
}
?>
<!DOCTYPE html>
<html lang ="en">

<head>
    <meta charset="utf-8">
    <meta name="description" content="Login and Register Page">
    <meta name="author" content="System32">
    <link rel="stylesheet" href="assets/styles/register_style.css">
    <script src="./assets/js/register.js" defer></script>
    <title>Register</title>
</head>
<body>
    <div class="center">
    <div class="home">
          <a href="index.php">Home</a>
        </div>
        <div class="homeimg"> 
          <a href="index.php" class="img"><img src="./assets/img/salamlar.png"></a>
        </div>
        <h1>Create Account</h1>
        <form method="post" action="register.php" id="registerForm">
            <div class="text_field"> 
                <p>Username</p>
                <input type="text" id="username" name="username" placeholder="Enter your username" required pattern="[a-zA-Z0-9_]{3,20}">
                <span id="error_username" style="color: red;"></span>
                <?php if ($error_username && empty($error_username)): ?>
                    <span style="color: red;"><?php echo $error_username; ?></span>
                <?php endif; ?>
            </div>
            <div class="text_field"> 
                <p>Email</p>
                <input type="text" id="email" name="email" placeholder="Enter your email" required >
                <span id="error_email" style="color: red;"></span>
                <?php if ($error_email && empty($error_email)): ?>
                    <span style="color: red;"><?php echo $error_email; ?></span>
                <?php endif; ?>
            </div>
            <div class="text_field"> 
                <p>Password</p>
                <input type="password" id="password"  name="password" placeholder ="Enter your password" required >
                <span id="error_password" style="color: red;"></span>
                <?php if ($error_password && empty($error_password)): ?>
                    <span style="color: red;"><?php echo $error_password; ?></span>
                <?php endif; ?>
            </div>
            <div class="text_field"> 
                <p>Re-enter Password</p>
                <input type="Password" id="repassword" name="repassword" placeholder ="Re-enter your password" required >
                <span id="error_repassword" style="color: red;"></span>
                <?php if ($error_repassword && empty($error_repassword)): ?>
                    <span style="color: red;"><?php echo $error_repassword; ?></span>
                <?php endif; ?>
            </div>
            <input type="submit" value="Register" name="submitRegisterForm" id="submitRegisterForm">
            <div class="Register">
                <label>Already Have An Account? </label> <a href="login.php">Login</a>
            </div>
        </form>
    </div>
</body>

