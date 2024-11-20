<?php
session_start();
include '../db.php'; // Adjust to your database connection

if (isset($_POST['query'])) {
    $queryString = "%" . $_POST['query'] . "%";
    
    // Update the SQL query to concatenate first name and last name
    $query = $conn->prepare("
        SELECT u.user_id, CONCAT(u.first_name, ' ', u.last_name) AS user_name, p.post_content, p.post_date 
        FROM posts p
        JOIN users u ON p.user_id = u.user_id 
        WHERE p.post_content LIKE ? OR u.email LIKE ? 
        ORDER BY p.post_date DESC
    ");
    $query->bind_param("ss", $queryString, $queryString);
    $query->execute();
    $result = $query->get_result();
    $posts = $result->fetch_all(MYSQLI_ASSOC);

    // Loop through the posts and display them
    foreach ($posts as $post) {
        echo '<div class="post">';
        echo '<h3>' . htmlspecialchars($post['user_name']) . '</h3>'; // Display the concatenated username
        echo '<p>' . htmlspecialchars($post['post_content']) . '</p>';
        echo '<small>Posted on: ' . date('F j, Y, g:i a', strtotime($post['post_date'])) . '</small>';
        echo '</div>';
    }

    // If no posts found
    if (count($posts) == 0) {
        echo '<p>No posts found matching your search.</p>';
    }
}
?>
