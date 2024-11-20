<?php
// Start the session
session_start();

// Check if the user is logged in by checking session variables
if (!isset($_SESSION['user_info'])) {
    header("Location: ../login.php");
    exit();
}

// Pull user's name and type from the session array
$username = $_SESSION['user_info']['email']; // Assuming 'username' is stored in the array
$usertype = $_SESSION['user_info']['user_type']; // Assuming 'user_type' is stored in the array

include '../db.php'; // Adjusted to match your db connection file name

// Fetch posts from the database with user names
$query = $conn->prepare("
    SELECT 
        posts.user_id, 
        posts.post_content, 
        posts.post_date, 
        CONCAT(users.first_name, ' ', users.last_name) AS user_name 
    FROM posts 
    JOIN users ON posts.user_id = users.user_id 
    ORDER BY posts.post_date DESC
");
$query->execute();
$result = $query->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);


// Handle like action
if (isset($_POST['action']) && $_POST['action'] == 'like') {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_info']['user_id'];

    // Check if the user has already liked the post
    $query = $conn->prepare("SELECT * FROM likes WHERE post_id = ? AND user_id = ?");
    $query->bind_param("ii", $post_id, $user_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows == 0) {
        // Insert new like
        $query = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
        $query->bind_param("ii", $post_id, $user_id);
        $query->execute();
    } else {
        // Remove like
        $query = $conn->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
        $query->bind_param("ii", $post_id, $user_id);
        $query->execute();
    }

    // Return updated like count
    $query = $conn->prepare("SELECT COUNT(*) AS like_count FROM likes WHERE post_id = ?");
    $query->bind_param("i", $post_id);
    $query->execute();
    $result = $query->get_result();
    $like_count = $result->fetch_assoc()['like_count'];
    echo json_encode(['like_count' => $like_count]);
}

// Handle save action
if (isset($_POST['action']) && $_POST['action'] == 'save') {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_info']['user_id'];

    // Check if the user has already saved the post
    $query = $conn->prepare("SELECT * FROM saved_posts WHERE post_id = ? AND user_id = ?");
    $query->bind_param("ii", $post_id, $user_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows == 0) {
        // Insert new save
        $query = $conn->prepare("INSERT INTO saved_posts (post_id, user_id) VALUES (?, ?)");
        $query->bind_param("ii", $post_id, $user_id);
        $query->execute();
    } else {
        // Remove save
        $query = $conn->prepare("DELETE FROM saved_posts WHERE post_id = ? AND user_id = ?");
        $query->bind_param("ii", $post_id, $user_id);
        $query->execute();
    }

    // Return updated save count
    $query = $conn->prepare("SELECT COUNT(*) AS save_count FROM saved_posts WHERE post_id = ?");
    $query->bind_param("i", $post_id);
    $query->execute();
    $result = $query->get_result();
    $save_count = $result->fetch_assoc()['save_count'];
    echo json_encode(['save_count' => $save_count]);
}

// Handle fetching comments
if (isset($_POST['action']) && $_POST['action'] == 'fetch_comments') {
    $post_id = $_POST['post_id'];
    $offset = $_POST['offset'];

    // Fetch next set of comments
    $query = $conn->prepare("SELECT user_id, comment_text, comment_date FROM comments JOIN users ON comments.user_id = users.user_id WHERE post_id = ? ORDER BY comment_date ASC LIMIT ?, 5");
    $query->bind_param("ii", $post_id, $offset);
    $query->execute();
    $result = $query->get_result();
    $comments = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($comments);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>YanBu Hub - Dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

<style type="">
/* Modal styles */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1001; /* Sit on top */
    left: 50%; /* Center horizontally */
    top: 50%; /* Center vertically */
    transform: translate(-50%, -50%); /* Offset for centering */
    width: 40%; /* Full width */
    height: auto; /* Auto height to fit content */
    max-height: 80%; /* Maximum height */
    overflow-y: auto; /* Enable vertical scroll if needed */
    background-color: rgba(0, 0, 0, 0.4); /* Black background with opacity */
}

section {
    max-height: 100vh;
    padding: 20px;
    overflow: auto; /* Enable scrolling for content */
}

.modal-content {
    background-color: #fefefe;
    padding: 20px;
    border: 1px solid #888;
    width: 100%; /* Full width */
    display: flex; /* Flexbox for input fields */
    flex-direction: column; /* Column layout */
    align-items: center; /* Center items */
    border-radius: 12px; /* Rounded corners */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Shadow for depth */
}

.modal-content input,
.modal-content textarea {
    margin: 10px; /* Add some spacing between fields */
    width: calc(100% - 20px); /* Full width with margin */
    padding: 10px; /* Increased padding for larger fields */
    font-size: 1.2em; /* Increased font size */
    border: 1px solid #ccc; /* Light border */
    border-radius: 5px; /* Rounded corners */
    transition: border-color 0.3s ease; /* Transition effect for border color */
}

.modal-content input:focus,
.modal-content textarea:focus {
    border-color: #1877f2; /* Change border color on focus */
    outline: none; /* Remove outline */
}

.modal-content button {
    margin-top: 10px; /* Add space above the button */
    padding: 10px 20px; /* Larger padding for buttons */
    font-size: 1.2em; /* Increase button font size */
    background-color: #1877f2; /* Button background color */
    color: white; /* Button text color */
    border: none; /* Remove border */
    border-radius: 5px; /* Rounded corners for button */
    cursor: pointer; /* Pointer cursor */
    transition: background-color 0.3s ease; /* Transition for button background */
}

.modal-content button:hover {
    background-color: #0c6abf; /* Darker blue on hover */
}

.close-modal {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close-modal:hover,
.close-modal:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.community {
    display: flex;
    flex-direction: column;
    height: auto; /* Allow for dynamic height */
    margin-right: auto; 
    margin: auto;
    background: white; /* White background for posts */
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Soft shadow */
    padding: 20px;
    width: 100%; /* Full width */
}

.posts-section {
    margin-bottom: 20px;
    width: 100%; /* Full width for the posts section */
}

/* Position Add Post Button */
#add-post-btn {
    width: 100%; /* Full width on all screens */
    height: 40px;
    margin-bottom: 20px; /* Space below the button */
    padding: 10px; /* Increase padding */
    background-color: rgb(23, 132, 140); /* Button background color */
    color: white; /* Button text color */
    border: none; /* No border */
    border-radius: 5px; /* Rounded corners */
    cursor: pointer; /* Pointer cursor */
    transition: background-color 0.3s ease; /* Transition for button background */
}

#add-post-btn:hover {
    background-color: #0c6abf; /* Darker blue on hover */
}

.post {
    border: 1px solid #ddd; /* Light border around posts */
    border-radius: 5px; /* Slightly rounded corners */
    padding: 20px; /* Increase padding for larger posts */
    margin-bottom: 20px; /* Space between posts */
    background-color: #fff; /* White background for each post */
    transition: transform 0.2s ease; /* Transition for hover effect */
    font-size: 1.1em; /* Slightly larger font for posts */
}

.post-header {
    display: flex;
    justify-content: space-between; /* Space between title and date */
    align-items: center;
    margin-bottom: 10px;
}

.post-header h3 {
    margin: 0;
    font-size: 1.2em; /* Larger font for username */
}

.post-actions {
    margin-top: 10px;
    display: flex;
    justify-content: flex-start; /* Align action buttons to the left */
    gap: 15px; /* Space between action buttons */
}

.action-btn {
    background: none; /* No background */
    border: none; /* No border */
    color: #1877f2; /* Facebook blue color */
    cursor: pointer; /* Pointer cursor on hover */
    display: flex;
    align-items: center;
    transition: color 0.2s ease; /* Transition for color change */
}

.action-btn i {
    margin-right: 5px; /* Space between icon and text */
}

.action-btn:hover {
    text-decoration: underline; /* Underline on hover */
    color: #0c6abf; /* Darker blue color on hover */
}

/* Commenting Area */
.commenting-area {
    margin-top: 15px; /* Space above the commenting area */
    display: flex; /* Flexbox layout */
    align-items: center; /* Center align items */
}

.comment-input {
    flex-grow: 1; /* Allow input to grow */
    padding: 10px; /* Padding for input */
    border: 1px solid #ccc; /* Light border */
    border-radius: 5px; /* Rounded corners */
    margin-right: 10px; /* Space between input and button */
}

.comment-btn {
    padding: 10px 15px; /* Button padding */
    background-color: #1877f2; /* Button background color */
    color: white; /* Button text color */
    border: none; /* Remove border */
    border-radius: 5px; /* Rounded corners */
    cursor: pointer; /* Pointer cursor */
    transition: background-color 0.3s ease; /* Transition for button background */
}

.comment-btn:hover {
    background-color: #0c6abf; /* Darker blue on hover */
}

.footer {
    background-color: var(--white);
    border-top: var(--border);
    position: fixed; /* Use fixed instead of sticky */
    bottom: 0;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 2rem;
    padding: 2.5rem 2rem;
    color: var(--black);
    margin-top: 1rem;
    z-index: 1000;
}

/* Media queries for responsiveness */
@media (max-width: 600px) {
    #add-post-btn {
        margin-top: 20px; /* Add space above the button */
    }
    
    .posts-section {
        width: 100%; /* Ensure full width on small screens */
    }
}

</style>

</head>
<body>

<header class="header">
   <section class="flex">
      <a href="home.php" class="logo">YanBu Hub</a>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="toggle-btn" class="fas fa-sun"></div>
      </div>

      <div class="profile">
         <img src="images/pic-1.jpg" class="image" alt="">
         <h3 class="name"><?php echo $username; ?></h3>
         <p class="role"><?php echo $usertype; ?></p>
         <a href="profile.php" class="btn">view profile</a>
         <a href="logout.php" class="btn">logout</a>
      </div>

   </section>
</header>

<div class="side-bar">
   <div id="close-btn">
      <i class="fas fa-times"></i>
   </div>

      <div class="profile">
         <img src="images/pic-1.jpg" class="image" alt="">
         <h3 class="name"><?php echo $username; ?></h3>
         <p class="role"><?php echo $usertype; ?></p>
         <a href="profile.php" class="btn">view profile</a>
      </div>

   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>
      <a href="comm.php"><i class="fas fa-question"></i><span>Community</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>Contact Us</span></a>
   </nav>
</div>


<section class="community" style="position: relative;">
    <button id="add-post-btn" class="btn">Add Post</button>
    <div class="posts-section">
        <input type="text" id="search-input" placeholder="Search by title or user..." />
        <div id="posts-container">
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post">
                        <div class="post-header">
                            <h3><?php echo htmlspecialchars($post['user_name']); ?></h3>
                            <small><?php echo date('F j, Y, g:i a', strtotime($post['post_date'])); ?></small>
                        </div>
                        <p><?php echo htmlspecialchars($post['post_content']); ?></p>
                        <div class="post-actions">
                            <button class="action-btn"><i class="far fa-thumbs-up"></i> Like</button>
                            <button class="action-btn"><i class="far fa-bookmark"></i> Save</button>
                            <button class="action-btn"><i class="far fa-comment-dots"></i> Comment</button>
                        </div>
                        <div class="commenting-area">
                            <input type="text" class="comment-input" placeholder="Write a comment..." />
                            <button class="comment-btn">Post</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No posts found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Modal for adding a post -->
<div class="modal" id="postModal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2>Add Post</h2>
        <input type="text" id="post-title" placeholder="Post Title" />
        <textarea id="post-content" placeholder="Post Content" rows="5"></textarea>
        <button id="submit-post">Submit</button>
    </div>
</div>


<footer class="footer">
   &copy; copyright @ 2024 YanBu Hub | all rights reserved!
</footer>

<!-- custom js file link  -->
<script src="js/script.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
// Load posts initially and set interval to check for updates
$(document).ready(function() {
    loadPosts();

    // Check for new posts every 5 seconds
    setInterval(function() {
        loadPosts();
    }, 5000);
    // Open modal on "Add Post" button click
    $('#add-post-btn').click(function() {
        $('#add-post-modal').show();
    });

    // Close modal
    $('.close-modal').click(function() {
        $('#add-post-modal').hide();
    });

    // Handle add post form submission
    $('#add-post-form').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.post('add_post.php', formData, function(response) {
            $('#add-post-modal').hide();
            loadPosts(); // Refresh the posts after adding a new one
        });
    });

    // Implement search functionality
    $('#search-input').on('input', function() {
        var searchQuery = $(this).val();

        $.post('search_posts.php', { query: searchQuery }, function(data) {
            $('#posts-container').html(data); // Replace posts with the search results
        });
    });
});

function loadPosts() {
    $.ajax({
        url: 'load_posts.php',
        method: 'GET',
        success: function(data) {
            $('#posts-section').html(data);
        }
    });
}
document.addEventListener("DOMContentLoaded", function() {
    // Handle Like Button
    document.querySelectorAll('.action-btn.like').forEach(function(button) {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const likeCountElement = this.querySelector('span.like-count');
            let likeCount = parseInt(likeCountElement.textContent);

            fetch('like_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ post_id: postId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Increment or decrement like count
                    likeCount = data.liked ? likeCount + 1 : likeCount - 1;
                    likeCountElement.textContent = likeCount;
                    this.querySelector('i').style.color = data.liked ? 'blue' : 'grey';
                }
            });
        });
    });

    // Handle Save Button
    document.querySelectorAll('.action-btn.save').forEach(function(button) {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;

            fetch('save_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ post_id: postId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Change save icon color based on save state
                    this.querySelector('i').style.color = data.saved ? 'blue' : 'grey';
                }
            });
        });
    });

    // Handle Comment Button
    document.querySelectorAll('.action-btn.comment').forEach(function(button) {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const commentBox = this.nextElementSibling; // Assuming comment box follows the button
            commentBox.style.display = commentBox.style.display === 'none' ? 'block' : 'none';

            commentBox.querySelector('form').addEventListener('submit', function(event) {
                event.preventDefault();
                const comment = this.querySelector('input').value;

                fetch('comment_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ post_id: postId, comment: comment })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Add the new comment to the comments section
                        const newComment = document.createElement('div');
                        newComment.className = 'comment';
                        newComment.textContent = data.comment;
                        commentBox.querySelector('.comments').appendChild(newComment);

                        this.querySelector('input').value = ''; // Clear input
                    }
                });
            });
        });
    });
});

</script>

</body>
</html>
