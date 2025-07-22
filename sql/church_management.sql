-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2025 at 06:11 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `church_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`, `created_at`, `reset_token`, `reset_expires`) VALUES
(1, 'admin@example.com', '$2y$10$CiVxH3v4pZcZ2p/dXqiTOuzRQp7i/Q0nYCOHka1jcxwR/WOIBhD9O', '2025-04-11 19:21:11', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `member_id`, `attendance_date`, `created_at`) VALUES
(14, 1, '2025-07-19', '2025-07-19 18:38:03'),
(18, 8, '2025-07-21', '2025-07-21 17:59:02'),
(19, 1, '2025-07-21', '2025-07-21 17:59:02');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `location` varchar(100) NOT NULL,
  `document_file` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `created_at`, `event_date`, `event_time`, `location`, `document_file`) VALUES
(5, 'tsfrrs', 'swsds', '2025-04-17 10:44:13', '2025-04-20', '10:45:00', 'teret', 'event_doc_6800db7da433d.pdf'),
(6, 'akwaaba', 'welcome home', '2025-04-17 11:16:55', '2025-04-27', '12:20:00', '323', 'event_doc_6800e327e9b2b.docx');

-- --------------------------------------------------------

--
-- Table structure for table `finances`
--

CREATE TABLE `finances` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_type` enum('Income','Expense','','') NOT NULL,
  `transaction_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `finances`
--

INSERT INTO `finances` (`id`, `title`, `description`, `amount`, `transaction_type`, `transaction_date`, `created_at`) VALUES
(1, 'rytr', 'yrtuf', 1213.00, 'Income', '2025-04-17', '2025-04-16 22:16:24'),
(2, 'trtry', 'fdvcc', 132543.00, 'Expense', '2025-03-02', '2025-04-16 22:23:01'),
(3, 'Sunday Offering', '', 30000.00, 'Income', '2025-05-08', '2025-05-08 19:02:37'),
(4, 'Sunday Offering', '', 7.00, 'Income', '2025-05-08', '2025-05-08 19:03:12'),
(5, 'Sunday Offering', '', 7.00, 'Income', '2025-05-08', '2025-05-08 19:10:50'),
(6, 'Sunday Offering', '', 7.00, 'Income', '2025-05-08', '2025-05-08 19:14:45'),
(7, 'Sunday Offering', '', 7.00, 'Income', '2025-05-08', '2025-05-08 19:15:22'),
(8, 'Sunday Offering', '', 3222.00, 'Income', '2025-07-12', '2025-07-12 19:21:16'),
(9, 'Benevolence', '', 97.00, 'Income', '2025-07-12', '2025-07-12 19:21:50'),
(10, 'Building Fund', 'Gods working', 90000.00, 'Income', '2025-07-12', '2025-07-12 19:25:39'),
(11, 'Missions', '', 44.97, 'Income', '2025-07-14', '2025-07-14 14:32:21'),
(12, 'Sunday Offering', 'Gods work', 3222.00, 'Income', '2025-07-14', '2025-07-14 16:00:55'),
(13, 'Sunday Offering', 'Gods work', 3222.00, 'Income', '2025-07-14', '2025-07-14 16:36:32'),
(14, 'Sunday Offering', 'Gods work', 3222.00, 'Income', '2025-07-14', '2025-07-14 16:36:54'),
(15, 'Benevolence', '', 76548.00, 'Income', '2025-07-14', '2025-07-14 16:38:39'),
(16, 'Benevolence', '', 76548.00, 'Income', '2025-07-14', '2025-07-14 16:42:39'),
(17, 'Missions', '', 532.00, 'Income', '2025-07-14', '2025-07-14 17:12:02');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `passport_picture` varchar(255) DEFAULT NULL,
  `birth_date` date NOT NULL,
  `is_staff` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `join_date` date DEFAULT NULL,
  `image` varchar(255) DEFAULT 'assets/default-avatar.png',
  `notes` text DEFAULT NULL,
  `category` varchar(10) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `first_name`, `last_name`, `email`, `phone`, `address`, `gender`, `passport_picture`, `birth_date`, `is_staff`, `created_at`, `join_date`, `image`, `notes`, `category`) VALUES
(1, 'koo', 'ding', 'yaa@gmail.com', '0232655778', 'GE-102-333', 'Male', 'uploads/passport_pictures/passport_6821216e4ab60.jpg', '1999-03-16', 1, '2025-04-11 19:58:56', '2020-05-16', 'assets/default-avatar.png', NULL, '0'),
(7, 'Edith', 'Osei', 'esi@yahoo.com', '0232655778', 'GW-123-452 ', 'Female', 'uploads/passport_pictures/passport_68571602a5597.jpg', '1975-06-14', 0, '2025-05-12 18:38:42', '2000-05-12', 'assets/default-avatar.png', NULL, 'youth'),
(8, 'Evans', 'Obeng', 'evans@gmail.com', '0544302312', 'GW-412-7761', 'Male', 'uploads/passport_pictures/passport_687e7d22b5d2c.jpg', '2005-03-08', 0, '2025-07-21 17:47:14', '2015-06-17', 'assets/default-avatar.png', NULL, '0');

-- --------------------------------------------------------

--
-- Table structure for table `sermons`
--

CREATE TABLE `sermons` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `preacher` varchar(100) NOT NULL,
  `sermon_date` date NOT NULL,
  `description` text NOT NULL,
  `audio_file` varchar(255) NOT NULL,
  `video_file` int(11) NOT NULL,
  `document_file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sermons`
--

INSERT INTO `sermons` (`id`, `title`, `preacher`, `sermon_date`, `description`, `audio_file`, `video_file`, `document_file`, `created_at`) VALUES
(1, 'grgg', 'sfs', '2025-04-02', 'sgt', '', 0, 'doc_67f975666bc9e.pdf', '2025-04-11 20:02:46'),
(2, 'esrrt', 'Paa Kwesi', '2025-04-18', 'ysgfy', 'audio_67fd4d9614392.mp3', 0, '', '2025-04-14 18:01:58'),
(3, 'God is Good', 'Pastor.AB', '2025-04-27', 'uyedydf', '', 0, '', '2025-04-14 18:11:22'),
(4, 'the role of a man', 'Pastor Mike', '2025-03-23', 'teeeg', 'audio_6800ea0cebc07.mp3', 0, '', '2025-04-17 11:46:20');

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `visit_date` date NOT NULL,
  `notes` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`id`, `first_name`, `last_name`, `email`, `phone`, `visit_date`, `notes`, `created_at`) VALUES
(1, 'ertrytu', 'jhlffgo', 'nios@gmail.ccom', '0544302312', '2025-04-20', 'from nima church', '2025-04-12 12:41:24'),
(2, 'Debra', 'Manu', 'manu@gmail.com', '0546677912', '2025-03-23', 'fgga', '2025-04-16 22:36:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`member_id`,`attendance_date`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `finances`
--
ALTER TABLE `finances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sermons`
--
ALTER TABLE `sermons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `finances`
--
ALTER TABLE `finances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sermons`
--
ALTER TABLE `sermons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`id`) REFERENCES `members` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
