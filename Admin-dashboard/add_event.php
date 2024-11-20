<?php
// Include database connection
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $description = $_POST['description'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO events (event_name, event_date, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $event_name, $event_date, $description);

    // Execute and check if successful
    if ($stmt->execute()) {
        echo "Event added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
