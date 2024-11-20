<?php
// Include database connection
include '../db.php';

// Start the session
session_start();

// Check if the user is logged in by checking session variables
if (!isset($_SESSION['user_info'])) {
    header("Location: ../login.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $task_description = $_POST['description'];
    $due_date = $_POST['due_date'];
    
    // Get user_id from the session
    $user_id = $_SESSION['user_info']['user_id'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO tasks (description, due_date, status, user_id) VALUES (?, ?, ?, ?)");
    $status = 'pending'; // Define the status as pending
    $stmt->bind_param("ssss", $task_description, $due_date, $status, $user_id); // Added user_id

    // Execute and check if successful
    if ($stmt->execute()) {
        echo "Task added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
