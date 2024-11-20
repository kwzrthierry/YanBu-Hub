<?php
include '../db.php';

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id']; // Assuming the user is logged in

// Check if the post is already saved
$query = $conn->prepare("SELECT * FROM saved_posts WHERE post_id = ? AND user_id = ?");
$query->bind_param('ii', $post_id, $user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    // If already saved, remove the save
    $delete = $conn->prepare("DELETE FROM saved_posts WHERE post_id = ? AND user_id = ?");
    $delete->bind_param('ii', $post_id, $user_id);
    $delete->execute();
    $saved = false;
} else {
    // If not saved, insert the save
    $insert = $conn->prepare("INSERT INTO saved_posts (post_id, user_id) VALUES (?, ?)");
    $insert->bind_param('ii', $post_id, $user_id);
    $insert->execute();
    $saved = true;
}

echo json_encode(['success' => true, 'saved' => $saved]);
?>
