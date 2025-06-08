-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2025 at 03:56 PM
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
-- Database: `ocrms`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `admin_id`, `action`, `timestamp`) VALUES
(1, 1, 'Updated profile information, including profile image.', '2025-05-10 23:06:10'),
(2, 1, 'Created a new brand: reyna', '2025-05-10 23:11:08'),
(3, 1, 'Deleted a brand: reyna', '2025-05-10 23:14:08'),
(4, 1, 'Updated brand: Aud', '2025-05-10 23:23:51'),
(5, 1, 'Updated brand: Audi', '2025-05-10 23:23:58'),
(6, 1, 'Cancelled booking ID: 1', '2025-05-10 23:29:46'),
(7, 1, 'Confirmed booking ID: 5', '2025-05-10 23:30:42'),
(8, 1, 'Confirmed booking ID: 5', '2025-05-10 23:30:46'),
(9, 1, 'Cancelled booking ID: 1', '2025-05-10 23:30:55'),
(10, 1, 'Cancelled booking ID: 1', '2025-05-10 23:30:57'),
(11, 1, 'Cancelled booking ID: 1', '2025-05-10 23:31:54'),
(12, 1, 'Cancelled booking ID: 1', '2025-05-10 23:31:57'),
(13, 1, 'Cancelled booking ID: 1', '2025-05-10 23:32:02'),
(14, 1, 'Confirmed booking ID: 1', '2025-05-10 23:32:05'),
(15, 1, 'Cancelled booking ID: 1', '2025-05-10 23:32:07'),
(16, 1, 'Confirmed booking ID: 1', '2025-05-10 23:32:08'),
(17, 1, 'Cancelled booking ID: 1', '2025-05-10 23:32:10'),
(18, 1, 'Confirmed booking ID: 1', '2025-05-10 23:32:12'),
(19, 1, 'Cancelled booking ID: 1', '2025-05-10 23:32:13'),
(20, 1, 'Confirmed booking ID: 1', '2025-05-10 23:32:15'),
(21, 1, 'Cancelled booking ID: 1', '2025-05-10 23:32:17'),
(22, 1, 'Confirmed booking ID: 1', '2025-05-10 23:32:19'),
(23, 1, 'Cancelled booking ID: 1', '2025-05-10 23:32:23'),
(24, 1, 'Cancelled booking ID: 1', '2025-05-10 23:34:07'),
(25, 1, 'Confirmed booking ID: 1', '2025-05-10 23:34:09'),
(26, 1, 'Cancelled booking ID: 1', '2025-05-10 23:34:10'),
(27, 1, 'Updated vehicle: 3 Series', '2025-05-10 23:39:18'),
(28, 1, 'Cancelled booking ID: 1', '2025-05-10 23:57:17'),
(29, 1, 'Cancelled booking ID: 9', '2025-05-15 12:17:00'),
(30, 1, 'Confirmed booking ID: 9', '2025-05-15 12:17:03'),
(31, 1, 'Confirmed booking ID: 9', '2025-05-15 12:17:05'),
(32, 1, 'Cancelled booking ID: 9', '2025-05-15 12:17:07'),
(33, 1, 'Confirmed booking ID: 9', '2025-05-15 12:17:08'),
(34, 1, 'Confirmed booking ID: 10', '2025-05-15 12:19:32'),
(35, 1, 'Created a new brand: Jeepney', '2025-05-16 15:22:40'),
(36, 1, 'Deleted brand: Lamborghini', '2025-05-16 15:49:06'),
(37, 9, 'Approved booking ID: 15', '2025-05-31 12:41:04'),
(38, 9, 'Approved booking ID: 16', '2025-05-31 12:41:06'),
(39, 9, 'Approved booking ID: 17', '2025-05-31 12:41:08'),
(40, 9, 'Approved booking ID: 18', '2025-05-31 12:41:10'),
(41, 9, 'Approved booking ID: 19', '2025-05-31 12:41:11'),
(42, 9, 'Approved booking ID: 20', '2025-05-31 12:41:13'),
(43, 9, 'Approved booking ID: 21', '2025-05-31 12:41:14'),
(44, 9, 'Approved booking ID: 22', '2025-05-31 12:41:17'),
(45, 9, 'Updated profile information, including profile image.', '2025-05-31 15:54:44'),
(46, 9, 'Updated profile information, including profile image.', '2025-05-31 17:06:44'),
(47, 9, 'Updated profile information, including profile image', '2025-06-05 00:13:51'),
(48, 9, 'Updated profile information, including profile image', '2025-06-05 00:18:21'),
(49, 10, 'Updated profile information, including profile image', '2025-06-06 00:48:20'),
(50, 10, 'Updated profile information', '2025-06-06 00:52:03'),
(51, 10, 'Posted a new vehicle: Maserati F40', '2025-06-07 18:09:14'),
(52, 11, 'Approved booking ID: 23', '2025-06-08 21:18:00'),
(53, 11, 'Updated profile information, including profile image', '2025-06-08 21:28:28');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `last_failed_login` timestamp NULL DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `first_name`, `last_name`, `email`, `phone_number`, `password`, `gender`, `created_at`, `updated_at`, `profile_image`, `failed_attempts`, `last_failed_login`, `status`) VALUES
(1, 'REYNA MARIE', 'BOYBOY', 'reynamarie.boyboy22@gmail.com', '09567833665', '$2y$10$XG/87xe1hMX9IHSUN5YqE.OC0w7J2.7kp00KLYoU.Gv66r7lW3qgG', 'Female', '2025-04-19 13:52:25', '2025-05-10 15:06:10', 'uploads/462540209_3766532803613738_1299628852025789676_n.jpg', 0, NULL, 'Active'),
(2, 'Reyna Marie', 'Boyboy', 'reynamarie.boyboy22@evsu.edu.ph', '09567833665', '$2y$10$QU9Bs62/5zUOeu9TkcZYl.Fwxz5ZhBe2SyP.G5Vop5BhX.OAb2iyG', 'Female', '2025-04-19 14:05:06', '2025-04-19 14:05:06', NULL, 0, NULL, 'Active'),
(4, 'Ruby', 'Tinunga', 'ruby@gmail.com', '09567833665', '$2y$10$Vbg9YNhB9/YVJfFjIPFoFeBjZSXQ/csHlXYRutOalTDPuNlxKY2n.', 'Female', '2025-04-19 14:14:37', '2025-06-08 13:15:11', 'Screenshot_20-9-2024_234122_www.instagram.com.jpeg', 1, '2025-06-08 13:15:11', 'Active'),
(6, 'Reyna', 'Boyboy', 'reyna@gmail.com', '094154652', '$2y$10$lDw0WpfXjzTIOic8k2SMHOtzwYh2PUE5IS/bjqAFKzLKnEUI8hsFO', 'Female', '2025-05-10 12:12:22', '2025-05-10 12:12:22', '462540209_3766532803613738_1299628852025789676_n.jpg', 0, NULL, 'Active'),
(7, 'Reyna', 'Boyboy', 'reyna@evsu.edu.ph', '09294154652', '$2y$10$.fbVD0AWX4XABtiu24C14.MF549.Q5nsMd8hTJ4plIjceGSz5Ahty', 'Female', '2025-05-10 12:15:58', '2025-05-10 12:15:58', '462540209_3766532803613738_1299628852025789676_n.jpg', 0, NULL, 'Active'),
(8, 'Rubbs', 'Babe', 'rubabe@gmail.com', '27394002762', '$2y$10$9qEed.PRsxHLeLcuiIiVoeLX2m8qVcJxAguuPXFuNn.aRsi7Zq9w6', 'Female', '2025-05-30 02:31:51', '2025-05-31 04:33:13', '3C_TINUNGA_Method_1.jpg', 2, '2025-05-31 04:33:13', 'Active'),
(9, 'Rubbs', 'Bee', 'rubee@gmail.com', '27394002762', '$2y$10$jf02KtZ2p1aajnGRqh9PR.Y4paROFaKb7M4PFV7WwzStE7ZLTiV/m', 'Female', '2025-05-31 04:40:16', '2025-06-04 16:18:21', 'uploads/profile_9_1749053901.jpg', 0, '2025-06-04 14:24:19', 'Active'),
(10, 'Rue', 'Bev', 'ruebev@gmail.com', '27394002762', '$2y$10$nDBQ6UDVN2zgVOecLf/Uquk.ge/boyVRXLhFzU3WJxI7fOIDWiQVu', 'Female', '2025-06-04 14:26:38', '2025-06-05 16:48:20', 'uploads/profile_10_1749142100.jpg', 0, '2025-06-05 16:44:41', 'Active'),
(11, 'Ruby', 'Todara', 'rubytinunga@gmail.com', '09347393825', '$2y$10$e5PlzAipVchD03/R3NnM3.eI87zN9bVgNIQqruTAqFmBjn4ps5gsS', 'Female', '2025-06-08 13:16:54', '2025-06-08 13:28:28', 'uploads/profile_11_1749389308.jpg', 0, NULL, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `admin_id`, `action`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 7, 'User Registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-10 12:15:58'),
(2, 7, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-10 12:21:18'),
(3, 6, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-10 12:21:54'),
(4, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-10 12:22:05'),
(5, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-10 12:23:37'),
(6, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-10 12:38:12'),
(7, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-10 14:27:02'),
(8, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-14 03:44:33'),
(9, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 05:09:03'),
(10, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-14 07:32:38'),
(11, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-14 14:49:59'),
(12, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-15 04:16:49'),
(13, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-15 11:13:54'),
(14, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-15 15:32:28'),
(15, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-16 07:22:09'),
(16, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-16 07:45:39'),
(17, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-16 07:48:23'),
(18, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-18 13:21:46'),
(19, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-18 13:53:48'),
(20, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-18 15:07:08'),
(21, 1, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-19 09:23:26'),
(22, 8, 'User Registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-05-30 02:31:51'),
(23, 8, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-05-30 02:32:18'),
(25, 9, 'User Registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-05-31 04:40:16'),
(26, 9, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-05-31 04:40:50'),
(27, 9, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-05-31 07:53:58'),
(28, 9, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-05-31 09:05:23'),
(30, 10, 'User Registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-04 14:26:38'),
(31, 10, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-04 14:27:10'),
(32, 10, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-04 14:38:40'),
(33, 10, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-04 15:41:06'),
(34, 9, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-04 16:09:13'),
(35, 9, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-04 19:08:32'),
(36, 9, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-04 19:11:22'),
(37, 9, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-04 20:39:25'),
(38, 10, 'Failed Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-05 16:44:25'),
(39, 10, 'Failed Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-05 16:44:41'),
(40, 10, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-05 16:47:26'),
(41, 10, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-07 09:42:24'),
(42, 10, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 10:55:32'),
(43, 11, 'User Registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 13:16:54'),
(44, 11, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 13:17:20'),
(45, 11, 'Successful Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 13:27:24');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `VehicleId` int(11) DEFAULT NULL,
  `FromDate` varchar(20) DEFAULT NULL,
  `ToDate` varchar(20) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `Status` int(11) DEFAULT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_seen_by_user` tinyint(1) DEFAULT 1,
  `is_seen_by_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `UserID`, `VehicleId`, `FromDate`, `ToDate`, `message`, `Status`, `PostingDate`, `is_seen_by_user`, `is_seen_by_admin`) VALUES
(1, 2, 23, '2025-05-08', '2025-05-10', 'hi', 0, '2025-05-07 16:32:13', 1, 0),
(2, 2, 23, '2025-05-08', '2025-05-10', 'hi', 1, '2025-05-07 16:33:55', 1, 0),
(3, 2, 23, '2025-05-08', '2025-05-10', 'hi', 1, '2025-05-07 16:35:34', 1, 0),
(4, 2, 22, '2025-05-09', '2025-05-10', 'jjkkk', 1, '2025-05-07 16:55:43', 1, 0),
(5, 2, 26, '2025-05-22', '2025-05-24', 'jjjj', 1, '2025-05-08 14:33:48', 1, 0),
(7, 2, 21, '2025-05-16', '2025-05-17', 'hi', 0, '2025-05-15 04:10:46', 1, 0),
(8, 2, 21, '2025-05-16', '2025-05-17', 'hi', 0, '2025-05-15 04:11:34', 1, 0),
(9, NULL, 23, '2025-05-31', '2025-06-01', 'hi', 1, '2025-05-15 04:11:56', 1, 0),
(10, 3, 24, '2025-05-24', '2025-05-26', 'hi', 1, '2025-05-15 04:15:58', 1, 0),
(11, 4, 24, '2025-05-16', '2025-05-17', 'hi', 0, '2025-05-15 04:22:18', 1, 0),
(12, 2, 23, '2025-05-16', '2025-05-23', 'HI', 0, '2025-05-15 13:18:25', 1, 0),
(13, 3, 24, '2025-05-18', '2025-05-20', 'hiii', 1, '2025-05-16 07:53:45', 1, 0),
(14, 2, 24, '2025-05-21', '2025-05-22', 'hi', 1, '2025-05-18 12:48:37', 1, 0),
(15, 2, 21, '2025-05-18', '2025-05-19', 'hi', 1, '2025-05-18 13:43:51', 1, 0),
(16, 3, 23, '2025-05-24', '2025-05-26', 'hi', 1, '2025-05-18 14:21:37', 1, 0),
(17, 5, 23, '2025-05-27', '2025-05-28', 'hi', 1, '2025-05-18 14:48:16', 1, 0),
(18, 7, 28, '2025-06-10', '2025-06-16', 'I need it by then', 1, '2025-05-31 03:19:46', 1, 0),
(19, 7, 29, '2025-06-19', '2025-06-21', 'Please make it available by the time', 1, '2025-05-31 03:52:42', 1, 0),
(20, 7, 26, '2025-07-09', '2025-07-13', 'MAke it available by then', 1, '2025-05-31 03:55:01', 1, 0),
(21, 7, 24, '2025-06-07', '2025-06-10', 'mb hfxdg jgcxtx fxxt', 1, '2025-05-31 04:10:27', 1, 0),
(22, 7, 24, '2025-06-28', '2025-06-30', 'vxgadvcdvbd', 1, '2025-05-31 04:16:10', 1, 0),
(23, 8, 30, '2025-06-16', '2025-06-19', 'Please make some effort to make it available by the time', 1, '2025-06-04 13:50:58', 1, 0),
(24, NULL, 22, '2025-06-20', '2025-06-27', 'PLEASE MAKE THE DATE AVAILABLE', 2, '2025-06-07 09:37:52', 1, 0),
(25, 19, 21, '2025-06-12', '2025-06-14', 'bsbhs', 2, '2025-06-08 13:12:29', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updation_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `brand_name`, `creation_date`, `updation_date`) VALUES
(75, 'Audi', '2025-05-04 13:40:09', '2025-05-10 15:23:58'),
(76, 'BMW', '2025-05-04 13:40:18', '2025-05-04 13:40:18'),
(77, 'Bugatti', '2025-05-04 13:40:31', '2025-05-04 13:40:31'),
(78, 'Ferrari', '2025-05-04 13:40:45', '2025-05-04 13:40:45'),
(79, 'Ford', '2025-05-04 13:40:53', '2025-05-04 13:40:53'),
(80, 'Honda', '2025-05-04 13:40:59', '2025-05-04 13:40:59'),
(81, 'Hyundai', '2025-05-04 13:41:04', '2025-05-04 13:41:04'),
(82, 'Isuzu', '2025-05-04 13:41:14', '2025-05-04 13:41:14'),
(83, 'Jeep', '2025-05-04 13:41:20', '2025-05-04 13:41:20'),
(85, 'Mercedes-Benz', '2025-05-04 13:41:47', '2025-05-04 13:41:47'),
(86, 'Mitsubishi', '2025-05-04 13:41:58', '2025-05-04 13:41:58'),
(87, 'Nissan', '2025-05-04 13:42:05', '2025-05-04 13:42:05'),
(88, 'Suzuki', '2025-05-04 13:42:20', '2025-05-04 13:42:20'),
(89, 'Tesla', '2025-05-04 13:42:25', '2025-05-04 13:42:25'),
(90, 'Toyota', '2025-05-04 13:42:29', '2025-05-04 13:42:29'),
(93, 'Volkswagen', '2025-05-30 23:49:46', '2025-05-30 23:49:46'),
(94, 'Citroën', '2025-05-30 23:49:46', '2025-05-30 23:49:46');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `posting_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `posting_date`) VALUES
(1, 'Reyna Marie Garciano Boyboy', 'reynamarie.boyboy22@gmail.com', 'gffgfg', '2025-05-18 15:08:16');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `message`, `is_read`, `created_at`) VALUES
(1, 'New booking by reyna', 1, '2025-05-08 14:33:48'),
(2, 'Admin Rue Bev logged in successfully', 1, '2025-06-04 14:38:40'),
(3, 'Admin Rubbs Bee logged in', 1, '2025-06-04 19:08:32'),
(4, 'Admin Rubbs Bee logged in', 1, '2025-06-04 19:11:22'),
(5, 'Admin Rubbs Bee logged in', 1, '2025-06-04 20:39:25'),
(6, 'Admin Rue Bev logged in', 1, '2025-06-05 16:47:26'),
(7, 'Admin Rue Bev logged in', 1, '2025-06-07 09:42:24'),
(8, 'Admin Rue Bev logged in', 1, '2025-06-08 10:55:32'),
(9, 'Admin Ruby Todara logged in', 1, '2025-06-08 13:17:20'),
(10, 'Admin Ruby Todara logged in', 0, '2025-06-08 13:27:24');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subscription_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscribers`
--

INSERT INTO `subscribers` (`id`, `email`, `subscription_date`) VALUES
(1, 'reynamarie.boyboy22@gmail.com', '2025-05-14 22:48:56'),
(2, 'reynamarie.boyboy22@gmail.com', '2025-05-14 22:51:42'),
(3, 'reynamarie.boyboy@evsu.edu.ph', '2025-05-14 22:51:49'),
(4, 'reynamarie.boyboy@evsu.edu.ph', '2025-05-14 22:53:17'),
(5, 'reynamarie.boyboy22@gmail.com', '2025-05-16 15:20:00'),
(6, 'reynamarie.boyboy22@gmail.com', '2025-05-16 15:29:19'),
(7, 'reynamarie.boyboy22@gmail.com', '2025-05-18 20:38:20'),
(8, 'reynamarie.boyboy22@gmail.com', '2025-05-18 20:38:37'),
(9, 'reynamarie.boyboy22@gmail.com', '2025-05-18 20:39:06'),
(10, 'reynamarie.boyboy22@gmail.com', '2025-05-18 20:39:18'),
(11, 'reynamarie.boyboy22@gmail.com', '2025-05-18 20:39:31'),
(12, 'reynamarie.boyboy22@gmail.com', '2025-05-18 20:40:35'),
(13, 'reynamarie.boyboy22@gmail.com', '2025-05-18 21:14:52'),
(14, 'reynamarie.boyboy22@gmail.com', '2025-05-18 21:15:02'),
(15, 'reynamarie.boyboy22@gmail.com', '2025-05-18 21:15:10'),
(16, 'reynamarie.boyboy22@gmail.com', '2025-05-18 21:25:40'),
(17, 'reynamarie.boyboy22@gmail.com', '2025-05-18 21:26:01'),
(18, 'reynamarie.boyboy22@gmail.com', '2025-05-18 21:41:00'),
(19, 'reynamarie.boyboy22@gmail.com', '2025-05-18 21:41:26'),
(20, 'reynamarie.boyboy22@gmail.com', '2025-05-18 21:42:17'),
(21, 'reynamarie.boyboy22@gmail.com', '2025-05-18 21:42:57'),
(22, 'reynamarie.boyboy22@gmail.com', '2025-05-18 21:47:04'),
(23, 'reynamarie.boyboy22@gmail.com', '2025-05-18 21:47:15'),
(24, 'reynamarie.boyboy22@gmail.com', '2025-05-18 21:52:19'),
(25, 'reynamarie.boyboy22@gmail.com', '2025-05-18 21:52:29'),
(26, 'reynamarie.boyboy22@gmail.com', '2025-05-18 21:58:37'),
(27, 'reynamarie.boyboy22@gmail.com', '2025-05-18 21:59:05'),
(28, 'reynamarie.boyboy22@gmail.com', '2025-05-18 21:59:21'),
(29, 'reynamarie.boyboy22@gmail.com', '2025-05-18 22:03:01'),
(30, 'reynamarie.boyboy22@gmail.com', '2025-05-18 22:03:14'),
(31, 'reynamarie.boyboy22@gmail.com', '2025-05-18 22:03:26'),
(32, 'reynamarie.boyboy22@gmail.com', '2025-05-18 22:03:47'),
(33, 'cora@gmail.com', '2025-05-18 22:09:11'),
(34, 'cora@gmail.com', '2025-05-18 22:09:18'),
(35, 'cora@gmail.com', '2025-05-18 22:09:23'),
(36, 'cora@gmail.com', '2025-05-18 22:13:10'),
(37, 'cora@gmail.com', '2025-05-18 22:13:40'),
(38, 'cora@gmail.com', '2025-05-18 22:14:04'),
(39, 'reynamarie.boyboy22@gmail.com', '2025-05-18 22:14:38'),
(40, 'reynamarie.boyboy22@gmail.com', '2025-05-18 22:18:48'),
(41, 'reynamarie.boyboy22@gmail.com', '2025-05-18 22:19:18'),
(42, 'reynamarie.boyboy22@gmail.com', '2025-05-18 22:19:46'),
(43, 'reynamarie.boyboy22@gmail.com', '2025-05-18 22:19:57'),
(44, 'cora@gmail.com', '2025-05-18 22:20:11'),
(45, 'cora@gmail.com', '2025-05-18 22:20:34'),
(46, 'reynamarie.boyboy22@gmail.com', '2025-05-18 22:22:32'),
(47, 'reynamarie.boyboy22@gmail.com', '2025-05-18 22:22:41'),
(48, 'renante@gmail.com', '2025-05-18 22:25:36'),
(49, 'renante@gmail.com', '2025-05-18 22:32:16'),
(50, 'renante@gmail.com', '2025-05-18 22:38:57'),
(51, 'marj@gmail.com', '2025-05-18 22:44:25'),
(52, 'reynamarie.boyboy22@gmail.com', '2025-05-23 20:24:42'),
(53, 'reynamarie.boyboy22@gmail.com', '2025-05-23 20:24:50'),
(54, 'reynamarie.boyboy22@gmail.com', '2025-05-23 20:25:02'),
(55, 'reynamarie.boyboy22@gmail.com', '2025-05-23 20:25:12'),
(56, 'reynamarie.boyboy22@gmail.com', '2025-05-23 20:25:18'),
(57, 'reynamarie.boyboy22@gmail.com', '2025-05-23 20:30:34'),
(58, 'reynamarie.boyboy22@gmail.com', '2025-05-23 21:00:25'),
(59, 'cora@gmail.com', '2025-05-23 23:04:15'),
(60, 'cora@gmail.com', '2025-05-23 23:04:25'),
(61, 'cora@gmail.com', '2025-05-23 23:04:30'),
(62, 'reynamarie.boyboy22@gmail.com', '2025-05-23 23:29:57'),
(63, 'reynamarie.boyboy22@gmail.com', '2025-05-23 23:30:03'),
(64, 'reynamarie.boyboy22@gmail.com', '2025-05-23 23:30:09'),
(65, 'reynamarie.boyboy22@gmail.com', '2025-05-23 23:30:26'),
(66, 'reynamarie.boyboy22@gmail.com', '2025-05-23 23:30:33'),
(67, 'rubae@pretty.com', '2025-05-31 12:16:59'),
(68, 'rubabe@gmail.com', '2025-05-31 12:17:32'),
(69, 'rubabe@gmail.com', '2025-05-31 12:17:59'),
(70, 'rubae@pretty.com', '2025-05-31 12:19:36'),
(71, 'rubabe@gmail.com', '2025-05-31 12:19:57'),
(72, 'rubabe@gmail.com', '2025-05-31 12:20:45'),
(73, 'rubabe@gmail.com', '2025-05-31 12:21:00'),
(74, 'ruby@gmail.com', '2025-05-31 12:22:01'),
(75, 'rubee@gmail.com', '2025-05-31 15:07:32'),
(76, 'ruby@gmail.com', '2025-05-31 17:08:59'),
(77, 'ruby@gmail.com', '2025-05-31 17:09:23');

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE `tblusers` (
  `UserID` int(11) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `EmailId` varchar(255) NOT NULL,
  `ContactNumber` varchar(15) DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `DateRegistered` timestamp NOT NULL DEFAULT current_timestamp(),
  `failed_attempts` int(11) DEFAULT 0,
  `lock_until` datetime DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `Otp_verified` tinyint(1) DEFAULT 0,
  `is_verified` tinyint(1) DEFAULT 0,
  `OTP` varchar(6) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `reset_otp` varchar(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblusers`
--

INSERT INTO `tblusers` (`UserID`, `FullName`, `EmailId`, `ContactNumber`, `Password`, `profile_image`, `DateRegistered`, `failed_attempts`, `lock_until`, `dob`, `address`, `Otp_verified`, `is_verified`, `OTP`, `last_login`, `reset_otp`) VALUES
(1, 'reyna', 'reyna@gmail.com', '09567833665', '827ccb0eea8a706c4c34a16891f84e7b', NULL, '2025-04-27 14:46:56', 0, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL),
(2, 'reyna', 'reynamarie.boyboy22@gmail.com', '09567833665', '$2y$10$RvegauLhFta1Ohe7HhbiOuQlWCle8wN3NzTjkrbsM8iG7HOu2VczW', 'images/profile_681c82009764c8.01624917.jpg', '2025-04-28 16:24:36', 5, '2025-05-23 17:35:33', NULL, NULL, 0, 0, NULL, NULL, NULL),
(3, 'Cora Laude Boyboy', 'cora@gmail.com', '09294154652', '$2y$10$E6SCsHfyhPjyevFfq.aby.Vi71RJpopV4IFBp8Y2PLAvac8ahmata', 'images/profile_6824075669ac94.08709959.jpeg', '2025-05-09 05:57:18', 3, '2025-05-23 17:09:30', NULL, NULL, 0, 0, NULL, NULL, NULL),
(4, 'Jenica', 'jenica@gmail.com', '09567833665', '$2y$10$yuw0KxgfmtFWcxyYnwJBteuvgGFbsU.ls5gAvhftshCWTOQ0O.Abq', NULL, '2025-05-13 15:45:07', 0, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL),
(5, 'Marj', 'marj@gmail.com', '09567833665', '$2y$10$0hk.7tx3eEaux0AnGoAk7exTTKG4h0L6q0XpA7hbjbNuT.VoitQGe', NULL, '2025-05-18 14:44:25', 0, NULL, '1996-03-19', 'brgy.catmon ormoc  city', 0, 0, NULL, NULL, NULL),
(6, 'Rub Bae', 'rubae@pretty.com', '24364758825', '$2y$10$gMgJ57oyRBzN1yD0gjcj6OSn.aihFqYT51qnwVQY4GTklZGX0tXQG', NULL, '2025-05-30 03:15:34', 1, NULL, '2025-05-21', 'Maybog, Baybay City, Leyte', 0, 0, NULL, NULL, NULL),
(7, 'Arr Half', 'arrhalf@pretty.com', '23456732143', '$2y$10$0pQrwfGzMZWu.K3CK5qw2uWuQyt1o2Mko9R9IFsPBMXoKWF8jX0b2', '', '2025-05-31 03:18:38', 6, '2025-06-04 22:32:12', '2025-05-25', 'Maybog, Baybay City, Leyte', 0, 0, NULL, NULL, NULL),
(8, 'Ruby Rhub', 'ruby@gmail.com', '24256784212', '$2y$10$2ebvP/3g1GbZl490ce/PP.oqo9dyRyE93dxZB2LnBxMx5ePqIK2A.', '', '2025-05-31 04:22:01', 1, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL),
(18, 'Rubs Todara', 'todararodrigo1993@gmail.com', '090568633242', '$2y$10$ER0X5BcC2wPczChiDqjXTuXuG0JdYj3ycYDqkuR5vuQKVPddrOs4G', 'images/profile_684570de0888c3.40782147.jpg', '2025-06-08 11:09:02', 0, NULL, '1992-10-13', 'Maybog, Baybay City, Leyte', 0, 1, NULL, '2025-06-08 20:16:40', NULL),
(19, 'Ruby Tinunga', 'ruby.tinunga@evsu.edu.ph', '09056863527', '$2y$10$tORuabeYDrw7s7hnr3tU8uixKOdZ9MFHjUzMCrndXT9lCtBst5IHu', 'images/profile_68458c953f25d1.51937794.jpg', '2025-06-08 13:08:55', 0, NULL, '2000-11-16', 'Maybog, Baybay City, Leyte', 0, 1, NULL, '2025-06-08 21:25:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(255) NOT NULL,
  `testimonial` text NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `user_id`, `user_name`, `testimonial`, `rating`, `status`, `created_at`) VALUES
(1, 2, 'reyna', 'Great service! The car was in perfect condition and the rental process was smooth.', 5, 'approved', '2025-05-15 00:30:00'),
(2, 3, 'Cora Laude Boyboy', 'Very professional staff and excellent vehicle selection.', 4, 'approved', '2025-05-16 02:15:00'),
(3, 5, 'Marj', 'Highly recommend their services. Will definitely rent again!', 5, 'approved', '2025-05-18 06:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `email`, `password`) VALUES
('User', 'makiemorales2@gmail.com', '$2y$10$OsyOVaT4y0ETOUEex2C.wu6/9W9lI.pEG3uUFoJMDZe6txwlL57eK');

-- --------------------------------------------------------

--
-- Table structure for table `users_activity_log`
--

CREATE TABLE `users_activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `log_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_activity_log`
--

INSERT INTO `users_activity_log` (`id`, `user_id`, `activity`, `log_time`) VALUES
(1, 8, 'Updated profile information', '2025-05-31 08:35:28'),
(2, 8, 'Updated profile information', '2025-05-31 08:35:31'),
(8, 18, 'Logged in successfully', '2025-06-08 11:10:07'),
(9, 18, 'Updated profile name, email, phone', '2025-06-08 11:15:31'),
(10, 18, 'Updated profile name, email, phone, profile image', '2025-06-08 11:15:42'),
(11, 18, 'Logged out', '2025-06-08 11:15:52'),
(12, 18, 'Logged in successfully', '2025-06-08 12:11:26'),
(13, 18, 'Logged out', '2025-06-08 12:13:28'),
(14, 18, 'password_reset', '2025-06-08 12:16:21'),
(15, 18, 'Logged in successfully', '2025-06-08 12:16:40'),
(16, 18, 'Logged out', '2025-06-08 13:01:25'),
(17, 19, 'Logged in successfully', '2025-06-08 13:11:16'),
(18, 19, 'Booked  ', '2025-06-08 13:12:43'),
(19, 19, 'Cancelled booking#25', '2025-06-08 13:13:10'),
(20, 19, 'Booked  ', '2025-06-08 13:13:10'),
(21, 19, 'Updated profile name, email, phone, profile image', '2025-06-08 13:13:57'),
(22, 19, 'password_reset', '2025-06-08 13:21:07'),
(23, 19, 'Logged in successfully', '2025-06-08 13:21:31'),
(24, 19, 'Logged out', '2025-06-08 13:23:45'),
(25, 19, 'password_reset', '2025-06-08 13:24:48'),
(26, 19, 'Logged in successfully', '2025-06-08 13:25:18'),
(27, 19, 'Booked  ', '2025-06-08 13:25:44');

-- --------------------------------------------------------

--
-- Table structure for table `user_logs`
--

CREATE TABLE `user_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_logs`
--

INSERT INTO `user_logs` (`id`, `user_id`, `event`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, NULL, 'Login Failed', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-09 05:54:29'),
(2, NULL, 'Login Failed', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-09 05:54:37'),
(3, NULL, 'Login Failed', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-09 05:54:44'),
(4, 3, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-09 05:57:35'),
(5, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-09 06:05:39'),
(6, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-09 06:11:17'),
(7, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-09 06:20:44'),
(8, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-09 06:24:46'),
(9, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-10 10:20:56'),
(10, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-10 11:09:43'),
(11, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-10 13:50:43'),
(12, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-10 15:33:20'),
(13, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-13 13:36:21'),
(14, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-13 14:06:27'),
(15, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-13 14:06:58'),
(16, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-13 14:19:33'),
(17, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-13 14:26:20'),
(18, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-13 15:08:32'),
(19, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-13 15:09:21'),
(20, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-13 15:10:52'),
(21, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-13 15:13:19'),
(22, 3, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-13 15:13:42'),
(23, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-13 15:43:39'),
(24, 4, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-13 15:45:28'),
(25, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-13 15:46:10'),
(26, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-13 15:47:05'),
(27, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-13 15:48:36'),
(28, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-14 02:38:59'),
(29, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-14 02:49:09'),
(30, 2, 'Login Successful', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-14 02:49:34'),
(31, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-18 12:51:52'),
(32, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-18 12:55:17'),
(33, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-18 13:44:05'),
(34, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-18 14:14:29'),
(35, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-18 14:19:12'),
(36, 3, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-18 14:22:24'),
(37, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-18 14:24:21'),
(38, 5, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-18 14:48:31'),
(39, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 15:30:19'),
(40, 7, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-05-31 04:16:20'),
(41, 8, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-05-31 04:25:17'),
(42, 7, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-05-31 04:43:11'),
(43, 8, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-05-31 09:04:17'),
(44, 8, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-02 12:54:20'),
(45, 8, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-04 20:39:53'),
(46, 8, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-04 20:45:53'),
(47, 13, 'login_failed_api', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-05 16:27:17'),
(48, 13, 'login_failed_api', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-05 16:27:45'),
(49, 13, 'login_failed_api', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-05 16:35:02'),
(50, 14, 'login_failed_api', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-05 18:06:05'),
(51, 14, 'login_failed_api', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-05 18:09:03'),
(52, 8, 'login_failed_password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-05 20:11:31'),
(53, 8, 'login_failed_password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-05 20:11:42'),
(54, 8, 'login_failed_password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-05 20:11:54'),
(55, 8, 'login_failed_api', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-06 02:33:55'),
(56, 15, 'login_failed_api', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-06 10:24:16'),
(57, 15, 'login_failed_api', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-06 10:26:45'),
(58, 15, 'login_success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-06 10:31:53'),
(59, 16, 'login_success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-07 09:34:52'),
(60, 16, 'login_failed_password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 10:33:21'),
(61, 16, 'login_failed_password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 10:33:37'),
(62, 16, 'login_failed_password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 10:34:09'),
(63, 17, 'login_success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 10:37:33'),
(64, 17, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 10:49:06'),
(65, 16, 'login_failed_password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 10:50:45'),
(66, 17, 'login_success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 10:51:02'),
(67, 17, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 10:51:07'),
(68, 17, 'login_success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 10:51:40'),
(69, 17, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 10:52:40'),
(70, 17, 'login_failed_api', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 11:06:30'),
(71, 17, 'login_failed_api', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 11:07:18'),
(72, 18, 'login_success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 11:10:07'),
(73, 18, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 11:15:52'),
(74, 18, 'login_failed_password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 11:16:35'),
(75, 18, 'login_success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 12:11:26'),
(76, 18, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 12:13:28'),
(77, 18, 'login_failed_password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 12:13:48'),
(78, 18, 'login_success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 12:16:40'),
(79, 18, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 13:01:25'),
(80, 19, 'login_success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 13:11:16'),
(81, 19, 'login_success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 13:21:31'),
(82, 19, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 13:23:45'),
(83, 19, 'login_success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-08 13:25:18');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `vehicle_title` varchar(255) NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `vehicle_overview` text NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `fuel_type` enum('petrol','diesel','electric','hybrid') NOT NULL,
  `model_year` int(11) NOT NULL,
  `seating_capacity` int(11) NOT NULL,
  `image1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `image4` varchar(255) DEFAULT NULL,
  `image5` varchar(255) DEFAULT NULL,
  `accessories` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_title`, `brand_name`, `vehicle_overview`, `price_per_day`, `fuel_type`, `model_year`, `seating_capacity`, `image1`, `image2`, `image3`, `image4`, `image5`, `accessories`, `created_at`) VALUES
(21, '3 Series', 'BMW', 'The BMW 3 Series is a compact luxury sedan known for its sporty performance, elegant design, and advanced technology. It offers a balanced mix of comfort, driving dynamics, and fuel efficiency, making it a favorite for both daily commutes and spirited drives.', 6000.00, 'petrol', 2021, 5, 'uploads/BMW SERIES 3.png', 'uploads/BMW.png', 'uploads/BMW BLACK.png', 'uploads/BMW BLUE.png', '', 'Air Conditioner,Power Door Locks,AntiLock Braking System,Brake Assist,Power Steering,Driver Airbag,Passenger Airbag,Power Windows,CD Player,Central Locking,Crash Sensor,Leather Seats', '2025-05-04 14:05:25'),
(22, 'Vios', 'Toyota', 'The Toyota Vios is a popular subcompact sedan in the Philippines, known for its affordability, reliability, and fuel efficiency. It\\\'s a top choice for both private owners and fleet operators.', 1799.00, 'petrol', 2025, 5, 'uploads/Screenshot 2025-05-04 221054.png', 'uploads/Screenshot 2025-05-04 221107.png', 'uploads/Screenshot 2025-05-04 221021.png', 'uploads/Screenshot 2025-05-04 221038.png', 'uploads/Screenshot 2025-05-04 221121.png', 'Air Conditioner,Power Door Locks,AntiLock Braking System,Brake Assist,Power Steering,Driver Airbag,Passenger Airbag,Power Windows,CD Player,Central Locking,Crash Sensor,Leather Seats', '2025-05-04 14:17:46'),
(23, 'Jimny', 'Suzuki', 'The Suzuki Jimny 5-Door (2025) is a compact SUV known for its strong off-road capability and practical city use. It features a 1.5L engine, 4WD system, and is available in manual and automatic transmissions. With seating for four, it has a rugged design, 210 mm ground clearance, and essential safety features like airbags, ABS, and hill control. In the Philippines, it starts at ₱1,558,000.', 2500.00, 'petrol', 2022, 4, 'uploads/JIMNY GREEN.jpg', 'uploads/JIMNY BLACK.jpg', 'uploads/JIMNY WHITE.jpg', 'uploads/JIMNY.webp', 'uploads/Screenshot 2025-05-04 222812.png', 'Air Conditioner,Power Door Locks,AntiLock Braking System,Brake Assist,Power Steering,Driver Airbag,Passenger Airbag,Power Windows,CD Player,Central Locking,Crash Sensor,Leather Seats', '2025-05-04 14:33:48'),
(24, 'Civic', 'Honda', 'The Honda Civic is a compact car known for its reliability, fuel efficiency, and modern styling. It’s ideal for city driving and long-distance travel, offering a comfortable interior and advanced safety features.', 1500.00, 'petrol', 2023, 5, 'uploads/HONDA.avif', 'uploads/BLACK.jpg', 'uploads/HONDA RED.jpg', 'uploads/HONDA BLUE.jpg', 'uploads/WHITE.jpg', 'Air Conditioner,Power Door Locks,AntiLock Braking System,Brake Assist,Power Steering,Driver Airbag,Passenger Airbag,Power Windows,CD Player,Central Locking,Crash Sensor,Leather Seats', '2025-05-04 14:51:27'),
(25, 'Jeep', 'Suzuki', 'fff', 500.00, 'petrol', 2022, 6, 'uploads/vroom_1.png', 'uploads/1925973a-30dd-49e4-b4a3-4411b24c34e5-6-_11_.webp', 'uploads/1925973a-30dd-49e4-b4a3-4411b24c34e5-6-_11__1.webp', '', '', 'Air Conditioner', '2025-05-08 04:36:11'),
(26, 'Jeep', 'BMW', 'ggg', 500.00, 'petrol', 2022, 6, 'uploads/HONDA RED_1.jpg', 'uploads/GRAY.jpg', 'uploads/HONDA_1.avif', '', '', 'Air Conditioner,Power Steering', '2025-05-08 04:58:08'),
(27, 'Panel Van', 'Volkswagen', 'Enclosed van used mainly for goods transport, ideal for delivery services.', 2500.00, 'diesel', 2023, 3, 'uploads/panel-van.jpg', 'uploads/panel-van.jpg', 'uploads/panel-van.jpg', 'uploads/panel-van.jpg', 'uploads/panel-van.jpg', 'Air Conditioner,Power Steering', '2025-05-30 22:58:43'),
(28, 'Car-Derived Van', 'Ford', 'Compact van based on a passenger car chassis, ideal for urban deliveries and light loads.', 2200.00, 'petrol', 2022, 2, 'uploads/car-derived-van.jpg', 'uploads/car-derived-van.jpg', 'uploads/car-derived-van.jpg', 'uploads/car-derived-van.jpg', 'uploads/car-derived-van.jpg', 'Air Conditioner,Power Steering', '2025-05-30 22:58:43'),
(29, 'Crew Van', 'Citroen', 'Van with additional seating for crew members, suitable for work teams.', 2800.00, 'diesel', 2021, 5, 'uploads/crew-vvan.png', 'uploads/crew-vvan.png', 'uploads/crew-vvan.png', 'uploads/crew-vvan.png', 'uploads/crew-vvan.png', 'Air Conditioner,Power Steering', '2025-05-30 22:58:43'),
(30, 'Mini Bus', 'Ford', 'Small bus designed for passenger transport, ideal for group travel.', 3200.00, 'diesel', 2020, 12, 'uploads/mini-bus.png', 'uploads/mini-bus.png', 'uploads/mini-bus.png', 'uploads/mini-bus.png', 'uploads/mini-bus.png', 'Air Conditioner,Power Steering', '2025-05-30 22:58:43'),
(32, 'Luton Van', 'Volkswagen', 'Large van with a box body that extends over the cab, ideal for moving house or transporting bulky items.', 3700.00, 'diesel', 2023, 3, 'uploads/luton-van.jpg', 'uploads/luton-van.jpg', 'uploads/luton-van.jpg', 'uploads/luton-van.jpg', 'uploads/luton-van.jpg', 'Air Conditioner,Power Steering', '2025-05-30 23:44:35'),
(33, 'Maserati F40', 'Ferrari', ' It was a true supercar, accelerating from 0 to 100 km/h in 4.1 seconds and boasting a claimed top speed of 324 km/h (201 mph). The car\\\'s lightweight design, with a dry weight of 1250 kg, contributed to its impressive performance figures. The F40 remains iconic for its raw power and contribution to the supercar legacy.', 4000.00, 'diesel', 1987, 4, 'ferrari.jpg', 'ferrari_1.jpg', 'ferrari_2.jpg', 'ferrari_3.jpg', 'ferrari_4.jpg', 'Air Conditioner,Power Door Locks,Brake Assist,Passenger Airbag,Power Windows,CD Player,Crash Sensor,Leather Seats', '2025-06-07 10:09:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `VehicleId` (`VehicleId`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `EmailId` (`EmailId`),
  ADD UNIQUE KEY `EmailId_2` (`EmailId`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users_activity_log`
--
ALTER TABLE `users_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users_activity_log`
--
ALTER TABLE `users_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`);

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`UserID`) REFERENCES `tblusers` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_vehicle_id` FOREIGN KEY (`VehicleId`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tblusers` (`UserID`) ON DELETE SET NULL;

--
-- Constraints for table `users_activity_log`
--
ALTER TABLE `users_activity_log`
  ADD CONSTRAINT `users_activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tblusers` (`UserID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
