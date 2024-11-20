<?php
// Start the session
session_start();

// Include the database connection file
include '../db.php';

// Check if the user is logged in by checking session variables
if (!isset($_SESSION['user_info'])) {
    header("Location: ../login.php");
    exit();
}

// Pull user's email and user type from the session array
$username = $_SESSION['user_info']['email']; // Assuming 'email' is stored in the array
$usertype = $_SESSION['user_info']['user_type']; // Assuming 'user_type' is stored in the array

// Fetching course progress and time spent data from the database
$userId = $_SESSION['user_info']['user_id']; // Assuming user ID is stored in session

// Update time spent at the end of the day
$currentDate = date('Y-m-d');
$query = "INSERT INTO time_records (user_id, record_date, time_spent) 
          VALUES (?, ?, ?) 
          ON DUPLICATE KEY UPDATE time_spent = time_spent + VALUES(time_spent)";
$stmt = $conn->prepare($query);
$timeSpent = $_SESSION['time_spent'] ?? 0; // Assuming you have a session variable to track time spent
$stmt->bind_param("isi", $userId, $currentDate, $timeSpent);
$stmt->execute();
$stmt->close();

// Fetching user's enrolled courses
$query = "
    SELECT c.course_name, e.enrollment_date 
    FROM enrollment e 
    JOIN courses c ON e.course_id = c.course_id 
    WHERE e.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$coursesData = [];
while ($row = $result->fetch_assoc()) {
    $coursesData[] = $row['course_name'];
}
$stmt->close();

// Fetching time spent data for line chart
$query = "SELECT record_date, time_spent FROM time_records WHERE user_id = ? ORDER BY record_date";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$timeSpentData = [];
$dates = [];
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $dates[] = $row['record_date'];
    $timeSpentData[] = $row['time_spent'] / 3600; // Convert seconds to hours
}
$stmt->close();
// Fetching available courses
$query = "SELECT course_name FROM courses WHERE course_id NOT IN (SELECT course_id FROM enrollment WHERE user_id = ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$availableCoursesData = [];
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $availableCoursesData[] = $row['course_name'];
}
$stmt->close();
$conn->close();
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

   <!-- Chart.js CDN -->
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

   <style>
      .button-container {
          display: flex;
          justify-content: center;
          gap: 10px;
      }
      .chart-container {
          width: 100%;
          height: 400px;
          margin-top: 20px;
      }
      .courses-container {
          display: flex;
          justify-content: space-between;
          margin-top: 20px;
      }
      .courses-list {
          width: 48%;
      }
      .message {
          font-size: 1.2em; /* Increase the font size */
          margin: 10px 0;
      }

      .inline-btn:disabled {
          background-color: #ccc; /* Gray background for disabled button */
          color: #666; /* Gray text for disabled button */
          cursor: not-allowed; /* Show not-allowed cursor */
          border: none; /* Remove border */
          padding: 10px 20px; /* Padding for a consistent size */
          text-align: center; /* Center the text */
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

<section class="home-grid">
   <h1 class="heading">Progress in Enrolled Courses</h1>

   <div class="box-container">
       <div class="box">
           <h3 class="title">Enrolled Courses</h3>
           <?php if (empty($coursesData)): ?>
               <p class="message">You are not enrolled in any course. <a href="courses.php">Enroll here</a>!</p>
               <button class="inline-btn" disabled>View Full Progress</button>
           <?php else: ?>
               <?php foreach ($coursesData as $course): ?>
                   <p><?php echo $course; ?></p>
               <?php endforeach; ?>
               <a href="progress.php" class="inline-btn">View Full Progress</a>
           <?php endif; ?>
       </div>
   </div>


   <h1 class="heading">Available Courses</h1>
   <div class="courses-container">
      <div class="courses-list">
         <h3 class="title">Courses Available</h3>
         <?php if (empty($availableCoursesData)): ?>
             <p>No available courses at the moment.</p>
         <?php else: ?>
             <?php foreach ($availableCoursesData as $availableCourse): ?>
                 <p><?php echo $availableCourse; ?></p>
             <?php endforeach; ?>
         <?php endif; ?>
      </div>
   </div>
</section>

<!-- Chart Section -->
<section class="chart-section">
   <h1 class="heading">Time Spent on Platform</h1>
   <div class="chart-container">
       <canvas id="timeSpentChart"></canvas>
   </div>
</section>

<footer class="footer">
   &copy; copyright @ 2024 by <span>YanBu Hub</span> | all rights reserved!
</footer>

<!-- Custom JS file link -->
<script src="js/script.js"></script>

<script>
// Chart.js code for time spent
const ctx = document.getElementById('timeSpentChart').getContext('2d');

// Prepare the data for the chart
const timeSpentData = <?php echo json_encode($timeSpentData); ?>;
const dates = <?php echo json_encode($dates); ?>;

const timeSpentChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: dates,
        datasets: [{
            label: 'Time Spent (hours)',
            data: timeSpentData,
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            fill: true,
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Hours'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Date'
                }
            }
        }
    }
});
</script>

</body>
</html>