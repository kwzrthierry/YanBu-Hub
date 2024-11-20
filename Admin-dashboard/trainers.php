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

include '../db.php'; // Adjusted to match your db connection file name

// Fetch trainers and their associated course and user information from the database
$query = $conn->prepare("
    SELECT 
        trainers.trainer_id,
        trainers.trainer_name,
        trainers.speciality,
        trainers.day_joiined,
        courses.course_name,
        CONCAT(users.first_name, ' ', users.last_name) AS user_name
    FROM trainers
    JOIN courses ON trainers.course_id = courses.course_id
    JOIN users ON trainers.user_id = users.user_id
    ORDER BY trainers.day_joiined DESC
");
$query->execute();
$result = $query->get_result();
$trainers = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>YanBu Hub - Trainers</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <!-- Add custom styles here for font size increase -->
   <style>
       table {
           font-size: 12px; /* Increase font size of table text */
       }
       th {
           font-size: 15px; /* Increase font size of table headers */
           background-color: rgb(215, 0, 0);
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
         <a href="update.php" class="inline-btn">update profile</a>
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
      <a href="update.php" class="inline-btn">update profile</a>
   </div>

   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>
      <a href="comm.php"><i class="fas fa-question"></i><span>Community</span></a>
   </nav>
</div>

<section>
    <div class="table-container">
        <h2>Trainers Information</h2>
        <table style="width: 100%; border-collapse: collapse; background-color: #f9f9f9;">
            <thead>
                <tr>
                    <th>Trainer Name</th>
                    <th>Speciality</th>
                    <th>Day Joined</th>
                    <th>Course Name</th>
                    <th>User Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($trainers) > 0) {
                    foreach ($trainers as $trainer) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($trainer['trainer_name']); ?></td>
                            <td><?php echo htmlspecialchars($trainer['speciality']); ?></td>
                            <td><?php echo htmlspecialchars($trainer['day_joined']); ?></td>
                            <td><?php echo htmlspecialchars($trainer['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($trainer['user_name']); ?></td>
                        </tr>
                <?php } } else { ?>
                    <tr>
                        <td colspan="5">No trainers found.</td>
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
