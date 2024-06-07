<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="System32">
    <link rel="stylesheet" href="./assets/styles/main.css">
    <title>Home</title>
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
        <section id="intro">
            <div class="intro-div">
            <?php if(isset($_SESSION['user_id'])): ?>
                <h1 class="intro-header">Welcome, <?php echo $_SESSION['username']; ?>!</h1>
            <?php else: ?>
                <h1 class="intro-header">Welcome!</h1>
            <?php endif; ?>
                
                <p class="intro-text">Lorem</p>
                <button id="startReadingBtn" class="start-reading btn">Start reading</button>
            </div>
        </section>
        <section id="recent-blogs">
            <div class="section-title">
                <h2 class="blogs-title">Recent Blog Posts</h2>
            </div>
            <div class="blogs">
            <?php
                include_once "connecttodb.php";
                $link = mysqli_connect($server, $user, $password_db, $database);
                
                // Check for database connection errors
                if (!$link) {
                    die("Connection to DB failed: " . mysqli_connect_error());
                }
                
                // Fetch two random blog entries
                $query = "SELECT id, title, descr, image_path FROM blogs ORDER BY RAND() LIMIT 4";
                $result = mysqli_query($link, $query);
                
                // Check if any blog entries found
                if ($result && mysqli_num_rows($result) > 0) {
                    // Display two random blog entries
                    while ($row = mysqli_fetch_assoc($result)) {
                        displayBlogEntry($row);
                    }
                } else {
                    echo "<p>There are no blog entries.</p>";
                }
                
                // Closing the connection
                mysqli_close($link);
                
                function displayBlogEntry($row)
                {
                    $blog_id = $row["id"];
                    $title = $row["title"];
                    $description = $row["descr"];
                    $image_path = $row["image_path"];
                
                    // Trim description to maximum 90 characters and append "..." if longer
                    if (strlen($description) > 90) {
                        $description = substr($description, 0, 90) . "...";
                    }
                
                    // Generate HTML for the blog entry
                    echo '<div class="single-blog">';
                    echo "<img  src=\"$image_path\" alt=\"Blog Image\">";
                    echo "<h2 class='blog-title'><a href='single-blog.php?blog_id=$blog_id'>$title</a></h2>";
                    echo "<p class='blog-description'>$description</p>";
                    echo '</div>';
                
                }
                ?>


            </div>
            <div class="read-more">
                <button id="readMore" class="readmore-button btn">Read More</button>
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
