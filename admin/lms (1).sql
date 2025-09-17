-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 13, 2025 at 09:10 PM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `exam_year` year DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `allow_payments` varchar(50) NOT NULL DEFAULT 'Yes - Allow payments',
  `class_type` varchar(50) NOT NULL DEFAULT 'Paid Class',
  `category` varchar(100) DEFAULT 'uncategorized',
  `difficulty` varchar(50) DEFAULT 'all-levels',
  `description` text,
  `thumbnail_url` varchar(255) DEFAULT NULL,
  `payment_info_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `title`, `exam_year`, `price`, `status`, `allow_payments`, `class_type`, `category`, `difficulty`, `description`, `thumbnail_url`, `payment_info_url`, `created_at`) VALUES
(1, 'new', 2025, '300.00', 'Active', 'Yes - Allow payments', 'Paid Class', 'uncategorized', 'all-levels', 'abcd', '../uploads/thumbnails/class-68c4a6d99d435.jpg', NULL, '2025-09-12 23:03:53'),
(2, 'web2', 2025, '10000.00', 'Active', 'No - Do not allow', 'Paid Class', 'uncategorized', 'all-levels', '', '../uploads/thumbnails/class-68c5d54e5071b.webp', NULL, '2025-09-13 20:34:22'),
(3, 'web2', 2025, '10000.00', 'Active', 'No - Do not allow', 'Paid Class', 'uncategorized', 'all-levels', '', '../uploads/thumbnails/class-68c5da3734304.webp', NULL, '2025-09-13 20:55:19');

-- --------------------------------------------------------

--
-- Table structure for table `class_recordings`
--

DROP TABLE IF EXISTS `class_recordings`;
CREATE TABLE IF NOT EXISTS `class_recordings` (
  `class_id` int NOT NULL,
  `recording_id` int NOT NULL,
  PRIMARY KEY (`class_id`,`recording_id`),
  KEY `recording_id` (`recording_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `class_recordings`
--

INSERT INTO `class_recordings` (`class_id`, `recording_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
CREATE TABLE IF NOT EXISTS `enrollments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `class_id` int NOT NULL,
  `enrollment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_class_unique` (`user_id`,`class_id`),
  KEY `class_id` (`class_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `user_id`, `class_id`, `enrollment_date`) VALUES
(1, 1, 1, '2025-09-13 19:48:20'),
(2, 1, 2, '2025-09-13 20:38:20');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `class_id` int NOT NULL,
  `reference_number` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Paid','Rejected') NOT NULL DEFAULT 'Pending',
  `slip_image_url` varchar(255) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `class_id` (`class_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recordings`
--

DROP TABLE IF EXISTS `recordings`;
CREATE TABLE IF NOT EXISTS `recordings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `video_type` enum('YouTube','Vimeo') DEFAULT 'YouTube',
  `video_url` varchar(255) NOT NULL,
  `duration_minutes` int DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `recordings`
--

INSERT INTO `recordings` (`id`, `title`, `video_type`, `video_url`, `duration_minutes`, `description`, `created_at`) VALUES
(1, 'aaaaaaaaa', 'YouTube', 'https://youtu.be/9YKnMhNs4N0?si=CIeZOVAJqAyzN3PS', 12, '', '2025-09-13 19:17:45');

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

DROP TABLE IF EXISTS `student_profiles`;
CREATE TABLE IF NOT EXISTS `student_profiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `exam_year` year DEFAULT NULL,
  `institute` varchar(100) DEFAULT NULL,
  `student_type` varchar(100) DEFAULT NULL,
  `stream` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `whatsapp_number` varchar(20) DEFAULT NULL,
  `nic_number` varchar(20) DEFAULT NULL,
  `school` varchar(255) DEFAULT NULL,
  `notes` text,
  `parent_name` varchar(255) DEFAULT NULL,
  `parent_contact` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for student, 1 for admin',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `is_admin`, `created_at`) VALUES
(1, 'Admin', 'admin@example.com', '$2y$10$3XCqlaZFWGCZ7ubqN5Sx/Of0whoCZtVvVJOWwm78U/M7tf5JUxTcO', 1, '2025-09-12 18:40:58');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
