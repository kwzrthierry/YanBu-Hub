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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="css/style.css">
    <style type="text/css">
        
.header .flex .icons div{
   font-size: 2rem;
   color:var(--black);
   background-color: var(--light-bg);
   border-radius: .5rem;
   height: 4.5rem;
   width: 4.5rem;
   line-height: 4.5rem;
   cursor: pointer;
   text-align: center;
   margin-left: .7rem;
}

.header .flex .icons div:hover{
   background-color: var(--black);
   color:var(--white);
}

        
        /* Additional styles for the courses table */
        .courses-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .courses-table th, .courses-table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        .courses-table th {
            background-color: #f4f4f4;
        }
        .courses-table tr:hover {
            background-color: #f1f1f1;
        }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 50%;
            max-height: 100%;
            overflow-y: auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            padding:30px;
        }
        .form-row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px; /* Space between fields */
        margin-bottom: 15px; /* Space below each row */
    }
        #add-course-btn {
           margin-left: 100%;
        width: 80px;
        height: 30px;
        position: absolute; /* Position relative to the nearest positioned ancestor */
        right: 20px; /* Right spacing */
        top: 20px; /* Top spacing */
        }
        /* Additional Modal Styles */
        .modal-content {
            display: flex;
            flex-direction: column;
            border: 10px;

        }
        .close-btn {
            cursor: pointer;
            color: #333;
            margin-left: auto;
        }
        .footer {
            background-color: var(--white);
            border-top: var(--border);
            position: fixed;
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
          <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
          <a href="comm.php"><i class="fas fa-question"></i><span>community</span></a>
       </nav>
    </div>

    <section class="courses" style="position: relative;">
       <h1 class="heading">Our Courses</h1>
       <div class="box-container">
          <button id="add-course-btn" class="btn">Add Course</button>
       </div>

       <table class="courses-table">
           <thead>
               <tr>
                   <th>Course ID</th>
                   <th>Course Name</th>
                   <th>Duration</th>
                   <th>Description</th>
                 
               </tr>
           </thead>
           <tbody>
               <?php include 'add_course.php'; ?>
               <?php if (!empty($courses)): ?>
                   <?php foreach ($courses as $course): ?>
                       <tr>
                           <td><?php echo $course['course_id']; ?></td>
                           <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                           <td><?php echo htmlspecialchars($course['course_duration']); ?></td>
                           <td><?php echo htmlspecialchars($course['course_description']); ?></td>

                       </tr>
                   <?php endforeach; ?>
               <?php else: ?>
                   <tr>
                       <td colspan="5">No courses found.</td>
                   </tr>
               <?php endif; ?>
           </tbody>
       </table>
    </section>

    <!-- Add Course Modal -->
   <div id="add-course-modal" class="modal" style="position: relative;">
   <div class="modal-content">
      <button class="close-btn">&times;</button> <!-- Close button now styled -->
      <h2>Add New Course</h2>
      <form id="add-course-form" method="POST" enctype="multipart/form-data">
         <div class="form-row">
            <div class="input-box">
               <label for="course-name">Course Name:</label>
               <input type="text" id="course-name" name="course_name" required placeholder="Enter course name">
            </div>
            <div class="input-box">
               <label for="course-duration">Duration:</label>
               <input type="text" id="course-duration" name="course_duration" required placeholder="e.g., 10 hours">
            </div>
         </div>
         <div class="form-row">
            <div class="input-box full-width">
               <label for="course-description">Description:</label>
               <textarea id="course-description" name="course_description" required placeholder="Enter course description"></textarea>
            </div>
         </div>
         <div class="form-row">
            <div class="input-box">
               <label for="course-video">Upload Video:</label>
               <input type="file" id="course-video" name="course_video" accept="video/*">
            </div>
            <div class="input-box">
               <label for="course-document">Upload Document:</label>
               <input type="file" id="course-document" name="course_document" accept=".pdf, .doc, .docx">
            </div>
         </div>
         <input type="submit" class="btn" value="Add Course">
      </form>
   </div>
</div>

<footer class="footer">

   &copy; 2024 YanBu Hub, all rights reserved!

</footer>
<script src="js/script.js"></script>
    <script>
       // JavaScript to handle modal display
       const addCourseBtn = document.getElementById('add-course-btn');
       const modal = document.getElementById('add-course-modal');
       const closeBtn = document.querySelector('.close-btn');

       addCourseBtn.onclick = function () {
          modal.style.display = 'block';
       }

       closeBtn.onclick = function () {
          modal.style.display = 'none';
       }

       window.onclick = function (event) {
          if (event.target === modal) {
             modal.style.display = 'none';
          }
       }
       document.getElementById('add-course-form').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent form from submitting the traditional way

            const formData = new FormData(this); // Create a FormData object with the form fields

            // Send the AJAX request
            fetch('add_course.php', {
               method: 'POST',
               body: formData,
            })
            .then(response => response.json())
            .then(data => {
               if (data.success) {
                  alert("Course added successfully!"); // Show success message
                  modal.style.display = 'none'; // Close the modal
                  location.reload(); // Reload the page to refresh the course list
               } else {
                  alert("Failed to add course: " + data.message); // Show error message
               }
            })
            .catch(error => {
               console.error("Error:", error);
               alert("An error occurred while adding the course.");
            });
         });
    </script>
</body>
</html>
