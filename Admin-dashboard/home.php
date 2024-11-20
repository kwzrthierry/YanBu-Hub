<?php
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
$userId = $_SESSION['user_info']['user_id'];

// Get the count of registered trainers, learners, and courses
$trainer_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'trainer'")->fetch_assoc()['count'];
$learner_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'learner'")->fetch_assoc()['count'];
$course_count = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];

// Fetch upcoming events
$events = $conn->query("SELECT event_name, event_date FROM events WHERE event_date >= CURDATE() ORDER BY event_date LIMIT 5");

// Fetch recent notifications
$notifications = $conn->query("SELECT title, message FROM notifications ORDER BY date_posted DESC LIMIT 5");

// Fetch monthly progress (completed courses in the current month)
$current_month = date('Y-m');
$lessons_completed = $conn->query("SELECT COUNT(*) as count FROM enrollment WHERE status = 'completed' AND DATE_FORMAT(completed_at, '%Y-%m') = '$current_month'")->fetch_assoc()['count'];

// Fetch support messages for the admin
$support_messages = $conn->query("SELECT message, response FROM support ORDER BY created_at DESC LIMIT 5");

// Fetch recent posts from the 'posts' table
$news_posts = $conn->query("SELECT post_description, post_date FROM posts ORDER BY post_date DESC LIMIT 5");

// Fetch user-specific tasks
$user_tasks = $conn->query("SELECT task_id, description, due_date, status FROM tasks WHERE user_id = '$userId' AND due_date >= CURDATE() ORDER BY due_date LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>YanBu Hub - Admin Dashboard</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">

   <!-- Custom styles -->
   <style>
      .counter-cards, .extra-sections {
         display: flex;
         flex-wrap: wrap;
         gap: 20px;
         margin-top: 30px;
         justify-content: space-around;
      }

      .counter-card, .info-card {
         background: #f4f4f4;
         padding: 20px;
         border-radius: 8px;
         flex: 1;
         min-width: 250px;
         text-align: center;
         box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
         transition: background-color 0.3s ease;
      }

      .counter-card:hover, .info-card:hover {
         background-color: #e0e0e0;
      }

      .counter-card h3, .info-card h3 {
         font-size: 24px;
         color: #333;
      }

      .counter-card p, .info-card p {
         font-size: 18px;
         color: #666;
      }

      .counter-card i, .info-card i {
         font-size: 40px;
         color: #007bff;
         margin-bottom: 10px;
      }

      .calendar, .support-section, .news-section {
         flex: 1;
         min-width: 300px;
      }

      .list-items {
         list-style: none;
         padding: 0;
      }

      .list-items li {
         background: #fff;
         padding: 10px;
         border-bottom: 1px solid #ddd;
      }

      .footer {
          background-color: var(--white);
          border-top: var(--border);
          position: fixed;
          bottom: 0;
          left: 0;
          right: 0;
          text-align: center;
          font-size: 1rem;
          padding: 1rem;
          color: var(--black);
          margin-top: 1rem;
          z-index: 1000;
      }

      /* Calendar styles */
      .calendar {
         margin-top: 20px;
         display: flex;
         justify-content: center;
         flex-direction: column;
         align-items: center;
      }

      .calendar table {
         border-collapse: collapse;
         width: 100%;
         max-width: 400px;
         text-align: center;
      }

      .calendar th, .calendar td {
         border: 1px solid #ddd;
         padding: 10px;
      }

      .calendar th {
         background-color: #007bff;
         color: white;
      }

      .calendar td:hover {
         background-color: #e0e0e0;
         cursor: pointer;
      }

      .btn-add {
         margin-top: 15px;
         display: flex;
         justify-content: space-between;
         align-items: center;
         gap: 10px;
      }

      .btn-add button {
         padding: 10px 20px;
         background-color: #007bff;
         color: white;
         border: none;
         border-radius: 5px;
         cursor: pointer;
         transition: background-color 0.3s;
      }

      .btn-add button:hover {
         background-color: #0056b3;
      }
      .modal {
          display: none; /* Hidden by default */
          position: fixed; /* Stay in place */
          z-index: 1000; /* Sit on top */
          left: 0;
          top: 0;
          width: 100%; /* Full width */
          height: 100%; /* Full height */
          overflow: hidden; /* Disable scrolling */
          background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
      }

      .modal-content {
          background-color: #fefefe;
          position: absolute; /* Changed from margin to position */
          left: 50%; /* Center horizontally */
          top: 50%; /* Center vertically */
          transform: translate(-50%, -50%); /* Adjust for element size */
          padding: 20px;
          border: 1px solid #888;
          width: 80%; /* Could be more or less, depending on screen size */
          max-width: 600px; /* Set a max-width */
          border-radius: 8px;
      }

      .close {
          color: #aaa;
          float: right;
          font-size: 28px;
          font-weight: bold;
      }

      .close:hover,
      .close:focus {
          color: black;
          text-decoration: none;
          cursor: pointer;
      }

      label {
          font-size: 18px;
          margin-bottom: 10px;
          display: block;
      }

      input[type="text"],
      input[type="date"],
      textarea {
          width: 100%;
          padding: 12px;
          margin: 5px 0 15px;
          border: 1px solid #ccc;
          border-radius: 4px;
          font-size: 16px; /* Increased font size */
      }

      button {
          background-color: #007bff;
          color: white;
          padding: 10px;
          border: none;
          border-radius: 4px;
          cursor: pointer;
          font-size: 16px; /* Increased font size */
      }

      button:hover {
          background-color: #0056b3;
      }

      #eventMessage,
      #taskMessage {
          margin-top: 10px;
          font-size: 18px; /* Increased font size */
          color: green; /* Success message color */
      }
      .info-card {
          padding: 20px; /* Padding around the card */
          background: #f4f4f4; /* Card background */
          border-radius: 8px; /* Rounded corners */
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect */
      }

      .task-table {
          width: 100%; /* Full width of the card */
          border-collapse: collapse; /* Merge borders */
          margin-top: 20px; /* Space above the table */
      }

      .task-table th, .task-table td {
          border: 1px solid #ddd; /* Table cell borders */
          padding: 10px; /* Padding inside cells */
          text-align: left; /* Left alignment of text */
      }

      .task-table th {
          background-color: #007bff; /* Header background color */
          color: white; /* Header text color */
      }

      .task-table tr:hover {
          background-color: #f1f1f1; /* Row hover effect */
      }

      .btn-done {
          padding: 6px 12px; /* Button padding */
          background-color: #28a745; /* Green background for Done button */
          color: white; /* Button text color */
          border: none; /* No border */
          border-radius: 4px; /* Rounded corners */
          cursor: pointer; /* Pointer cursor on hover */
          transition: background-color 0.3s; /* Smooth background transition */
      }

      .btn-done:hover {
          background-color: #218838; /* Darker green on hover */
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

<section class="home-grid">
   <div class="counter-cards">
      <!-- Analytics cards -->
      <div class="counter-card">
         <i class="fas fa-calendar-check"></i>
         <h3 class="count-up" data-count="<?php echo $lessons_completed; ?>">0</h3>
         <p>Lessons Completed This Month</p>
      </div>
      
      <!-- Existing analytics -->
      <div class="counter-card" onclick="window.location.href='trainers.php'">
         <i class="fas fa-chalkboard-teacher"></i>
         <h3 class="count-up" data-count="<?php echo $trainer_count; ?>">0</h3>
         <p>Registered Trainers</p>
      </div>
      
      <div class="counter-card" onclick="window.location.href='learners.php'">
         <i class="fas fa-user-graduate"></i>
         <h3 class="count-up" data-count="<?php echo $learner_count; ?>">0</h3>
         <p>Registered Learners</p>
      </div>
      
      <div class="counter-card" onclick="window.location.href='courses.php'">
         <i class="fas fa-book"></i>
         <h3 class="count-up" data-count="<?php echo $course_count; ?>">0</h3>
         <p>Courses Available</p>
      </div>
   </div>

   <div class="extra-sections">
      <!-- Upcoming Events Section -->
      <div class="info-card">
         <div class="btn-add">
            <h3>Upcoming Events</h3>
            <button onclick="openModal('addEventModal')">Add Event</button>
         </div>
         <ul class="list-items">
         <?php if ($events->num_rows > 0) {
            while ($event = $events->fetch_assoc()) { ?>
               <li><?php echo $event['event_name'] . ' on ' . $event['event_date']; ?></li>
         <?php } } else { ?>
            <li>No events available</li>
         <?php } ?>
         </ul>
      </div>

      <!-- Recent Posts / News Section -->
      <div class="info-card news-section">
         <h3>Recent Posts</h3>
         <ul class="list-items">
         <?php if ($news_posts->num_rows > 0) {
            while ($news = $news_posts->fetch_assoc()) { ?>
               <li><?php echo $news['post_description'] . ' - ' . $news['post_date']; ?></li>
         <?php } } else { ?>
            <li>No news available</li>
         <?php } ?>
         </ul>
      </div>
   </div>

   <div class="extra-sections">
      <!-- Support Messages Section -->
      <div class="info-card support-section">
         <h3>Support Messages</h3>
         <ul class="list-items">
         <?php if ($support_messages->num_rows > 0) {
            while ($message = $support_messages->fetch_assoc()) { ?>
               <li><?php echo $message['message'] . ' - ' . $message['response']; ?></li>
         <?php } } else { ?>
            <li>No support messages available</li>
         <?php } ?>
         </ul>
      </div>

      <!-- Tasks Section -->
      <div class="info-card">
          <div class="btn-add">
              <h3>Your Tasks</h3>
              <button onclick="openModal('addTaskModal')">Add Task</button>
          </div>
          <table class="task-table">
              <thead>
                  <tr>
                      <th>Description</th>
                      <th>Due Date</th>
                      <th>Status</th>
                      <th>Actions</th>
                  </tr>
              </thead>
              <tbody>
              <?php if ($user_tasks->num_rows > 0) {
                  while ($task = $user_tasks->fetch_assoc()) { ?>
                      <tr>
                          <td><?php echo htmlspecialchars($task['description']); ?></td>
                          <td><?php echo htmlspecialchars($task['due_date']); ?></td>
                          <td><?php echo htmlspecialchars($task['status']); ?></td>
                          <td>
                              <?php if ($task['status'] == 'pending') { ?>
                                  <form action="mark_done.php" method="POST" style="display:inline;">
                                      <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                                      <button type="submit">Done</button>
                                  </form>
                              <?php } ?>
                          </td>
                      </tr>
              <?php } } else { ?>
                  <tr>
                      <td colspan="4">No tasks available</td>
                  </tr>
              <?php } ?>
              </tbody>
          </table>
      </div>
   </div>

</section>
<!-- Add Event Modal -->
   <div id="addEventModal" class="modal">
      <div class="modal-content">
         <span class="close" onclick="closeModal('addEventModal')">&times;</span>
         <h2>Add Event</h2>
         <form id="eventForm">
            <label for="event_name">Event Name:</label>
            <input type="text" id="event_name" name="event_name" required>
            
            <label for="event_date">Event Date:</label>
            <input type="date" id="event_date" name="event_date" required>
            
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea>
            
            <button type="submit">Add Event</button>
         </form>
         <div id="eventMessage"></div>
      </div>
   </div>

   <!-- Add Task Modal -->
   <div id="addTaskModal" class="modal">
      <div class="modal-content">
         <span class="close" onclick="closeModal('addTaskModal')">&times;</span>
         <h2>Add Task</h2>
         <form id="taskForm">
            <label for="description">Description:</label>
            <input type="text" id="task_description" name="description" required>
            
            <label for="due_date">Due Date:</label>
            <input type="date" id="due_date" name="due_date" required>
            
            <button type="submit">Add Task</button>
         </form>
         <div id="taskMessage"></div>
      </div>
   </div>

<footer class="footer">
   <p>&copy; 2024 YanBu Hub. All Rights Reserved.</p>
</footer>
<script src="js/script.js"></script>

<!-- Optional JavaScript for interactive functionality -->
<script>
   document.querySelectorAll('.count-up').forEach(el => {
      let countTo = el.getAttribute('data-count');
      let count = 0;
      let increment = Math.ceil(countTo / 50); // speed up the count up

      let interval = setInterval(() => {
         count += increment;
         el.innerText = count;
         if (count >= countTo) {
            el.innerText = countTo;
            clearInterval(interval);
         }
      }, 30); // increase the interval for faster animation
   });
   // Function to open modal
   function openModal(modalId) {
      document.getElementById(modalId).style.display = "block";
   }

   // Function to close modal
   function closeModal(modalId) {
      document.getElementById(modalId).style.display = "none";
   }

   // Event listener for the event form submission
   document.getElementById('eventForm').addEventListener('submit', function(event) {
      event.preventDefault();
      const formData = new FormData(this);
      fetch('add_event.php', {
         method: 'POST',
         body: formData
      })
      .then(response => response.text())
      .then(data => {
         document.getElementById('eventMessage').innerHTML = data;
         closeModal('addEventModal');
         location.reload(); // refresh the page
      })
      .catch(error => console.error('Error:', error));
   });

   // Event listener for the task form submission
   document.getElementById('taskForm').addEventListener('submit', function(event) {
      event.preventDefault();
      const formData = new FormData(this);
      fetch('add_task.php', {
         method: 'POST',
         body: formData
      })
      .then(response => response.text())
      .then(data => {
         document.getElementById('taskMessage').innerHTML = data;
         closeModal('addTaskModal');
         location.reload(); // refresh the page
      })
      .catch(error => console.error('Error:', error));
   });

</script>

</body>
</html>
