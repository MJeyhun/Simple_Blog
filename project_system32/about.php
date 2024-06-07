<?php
session_start();
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
    <title>About</title><!-- Title of the page -->
    <link rel="stylesheet" href="./assets/styles/about.css">
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
        <h1>About System32</h1>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In faucibus aliquam massa, ut blandit orci lacinia
            sit amet. Integer non quam facilisis, condimentum augue eget, volutpat erat. Fusce justo nunc, lacinia nec
            nisl vitae, aliquet molestie dui. Aliquam vitae magna vitae odio gravida volutpat. Pellentesque pretium a
            eros in venenatis. Ut sodales ligula erat, ut tincidunt leo semper quis. Vivamus rhoncus fermentum erat.
            Pellentesque blandit eleifend lacus. Suspendisse blandit velit sit amet scelerisque elementum. Sed lobortis
            massa nibh, at lobortis ipsum tempus in. Quisque commodo molestie lacus, sed euismod nisi mollis ac.
            Praesent vulputate nulla eu ex dignissim, in gravida purus tincidunt. Praesent eu sem magna. Integer lacus
            diam, condimentum eget scelerisque vel, condimentum quis risus. Phasellus ac velit neque.</p>
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