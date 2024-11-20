<?php
session_start();
include '../db.php'; // Adjust to your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_info']['user_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Insert new post
    $query = $conn->prepare("INSERT INTO posts (user_id, post_content, post_description) VALUES (?, ?, ?)");
    $query->bind_param("iss", $user_id, $content, $title);
    $query->execute();
    echo json_encode(['status' => 'success']);
}
?>
