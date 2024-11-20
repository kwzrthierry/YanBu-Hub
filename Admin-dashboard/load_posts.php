<?php
// Start the session
session_start();

// Check if the user is logged in by checking session variables
if (!isset($_SESSION['user_info'])) {
    header("Location: ../login.php");
    exit();
}

// Include the database connection
include '../db.php'; // Adjust this to the path of your database connection file

// Fetch posts from the database
$query = $conn->prepare("SELECT posts.post_id, users.user_name, posts.post_content, posts.post_date FROM posts JOIN users ON posts.user_id = users.user_id ORDER BY posts.post_date DESC");
$query->execute();
$result = $query->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);

// Output the posts as HTML
if (count($posts) > 0) {
    foreach ($posts as $post) {
        echo '<div class="post">';
        echo '<h3>' . htmlspecialchars($post['user_name']) . '</h3>';
        echo '<p>' . htmlspecialchars($post['post_content']) . '</p>';
        echo '<small>Posted on: ' . date('F j, Y, g:i a', strtotime($post['post_date'])) . '</small>';
        // Display like and save buttons
        echo '<div id="like-count-' . $post['post_id'] . '">0 Likes</div>';
        echo '<button class="like-btn" data-post-id="' . $post['post_id'] . '">Like</button>';
        echo '<div id="save-count-' . $post['post_id'] . '">0 Saves</div>';
        echo '<button class="save-btn" data-post-id="' . $post['post_id'] . '">Save</button>';
        // Add comments section
        echo '<div id="comments-section-' . $post['post_id'] . '"></div>';
        echo '<button class="comment-btn" data-post-id="' . $post['post_id'] . '" data-offset="0">Show Comments</button>';
        echo '</div>';
    }
} else {
    echo '<p>No posts available yet. Be the first to post!</p>';
}
?>
