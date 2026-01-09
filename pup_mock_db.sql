-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 09, 2026 at 10:03 AM
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
-- Database: `pup_mock_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `service_type` varchar(100) NOT NULL,
  `appointment_date` datetime NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `student_id`, `service_type`, `appointment_date`, `status`, `created_at`) VALUES
(1, '2024-87629-MN-0', 'Scholarship Renewal', '2026-02-02 14:00:00', 'Pending', '2026-01-06 05:02:46'),
(2, '2024-87629-MN-0', 'Scholarship Interview', '2026-02-23 14:00:00', 'Done', '2026-01-06 05:06:01'),
(3, '2024-87629-MN-0', 'Document Verification', '2026-02-23 14:00:00', 'Rejected', '2026-01-06 05:06:34'),
(4, '2022-00001-MN-0', 'Scholarship Interview', '2026-02-25 14:00:00', 'Rejected', '2026-01-06 05:59:35'),
(5, '2022-00001-MN-0', 'Scholarship Interview', '2026-02-23 14:00:00', 'Done', '2026-01-06 06:00:08'),
(6, '2022-00002-MN-0', 'Scholarship Interview', '2200-02-23 14:00:00', 'Done', '2026-01-06 06:04:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `grades_status` varchar(50) DEFAULT 'Pending',
  `cor_status` varchar(50) DEFAULT 'Pending',
  `recognition_status` varchar(50) DEFAULT 'For Submission',
  `grades_file` varchar(255) DEFAULT NULL,
  `cor_file` varchar(255) DEFAULT NULL,
  `recognition_file` varchar(255) DEFAULT NULL,
  `application_step` varchar(100) DEFAULT 'Online appointments/scheduling for interviews',
  `progress_percentage` int(11) DEFAULT 66,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `appointment_date` datetime DEFAULT NULL,
  `role` enum('student','admin') DEFAULT 'student',
  `service_type` varchar(100) DEFAULT NULL,
  `appointment_status` varchar(100) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `student_id`, `password`, `full_name`, `grades_status`, `cor_status`, `recognition_status`, `grades_file`, `cor_file`, `recognition_file`, `application_step`, `progress_percentage`, `created_at`, `appointment_date`, `role`, `service_type`, `appointment_status`) VALUES
(1, '2024-87629-MN-0', 'password123', 'COPADA, AYEHSSA JUSTINE', 'Verified', 'Verified', 'Verified', 'uploads/2024-87629-MN-0_grades_1767659020.pdf', 'uploads/2024-87629-MN-0_cor_1767663913.pdf', 'uploads/2024-87629-MN-0_recognition_1767667221.pdf', 'Step 3: Process Completed', 100, '2026-01-05 23:34:37', '2026-02-23 14:00:00', 'student', 'Document Verification', 'Rejected'),
(2, 'ADMIN-2026', 'admin12345', 'System Administrator', 'Pending', 'Pending', 'For Submission', NULL, NULL, NULL, 'Supervising', 100, '2026-01-06 01:22:13', NULL, 'admin', NULL, 'Pending'),
(3, '2022-00001-MN-0', 'password123', 'JUAN DELA CRUZ', 'Verified', 'Verified', 'Verified', 'uploads/2022-00001-MN-0_grades_1767679074.webp', 'uploads/2022-00001-MN-0_cor_1767679081.jpg', 'uploads/2022-00001-MN-0_recognition_1767679086.jpg', 'Step 3: Process Completed', 100, '2026-01-06 05:09:37', '2026-02-23 14:00:00', 'student', 'Scholarship Interview', 'Done'),
(4, '2022-00002-MN-0', 'password123', 'MARIA CLARA SANTOS', 'Verified', 'Rejected', 'Verified', 'uploads/2022-00002-MN-0_grades_1767679373.jpg', 'uploads/2022-00002-MN-0_cor_1767679368.jpg', 'uploads/2022-00002-MN-0_recognition_1767679379.webp', 'Step 2: For Appointment/Interview', 83, '2026-01-06 05:09:37', '2200-02-23 14:00:00', 'student', 'Scholarship Interview', 'Done'),
(5, '2022-00003-MN-0', 'password123', 'JOSE RIZAL', 'Pending', 'Pending', 'Pending', NULL, NULL, NULL, 'Online appointments/scheduling for interviews', 66, '2026-01-06 05:09:37', NULL, 'student', NULL, NULL),
(6, '2023-00142-MN-0', 'password123', 'ANDRES BONIFACIO', 'Pending', 'Pending', 'Pending', NULL, NULL, NULL, 'Online appointments/scheduling for interviews', 66, '2026-01-06 05:09:37', NULL, 'student', NULL, NULL),
(7, '2023-00567-MN-0', 'password123', 'EMILIO AGUINALDO', 'Pending', 'Pending', 'Pending', NULL, NULL, NULL, 'Online appointments/scheduling for interviews', 66, '2026-01-06 05:09:37', NULL, 'student', NULL, NULL),
(8, '2021-00998-MN-0', 'password123', 'GABRIELA SILANG', 'Pending', 'Pending', 'Pending', NULL, NULL, NULL, 'Online appointments/scheduling for interviews', 66, '2026-01-06 05:09:37', NULL, 'student', NULL, NULL),
(9, '2022-01234-MN-0', 'password123', 'MELCHORA AQUINO', 'Pending', 'Pending', 'Pending', NULL, NULL, NULL, 'Online appointments/scheduling for interviews', 66, '2026-01-06 05:09:37', NULL, 'student', NULL, NULL),
(10, '2023-08877-MN-0', 'password123', 'ANTONIO LUNA', 'Pending', 'Pending', 'Pending', NULL, NULL, NULL, 'Step 1: Document Submission', 0, '2026-01-06 05:09:37', NULL, 'student', NULL, NULL),
(11, '2022-04433-MN-0', 'password123', 'APOLINARIO MABINI', 'Pending', 'Pending', 'Pending', NULL, NULL, NULL, 'Online appointments/scheduling for interviews', 66, '2026-01-06 05:09:37', NULL, 'student', NULL, NULL),
(12, '2021-07766-MN-0', 'password123', 'MARCELO H. DEL PILAR', 'Pending', 'Pending', 'Pending', NULL, NULL, NULL, 'Online appointments/scheduling for interviews', 66, '2026-01-06 05:09:37', NULL, 'student', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
