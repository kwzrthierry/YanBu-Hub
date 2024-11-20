<?php
include '../db.php'; // Your DB connection file

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id']; // Assuming the user is logged in

// Check if the user already liked the post
$query = $conn->prepare("SELECT * FROM likes WHERE post_id = ? AND user_id = ?");
$query->bind_param('ii', $post_id, $user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    // If already liked, remove the like
    $delete = $conn->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
    $delete->bind_param('ii', $post_id, $user_id);
    $delete->execute();
    $liked = false;
} else {
    // If not liked, insert the like
    $insert = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
    $insert->bind_param('ii', $post_id, $user_id);
    $insert->execute();
    $liked = true;
}

echo json_encode(['success' => true, 'liked' => $liked]);
?>

