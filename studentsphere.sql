-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2025 at 06:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `studentsphere`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `item` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `action`, `item`, `created_at`) VALUES
(1, 1, 'Test', 'Debug Activity', '2025-09-15 13:51:53');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `icon` varchar(255) DEFAULT 'fas fa-folder',
  `link` varchar(255) DEFAULT '#'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `icon`, `link`) VALUES
(1, 'Sports', 'All sporting events on campus', '2025-09-10 13:18:07', 'fas fa-basketball-ball', 'sports.php'),
(2, 'Debates', 'Debate competitions and workshops', '2025-09-10 13:18:07', 'fas fa-microphone', 'debates.php'),
(3, 'Culture & Arts', 'Cultural and arts events', '2025-09-10 13:18:07', 'fas fa-paint-brush', 'culture.php'),
(4, 'Charity', 'Charity and volunteering events', '2025-09-10 13:18:07', 'fas fa-hand-holding-heart', 'charity.php');

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`id`, `name`, `description`, `created_by`, `created_at`) VALUES
(1, 'Drama Club', 'All students interested in drama and theater', NULL, '2025-09-10 13:18:07'),
(2, 'Chess Club', 'Competitive and casual chess enthusiasts', NULL, '2025-09-10 13:18:07'),
(3, 'Music Club', 'For students passionate about music', NULL, '2025-09-10 13:18:07'),
(4, 'Basketball Club', 'For all basketball enthusiasts.', 1, '2025-09-10 13:34:41'),
(5, 'Debate Society', 'Sharpen your debating and public speaking skills.', 1, '2025-09-10 13:34:41'),
(6, 'Drama & Arts Club', 'Creative arts, drama, and music lovers.', 1, '2025-09-10 13:34:41'),
(7, 'Charity Club', 'Volunteer for community impact.', 1, '2025-09-10 13:34:41');

-- --------------------------------------------------------

--
-- Table structure for table `club_members`
--

CREATE TABLE `club_members` (
  `id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `club_members`
--

INSERT INTO `club_members` (`id`, `club_id`, `user_id`, `joined_at`) VALUES
(1, 1, 2, '2025-09-10 13:18:08'),
(2, 2, 3, '2025-09-10 13:18:08'),
(3, 3, 2, '2025-09-10 13:18:08');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `category_id`, `title`, `description`, `location`, `created_by`, `event_date`, `image`, `created_at`) VALUES
(1, 1, 'Basketball Tournament Finals', 'Finals of the university basketball tournament', 'Main Gymnasium', NULL, '2025-09-28 15:00:00', 'event1.jpg', '2025-09-10 13:18:07'),
(2, 2, 'Debate Society Open Meeting', 'Open meeting for students interested in debating', 'Student Union', NULL, '2025-10-02 18:00:00', 'event2.jpg', '2025-09-10 13:18:07'),
(3, 3, 'Art Exhibition Opening', 'Exhibition showcasing student artworks', 'Campus Gallery', NULL, '2025-10-05 16:00:00', 'event3.jpg', '2025-09-10 13:18:07'),
(4, 4, 'Charity Run Registration', 'Sign up for the university charity run', 'University Park', NULL, '2025-09-15 00:00:00', 'event4.jpg', '2025-09-10 13:18:07'),
(5, 1, 'Basketball Tournament Finals', 'Finals of the university basketball tournament', 'Main Gymnasium', 1, '2025-09-28 15:00:00', 'event1.jpg', '2025-09-10 13:34:41'),
(6, 2, 'Debate Society Open Meeting', 'Open meeting for students interested in debating', 'Student Union', 1, '2025-10-02 18:00:00', 'event2.jpg', '2025-09-10 13:34:41'),
(7, 3, 'Art Exhibition Opening', 'Exhibition showcasing student artworks', 'Campus Gallery', 1, '2025-10-05 16:00:00', 'event3.jpg', '2025-09-10 13:34:41'),
(8, 4, 'Charity Run Registration', 'Sign up for the university charity run', 'University Park', 1, '2025-10-10 14:00:00', 'event4.jpg', '2025-09-10 13:34:41'),
(9, 1, 'Annual Sports Day', 'University-wide annual sports event', 'University Stadium', 1, '2025-10-15 10:00:00', 'event5.jpg', '2025-09-10 13:34:41'),
(11, 4, 'demo', 'demo', 'demo', NULL, '2025-09-18 00:00:00', 'uploads/events/1757930752_contacts for grace textile and comfoam agents.jpg', '2025-09-15 10:05:52');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `author_id`, `image_url`, `image`, `created_at`, `updated_at`) VALUES
(4, 'New Debate Club Forming', 'Students interested in debate are invited to the first organizational meeting.', 1, NULL, 'news1.jpg', '2025-09-10 13:42:08', '2025-09-15 12:42:02'),
(5, 'New Debate Club Forming', 'Students interested in debate are invited to the first organizational meeting.', 1, NULL, 'post_1757940221_68c809fdea9f8.avif', '2025-09-10 13:44:00', '2025-09-15 12:43:41'),
(6, 'Charity Drive Success', 'Over 500 items collected for local shelters during last week\'s drive.', 1, NULL, 'news2.jpg', '2025-09-10 13:44:00', '2025-09-15 12:42:02'),
(7, 'Sports Teams Tryouts', 'Basketball and soccer teams are holding tryouts next week. All skill levels welcome.', 1, NULL, 'news3.jpg', '2025-09-10 13:44:00', '2025-09-15 12:42:02'),
(11, 'New Library Extension Opens', 'The University has officially opened its new library extension, featuring modern study spaces, digital resources, and collaborative rooms for group discussions. This development aims to improve research and learning for all students.', 1, NULL, 'post_1757937835_68c800ab9cd2e.png', '2025-09-15 12:03:55', '2025-09-15 12:42:02'),
(12, 'University Sports Day 2025', 'The Annual Sports Day is scheduled for October 15th, 2025. Students from all faculties will compete in athletics, football, basketball, and more. Don’t miss the inter-faculty tug of war!', 1, NULL, 'post_1757938652_68c803dcaf020.avif', '2025-09-15 12:17:32', '2025-09-15 12:42:02');

-- --------------------------------------------------------

--
-- Table structure for table `post_categories`
--

CREATE TABLE `post_categories` (
  `post_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_categories`
--

INSERT INTO `post_categories` (`post_id`, `category_id`) VALUES
(5, 2),
(11, 3),
(12, 1);

-- --------------------------------------------------------

--
-- Table structure for table `post_images`
--

CREATE TABLE `post_images` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quick_links`
--

CREATE TABLE `quick_links` (
  `id` int(11) NOT NULL,
  `label` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `order_index` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quick_links`
--

INSERT INTO `quick_links` (`id`, `label`, `url`, `order_index`, `created_at`) VALUES
(1, 'Join a Club', 'join-club.php', 1, '2025-09-10 13:29:21'),
(2, 'Event Calendar', 'calendar.php', 2, '2025-09-10 13:29:21'),
(3, 'Campus Map', 'campus-map.php', 3, '2025-09-10 13:29:21'),
(4, 'Contact Us', 'contact.php', 4, '2025-09-10 13:29:21'),
(5, 'Join a Club', 'join-club.php', 1, '2025-09-10 13:30:11'),
(6, 'Event Calendar', 'calendar.php', 2, '2025-09-10 13:30:11'),
(7, 'Campus Map', 'campus-map.php', 3, '2025-09-10 13:30:11'),
(8, 'Contact Us', 'contact.php', 4, '2025-09-10 13:30:11'),
(9, 'Join a Club', 'join-club.php', 1, '2025-09-10 13:30:25'),
(10, 'Event Calendar', 'calendar.php', 2, '2025-09-10 13:30:25'),
(11, 'Campus Map', 'campus-map.php', 3, '2025-09-10 13:30:25'),
(12, 'Contact Us', 'contact.php', 4, '2025-09-10 13:30:25');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Student'),
(2, 'Club Leader'),
(3, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `slides`
--

CREATE TABLE `slides` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slides`
--

INSERT INTO `slides` (`id`, `title`, `description`, `image_url`, `link_url`, `status`, `created_at`) VALUES
(1, 'Annual University Sports Day', 'Join us on October 15th for the biggest sports event of the year', 'https://images.unsplash.com/photo-1543269865-cbf427effbad?auto=format&fit=crop&w=1200&h=400&q=80', '#', 'active', '2025-09-10 13:29:21'),
(2, 'Inter-University Debate Championship', 'Registration open until September 30th', 'https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=1200&h=400&q=80', '#', 'active', '2025-09-10 13:29:21'),
(3, 'Cultural Festival 2023', 'Experience the diversity of our campus culture November 5-7', 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1200&h=400&q=80', '#', 'active', '2025-09-10 13:29:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role_id`, `created_at`) VALUES
(1, 'Admin User', 'admin@studentsphere.com', 'adminpass', 3, '2025-09-10 13:18:07'),
(2, 'John Doe', 'johndoe@student.com', 'password123', 1, '2025-09-10 13:18:07'),
(3, 'Jane Smith', 'janesmith@student.com', 'password123', 1, '2025-09-10 13:18:07'),
(5, 'John Mulumba', 'johnmulumba@student.com', 'password123', 1, '2025-09-15 13:14:40'),
(7, 'Robert Mugabe', 'RobertMugabe@students.com', 'password123', 1, '2025-09-15 13:24:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_activity_log_user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_clubs_user` (`created_by`);

--
-- Indexes for table `club_members`
--
ALTER TABLE `club_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `club_id` (`club_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `fk_events_user` (`created_by`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_posts_user` (`author_id`);

--
-- Indexes for table `post_categories`
--
ALTER TABLE `post_categories`
  ADD PRIMARY KEY (`post_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `post_images`
--
ALTER TABLE `post_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `quick_links`
--
ALTER TABLE `quick_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slides`
--
ALTER TABLE `slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `club_members`
--
ALTER TABLE `club_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `post_images`
--
ALTER TABLE `post_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `quick_links`
--
ALTER TABLE `quick_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `slides`
--
ALTER TABLE `slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `fk_activity_log_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `clubs`
--
ALTER TABLE `clubs`
  ADD CONSTRAINT `fk_clubs_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `club_members`
--
ALTER TABLE `club_members`
  ADD CONSTRAINT `club_members_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `club_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_events_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_user` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `post_categories`
--
ALTER TABLE `post_categories`
  ADD CONSTRAINT `post_categories_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_images`
--
ALTER TABLE `post_images`
  ADD CONSTRAINT `post_images_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
