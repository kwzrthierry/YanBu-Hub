<?php
// db_conn.php
$host = 'localhost';
$db = 'yanbu_hub'; // Change this to your database name
$user = 'root';    // Change this to your database username
$pass = '';        // Change this to your database password
$charset = 'utf8mb4'; // Charset definition

// Create a new mysqli connection
$conn = new mysqli($host, $user, $pass, $db);

// Check for a connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the charset
$conn->set_charset($charset);

?>
