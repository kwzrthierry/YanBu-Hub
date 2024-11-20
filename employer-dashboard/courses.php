<?php
// Start the session
session_start();

// Check if the user is logged in by checking session variables
if (!isset($_SESSION['user_info'])) {
    header("Location: ../login.php");
    exit();
}

// Pull user's name and type from the session array
$username = $_SESSION['user_info']['email'];
$usertype = $_SESSION['user_info']['user_type'];

include_once '../db.php';

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to retrieve courses from the database
$sql = "SELECT course_id, course_name, course_description FROM courses";
$result = $conn->query($sql);

// Function to check if a course has a trainer
function has_trainer($conn, $course_id) {
    $trainer_check_sql = "SELECT COUNT(*) as count FROM trainers WHERE course_id = ?";
    $stmt = $conn->prepare($trainer_check_sql);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>YanBu Hub - Courses</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <style>
      .course-table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 20px;
      }

      .course-table th, .course-table td {
          border: 1px solid #ddd;
          padding: 12px;
          text-align: left;
      }

      .course-table th {
          background-color: #f1f1f1;
          font-weight: bold;
      }

      .course-table tr:hover {
          background-color: #f9f9f9;
      }
      table{
         font-size: 12px;
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
         <a href="profile.php" class="btn">View profile</a>
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
      <a href="contact.php"><i class="fas fa-headset"></i><span>Contact Admin</span></a>
   </nav>
</div>

<section class="courses">
   <h1 class="heading">Available Courses</h1>
   <table class="course-table">
      <thead>
         <tr>
            <th>Course ID</th>
            <th>Course Name</th>
            <th>Description</th>
            <th>Trainer</th>
         </tr>
      </thead>
      <tbody>
         <?php
         // Check if there are any courses
         if ($result->num_rows > 0) {
             // Loop through each course and display it in the table
             while ($row = $result->fetch_assoc()) {
                 $hasTrainer = has_trainer($conn, $row['course_id']);
                 ?>
                 <tr>
                    <td><?php echo htmlspecialchars($row['course_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['course_description']); ?></td>
                    <td><?php echo $hasTrainer ? 'Yes' : 'No'; ?></td>
                 </tr>
                 <?php
             }
         } else {
             echo "<tr><td colspan='4'>No courses available.</td></tr>";
         }
         ?>
      </tbody>
   </table>
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
