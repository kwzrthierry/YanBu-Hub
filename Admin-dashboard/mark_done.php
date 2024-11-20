<?php
// Include database connection
include '../db.php';
session_start(); // Start the session to access user info

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if task_id is set in POST request
    if (isset($_POST['task_id'])) {
        $task_id = $_POST['task_id'];

        // Prepare and bind
        $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE task_id = ? AND user_id = ?");
        $status = 'completed';
        $user_id = $_SESSION['user_info']['user_id']; // Assuming user_id is stored in session
        $stmt->bind_param("sii", $status, $task_id, $user_id);

        // Execute and check if successful
        if ($stmt->execute()) {
            echo "Task marked as done successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close statement and connection
        $stmt->close();
    } else {
        echo "No task ID provided.";
    }
} else {
    echo "Invalid request method.";
}

// Close the database connection
$conn->close();

// Redirect back to the previous page (optional)
header("Location: home.php"); // Change to your previous page
exit();
?>
