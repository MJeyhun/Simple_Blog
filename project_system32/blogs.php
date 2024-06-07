<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="saakba">
    <meta name="author" content="jemust">
    <meta name="author" content="vusaya">
    <title>All Blogs</title>
    <link rel="stylesheet" href="./assets/styles/blogs.css">
    <script src="./assets/js/main.js" defer></script>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const blogContainer = document.querySelector(".blog-container");
    const blogItems = document.querySelectorAll(".blog-item");
    const itemsPerPage = 6;
    const numPages = Math.ceil(blogItems.length / itemsPerPage);
    let currentPage = 1;

    function showPage(page) {
        const startIndex = (page - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        blogItems.forEach((item, index) => {
            if (index >= startIndex && index < endIndex) {
                item.style.display = "block";
            } else {
                item.style.display = "none";
            }
        });
        updatePagination(); // Update pagination to highlight the current page
    }

    function updatePagination() {
        const pagination = document.querySelector(".pagination");
        pagination.innerHTML = "";

        if (numPages > 1) {
            const prevPageButton = document.createElement("div");
            prevPageButton.classList.add("prev-page");
            prevPageButton.innerHTML = `<span class="page-num">«</span><span class="page-text">Previous</span>`;
            prevPageButton.addEventListener("click", () => {
                if (currentPage > 1) {
                    currentPage--;
                    showPage(currentPage);
                }
            });
            pagination.appendChild(prevPageButton);

            const pageNumbers = document.createElement("div");
            pageNumbers.classList.add("page-numbers");
            for (let i = 1; i <= numPages; i++) {
                const pageNumber = document.createElement("span");
                pageNumber.classList.add("page-num", "pn");
                pageNumber.textContent = i;
                if (i === currentPage) { // Highlight the current page
                    pageNumber.classList.add("active");
                }
                pageNumber.addEventListener("click", () => {
                    currentPage = i;
                    showPage(currentPage);
                });
                pageNumbers.appendChild(pageNumber);
            }
            pagination.appendChild(pageNumbers);

            const nextPageButton = document.createElement("div");
            nextPageButton.classList.add("next-page");
            nextPageButton.innerHTML = `<span class="page-text">Next</span><span class="page-num">»</span>`;
            nextPageButton.addEventListener("click", () => {
                if (currentPage < numPages) {
                    currentPage++;
                    showPage(currentPage);
                }
            });
            pagination.appendChild(nextPageButton);
        }
    }

    showPage(currentPage);
    updatePagination();
});


    </script>

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
        <form action="" method="post">
            <div class="search-container">
                <input name="search_query" id="searchbar_itself" type="text" placeholder="Search">
                <button type="submit" name="search_submit" id="search_submit">Search</button>
            </div>
        </form>
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
        <div class="latest-blogs">
            <h1>Latest Blogs</h1>
            <div class="blog-container">
                <?php
                include_once "connecttodb.php";
                $link = mysqli_connect($server, $user, $password_db, $database);

                // Check for database connection errors
                if (!$link) {
                    die("Connection to DB failed: " . mysqli_connect_error());
                }

                if (isset($_POST['search_submit'])) {
                    $search_query = $_POST['search_query'];

                    // Prepare the SQL statement
                    $query = "SELECT id, title, descr, image_path FROM blogs WHERE title LIKE ? OR descr LIKE ?";
                    $stmt = mysqli_prepare($link, $query);

                    // Bind parameters
                    mysqli_stmt_bind_param($stmt, "ss", $search_query_like, $search_query_like);

                    // Set search query with wildcards
                    $search_query_like = '%' . $search_query . '%';

                    // Execute the prepared statement
                    mysqli_stmt_execute($stmt);

                    // Get result
                    $result = mysqli_stmt_get_result($stmt);

                    if ($result && mysqli_num_rows($result) > 0) {
                        // Fetching data and displaying search results
                        while ($row = mysqli_fetch_assoc($result)) {
                            displayBlogEntry($row);
                        }
                    } else {
                        echo "<p>No search results with '" . htmlspecialchars($search_query) . "' found.</p>";
                    }
                    // Close statement
                    mysqli_stmt_close($stmt);
                } else {
                    // Display all blog entries if search not submitted
                    displayAllBlogEntries();
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
                    echo '<div class="blog-item">';
                    echo '<div class="image-container">';
                    echo "<img src=\"" . htmlspecialchars($image_path) . "\" alt=\"Blog Image\">";
                    echo '</div>';
                    echo '<div class="blog-details">';
                    echo "<a href='single-blog.php?blog_id=$blog_id'>";
                    echo "<h2>" . htmlspecialchars($title) . "</h2>";
                    echo "<p>" . htmlspecialchars($description) . "</p>";
                    echo '</a>';
                    echo '</div>';
                    echo '</div>';
                }

                function displayAllBlogEntries()
                {
                    global $link;

                    // Query to select all blog entries from the database
                    $query = "SELECT id, title, descr, image_path FROM blogs";
                    $result = mysqli_query($link, $query);

                    // Check if any blog entries found
                    if ($result && mysqli_num_rows($result) > 0) {
                        // Fetching data and displaying blog entries
                        while ($row = mysqli_fetch_assoc($result)) {
                            displayBlogEntry($row);
                        }
                    } else {
                        echo "No blog entries found.";
                    }
                }
                ?>

            </div>
        </div>
        <div class="pagination">
        </div><!--Here-->
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