<?php
include '../db.php';

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id']; // Assuming the user is logged in
$comment = $_POST['comment'];

// Insert comment into the database
$insert = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_text, comment_date) VALUES (?, ?, ?, NOW())");
$insert->bind_param('iis', $post_id, $user_id, $comment);
$insert->execute();

echo json_encode(['success' => true, 'comment' => htmlspecialchars($comment)]);
?>
