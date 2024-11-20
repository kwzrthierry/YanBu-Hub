-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2024 at 03:02 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yanbu_hub`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_password` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `comment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(80) NOT NULL,
  `course_duration` varchar(20) NOT NULL,
  `course_description` varchar(1000) NOT NULL,
  `course_entry_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `video_path` varchar(120) NOT NULL,
  `document_path` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `course_duration`, `course_description`, `course_entry_date`, `video_path`, `document_path`) VALUES
(1, 'web Design', '50 weeks', 'here yoou will study different languanges included in website development includind js , html and css', '2024-10-17 22:07:32', 'uploads/web Design/videos/T.mp4', 'uploads/web Design/documents/Copy of Norwegian Aid New Staff Entry Assessment Form.docx');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment`
--

CREATE TABLE `enrollment` (
  `enroll_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `price` decimal(10,2) NOT NULL,
  `status` enum('ongoing','completed') NOT NULL,
  `completed_at` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `event_date`, `description`) VALUES
(1, 'bootcamp', '2024-10-20', 'go'),
(2, 'bootcamp', '2024-10-13', 'bbb');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `like_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `like_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `status` enum('sent','failed') DEFAULT 'sent'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `user_id`, `message`, `timestamp`, `status`) VALUES
(1, 5, 'welcome', '2024-10-17 21:30:11', 'sent');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date_posted` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_description` text NOT NULL,
  `post_content` text NOT NULL,
  `post_date` datetime DEFAULT current_timestamp(),
  `saves` int(11) DEFAULT 0,
  `like_id` int(11) DEFAULT NULL,
  `Comment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `user_id`, `post_description`, `post_content`, `post_date`, `saves`, `like_id`, `Comment_id`) VALUES
(1, 5, 'inka', 'ibya', '2024-10-17 14:03:53', 0, NULL, NULL),
(2, 7, 'web', 'good morning', '2024-10-18 14:32:34', 0, NULL, NULL),
(4, 7, 'web', 'good morning', '2024-10-18 14:34:20', 0, NULL, NULL),
(5, 7, 'web', 'bbb', '2024-10-18 14:59:23', 0, NULL, NULL),
(6, 7, 'hey there', 'some new information coming soon', '2024-10-18 15:00:03', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `saved_posts`
--

CREATE TABLE `saved_posts` (
  `save_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `save_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support`
--

CREATE TABLE `support` (
  `support_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `response` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `due_date` varchar(30) DEFAULT NULL,
  `status` enum('pending','completed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`task_id`, `user_id`, `description`, `due_date`, `status`) VALUES
(0, 7, 'add english course', '2024-10-21', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `time_records`
--

CREATE TABLE `time_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time_spent` int(11) NOT NULL,
  `record_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time_records`
--

INSERT INTO `time_records` (`id`, `user_id`, `time_spent`, `record_date`) VALUES
(1, 5, 0, '2024-10-17 00:00:00'),
(2, 5, 0, '2024-10-17 00:00:00'),
(3, 5, 0, '2024-10-17 00:00:00'),
(4, 5, 0, '2024-10-17 00:00:00'),
(5, 5, 0, '2024-10-17 00:00:00'),
(6, 5, 0, '2024-10-17 00:00:00'),
(7, 5, 0, '2024-10-17 00:00:00'),
(8, 5, 0, '2024-10-17 00:00:00'),
(9, 5, 0, '2024-10-17 00:00:00'),
(10, 5, 0, '2024-10-17 00:00:00'),
(11, 5, 0, '2024-10-17 00:00:00'),
(12, 5, 0, '2024-10-17 00:00:00'),
(13, 5, 0, '2024-10-17 00:00:00'),
(14, 5, 0, '2024-10-17 00:00:00'),
(15, 5, 0, '2024-10-17 00:00:00'),
(16, 5, 0, '2024-10-17 00:00:00'),
(17, 5, 0, '2024-10-17 00:00:00'),
(18, 5, 0, '2024-10-17 00:00:00'),
(19, 5, 0, '2024-10-17 00:00:00'),
(20, 5, 0, '2024-10-17 00:00:00'),
(21, 5, 0, '2024-10-17 00:00:00'),
(22, 5, 0, '2024-10-17 00:00:00'),
(23, 5, 0, '2024-10-17 00:00:00'),
(24, 5, 0, '2024-10-17 00:00:00'),
(25, 6, 0, '2024-10-17 00:00:00'),
(26, 6, 0, '2024-10-17 00:00:00'),
(27, 6, 0, '2024-10-17 00:00:00'),
(28, 6, 0, '2024-10-17 00:00:00'),
(29, 6, 0, '2024-10-17 00:00:00'),
(30, 6, 0, '2024-10-17 00:00:00'),
(31, 6, 0, '2024-10-17 00:00:00'),
(32, 6, 0, '2024-10-17 00:00:00'),
(33, 6, 0, '2024-10-17 00:00:00'),
(34, 6, 0, '2024-10-17 00:00:00'),
(35, 6, 0, '2024-10-17 00:00:00'),
(36, 6, 0, '2024-10-17 00:00:00'),
(37, 6, 0, '2024-10-17 00:00:00'),
(38, 6, 0, '2024-10-17 00:00:00'),
(39, 6, 0, '2024-10-17 00:00:00'),
(40, 6, 0, '2024-10-17 00:00:00'),
(41, 6, 0, '2024-10-17 00:00:00'),
(42, 6, 0, '2024-10-17 00:00:00'),
(43, 6, 0, '2024-10-17 00:00:00'),
(44, 6, 0, '2024-10-17 00:00:00'),
(45, 6, 0, '2024-10-17 00:00:00'),
(46, 6, 0, '2024-10-17 00:00:00'),
(47, 6, 0, '2024-10-17 00:00:00'),
(48, 6, 0, '2024-10-17 00:00:00'),
(49, 6, 0, '2024-10-17 00:00:00'),
(50, 6, 0, '2024-10-17 00:00:00'),
(51, 6, 0, '2024-10-17 00:00:00'),
(52, 6, 0, '2024-10-17 00:00:00'),
(53, 6, 0, '2024-10-17 00:00:00'),
(54, 6, 0, '2024-10-17 00:00:00'),
(55, 6, 0, '2024-10-17 00:00:00'),
(56, 6, 0, '2024-10-17 00:00:00'),
(57, 5, 0, '2024-10-17 00:00:00'),
(58, 5, 0, '2024-10-17 00:00:00'),
(59, 5, 0, '2024-10-17 00:00:00'),
(60, 5, 0, '2024-10-17 00:00:00'),
(61, 5, 0, '2024-10-17 00:00:00'),
(62, 6, 0, '2024-10-17 00:00:00'),
(63, 6, 0, '2024-10-17 00:00:00'),
(64, 5, 0, '2024-10-17 00:00:00'),
(65, 6, 0, '2024-10-17 00:00:00'),
(66, 6, 0, '2024-10-17 00:00:00'),
(67, 6, 0, '2024-10-17 00:00:00'),
(68, 6, 0, '2024-10-17 00:00:00'),
(69, 6, 0, '2024-10-17 00:00:00'),
(70, 6, 0, '2024-10-17 00:00:00'),
(71, 6, 0, '2024-10-17 00:00:00'),
(72, 6, 0, '2024-10-17 00:00:00'),
(73, 6, 0, '2024-10-17 00:00:00'),
(74, 6, 0, '2024-10-17 00:00:00'),
(75, 6, 0, '2024-10-17 00:00:00'),
(76, 6, 0, '2024-10-17 00:00:00'),
(77, 6, 0, '2024-10-17 00:00:00'),
(78, 6, 0, '2024-10-17 00:00:00'),
(79, 6, 0, '2024-10-17 00:00:00'),
(80, 6, 0, '2024-10-17 00:00:00'),
(81, 6, 0, '2024-10-17 00:00:00'),
(82, 6, 0, '2024-10-17 00:00:00'),
(83, 6, 0, '2024-10-17 00:00:00'),
(84, 6, 0, '2024-10-17 00:00:00'),
(85, 6, 0, '2024-10-17 00:00:00'),
(86, 5, 0, '2024-10-17 00:00:00'),
(87, 6, 0, '2024-10-17 00:00:00'),
(88, 6, 0, '2024-10-17 00:00:00'),
(89, 6, 0, '2024-10-17 00:00:00'),
(90, 6, 0, '2024-10-17 00:00:00'),
(91, 6, 0, '2024-10-17 00:00:00'),
(92, 5, 0, '2024-10-17 00:00:00'),
(93, 6, 0, '2024-10-17 00:00:00'),
(94, 5, 0, '2024-10-18 00:00:00'),
(95, 5, 0, '2024-10-18 00:00:00'),
(96, 5, 0, '2024-10-18 00:00:00'),
(97, 5, 0, '2024-10-18 00:00:00'),
(98, 5, 0, '2024-10-18 00:00:00'),
(99, 5, 0, '2024-10-18 00:00:00'),
(100, 6, 0, '2024-10-18 00:00:00'),
(101, 5, 0, '2024-10-18 00:00:00'),
(102, 5, 0, '2024-10-18 00:00:00'),
(103, 5, 0, '2024-10-18 00:00:00'),
(104, 5, 0, '2024-10-18 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `trainer_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trainer_name` varchar(120) NOT NULL,
  `speciality` varchar(255) NOT NULL,
  `day_joiined` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(60) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(400) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `gender` varchar(7) NOT NULL,
  `time_registered` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_type` enum('learner','admin','trainer','employer') NOT NULL DEFAULT 'learner',
  `profile_pic` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password`, `phone_number`, `gender`, `time_registered`, `user_type`, `profile_pic`) VALUES
(5, 'kwizera', 'thierry', 'micky@gmail.com', '$2y$10$WAu9zeNcGu1i1T1jkCB9a.gQALscP0s4j5xy/SRnPAwjO0KNZ7MYu', '788906652', 'Female', '2024-10-17 07:01:14', 'learner', ''),
(6, 'shyaka', 'crispin', 'shyaka@gmail.com', '1234', '123456', 'male', '2024-10-17 19:37:22', 'employer', ''),
(7, 'BENI', 'Yakini', 'beni@gmail.com', '1234', '23456789', 'female', '2024-10-17 21:29:52', 'admin', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD PRIMARY KEY (`enroll_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`like_id`),
  ADD UNIQUE KEY `post_id` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `saved_posts`
--
ALTER TABLE `saved_posts`
  ADD PRIMARY KEY (`save_id`),
  ADD UNIQUE KEY `post_id` (`post_id`,`user_id`),
  ADD KEY `fk_user_id_saved` (`user_id`);

--
-- Indexes for table `support`
--
ALTER TABLE `support`
  ADD PRIMARY KEY (`support_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`);

--
-- Indexes for table `time_records`
--
ALTER TABLE `time_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`trainer_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enrollment`
--
ALTER TABLE `enrollment`
  MODIFY `enroll_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `saved_posts`
--
ALTER TABLE `saved_posts`
  MODIFY `save_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support`
--
ALTER TABLE `support`
  MODIFY `support_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `time_records`
--
ALTER TABLE `time_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `trainer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD CONSTRAINT `enrollment_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `likes` (`like_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `saved_posts`
--
ALTER TABLE `saved_posts`
  ADD CONSTRAINT `fk_post_id_saved` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_id_saved` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `time_records`
--
ALTER TABLE `time_records`
  ADD CONSTRAINT `time_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `trainers`
--
ALTER TABLE `trainers`
  ADD CONSTRAINT `trainers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `trainers_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
