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
$username = $_SESSION['user_info']['email'];
$usertype = $_SESSION['user_info']['user_type'];

// Fetch statistics: number of trainers, learners, and courses
$query = "
    SELECT 
        (SELECT COUNT(*) FROM users WHERE user_type = 'trainer') AS total_trainers,
        (SELECT COUNT(DISTINCT user_id) FROM enrollment) AS total_enrolled,
        (SELECT COUNT(user_id) FROM users WHERE user_type = 'learner') AS total_learners,
        (SELECT COUNT(*) FROM courses) AS total_courses
";

$result = $conn->query($query);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalenrolled = $row['total_enrolled'];
    $totalTrainers = $row['total_trainers'];
    $totalLearners = $row['total_learners'];
    $totalCourses = $row['total_courses'];
} else {
    $totalTrainers = 0;
    $totalLearners = 0;
    $totalCourses = 0;
}

// Fetch time spent on courses (distinguishing between those with and without trainers)
$query = "
    SELECT c.course_name, IFNULL(SUM(tr.time_spent), 0) AS time_spent_hours, 
           IF(t.trainer_id IS NOT NULL, 'With Trainer', 'No Trainer') AS trainer_status
    FROM courses c
    LEFT JOIN enrollment e ON c.course_id = e.course_id
    LEFT JOIN time_records tr ON e.user_id = tr.user_id AND tr.record_date = CURDATE()
    LEFT JOIN trainers t ON c.course_id = t.course_id
    GROUP BY c.course_id
";
$result = $conn->query($query);
$coursesWithTrainers = [];
$coursesWithoutTrainers = [];
while ($row = $result->fetch_assoc()) {
    if ($row['trainer_status'] === 'With Trainer') {
        $coursesWithTrainers[] = ['course_name' => $row['course_name'], 'time_spent_hours' => $row['time_spent_hours']];
    } else {
        $coursesWithoutTrainers[] = ['course_name' => $row['course_name'], 'time_spent_hours' => $row['time_spent_hours']];
    }
}

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
      .card-container {
          display: flex;
          justify-content: space-around;
          margin-top: 20px;
      }
      .card {
          width: 22%;
          padding: 20px;
          border-radius: 10px;
          text-align: center;
          color: white;
      }
      .card i {
          font-size: 3em;
          margin-bottom: 10px;
      }
      .card.trainings {
          background-color: #4CAF50;
      }
      .card.learners {
          background-color: #2196F3;
      }
      .card.courses {
          background-color: #FF9800;
      }
      .card.enrolled {
          background-color: black;
      }

      .chart-container {
          width: 100%;
          height: 400px;
          margin-top: 20px;
      }

      .home-grid {
          margin-top: 20px;
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

<section class="home-grid">
   <h1 class="heading">Statistics</h1>

   <div class="card-container">
       <a href="trainers.php" class="card trainings">
           <i class="fas fa-chalkboard-teacher"></i>
           <h3>Total Trainers</h3>
           <p><?php echo $totalTrainers; ?></p>
       </a>
       <a href="learners.php" class="card learners">
           <i class="fas fa-users"></i>
           <h3>Total Learners</h3>
           <p><?php echo $totalLearners; ?></p>
       </a>
       <a href="courses.php" class="card courses">
           <i class="fas fa-book"></i>
           <h3>Total Courses</h3>
           <p><?php echo $totalCourses; ?></p>
       </a>
       <a href="enrolled.php" class="card enrolled">
           <i class="fas fa-users"></i>
           <h3>Total Enrolled</h3>
           <p><?php echo $totalenrolled; ?></p>
       </a>
   </div>
</section>

<!-- Chart Section -->
<section class="chart-section">
   <h1 class="heading">Time Spent on Courses</h1>
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
// Prepare the data for the chart
const coursesWithTrainers = <?php echo json_encode(array_column($coursesWithTrainers, 'course_name')); ?>;
const timeSpentWithTrainers = <?php echo json_encode(array_column($coursesWithTrainers, 'time_spent_hours')); ?>;
const coursesWithoutTrainers = <?php echo json_encode(array_column($coursesWithoutTrainers, 'course_name')); ?>;
const timeSpentWithoutTrainers = <?php echo json_encode(array_column($coursesWithoutTrainers, 'time_spent_hours')); ?>;

const ctx = document.getElementById('timeSpentChart').getContext('2d');
const timeSpentChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [...coursesWithTrainers, ...coursesWithoutTrainers], // Combine both lists of course names
        datasets: [
            {
                label: 'Time Spent (with Trainers)',
                data: timeSpentWithTrainers,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                tension: 0.1
            },
            {
                label: 'Time Spent (without Trainers)',
                data: timeSpentWithoutTrainers,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                fill: true,
                tension: 0.1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Time Spent on Courses (With and Without Trainers)'
            }
        }
    }
});
</script>

</body>
</html>
