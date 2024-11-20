<?php
// Include the database connection file
include 'db.php';

// Function to verify user login and get user type
function verifyLogin($conn, $email, $password) {
    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a row with the given email exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check the user type
        if ($user['user_type'] === 'admin' || $user['user_type'] === 'employer') {
            // For 'admin' and 'employer', check password normally (not using password_verify)
            if ($password === $user['password']) {
                // Return an array with user type and user ID on successful login
                return [
                    'user_type' => $user['user_type'],
                    'user_id' => $user['user_id']
                ];
            } else {
                // Password does not match
                echo "Password Verification Failed for Admin/Employer";
            }
        } else if ($user['user_type'] === 'learner' || $user['user_type'] === 'trainer') {
            // For 'learner' and 'trainer', use password_verify
            if (password_verify($password, $user['password'])) {
                // Return an array with user type and user ID on successful login
                return [
                    'user_type' => $user['user_type'],
                    'user_id' => $user['user_id']
                ];
            } else {
                // Password verification failed
                echo "Password Verification Failed for Learner/Trainer";
            }
        } else {
            // User type not recognized
            echo "User type is not recognized";
        }
    } else {
        // No user found with the given email
        echo "No User Found";
    }
    // Return false if login fails
    return false;
}

// Start a session
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get email and password from POST request
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verify login credentials
    if (!empty($email) && !empty($password)) {
        $userData = verifyLogin($conn, $email, $password);
        if ($userData) {
            // Store user info in session
            $_SESSION['user_info'] = array(
                'user_id' => $userData['user_id'], // Storing user_id
                'email' => $email,
                'user_type' => $userData['user_type']
            );

            // Show success message and redirect based on user type
            switch ($userData['user_type']) {
                case 'learner':
                    echo "<script>
                            setTimeout(function() {
                                alert('Login successful');
                                window.location.href = 'Learner-dashboard/home.php';
                            }, 500);
                          </script>";
                    break;
                case 'admin':
                    echo "<script>
                            setTimeout(function() {
                                alert('Login successful');
                                window.location.href = 'Admin-dashboard/home.php';
                            }, 500);
                          </script>";
                    break;
                case 'trainer':
                    echo "<script>
                            setTimeout(function() {
                                alert('Login successful');
                                window.location.href = 'trainer-dashboard/home.php';
                            }, 500);
                          </script>";
                    break;
                case 'employer':
                    echo "<script>
                            setTimeout(function() {
                                alert('Login successful');
                                window.location.href = 'employer-dashboard/home.php';
                            }, 500);
                          </script>";
                    break;
                default:
                    echo "<script>alert('User type is not recognized');</script>";
            }
        } else {
            // Show error message
            echo "<script>alert('Invalid email or password');</script>";
        }
    } else {
        // Show error message if fields are empty
        echo "<script>alert('Email and password are required');</script>";
    }
} else {
    // Redirect to login page if accessed directly
    header("Location: home.html");
    exit();
}
?>
