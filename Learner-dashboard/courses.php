<?php
// Start the session
session_start();

// Check if the user is logged in by checking session variables
if (!isset($_SESSION['user_info'])) {
    header("Location: ../login.php");
    exit();
}

// Pull user's name and type from the session array
$username = $_SESSION['user_info']['email']; // Assuming 'username' is stored in the array
$usertype = $_SESSION['user_info']['user_type']; // Assuming 'user_type' is stored in the array

include_once '../db.php';


// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to retrieve courses from the database
$sql = "SELECT * FROM courses";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>YanBu Hub - Dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <style>
      .button-container {
          display: flex; /* Use flexbox to arrange children in a row */
          justify-content: center; /* Center align buttons (optional) */
          gap: 10px; /* Space between buttons */
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
      .course-thumb {
          background-color: blue; /* Set background color */
          color: white; /* Text color */
          display: flex; /* Center text */
          align-items: center; /* Center vertically */
          justify-content: center; /* Center horizontally */
          font-size: 24px; /* Font size for course name */
          height: 150px; /* Fixed height for thumbnails */
      }
   </style>
</head>
<body>

<header class="header">
   <section class="flex">
      <a href="home.html" class="logo">YanBu Hub</a>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="toggle-btn" class="fas fa-sun"></div>
      </div>

      <div class="profile">
         <img src="images/pic-1.jpg" class="image" alt="">
         <h3 class="name"><?php echo htmlspecialchars($username); ?></h3>
         <p class="role"><?php echo htmlspecialchars($usertype); ?></p>
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
      <h3 class="name"><?php echo htmlspecialchars($username); ?></h3>
      <p class="role"><?php echo htmlspecialchars($usertype); ?></p>
      <a href="profile.php" class="btn">view profile</a>
   </div>

   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>
      <a href="comm.php"><i class="fas fa-question"></i><span>Community</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>Contact Us</span></a>
   </nav>
</div>

<section class="courses">
   <h1 class="heading">our courses</h1>
   <div class="box-container">

      <?php if ($result->num_rows > 0): ?>
         <?php while($row = $result->fetch_assoc()): ?>
            <div class="box">
               <div class="thumb course-thumb">
                  <?php echo htmlspecialchars($row['course_name']); // Display course name here ?> 
               </div>
               <h3 class="title"><?php echo htmlspecialchars($row['course_name']); ?></h3>
               <p><?php echo htmlspecialchars($row['course_description']); ?></p>
               <div class="button-container">
                  <a href="playlist.php?id=<?php echo $row['course_id']; ?>" class="inline-btn">Read More</a>
                  <a href="checkout.php?id=<?php echo $row['course_id']; ?>" class="inline-btn">Enroll</a>
               </div>
            </div>
         <?php endwhile; ?>
      <?php else: ?>
         <div class="box">
            <h3 class="title">No Courses Available</h3>
            <p>Currently, there are no courses available. Please check back later.</p>
         </div>
      <?php endif; ?>

   </div>
</section>

<footer class="footer">
   &copy; copyright @ 2024 YanBu Hub | all rights reserved!
</footer>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
