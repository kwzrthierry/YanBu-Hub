<?php
// Start the session
session_start();

// Check if the user is logged in by checking session variables
if (!isset($_SESSION['user_info'])) {
    header("Location: ../login.php");
    exit();
}

// Pull user's email from the session array
$username = $_SESSION['user_info']['email'];
$usertype = $_SESSION['user_info']['user_type'];

// Include database connection
include_once '../db.php';

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get user information from the database
$user_info_sql = "SELECT user_id, first_name, last_name, email, phone_number, gender, time_registered, user_type, profile_pic FROM users WHERE email = ?";
$stmt = $conn->prepare($user_info_sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id, $first_name, $last_name, $email, $phone_number, $gender, $time_registered, $user_type, $profile_pic);
$stmt->fetch();
$stmt->close();

// Use the fetched profile picture if available, otherwise use the default image
$profile_img = $profile_pic ? $profile_pic : 'images/pic-1.jpg';
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
      <style>
      .box span {
         color: #004080; /* Darker blue color */
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
         <img src="<?php echo htmlspecialchars($profile_img); ?>" class="image" alt="">
         <h3 class="name"><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></h3>
         <p class="role"><?php echo htmlspecialchars($user_type); ?></p>
         <a href="logout.php" class="btn">Logout</a>
      </div>
   </section>
</header>

<div class="side-bar">
   <div id="close-btn">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
      <img src="<?php echo htmlspecialchars($profile_img); ?>" class="image" alt="">
      <h3 class="name"><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></h3>
      <p class="role"><?php echo htmlspecialchars($user_type); ?></p>
      <a href="profile.php" class="btn">View Profile</a>
   </div>

   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>
      <a href="comm.php"><i class="fas fa-question"></i><span>Community</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>Contact Us</span></a>
   </nav>
</div>

<section class="user-profile">
   <h1 class="heading">Your Profile</h1>

   <div class="info">
      <div class="user">
         <img src="<?php echo htmlspecialchars($profile_img); ?>" alt="">
         <h3><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></h3>
         <p><?php echo htmlspecialchars($user_type); ?></p>
         <a href="update.php" class="inline-btn">Update Profile</a>
      </div>

      <div class="box-container">
         <div class="box">
            <div class="flex">
               <i class="fas fa-id-badge"></i>
               <div>
                  <p>User ID:</p>
                  <span><?php echo htmlspecialchars($user_id); ?></span>
               </div>
            </div>
         </div>

         <div class="box">
            <div class="flex">
               <i class="fas fa-user"></i>
               <div>
                  <p>Full Name:</p>
                  <span><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></span>
               </div>
            </div>
         </div>

         <div class="box">
            <div class="flex">
               <i class="fas fa-envelope"></i>
               <div>
                  <p>Email:</p>
                  <span><?php echo htmlspecialchars($email); ?></span>
               </div>
            </div>
         </div>

         <div class="box">
            <div class="flex">
               <i class="fas fa-phone"></i>
               <div>
                  <p>Phone Number:</p>
                  <span><?php echo htmlspecialchars($phone_number); ?></span>
               </div>
            </div>
         </div>

         <div class="box">
            <div class="flex">
               <i class="fas fa-venus-mars"></i>
               <div>
                  <p>Gender:</p>
                  <span><?php echo htmlspecialchars($gender); ?></span>
               </div>
            </div>
         </div>

         <div class="box">
            <div class="flex">
               <i class="fas fa-user-tag"></i>
               <div>
                  <p>User Type:</p>
                  <span><?php echo htmlspecialchars($user_type); ?></span>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<footer class="footer">
   &copy; copyright @ 2024 YanBu Hub | all rights reserved!
</footer>

<!-- Custom JS file link -->
<script src="js/script.js"></script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
