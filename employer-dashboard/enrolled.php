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

include '../db.php'; // Database connection

// Fetch enrolled learners and their associated information from the database
$enrollment_query = $conn->prepare("
    SELECT 
        enrollment.enroll_id,
        enrollment.course_id,
        enrollment.enrollment_date,
        enrollment.price,
        enrollment.status,
        enrollment.completed_at,
        users.user_id,
        CONCAT(users.first_name, ' ', users.last_name) AS learner_name,
        users.email
    FROM enrollment
    JOIN users ON enrollment.user_id = users.user_id
");
$enrollment_query->execute();
$enrollment_result = $enrollment_query->get_result();
$enrolled_learners = $enrollment_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>YanBu Hub - Enrolled Learners</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <!-- Add custom styles here -->
   <style>
       table {
           font-size: 12px;
           width: 100%;
           border-collapse: collapse;
           background-color: #f9f9f9;
       }
       th {
           font-size: 15px;
           background-color: rgb(37, 186, 0);
           color: white;
           padding: 10px;
       }
       td {
           padding: 10px;
           border: 1px solid #ddd;
       }
       .table-container h2 {
           font-size: 24px;
       }
       .footer {
           background-color: var(--white);
           border-top: var(--border);
           position: sticky;
           bottom: 0; left: 0; right: 0;
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

<section>
    <div class="table-container">
        <h2>Enrolled Learners</h2>
        <table>
            <thead>
                <tr>
                    <th>Enrollment ID</th>
                    <th>Learner Name</th>
                    <th>Email</th>
                    <th>Course ID</th>
                    <th>Enrollment Date</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Completed At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($enrolled_learners) > 0) {
                    foreach ($enrolled_learners as $learner) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($learner['enroll_id']); ?></td>
                            <td><?php echo htmlspecialchars($learner['learner_name']); ?></td>
                            <td><?php echo htmlspecialchars($learner['email']); ?></td>
                            <td><?php echo htmlspecialchars($learner['course_id']); ?></td>
                            <td><?php echo htmlspecialchars($learner['enrollment_date']); ?></td>
                            <td><?php echo htmlspecialchars($learner['price']); ?></td>
                            <td><?php echo htmlspecialchars($learner['status']); ?></td>
                            <td><?php echo htmlspecialchars($learner['completed_at'] ?? 'Not completed'); ?></td>
                        </tr>
                <?php } } else { ?>
                    <tr>
                        <td colspan="8">No learners are currently enrolled in any courses.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>

<footer class="footer">
   &copy; copyright @ 2024 YanBu Hub | all rights reserved!
</footer>

<!-- custom js file link  -->
<script src="js/script.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

</body>
</html>
