<?php
// Start the session
session_start();

// Check if the user is logged in by checking session variables
if (!isset($_SESSION['user_info'])) {
    header("Location: ../login.php");
    exit();
}

// Pull user's name and type from the session array
$username = $_SESSION['user_info']['email']; // Assuming 'email' is stored in the array
$usertype = $_SESSION['user_info']['user_type']; // Assuming 'user_type' is stored in the array
$userId = $_SESSION['user_info']['user_id']; // Assuming user_id is stored in the session

// Database connection (assuming you have a db.php file with $conn variable)
include '../db.php';

// Initialize variables
$message = "";

// Handle form submission
if (isset($_POST['submit'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $c_pass = $_POST['c_pass'];

    // Validate input
    if ($new_pass !== $c_pass) {
        $message = "New password and confirm password do not match.";
    } else {
        // Fetch current password from the database
        $query = "SELECT password FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($old_pass, $row['password'])) {
                // Update user details
                $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);
                $updateQuery = "UPDATE users SET first_name = ?, last_name = ?, password = ? WHERE user_id = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("sssi", $first_name, $last_name, $hashed_new_pass, $userId);

                if ($updateStmt->execute()) {
                    // Handle file upload if update was successful
                    $fileUploadSuccess = true;
                    $profilePicturePath = "";
                    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
                        // Create uploads directory if it doesn't exist
                        $uploadsDir = '../uploads/users/';
                        if (!is_dir($uploadsDir)) {
                            mkdir($uploadsDir, 0777, true);
                        }

                        // Create a directory for the user if it doesn't exist
                        $userDir = $uploadsDir . $userId . "_" . str_replace(" ", "_", $first_name . "_" . $last_name) . "/";
                        if (!is_dir($userDir)) {
                            mkdir($userDir, 0777, true);
                        }

                        // Move uploaded file to the user's directory
                        $fileName = basename($_FILES['profile_pic']['name']);
                        $targetFilePath = $userDir . $fileName;

                        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFilePath)) {
                            $profilePicturePath = $targetFilePath;

                            // Update the picture location in the database
                            $updatePictureQuery = "UPDATE users SET profile_pic = ? WHERE user_id = ?";
                            $updatePictureStmt = $conn->prepare($updatePictureQuery);
                            $updatePictureStmt->bind_param("si", $profilePicturePath, $userId);

                            if (!$updatePictureStmt->execute()) {
                                $fileUploadSuccess = false;
                                $message = "Profile updated, but failed to update the profile picture location.";
                            }
                        } else {
                            $fileUploadSuccess = false;
                            $message = "Profile updated, but failed to upload the picture.";
                        }
                    }

                    // Update session info if the name changes
                    $_SESSION['user_info']['first_name'] = $first_name;
                    $_SESSION['user_info']['last_name'] = $last_name;

                    // Display success message only if everything went well
                    if ($fileUploadSuccess) {
                        $message = "Profile updated successfully, including profile picture.";
                    } else {
                        $message = "Profile updated successfully.";
                    }
                } else {
                    $message = "Failed to update profile.";
                }
            } else {
                $message = "Old password is incorrect.";
            }
        } else {
            $message = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>YanBu Hub - Dashboard</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">

   <script>
       function validateForm() {
           const newPass = document.getElementById('new_pass').value;
           const confirmPass = document.getElementById('c_pass').value;

           if (newPass !== confirmPass) {
               alert("New password and confirm password do not match.");
               return false;
           }
           return true;
       }
   </script>
   <style>
       .form-row {
           display: flex;
           justify-content: space-between;
           margin-bottom: 20px;
       }
       .form-group {
           width: 47%;
       }
       .form-group.wide {
           width: 100%;
       }
      .footer {
          background-color: var(--white);
          border-top: var(--border);
          position: fixed; /* Use fixed instead of sticky */
          bottom: 0;
          left: 0;
          right: 0;
          text-align: center;
          font-size: 2rem;
          padding: 2.5rem 2rem;
          color: var(--black);
          margin-top: 1rem;
          z-index: 1000;
      }
   </style>
</head>
<body>

<header class="header">
   <section class="flex">
      <a href="home.php" class="logo">YanBu Hub</a>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="toggle-btn" class="fas fa-sun"></div>
      </div>

      <div class="profile">
         <img src="images/pic-1.jpg" class="image" alt="">
         <h3 class="name"><?php echo $username; ?></h3>
         <p class="role"><?php echo $usertype; ?></p>
         <a href="profile.php" class="btn">view profile</a>
         <a href="logout.php" class="btn">logout</a>
      </div>

   </section>
</header>

<div class="side-bar">
   <div id="close-btn">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
      <img src="images/pic-1.jpg" class="image" alt="">
      <h3 class="name"><?php echo $username; ?></h3>
      <p class="role"><?php echo $usertype; ?></p>
      <a href="profile.html" class="btn">view profile</a>
   </div>

   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>
      <a href="comm.php"><i class="fas fa-question"></i><span>Community</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>Contact Us</span></a>
   </nav>
</div>

<section class="form-container">

   <form action="" method="post" enctype="multipart/form-data" onsubmit="return validateForm();">
      <h3>Update Profile</h3>
      <?php if ($message): ?>
         <p class="message"><?php echo $message; ?></p>
      <?php endif; ?>
      
      <div class="form-row">
          <div class="form-group">
              <p>First Name</p>
              <input type="text" name="first_name" placeholder="Enter your first name" maxlength="50" class="box" required>
          </div>
          <div class="form-group">
              <p>Last Name</p>
              <input type="text" name="last_name" placeholder="Enter your last name" maxlength="50" class="box" required>
          </div>
      </div>

      <div class="form-row">
          <div class="form-group">
              <p>Previous Password</p>
              <input type="password" name="old_pass" placeholder="Enter your old password" maxlength="20" class="box" required>
          </div>
          <div class="form-group">
              <p>New Password</p>
              <input type="password" id="new_pass" name="new_pass" placeholder="Enter new password" maxlength="20" class="box" required>
          </div>
      </div>

      <div class="form-row">
          
          <div class="form-group">
              <p>Confirm Password</p>
              <input type="password" id="c_pass" name="c_pass" placeholder="Confirm new password" maxlength="20" class="box" required>
          </div>
          <div class="form-group">
              <p>Profile Picture</p>
                <input type="file" name="profile_pic" class="box">
          </div>
      </div>

      <input type="submit" name="submit" value="Update Profile" class="btn">
   </form>
</section>

<footer class="footer">
   &copy; copyright @ 2024 YanBu Hub | all rights reserved!
</footer>

<!-- Custom JS file link -->
<script src="js/script.js"></script>

</body>
</html>
