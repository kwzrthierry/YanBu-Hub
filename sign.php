<?php
// signup.php
require 'db.php'; // Include the database connection

// Validate and sanitize input
$firstName = htmlspecialchars(trim($_POST['firstName']));
$lastName = htmlspecialchars(trim($_POST['lastName']));
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(trim($_POST['phone']));
$password = $_POST['password']; // raw password for hashing
$gender = htmlspecialchars(trim($_POST['gender']));

// Check for empty fields
if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password) || empty($gender)) {
    echo "<script>alert('All fields are required.'); window.history.back();</script>";
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format.'); window.history.back();</script>";
    exit();
}

// Password strength validation
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
    echo "<script>alert('Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.'); window.history.back();</script>";
    exit();
}

// Check if the email already exists
$sql = "SELECT email FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<script>alert('Email already exists. Please use a different email.'); window.history.back();</script>";
    $stmt->close();
    exit();
}
$stmt->close();

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Insert data into the database
$sql = "INSERT INTO users (first_name, last_name, email, password, phone_number, gender) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $firstName, $lastName, $email, $hashedPassword, $phone, $gender);

try {
    $stmt->execute();
    echo "<script>alert('Signup successful!'); window.location.href = 'login.php';</script>"; // Redirect to login or success page
} catch (\Exception $e) {
    echo "<script>alert('Error: Unable to complete registration. Please try again later.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
