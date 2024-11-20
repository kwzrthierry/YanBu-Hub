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

include '../db.php'; // Adjust this to your db connection file

// Fetch learners with enrolled and completed courses
$query = "
    SELECT 
        users.email, 
        users.first_name, 
        users.last_name, 
        users.time_registered, 
        GROUP_CONCAT(courses.course_name SEPARATOR ', ') AS enrolled_courses, 
        COUNT(CASE WHEN enrollment.status = 'completed' THEN 1 END) AS completed_courses
    FROM users 
    LEFT JOIN enrollment ON users.user_id = enrollment.user_id
    LEFT JOIN courses ON enrollment.course_id = courses.course_id
    WHERE users.user_type = 'learner'
    GROUP BY users.user_id
";

$result = $conn->query($query);

// Fetch all learner data
$learners = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $learners[] = $row;
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

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
   <style type="text/css">
       table {
           font-size: 12px; /* Increase font size of table text */
       }
       th {
           font-size: 15px; /* Increase font size of table headers */
           background-color: rgb(37, 186, 0);
           color: white;
           padding: 10px;
       }
       td {
           padding: 10px;
           border: 1px solid #ddd;
       }
       .table-container h2 {
           font-size: 24px; /* Increase font size of heading */
       }
       .footer{
           background-color: var(--white);
           border-top: var(--border);
           position: sticky;
           bottom: 0; left: 0; right: 0;
           text-align: center;
           font-size: 2rem;
           padding:2.5rem 2rem;
           color:var(--black);
           margin-top: 1rem;
           z-index: 1000;
           /* padding-bottom: 9.5rem; */
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
        <h2>Learners Information</h2>
        <table style="width: 100%; border-collapse: collapse; background-color: #f9f9f9;">
            <thead>
                <tr style="background-color: #4CAF50; color: white;">
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Registration Date</th>
                    <th>Enrolled Courses</th>
                    <th>Completed Courses</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($learners) > 0) {
                    foreach ($learners as $learner) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($learner['email']); ?></td>
                            <td><?php echo htmlspecialchars($learner['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($learner['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($learner['time_registered']); ?></td>
                            <td><?php echo htmlspecialchars($learner['enrolled_courses']); ?></td>
                            <td><?php echo htmlspecialchars($learner['completed_courses']); ?></td>
                        </tr>
                <?php } } else { ?>
                    <tr>
                        <td colspan="6">No learners found.</td>
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
