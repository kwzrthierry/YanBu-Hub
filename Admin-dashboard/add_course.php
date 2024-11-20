<?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "yanbu_hub"; // Replace with your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax'])) {
    $course_name = $_POST['course_name'];
    $course_duration = $_POST['course_duration'];
    $course_description = $_POST['course_description'];

    // Directory setup
    $upload_dir = 'uploads/' . $course_name;
    $video_dir = $upload_dir . '/videos';
    $document_dir = $upload_dir . '/documents';

    // Create directories if they don't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    if (!is_dir($video_dir)) {
        mkdir($video_dir, 0777, true);
    }
    if (!is_dir($document_dir)) {
        mkdir($document_dir, 0777, true);
    }

    // File upload handling
    $video_path = $video_dir . '/' . basename($_FILES['course_video']['name']);
    $document_path = $document_dir . '/' . basename($_FILES['course_document']['name']);

    // Move uploaded files to their respective directories
    if (move_uploaded_file($_FILES['course_video']['tmp_name'], $video_path) &&
        move_uploaded_file($_FILES['course_document']['tmp_name'], $document_path)) {
        
        // Insert course details into the database
        $sql = "INSERT INTO courses (course_name, course_duration, course_description, video_path, document_path)
                VALUES ('$course_name', '$course_duration', '$course_description', '$video_path', '$document_path')";

        if ($conn->query($sql) === TRUE) {
            // Return a success response for AJAX
            echo json_encode(['status' => 'success', 'message' => 'Course added successfully!']);
        } else {
            // Return error with debugging info
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error, 'debug' => 'Database insertion failed']);
        }
    } else {
        // Return error with debugging info
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload files.', 'debug' => 'File move operation failed']);
    }
    exit; // Stop further execution
}

// Function to get courses data as an array or return JSON if requested
function get_courses_data($conn, $return_as_json = false) {
    $sql = "SELECT * FROM courses";
    $result = $conn->query($sql);
    $courses = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
    }

    // Return JSON if requested, otherwise return the array
    if ($return_as_json) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'courses' => $courses]);
        exit;
    }

    return $courses;
}

// Check if the request is for fetching courses data via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['ajax']) && $_GET['ajax'] == 'fetch_courses') {
    get_courses_data($conn, true);
}

// Fetch courses data for rendering in the HTML table
$courses = get_courses_data($conn);

// Close connection
$conn->close();
?>
