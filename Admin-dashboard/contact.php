<?php
// Start the session
session_start();

// Check if the user is logged in by checking session variables
if (!isset($_SESSION['user_info'])) {
    header("Location: ../login.php");
    exit();
}

// Pull user's name, type, and user ID from the session array
$username = $_SESSION['user_info']['email'];
$usertype = $_SESSION['user_info']['user_type'];
$user_id = $_SESSION['user_info']['user_id']; // Assuming 'user_id' is stored in the session array

// Include database connection file
include '../db.php'; // Ensure this file contains the $conn variable for database connection

$response = $status = null;

// Handle form submission for sending messages
if (isset($_POST['submit'])) {
    $message = trim($_POST['msg']);

    if (!empty($message)) {
        // Prepare SQL query to insert the message
        $stmt = $conn->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $message);

        // Execute the query
        if ($stmt->execute()) {
            $response = "Message sent successfully!";
            $status = "success";
        } else {
            $response = "Failed to send the message.";
            $status = "failed";
        }
        $stmt->close();
    } else {
        $response = "Please enter a message.";
        $status = "failed";
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

   <!-- Custom styles for feedback animation -->
   <style>
       .feedback {
           display: none;
           padding: 10px;
           margin-top: 10px;
           border-radius: 5px;
       }
       .feedback.success {
           background-color: #d4edda;
           color: #155724;
       }
       .feedback.failed {
           background-color: #f8d7da;
           color: #721c24;
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
      <a href="profile.php" class="btn">view profile</a>
   </div>
   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>
      <a href="comm.php"><i class="fas fa-question"></i><span>Community</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>Contact Us</span></a>
   </nav>
</div>

<section class="contact">
   <div class="row">
      <form action="" method="post">
         <h3>get in touch</h3>
         <div class="input-group">
            <input type="text" placeholder="enter your name" name="name" required maxlength="50" class="box">
            <input type="email" placeholder="enter your email" name="email" required maxlength="50" class="box">
         </div>
         <input type="number" placeholder="enter your number" name="number" required maxlength="50" class="box">
         <textarea name="msg" class="box" placeholder="enter your message" required maxlength="1000" cols="30" rows="10"></textarea>
         <input type="submit" value="send message" class="inline-btn" name="submit">
      </form>
   </div>

   <!-- Feedback message -->
   <div id="feedback-message" class="feedback"></div>
</section>

<footer class="footer">
   &copy; copyright @ 2024 YanBu Hub | all rights reserved!
</footer>

<!-- Custom JavaScript file link -->
<script src="js/script.js"></script>

<!-- Script for showing animated feedback -->
<script>
   function showFeedbackMessage(status, message) {
       const feedbackDiv = document.getElementById('feedback-message');
       feedbackDiv.className = 'feedback ' + status; // Apply status class
       feedbackDiv.textContent = message;
       feedbackDiv.style.display = 'block';

       // Hide the message after 3 seconds
       setTimeout(() => {
           feedbackDiv.style.display = 'none';
       }, 3000);
   }

   <?php if (isset($status) && isset($response)): ?>
       // Show feedback based on the PHP backend response
       showFeedbackMessage('<?php echo $status; ?>', '<?php echo $response; ?>');
   <?php endif; ?>
</script>

</body>
</html>
