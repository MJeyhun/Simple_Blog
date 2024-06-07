<?php 
session_start();
$user_id = $_SESSION["user_id"];

// Enable displaying errors in the browser (for debugging purposes)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Function to sanitize a string for use in file names
function sanitizeFileName($string) {
    // Remove special characters except underscores and hyphens
    $string = preg_replace('/[^\w\-\.]/', '_', $string);
    // Remove multiple underscores or hyphens
    $string = preg_replace('/[\-\_]+/', '_', $string);
    // Remove leading and trailing underscores or hyphens
    $string = trim($string, '_-');
    return $string;
}

// Connecting to the DB 
include_once "connecttodb.php";
$link = mysqli_connect($server, $user, $password_db, $database);
if (!$link) die("Connection to DB failed: " . mysqli_connect_error());

// Checking the input
if (!isset($_POST["submitPost"])) {
    exit("Not submitted!");
}

if (!isset($_POST['title'])) {
    exit('No title');
}

if (!isset($_POST['content'])) {
    exit('No content');
}

// Function to limit input length
function limitInputLength($input, $maxLength) {
    // Truncate input if it exceeds the maximum length
    if (strlen($input) > $maxLength) {
        $input = substr($input, 0, $maxLength);
    }
    return $input;
}

$title_trimmed = limitInputLength($_POST['title'], 70);
$desc_trimmed = isset($_POST["description"]) ? limitInputLength($_POST['description'], 90) : '';


// Sanitize and prepare blog data
$title = mysqli_real_escape_string($link, $title_trimmed);
$desc = mysqli_real_escape_string($link, $desc_trimmed);
$blog = mysqli_real_escape_string($link, $_POST["content"]);
$condensedDesc = str_replace("\r\n\r\n"," |||| ", $desc);
$condensedBlog = str_replace("\r\n\r\n", " |||| ", $blog);
$date = date('Ymd_His');
$title_sanitized = sanitizeFileName($title); // Sanitize the title for use in file names

// Actions with the database
$create_table = "CREATE TABLE IF NOT EXISTS blogs (
    ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(70) NOT NULL,
    descr VARCHAR(90),
    cont TEXT NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

$result = mysqli_query($link, $create_table);
if (!$result) {
    die("Error creating table: " . mysqli_error($link));
}

// File upload handling
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_type = $_FILES['image']['type'];
    $image_name = $title_sanitized . "_" . $date . "_" . $_FILES['image']['name'];

    // Check if the uploaded file is an image
    $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
    if (in_array($image_type, $allowed_types)) {
        // Move the uploaded file to the desired location
        $upload_dir = "./assets/img/Blogs/";
        $upload_path = $upload_dir . $image_name;
        if (move_uploaded_file($image_tmp, $upload_path)) {
            echo "Image uploaded successfully!";
            // You can save $upload_path in your database if needed

            $append_blog = "INSERT INTO blogs (user_id, title, descr, cont, image_path) VALUES ('$user_id', '$title', '$condensedDesc', '$condensedBlog', '$upload_path')";

            $result = mysqli_query($link, $append_blog);
            if (!$result) {
                die("Error inserting data: " . mysqli_error($link));
            }
            mysqli_close($link);

            echo "Check the DB";

            header('Location: blogs.php');
            exit();

        } else {
            echo "Error uploading image!" . '\n';
            echo "Upload directory: " . $upload_dir . "<br>";
            echo "Image name: " . $image_name . "<br>";
            echo "Upload path: " . $upload_path . "<br>";

        }
    } else {
        echo "Invalid file format! Please upload an image.";
    }
}
?>