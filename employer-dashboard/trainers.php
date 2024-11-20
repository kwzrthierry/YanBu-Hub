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

// Fetch learners who are eligible for employment as trainers based on course completion
$learner_query = $conn->prepare("
    SELECT 
        users.user_id, 
        CONCAT(users.first_name, ' ', users.last_name) AS learner_name, 
        users.email
    FROM users
    LEFT JOIN trainers ON users.user_id = trainers.user_id
    WHERE trainers.user_id IS NULL 
    AND users.user_type = 'learner'
    AND EXISTS (
        SELECT 1 
        FROM enrollment 
        WHERE enrollment.user_id = users.user_id 
        AND enrollment.completed_at IS NOT NULL
    )
");
$learner_query->execute();
$learner_result = $learner_query->get_result();
$learners = $learner_result->fetch_all(MYSQLI_ASSOC);

// Fetch the list of courses that the learners have completed
$course_query = $conn->prepare("
    SELECT 
        courses.course_id, 
        courses.course_name, 
        enrollment.user_id
    FROM courses
    JOIN enrollment ON courses.course_id = enrollment.course_id
    WHERE enrollment.completed_at IS NOT NULL
");
$course_query->execute();
$course_result = $course_query->get_result();
$completed_courses = $course_result->fetch_all(MYSQLI_ASSOC);

// If the form is submitted for employing a learner as a trainer
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['employ_learner'])) {
    $learner_id = $_POST['learner_id'];
    $trainer_name = $_POST['trainer_name'];
    $speciality = $_POST['speciality'];
    $course_id = $_POST['course_id'];

    // Insert the new trainer into the trainers table
    $insert_trainer = $conn->prepare("
        INSERT INTO trainers (trainer_name, speciality, day_joined, course_id, user_id)
        VALUES (?, ?, NOW(), ?, ?)
    ");
    $insert_trainer->bind_param("ssii", $trainer_name, $speciality, $course_id, $learner_id);

    if ($insert_trainer->execute()) {
        echo "<script>alert('Learner employed successfully as a trainer!');</script>";
    } else {
        echo "<script>alert('Failed to employ learner. Please try again.');</script>";
    }

    $insert_trainer->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>YanBu Hub - Trainers and Learners</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <!-- Add custom styles here -->
   <style>
       table {
           font-size: 12px;
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

<section>
    <div class="table-container">
        <h2>Available Learners to Employ</h2>
        <table style="width: 100%; border-collapse: collapse; background-color: #f9f9f9;">
            <thead>
                <tr>
                    <th>Learner Name</th>
                    <th>Email</th>
                    <th>Course to Train</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($learners) > 0) {
                    foreach ($learners as $learner) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($learner['learner_name']); ?></td>
                            <td><?php echo htmlspecialchars($learner['email']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="learner_id" value="<?php echo $learner['user_id']; ?>">
                                    <input type="text" name="trainer_name" placeholder="Trainer Name" required>
                                    <input type="text" name="speciality" placeholder="Speciality" required>
                                    <select name="course_id" required>
                                        <option value="">Select Course</option>
                                        <?php
                                        foreach ($completed_courses as $course) {
                                            if ($course['user_id'] == $learner['user_id']) {
                                                echo "<option value='" . $course['course_id'] . "'>" . htmlspecialchars($course['course_name']) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <button type="submit" name="employ_learner" class="view-details-btn">Employ</button>
                                </form>
                            </td>
                        </tr>
                <?php } } else { ?>
                    <tr>
                        <td colspan="4">No learners available for employment.</td>
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
