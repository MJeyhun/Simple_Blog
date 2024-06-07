<?php
session_start();

include_once "connecttodb.php";
$conn = new mysqli($server, $user, $password_db, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$blog_title = "";
$blog_description = "";
$blog_content = "";
$blog_image_path = "";
$blog_creator_username = "";


// Check if a specific blog ID is provided in the URL parameters
if (isset($_GET['blog_id'])) {
    $blog_id = $_GET['blog_id'];

    // Fetch the selected blog content from the database
    $sql_blog = "SELECT * FROM blogs WHERE id = ?";
    $stmt_blog = $conn->prepare($sql_blog);
    $stmt_blog->bind_param("i", $blog_id);
    $stmt_blog->execute();
    $result_blog = $stmt_blog->get_result();

    if ($result_blog->num_rows > 0) {
        // Fetch the blog content
        $blog = $result_blog->fetch_assoc();
        $blog_title = $blog['title'];
        $blog_description = $blog['descr'];
        $blog_content = $blog['cont'];
        $blog_image_path = $blog['image_path'];

        // Fetch the username of the blog creator
        $blog_creator_user_id = $blog['user_id'];
        $sql_username = "SELECT username FROM users WHERE id = ?";
        $stmt_username = $conn->prepare($sql_username);
        $stmt_username->bind_param("i", $blog_creator_user_id);
        $stmt_username->execute();
        $result_username = $stmt_username->get_result();

        if ($result_username->num_rows > 0) {
            $username_row = $result_username->fetch_assoc();
            $blog_creator_username = $username_row['username'];
        } else {
            $blog_creator_username = "Unknown"; 
        }
    } else {
        echo "Blog not found.";
        exit();
    }
} else {
    echo "No blog selected.";
    exit();
}

// Create comments table if not exists
$createCommentsTable = "CREATE TABLE IF NOT EXISTS comments (
        ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        comment TEXT DEFAULT NULL,
        date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        isDeleted TINYINT(1) DEFAULT 0,
        blog_id INT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (blog_id) REFERENCES blogs(id)
    )";
$conn->query($createCommentsTable);

// Create replies table if not exists
$createRepliesTable = "CREATE TABLE IF NOT EXISTS replies (
        ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        comment_id INT(11) NOT NULL,
        reply TEXT DEFAULT NULL,
        date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        isDeleted TINYINT(1) DEFAULT 0,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (comment_id) REFERENCES comments(ID)
    )";
$conn->query($createRepliesTable);

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql_user = "SELECT id FROM users WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
}

// Process comment/reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $issubmitted = true;

    // Check if "comment" key exists in $_POST array
    if (isset($_POST["comment"])) {
        $comment = htmlspecialchars($_POST["comment"]);
    } else {

        $errors[] = "Comment field is missing.";
    }
    //Serverside validation
    if (empty($comment)) {
        $errors[] = "Please enter a comment.";
    } elseif (strlen($comment) > 300) {
        $errors[] = "Comment must not exceed 300 characters.";
    }


    $comment_id = isset($_POST["comment_id"]) ? $_POST["comment_id"] : null;

    // Get the blog_id from the URL parameters
    $blog_id = $_GET['blog_id'];

    if (empty($errors)) {
        if ($comment_id !== null) {
            // Insert reply into replies table
            $sql = "INSERT INTO replies (user_id, comment_id, reply) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iis", $user_id, $comment_id, $comment);
        } else {
            // Insert comment into comments table
            $sql = "INSERT INTO comments (user_id, comment, blog_id) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isi", $user_id, $comment, $blog_id);
        }

        $stmt->execute();

        if ($stmt->error) {
            $errors[] = "Error: " . $stmt->error;
        }

        $stmt->close();

        if (empty($errors)) {
            header("Location: single-blog.php?blog_id=" . $blog_id); 
            exit();
        }
    }
}

// Delete comment
if (isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];

    $sql = "UPDATE comments SET isDeleted = 1 WHERE ID = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $comment_id, $user_id);
    $stmt->execute();


}

// Delete reply
if (isset($_POST['delete_reply'])) {
    $reply_id = $_POST['reply_id'];

    $sql = "UPDATE replies SET isDeleted = 1 WHERE ID = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $reply_id, $user_id);
    $stmt->execute();


}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="jemust">
    <link rel="stylesheet" href="./assets/styles/main.css">
    <title><?php echo $blog_title; ?></title>
    <style>
        textarea {
            resize: none;
        }

        .comment-container {
            margin-bottom: 20px;
        }

        .reply-container {
            margin-left: 30px;
        }
    </style>
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
        <section id="blog-head-section">
            <div class="blog-head">
                <h1 class="single-blog-title"><?php echo $blog_title; ?></h1>
                <img class="blog-image" src="<?php echo $blog_image_path; ?>" alt="">
                <p class="blog-author"><?php echo $blog_creator_username; ?></p>
                <p class="single-blog-description"><?php echo $blog_description; ?></p>
            </div>
        </section>
        <section id="blog-content-section">
            <div>
                <p class="text-blog">
                    <!-- Blog content -->
                    <?php echo $blog_content; ?>
                </p>
            </div>
        </section>
        <section id="comments-section">
            <div class="comments">
                <h2 class="comment-title">Comments</h2>
                <div class="send-comment">
                    <form action="single-blog.php?blog_id=<?php echo $blog_id; ?>" id="formReserve" method="post">
                        <textarea type="text" class="comment-input" name="comment" id="comment" placeholder="Comment" maxlength="300" required></textarea><br>
                        <input type="submit" class="send-button" name="Submit" id="submit" value="Submit">
                    </form>
                </div>
                <?php
                // Display comments
                $sql_comments = "SELECT comments.*, users.username FROM comments 
INNER JOIN users ON comments.user_id = users.id 
WHERE comments.blog_id = ? AND comments.isDeleted = 0 
ORDER BY comments.date DESC";
                $stmt_comments = $conn->prepare($sql_comments);
                $stmt_comments->bind_param("i", $blog_id);
                $stmt_comments->execute();
                $result_comments = $stmt_comments->get_result();

                if ($result_comments->num_rows > 0) {
                    while ($row = $result_comments->fetch_assoc()) {
                        echo '
<div class="comment-container">
<div class="single-comment">
<img class="comment-img" src="./assets/img/account.png" alt="profile photo">
<p class="comment-content">' . $row["comment"] . '</p>
<div class="username">' . $row["username"] . '</div>
<p class="date-p" style="font-size: 12px;">' . $row["date"] . '</p>';

                        // Add delete button for comment
                        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']) {
                            echo '
<form action="single-blog.php?blog_id=' . $blog_id . '" method="post">
<input type="hidden" name="delete_comment" value="1">
<input type="hidden" name="comment_id" value="' . $row['ID'] . '">
<button type="submit" class="delete-button" ">Delete</button>
</form>';
                        }

                        echo '
<p class="reply" onclick="toggleReplyForm(' . $row["ID"] . ')">Reply</p>
</div>
<!-- Reply Form -->
<div id="replyForm_' . $row["ID"] . '" style="display:none;">
<form action="single-blog.php?blog_id=' . $blog_id . '" method="post">
<input type="hidden" name="comment_id" value="' . $row["ID"] . '">
<textarea type="text" class="reply-input" name="comment" placeholder="Reply" maxlength="300" required></textarea><br>
<input type="submit" class="reply-button" name="Submit" value="Reply">
</form>
</div>';

                        // Display replies
                        $comment_id = $row["ID"];
                        $sql_replies = "SELECT replies.*, users.username FROM replies 
        INNER JOIN users ON replies.user_id = users.id 
        WHERE replies.comment_id = $comment_id AND replies.isDeleted = 0 
        ORDER BY replies.date ASC";
                        $result_replies = $conn->query($sql_replies);

                        if ($result_replies->num_rows > 0) {
                            echo '<div class="reply-container">';
                            while ($reply = $result_replies->fetch_assoc()) {
                                echo '
<div class="single-reply">
    <img class="comment-img" src="./assets/img/account.png" alt="profile photo">
    <p class="reply-content">' . $reply["reply"] . '</p>
    <p class="username">' . $reply["username"] . '</p>
    <p class="date-p" style= "font-size: 12px;">' . $reply["date"] . '</p>';

                                // Add delete button for reply
                                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $reply['user_id']) {
                                    echo '
    <form action="single-blog.php?blog_id=' . $blog_id . '" method="post">
        <input type="hidden" name="delete_reply" value="1">
        <input type="hidden" name="reply_id" value="' . $reply['ID'] . '">
        <button type="submit" class="delete-button" ">Delete</button>
    </form>';
                                }

                                echo '
</div>';
                            }
                            echo '</div>';
                        }

                        echo '</div>';
                    }
                } else {
                    echo "No comments yet.";
                }
                ?>
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
    <script>
        function toggleReplyForm(commentId) {
            var replyForm = document.getElementById('replyForm_' + commentId);
            if (replyForm.style.display === 'none') {
                replyForm.style.display = 'block';
            } else {
                replyForm.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var form = document.getElementById('formReserve');
            var commentInput = document.getElementById('comment');

            form.addEventListener('submit', function(event) {
                var commentValue = commentInput.value.trim();
                if (commentValue === '') {
                    event.preventDefault();
                } else if (commentValue.length > 300) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>

</html>

<?php
$stmt_blog->close();
$conn->close();
?>