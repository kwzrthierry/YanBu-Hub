<?php
session_start(); // Start the session

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page (or any page you prefer)
header("Location: ../login.php");
exit; // Ensure no further code is executed after the redirect
?>
