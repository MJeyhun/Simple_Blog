<?php
session_start();

// Check if user_id is not set, then redirect to another page or show an error
if (!isset($_SESSION['user_id'])) {
    // Redirect to another page
    header("Location: login.php");
    exit(); // Make sure to exit after redirection
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"><!-- Encoding to UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Configures the viewport for responsive design -->
    <!-- Authors -->
    <meta name="author" content="saakba">
    <meta name="author" content="jemust">
    <meta name="author" content="vusaya">
    <title>Create Blog</title><!-- Title of the page -->
    <link rel="stylesheet" href="./assets/styles/create_blog.css">
    <script src="./assets/js/main.js" defer></script>
    <script>
        // Function to sanitize input values to prevent XSS and SQL injection
        function sanitizeInputs() {
            // Get the input elements
            var titleInput = document.getElementById('title');
            var descriptionInput = document.getElementById('description');
            var contentInput = document.querySelector('.content');

            // Sanitize title input
            var sanitizedTitle = titleInput.value.trim(); // Trim whitespace
            sanitizedTitle = sanitizedTitle.replace(/</g, '&lt;').replace(/>/g, '&gt;'); // Replace < and > with HTML entities
            titleInput.value = sanitizedTitle;

            // Sanitize description input
            var sanitizedDescription = descriptionInput.value.trim(); // Trim whitespace
            sanitizedDescription = sanitizedDescription.replace(/</g, '&lt;').replace(/>/g, '&gt;'); // Replace < and > with HTML entities
            descriptionInput.value = sanitizedDescription;

            // Sanitize content input
            var sanitizedContent = contentInput.value.trim(); // Trim whitespace
            sanitizedContent = sanitizedContent.replace(/</g, '&lt;').replace(/>/g, '&gt;'); // Replace < and > with HTML entities
            contentInput.value = sanitizedContent;
        }

        // Add an event listener to the form submission
        document.querySelector('form').addEventListener('submit', function (event) {
            // Call the sanitizeInputs function before submitting the form
            sanitizeInputs();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const menuIcon = document.querySelector(".menu-for-nav");
            const headerNav = document.querySelector(".header-navbar");
            
            // Get the input elements for title and description
            const titleInput = document.getElementById('title');
            const descriptionInput = document.getElementById('description');

            menuIcon.addEventListener("click", function () {
                headerNav.classList.toggle("open");
            });

            // Add event listener for title input
            titleInput.addEventListener('input', function () {
                // Limit title input to 70 characters
                if (titleInput.value.length > 70) {
                    titleInput.value = titleInput.value.slice(0, 70);
                }
            });

            // Add event listener for description input
            descriptionInput.addEventListener('input', function () {
                // Limit description input to 90 characters
                if (descriptionInput.value.length > 90) {
                    descriptionInput.value = descriptionInput.value.slice(0, 90);
                }
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
        <div class="create">
            <h1 class="post-title">Create a Blog Post</h2>
                <form action="sqlcreate_blog.php" method="post" enctype="multipart/form-data">
                    <input type="text" id="title" name="title" placeholder="Enter title..." maxlength="70" required>
                    <textarea type="text" id="description" name="description" maxlength="90" rows="1" cols="100"
                        placeholder="Enter description..."></textarea>
                    <textarea name="content" class="content" placeholder="Enter your blog content..." rows="30"
                        cols="100" required></textarea>
                    <input type="file" name="image" id="image" required>
                    <button name="submitPost" type="submit" class="submit-btn">Submit</button>
                </form>
        </div>
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