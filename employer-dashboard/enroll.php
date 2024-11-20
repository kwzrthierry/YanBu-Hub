<?php
// Start the session
session_start();

// Database connection
include_once '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $learner_user_id = $_POST['learner_user_id'];

    // SQL to insert enrollment record
    $sql = "INSERT INTO enrollments (course_id, learner_user_id, enrollment_date) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $course_id, $learner_user_id);

    if ($stmt->execute()) {
        echo "Enrollment successful!";
    } else {
        echo "Enrollment failed!";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request!";
}
?>
