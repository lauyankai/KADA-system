-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 23, 2025 at 08:11 AM
-- Server version: 9.1.0
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kada_mvc_2101`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `is_admin`, `created_at`, `name`) VALUES
(1, 'eve', 'evegoh@gmail.com', '$2y$10$iyF5wivb0pYWIWaqS3H8O.UVvmo0Eg//PUmTt03aygcDCTEdbKwn6', 1, '2024-12-17 18:48:02', NULL),
(2, 'nyh', 'yuhin02@gmail.com', '$2y$10$TBIbbct7t.HZiT16RbJvZeln1TWwmejWLa/iuLGQknKo48wY5yZoi', 1, '2024-12-17 20:35:58', NULL),
(3, 'dhesh', 'dheshieghan@gmail.com', '$2y$10$nMsdE4zfPsGTSN74p3csO.KLp.1HMy3X/7fqbP1Vps4l6U8VVgBfy', 1, '2024-12-17 21:09:45', NULL),
(6, 'yk', 'yk@g.com', '$2y$10$ijpC93q.kHcgGE5Bxhr0uepocaTtZfqFyMuLUyZQmlOB5QuAmyNm2', 1, '2025-01-01 01:09:41', NULL),
(7, 'chew', 'chewchiuxian@graduate.utm.my', '$2y$10$0QJmafqKzfqoCtML8tj7rejnN4fKAonyZmVbr9YV.lrGM/7VovsyK', 1, '2025-01-01 13:26:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `annual_report`
--

CREATE TABLE `annual_report` (
  `id` int NOT NULL,
  `year` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int NOT NULL,
  `description` text,
  `uploaded_by` int NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','archived') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `annual_report`
--

INSERT INTO `annual_report` (`id`, `year`, `title`, `file_name`, `file_path`, `file_size`, `description`, `uploaded_by`, `uploaded_at`, `updated_at`, `status`) VALUES
(1, 2022, 'Laporan Tahunan 2022', 'annual_report_2022_678fde3670e97.pdf', '/uploads/annual-reports/annual_report_2022_678fde3670e97.pdf', 1789918, 'test', 6, '2025-01-21 17:49:42', '2025-01-21 17:49:42', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `directors`
--

CREATE TABLE `directors` (
  `id` int NOT NULL,
  `director_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `position` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `directors`
--

INSERT INTO `directors` (`id`, `director_id`, `username`, `name`, `email`, `password`, `position`, `department`, `phone_number`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, NULL, 'director', 'Director KADA', 'director@kada.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pengarah', 'Pengurusan', '012-3456789', 'active', NULL, '2025-01-14 11:20:05', '2025-01-14 11:20:05'),
(4, 'DIR20250001', 'DIR001', 'Director 1 ', 'director@kada.co', '$2y$10$uS7OoYW8zHHHPg4HfgQWcOmUl6Eg48msdVtVixmmhVujIaEsg0gPa', 'Pengarah Eksekutif', 'Pengurusan', '011', 'active', '2025-01-23 05:49:44', '2025-01-14 11:45:06', '2025-01-22 21:49:44');

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int NOT NULL,
  `reference_no` varchar(20) NOT NULL,
  `member_id` int NOT NULL,
  `loan_type` enum('al_bai','al_innah','skim_khas','road_tax','al_qardhul','other') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `duration` int NOT NULL,
  `monthly_payment` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `date_received` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `bank_name` varchar(50) NOT NULL,
  `bank_account` varchar(20) NOT NULL,
  `approved_by` int DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `remarks` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `reference_no`, `member_id`, `loan_type`, `amount`, `duration`, `monthly_payment`, `status`, `date_received`, `updated_at`, `bank_name`, `bank_account`, `approved_by`, `approved_at`, `remarks`) VALUES
(1, 'LOAN202501214612', 12, 'al_innah', '20000.00', 12, '1736.67', 'approved', '2025-01-21 01:14:45', '2025-01-21 17:09:46', 'CIMB Bank', '1290812312', 4, '2025-01-21 17:09:46', 'Noted.'),
(2, 'LOAN202501188719', 12, 'skim_khas', '24000.00', 24, '1042.00', 'approved', '2025-01-18 05:41:09', '2025-01-22 07:58:16', 'Bank Rakyat', '1258921', 4, '2025-01-22 07:58:16', 'Approved\r\n'),
(3, 'LOAN202501221422', 13, 'al_qardhul', '25000.00', 24, '1085.42', 'approved', '2025-01-22 13:32:28', '2025-01-22 21:50:03', 'Maybank', '10289328031', 4, '2025-01-22 21:50:03', ''),
(4, 'LOAN202501227082', 13, 'skim_khas', '8500.00', 12, '738.08', 'approved', '2025-01-22 13:33:44', '2025-01-22 21:50:14', 'Public Bank', '1391028372', 4, '2025-01-22 21:50:14', '');

-- --------------------------------------------------------

--
-- Table structure for table `loan_guarantors`
--

CREATE TABLE `loan_guarantors` (
  `id` int NOT NULL,
  `loan_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `ic_no` varchar(14) NOT NULL,
  `home_address` varchar(255) NOT NULL,
  `member_id` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_payments`
--

CREATE TABLE `loan_payments` (
  `id` int NOT NULL,
  `loan_id` int NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `remaining_balance` decimal(10,2) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loan_payments`
--

INSERT INTO `loan_payments` (`id`, `loan_id`, `payment_amount`, `remaining_balance`, `description`, `payment_date`, `created_at`, `updated_at`) VALUES
(1, 1, '671.16', '19254.66', 'Pembayaran bulanan', '2024-02-04', '2024-02-19 20:00:00', '2024-07-20 21:00:00'),
(2, 1, '1259.86', '18768.01', 'Bayaran ansuran', '2024-04-07', '2024-08-30 20:00:00', '2024-01-18 17:00:00'),
(3, 1, '1079.58', '19242.74', 'Pembayaran awal', '2024-05-10', '2024-05-31 03:00:00', '2024-08-07 03:00:00'),
(4, 1, '618.67', '19119.44', 'Bayaran ansuran', '2024-06-12', '2024-12-19 19:00:00', '2024-10-26 03:00:00'),
(5, 1, '873.17', '19338.60', 'Bayaran ansuran', '2024-07-02', '2024-08-17 22:00:00', '2024-12-29 16:00:00'),
(6, 1, '912.90', '18914.15', 'Pembayaran bulanan', '2024-07-28', '2024-01-03 21:00:00', '2024-02-02 17:00:00'),
(7, 1, '1372.06', '18703.18', 'Bayaran ansuran', '2025-01-05', '2024-03-14 17:00:00', '2024-12-12 18:00:00'),
(8, 1, '1311.65', '18822.90', 'Bayaran separa', '2025-01-09', '2024-09-21 22:00:00', '2024-08-20 21:00:00'),
(9, 2, '527.88', '23442.90', 'Bayaran separa', '2024-02-18', '2024-04-29 18:00:00', '2024-12-31 18:00:00'),
(10, 2, '832.49', '23541.03', 'Pembayaran awal', '2024-03-23', '2024-11-17 22:00:00', '2024-01-27 01:00:00'),
(11, 2, '319.91', '23578.56', 'Bayaran separa', '2024-05-29', '2024-12-21 21:00:00', '2024-06-26 03:00:00'),
(12, 2, '710.94', '23356.91', 'Pembayaran bulanan', '2024-07-07', '2024-11-05 22:00:00', '2024-04-25 02:00:00'),
(13, 2, '454.91', '23317.05', 'Bayaran ansuran', '2024-07-28', '2024-07-13 02:00:00', '2024-12-21 03:00:00'),
(14, 2, '512.65', '23513.91', 'Bayaran ansuran', '2024-08-07', '2024-04-23 01:00:00', '2024-12-27 20:00:00'),
(15, 2, '648.79', '23582.48', 'Bayaran separa', '2024-10-18', '2024-07-28 21:00:00', '2024-09-18 16:00:00'),
(16, 2, '398.60', '23676.72', 'Bayaran ansuran', '2025-01-13', '2024-01-20 20:00:00', '2024-07-24 23:00:00'),
(17, 3, '832.28', '24442.39', 'Pembayaran awal', '2024-06-04', '2025-01-20 01:00:00', '2024-11-21 02:00:00'),
(18, 3, '387.15', '24596.87', 'Pembayaran awal', '2024-06-20', '2024-02-02 17:00:00', '2024-04-07 03:00:00'),
(19, 3, '519.20', '24209.86', 'Bayaran separa', '2024-07-05', '2024-10-24 20:00:00', '2024-10-18 22:00:00'),
(20, 3, '586.55', '24479.59', 'Pembayaran awal', '2024-09-22', '2024-05-21 01:00:00', '2024-10-12 20:00:00'),
(21, 3, '710.67', '24320.89', 'Bayaran separa', '2024-09-23', '2024-01-20 18:00:00', '2024-11-16 22:00:00'),
(22, 3, '389.11', '24460.10', 'Bayaran ansuran', '2024-12-27', '2024-10-31 17:00:00', '2024-05-16 20:00:00'),
(23, 3, '780.28', '24316.23', 'Pembayaran bulanan', '2025-01-04', '2024-06-03 17:00:00', '2024-07-22 18:00:00'),
(24, 3, '555.52', '24665.83', 'Pembayaran bulanan', '2025-01-20', '2024-07-27 00:00:00', '2025-01-01 23:00:00'),
(25, 4, '321.13', '7991.12', 'Bayaran separa', '2024-02-02', '2024-03-04 23:00:00', '2024-06-11 20:00:00'),
(26, 4, '375.53', '8186.32', 'Pembayaran bulanan', '2024-04-01', '2024-03-13 18:00:00', '2024-08-28 20:00:00'),
(27, 4, '335.44', '8145.99', 'Pembayaran bulanan', '2024-04-14', '2024-10-14 02:00:00', '2024-03-31 22:00:00'),
(28, 4, '432.21', '8146.99', 'Bayaran separa', '2024-04-17', '2024-03-08 16:00:00', '2024-10-01 20:00:00'),
(29, 4, '299.56', '8236.34', 'Pembayaran bulanan', '2024-05-12', '2024-12-13 21:00:00', '2024-09-06 01:00:00'),
(30, 4, '326.53', '8216.50', 'Pembayaran bulanan', '2024-06-15', '2024-03-10 23:00:00', '2024-08-12 16:00:00'),
(31, 4, '411.36', '8140.74', 'Pembayaran awal', '2024-07-11', '2024-07-17 16:00:00', '2024-11-22 03:00:00'),
(32, 4, '489.85', '8181.76', 'Bayaran separa', '2024-11-30', '2024-12-20 16:00:00', '2024-04-10 19:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int NOT NULL,
  `member_id` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ic_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `gender` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `religion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `race` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `marital_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `position` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `grade` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `monthly_salary` decimal(10,2) DEFAULT NULL,
  `home_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `home_postcode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `home_state` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `office_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `office_postcode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `office_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `home_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fax` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `registration_fee` decimal(10,2) DEFAULT NULL,
  `share_capital` decimal(10,2) DEFAULT NULL,
  `fee_capital` decimal(10,2) DEFAULT NULL,
  `deposit_funds` decimal(10,2) DEFAULT NULL,
  `welfare_fund` decimal(10,2) DEFAULT NULL,
  `fixed_deposit` decimal(10,2) DEFAULT NULL,
  `other_contributions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `family_relationship` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `family_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `family_ic` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `birthday` date DEFAULT NULL,
  `age` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `member_id`, `name`, `ic_no`, `gender`, `religion`, `race`, `marital_status`, `position`, `grade`, `monthly_salary`, `home_address`, `home_postcode`, `home_state`, `office_address`, `office_postcode`, `office_phone`, `home_phone`, `fax`, `registration_fee`, `share_capital`, `fee_capital`, `deposit_funds`, `welfare_fund`, `fixed_deposit`, `other_contributions`, `family_relationship`, `family_name`, `family_ic`, `password`, `status`, `created_at`, `updated_at`, `birthday`, `age`) VALUES
(5, '20250001', 'WEE LEONG CHUAN', '990000-00-0000', 'Male', 'Buddha', 'Malay', 'Married', 'Pengguna', 'VU2', '2000.00', 'JLN UTAMA', '81000', 'WP Kuala Lumpur', 'JLN AMANAH', '80390', '292', '019', '0923', '50.00', '300.00', '50.00', '500.00', '50.00', '50.00', '', 'Mother', 'ZJS', '093101-81-0111', NULL, 'Active', '2025-01-14 08:47:34', '2025-01-14 08:47:34', NULL, NULL),
(6, '20250002', 'TAN YEE BAL', '001020-10-1203', 'Female', 'Kristian', 'Chinese', 'Single', 'PENSYARAH', 'DS54', '1000.00', 'JLN MALAYSIA', '81300', 'Selangor', 'JLN UTAMA', '81300', '123', '123', '123', '50.00', '300.00', '50.00', '50.00', '50.00', '50.00', '', 'Father', 'TAN JS', '011201-01-9112', NULL, 'Active', '2025-01-14 12:48:35', '2025-01-14 12:48:35', NULL, NULL),
(7, '20250003', 'MUHAMMAD ZJ', '040000-00-0000', 'Male', 'Islam', 'Malay', 'Single', 'IT', 'DS54', '9000.00', 'JLN 5', '81000', 'Selangor', 'JLN 10', '81000', '010', '00000', '1111', '50.00', '300.00', '50.00', '50.00', '50.00', '50.00', '', 'Husband', 'ENCIK TAN', '019281-91-2312', NULL, 'Active', '2025-01-14 13:48:30', '2025-01-14 13:48:30', NULL, NULL),
(9, '20250004', 'A', '777777-77-7777', 'Male', 'Islam', 'Chinese', 'Single', 'Pelajar UTM', 'VU1', '4444.00', 'AQSDF', '33', 'Terengganu', 'ASDF', '22222', '01110830382', '01110830382', '55555', '50.00', '300.00', '333.00', '33.00', '33.00', '33.00', '333', 'Husband', 'Cheryl', '999999-99-9999', NULL, 'Active', '2025-01-14 14:42:49', '2025-01-14 14:42:49', NULL, NULL),
(10, '20250005', 'LIM ZI MING', '888888-00-0000', 'Female', 'Kristian', 'Indian', 'Single', 'IT', 'VU1', '8000.00', 'NO 15', '80000', 'WP Kuala Lumpur', 'NO 100', '15101', '018231', '01222313', '12312', '50.00', '300.00', '50.00', '50.00', '50.00', '50.00', '', 'Mother', 'TESTING', '091231-12-3123', NULL, 'Active', '2025-01-14 18:30:43', '2025-01-14 18:30:43', NULL, NULL),
(12, '20250006', 'CHEW CX', '222222-22-2222', 'Male', 'Kristian', 'Malay', 'Single', 'Free Rider', 'VK6', '300.00', 'K', '33', 'Sarawak', 'BHJKL', '8', '0199665555', '0199665555', '55555', '50.00', '300.00', '22.00', '22.00', '22.00', '22.00', '22', 'Mother', 'gggg', '999999-99-9999', NULL, 'Active', '2025-01-16 10:24:42', '2025-01-16 10:24:42', NULL, NULL),
(13, '20250007', 'MUHAMMAD TAUFIQ BIN ABDULLAH', '981114-01-2841', 'Male', 'Islam', 'Malay', 'Single', 'PEGAWAI PERUBATAN', 'DUG54', '6400.00', 'NO 195, JLN PADI RIA 19, BANDAR UDA UTAMA, 81200 JOHOR BARHU, JOHOR', '81200', 'JOHOR', 'EY, B-15, MEDINI 9, PERSIARAN MEDINI SENTRAL 1, BANDAR, 79250 ISKANDAR PUTERI, JOHOR', '79250', '075209290', '075201932', '074191292', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, 'Father', 'AHMAD FIRDAUS', '691204-12-1248', NULL, 'Active', '2025-01-22 21:30:03', '2025-01-22 21:30:03', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pendingloans`
--

CREATE TABLE `pendingloans` (
  `id` int NOT NULL,
  `reference_no` varchar(50) NOT NULL,
  `member_id` int NOT NULL,
  `loan_type` enum('al_bai','al_innah','skim_khas','road_tax','al_qardhul','other') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `duration` int NOT NULL,
  `monthly_payment` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `date_received` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `bank_name` varchar(25) DEFAULT NULL,
  `bank_account` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pendingloans`
--

INSERT INTO `pendingloans` (`id`, `reference_no`, `member_id`, `loan_type`, `amount`, `duration`, `monthly_payment`, `status`, `date_received`, `updated_at`, `bank_name`, `bank_account`) VALUES
(7, 'LOAN202501226265', 12, 'al_innah', '20000.00', 12, '1736.67', 'pending', '2025-01-22 00:07:04', '2025-01-22 08:07:04', 'Bank Rakyat', '12798123121');

-- --------------------------------------------------------

--
-- Table structure for table `pendingmember`
--

CREATE TABLE `pendingmember` (
  `id` int NOT NULL,
  `reference_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ic_no` varchar(22) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `age` int DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `gender` enum('Male','Female') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `religion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `race` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `marital_status` enum('Single','Married','Divorced','Widowed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `position` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `grade` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `monthly_salary` decimal(10,2) NOT NULL,
  `home_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `home_postcode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `home_state` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `office_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `office_postcode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `office_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `home_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fax` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `family_relationship` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `family_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `family_ic` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `registration_fee` decimal(10,2) NOT NULL,
  `share_capital` decimal(10,2) NOT NULL,
  `fee_capital` decimal(10,2) NOT NULL,
  `deposit_funds` decimal(10,2) NOT NULL,
  `welfare_fund` decimal(10,2) NOT NULL,
  `fixed_deposit` decimal(10,2) NOT NULL,
  `other_contributions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('Pending','Lulus','Tolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pendingmember`
--

INSERT INTO `pendingmember` (`id`, `reference_no`, `name`, `ic_no`, `age`, `birthday`, `gender`, `religion`, `race`, `marital_status`, `position`, `grade`, `monthly_salary`, `home_address`, `home_postcode`, `home_state`, `office_address`, `office_postcode`, `office_phone`, `home_phone`, `fax`, `family_relationship`, `family_name`, `family_ic`, `registration_fee`, `share_capital`, `fee_capital`, `deposit_funds`, `welfare_fund`, `fixed_deposit`, `other_contributions`, `created_at`, `updated_at`, `status`) VALUES
(13, NULL, 'TEST', '111111-11-1111', NULL, NULL, 'Female', 'Kristian', 'Indian', 'Married', '12', 'VU1', '1212.00', '1', '12121', 'Selangor', '12', '12', '12', '12', '12', 'Mother', '12', '111111-11-1111', '50.00', '300.00', '59.00', '50.00', '50.00', '50.00', '', '2025-01-15 03:25:54', '2025-01-15 03:25:54', 'Pending'),
(14, 'REF202501150001', 'TEO KAH WEE', '880011-01-0129', NULL, NULL, 'Male', 'Islam', 'Malay', 'Married', 'Doctor', 'VU1', '12000.00', 'JLN MALAYSIA 1', '81300', 'WP Kuala Lumpur', 'JLN SNP', '91320', '123', '0123', '123', 'Mother', 'TESTING L', '109182-11-2232', '50.00', '300.00', '50.00', '50.00', '50.00', '50.00', '', '2025-01-15 07:31:41', '2025-01-15 07:31:41', 'Pending'),
(17, 'REF202501210001', 'AHMAD IQBAL', '880123-91-9123', NULL, NULL, 'Male', 'Islam', 'Malay', 'Single', 'Aha', 'VU1', '123.00', 'OIU', '1231', 'WP Kuala Lumpur', '123', '123', '123', '123', '12', 'Mother', 'sad', '123132-12-3132', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '2025-01-21 18:22:45', '2025-01-21 18:22:45', 'Pending'),
(18, 'REF202501210002', 'AHMAD C', '910123-29-2312', 12, '2012-11-11', 'Male', 'Islam', 'Malay', 'Single', 'FREE', 'VU1', '123132.00', 'ASD', '123', 'Sabah', '123', '123', '123', '123', '132', 'Mother', '123', '121111-12-3910', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '2025-01-21 18:37:40', '2025-01-21 18:37:40', 'Pending'),
(19, 'REF202501210003', 'AHMAD E', '190312-39-1023', 12, '2012-11-22', 'Female', 'Islam', 'Chinese', 'Single', 'Free', 'VU1', '12312.00', 'NO 59, JLN UNITE, TMN MS, 81032 JOHOR', '81032', 'JOHOR', '123, WISMA KFC, TMN KL, 15920 KELANTAN ', '15920', '1231', '3123', '123', 'Father', 'RJ', '121122-31-2312', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '2025-01-21 18:54:40', '2025-01-21 18:54:40', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `recurring_payments`
--

CREATE TABLE `recurring_payments` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `deduction_day` int NOT NULL,
  `payment_method` enum('salary','fpx','card') NOT NULL,
  `status` enum('active','paused','cancelled') DEFAULT 'active',
  `last_deduction_date` date DEFAULT NULL,
  `next_deduction_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `recurring_payments`
--

INSERT INTO `recurring_payments` (`id`, `member_id`, `amount`, `deduction_day`, `payment_method`, `status`, `last_deduction_date`, `next_deduction_date`, `created_at`, `updated_at`) VALUES
(2, 9, '1000.00', 5, 'fpx', 'active', NULL, '2025-02-05', '2025-01-14 16:30:37', '2025-01-14 16:30:37');

-- --------------------------------------------------------

--
-- Table structure for table `rejectedloans`
--

CREATE TABLE `rejectedloans` (
  `id` int NOT NULL,
  `reference_no` varchar(20) NOT NULL,
  `member_id` int NOT NULL,
  `loan_type` enum('al_bai','al_innah','skim_khas','road_tax','al_qardhul','other') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `duration` int NOT NULL,
  `monthly_payment` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `date_received` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `bank_name` varchar(50) NOT NULL,
  `bank_account` varchar(20) NOT NULL,
  `rejected_by` int DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `remarks` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rejectedloans`
--

INSERT INTO `rejectedloans` (`id`, `reference_no`, `member_id`, `loan_type`, `amount`, `duration`, `monthly_payment`, `status`, `date_received`, `updated_at`, `bank_name`, `bank_account`, `rejected_by`, `rejected_at`, `remarks`) VALUES
(1, 'LOAN202501194120', 6, 'al_bai', '14400.00', 12, '1250.40', 'approved', '2025-01-18 21:13:59', '2025-01-21 17:11:28', 'Hong Leong Bank', '252352', 4, '2025-01-21 17:11:28', 'thx');

-- --------------------------------------------------------

--
-- Table structure for table `rejectedmember`
--

CREATE TABLE `rejectedmember` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ic_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `gender` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `religion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `race` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `marital_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `position` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `grade` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `monthly_salary` decimal(10,2) DEFAULT NULL,
  `home_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `home_postcode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `home_state` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `office_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `office_postcode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `office_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `home_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fax` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `registration_fee` decimal(10,2) DEFAULT NULL,
  `share_capital` decimal(10,2) DEFAULT NULL,
  `fee_capital` decimal(10,2) DEFAULT NULL,
  `deposit_funds` decimal(10,2) DEFAULT NULL,
  `welfare_fund` decimal(10,2) DEFAULT NULL,
  `fixed_deposit` decimal(10,2) DEFAULT NULL,
  `other_contributions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `family_relationship` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `family_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `family_ic` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Inactive',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `birthday` date DEFAULT NULL,
  `age` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rejectedmember`
--

INSERT INTO `rejectedmember` (`id`, `name`, `ic_no`, `gender`, `religion`, `race`, `marital_status`, `position`, `grade`, `monthly_salary`, `home_address`, `home_postcode`, `home_state`, `office_address`, `office_postcode`, `office_phone`, `home_phone`, `fax`, `registration_fee`, `share_capital`, `fee_capital`, `deposit_funds`, `welfare_fund`, `fixed_deposit`, `other_contributions`, `family_relationship`, `family_name`, `family_ic`, `password`, `status`, `created_at`, `updated_at`, `birthday`, `age`) VALUES
(6, 'TEST', '011111-11-1111', 'Male', 'Others-Religion', 'Malay', 'Single', '124', 'VU1', '24444.00', 'AD', '1212', 'WP Labuan', '1', '2', '1', '1', '1', '50.00', '300.00', '50.00', '50.00', '50.00', '50.00', '', 'Mother', 'a', '111111-41-1111', NULL, 'Inactive', '2025-01-15 02:16:53', '2025-01-15 02:16:53', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `savings_accounts`
--

CREATE TABLE `savings_accounts` (
  `id` int NOT NULL,
  `account_number` varchar(20) DEFAULT NULL,
  `member_id` int NOT NULL,
  `current_amount` decimal(10,2) DEFAULT '0.00',
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `savings_accounts`
--

INSERT INTO `savings_accounts` (`id`, `account_number`, `member_id`, `current_amount`, `status`, `created_at`, `updated_at`) VALUES
(28, 'SAV-000009-8692', 9, '712.00', 'active', '2025-01-14 14:42:49', '2025-01-16 12:47:24'),
(29, 'SAV-000012-5441', 12, '940.00', 'active', '2025-01-16 10:24:42', '2025-01-18 07:35:33'),
(30, 'SAV-000013-6893', 13, '24336.98', 'active', '2025-01-22 21:30:03', '2025-01-23 06:39:05');

-- --------------------------------------------------------

--
-- Table structure for table `savings_goals`
--

CREATE TABLE `savings_goals` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `target_amount` decimal(10,2) NOT NULL,
  `current_amount` decimal(10,2) DEFAULT '0.00',
  `target_date` date NOT NULL,
  `monthly_target` decimal(10,2) DEFAULT '0.00',
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `savings_goals`
--

INSERT INTO `savings_goals` (`id`, `member_id`, `name`, `target_amount`, `current_amount`, `target_date`, `monthly_target`, `status`, `created_at`, `updated_at`) VALUES
(4, 9, 'Simpanan Rumah', '1900.00', '0.00', '2025-02-27', '1900.00', 'active', '2025-01-14 16:26:20', '2025-01-16 08:56:45'),
(5, 9, '2333', '24444.00', '0.00', '2025-02-28', '24444.00', 'active', '2025-01-16 08:32:36', '2025-01-16 08:32:36'),
(7, 12, 'Savings', '1000.00', '0.00', '2025-04-30', '333.33', 'active', '2025-01-18 07:17:21', '2025-01-18 07:17:35');

-- --------------------------------------------------------

--
-- Table structure for table `savings_transactions`
--

CREATE TABLE `savings_transactions` (
  `id` int NOT NULL,
  `savings_account_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('deposit','withdrawal','transfer_in','transfer_out') NOT NULL,
  `payment_method` enum('cash','bank_transfer','salary_deduction','fpx','card','ewallet') NOT NULL,
  `reference_no` varchar(50) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `sender_account_number` varchar(50) DEFAULT NULL,
  `recipient_account_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `savings_transactions`
--

INSERT INTO `savings_transactions` (`id`, `savings_account_id`, `amount`, `type`, `payment_method`, `reference_no`, `description`, `created_at`, `sender_account_number`, `recipient_account_number`) VALUES
(22, 28, '344.00', 'deposit', 'fpx', 'DEP17370186495067', 'Deposit ke akaun simpanan', '2025-01-16 09:10:49', NULL, NULL),
(23, 28, '300.00', 'deposit', 'fpx', 'DEP17370187607513', 'Deposit ke akaun simpanan', '2025-01-16 09:12:40', NULL, NULL),
(24, 28, '30.00', 'deposit', 'fpx', 'DEP17370189325980', 'Deposit ke akaun simpanan', '2025-01-16 09:15:32', NULL, NULL),
(25, 28, '10.00', 'deposit', 'fpx', 'DEP17370191296296', 'Deposit ke akaun simpanan', '2025-01-16 09:18:49', NULL, NULL),
(26, 28, '12.00', 'deposit', 'fpx', 'DEP17370295784453', 'Deposit ke akaun simpanan', '2025-01-16 12:12:58', NULL, NULL),
(35, 28, '10.00', 'transfer_out', 'fpx', 'TRF20250116124724226', 'Pembayaran ', '2025-01-16 12:47:24', 'SAV-000009-8692', 'SAV-000012-5441'),
(36, 29, '10.00', 'transfer_in', 'fpx', 'TRF20250116124724226', 'Pembayaran ', '2025-01-16 12:47:24', 'SAV-000009-8692', 'SAV-000012-5441'),
(37, 29, '800.00', 'deposit', 'fpx', 'DEP17371857333743', 'Deposit ke akaun simpanan', '2025-01-18 07:35:33', NULL, NULL),
(68, 30, '841.63', 'transfer_out', 'card', 'TRX202404194732', 'Pengeluaran', '2024-04-18 17:21:00', 'SAV-000013-6893', 'SAV-000013-6893'),
(69, 30, '654.37', 'transfer_out', 'ewallet', 'TRX202404180021', 'Pindahan masuk', '2024-04-18 07:59:00', 'SAV-000013-6893', 'SAV-000013-6893'),
(70, 30, '476.78', 'deposit', 'fpx', 'TRX202404157113', 'Pindahan keluar', '2024-04-15 04:46:00', 'SAV-000012-5441', 'SAV-938561-5335'),
(71, 30, '765.03', 'withdrawal', 'fpx', 'TRX202407229758', 'Pengeluaran', '2024-07-22 15:45:00', 'SAV-668230-8738', 'SAV-000013-6893'),
(72, 30, '922.15', 'transfer_out', 'fpx', 'TRX202410308483', 'Pindahan keluar', '2024-10-29 22:05:00', 'SAV-000013-6893', 'SAV-000013-6893'),
(73, 30, '527.82', 'transfer_out', 'fpx', 'TRX202401036522', 'Pengeluaran', '2024-01-03 09:36:00', 'SAV-000012-5441', 'SAV-000012-5441'),
(74, 30, '417.82', 'transfer_in', 'fpx', 'TRX202410288728', 'Pindahan keluar', '2024-10-28 05:22:00', 'SAV-000013-6893', 'SAV-660182-4643'),
(75, 30, '972.54', 'withdrawal', 'card', 'TRX202404293602', 'Pindahan keluar', '2024-04-28 16:36:00', 'SAV-000009-8692', 'SAV-000013-6893'),
(76, 30, '775.78', 'transfer_in', 'ewallet', 'TRX202411159769', 'Pindahan masuk', '2024-11-14 17:31:00', 'SAV-236777-0050', 'SAV-000013-6893'),
(77, 30, '211.57', 'transfer_in', 'ewallet', 'TRX202401188539', 'Pengeluaran', '2024-01-18 14:44:00', 'SAV-000009-8692', 'SAV-575650-6395'),
(78, 30, '338.53', 'deposit', 'fpx', 'TRX202411118965', 'Simpanan bulanan', '2024-11-10 21:32:00', 'SAV-000013-6893', 'SAV-000012-5441'),
(79, 30, '992.81', 'transfer_in', 'fpx', 'TRX202401152827', 'Pengeluaran', '2024-01-15 15:49:00', 'SAV-000013-6893', 'SAV-000013-6893'),
(80, 30, '610.21', 'deposit', 'fpx', 'TRX202411100788', 'Simpanan bulanan', '2024-11-10 13:23:00', 'SAV-000009-8692', 'SAV-000013-6893'),
(81, 30, '828.01', 'withdrawal', 'ewallet', 'TRX202404230841', 'Pengeluaran', '2024-04-23 10:37:00', 'SAV-000009-8692', 'SAV-000013-6893'),
(82, 30, '777.83', 'deposit', 'card', 'TRX202407318434', 'Pindahan keluar', '2024-07-31 07:47:00', 'SAV-000012-5441', 'SAV-000013-6893'),
(83, 30, '332.08', 'deposit', 'fpx', 'TRX202404218793', 'Pengeluaran', '2024-04-21 04:20:00', 'SAV-000013-6893', 'SAV-000012-5441'),
(84, 30, '724.05', 'transfer_out', 'card', 'TRX202407290218', 'Simpanan bulanan', '2024-07-29 00:35:00', 'SAV-000012-5441', 'SAV-000012-5441'),
(85, 30, '532.08', 'withdrawal', 'ewallet', 'TRX202411066049', 'Pindahan masuk', '2024-11-05 23:41:00', 'SAV-000013-6893', 'SAV-000012-5441'),
(86, 30, '462.83', 'withdrawal', 'card', 'TRX202401290736', 'Simpanan bulanan', '2024-01-28 23:09:00', 'SAV-000009-8692', 'SAV-000012-5441'),
(87, 30, '536.49', 'withdrawal', 'fpx', 'TRX202405083245', 'Pengeluaran', '2024-05-07 23:08:00', 'SAV-000012-5441', 'SAV-000013-6893'),
(88, 30, '286.46', 'withdrawal', 'ewallet', 'TRX202401278996', 'Pengeluaran', '2024-01-27 09:15:00', 'SAV-000013-6893', 'SAV-000012-5441'),
(89, 30, '99.05', 'transfer_in', 'card', 'TRX202408146237', 'Pindahan keluar', '2024-08-13 20:30:00', 'SAV-000012-5441', 'SAV-000013-6893'),
(90, 30, '88.02', 'transfer_out', 'ewallet', 'TRX202408138502', 'Pindahan masuk', '2024-08-12 16:56:00', 'SAV-000013-6893', 'SAV-741962-9962'),
(91, 30, '179.97', 'transfer_in', 'card', 'TRX202408126251', 'Pindahan masuk', '2024-08-12 08:19:00', 'SAV-000013-6893', 'SAV-523964-5305'),
(92, 30, '822.82', 'transfer_out', 'ewallet', 'TRX202411209326', 'Pindahan masuk', '2024-11-19 23:39:00', 'SAV-936615-2903', 'SAV-000012-5441'),
(93, 30, '440.20', 'transfer_out', 'fpx', 'TRX202408093257', 'Simpanan bulanan', '2024-08-08 21:58:00', 'SAV-000013-6893', 'SAV-000009-8692'),
(94, 30, '563.67', 'deposit', 'fpx', 'TRX202411170595', 'Simpanan bulanan', '2024-11-17 12:09:00', 'SAV-000009-8692', 'SAV-260114-7910'),
(95, 30, '527.30', 'transfer_out', 'card', 'TRX202401211702', 'Pindahan masuk', '2024-01-21 00:03:00', 'SAV-000013-6893', 'SAV-000013-6893'),
(96, 30, '847.48', 'transfer_out', 'fpx', 'TRX202404309727', 'Simpanan bulanan', '2024-04-30 08:09:00', 'SAV-000013-6893', 'SAV-000013-6893'),
(97, 30, '149.55', 'transfer_out', 'card', 'TRX202405197687', 'Pindahan keluar', '2024-05-19 04:40:00', 'SAV-000009-8692', 'SAV-000013-6893'),
(98, 30, '196.95', 'transfer_in', 'fpx', 'TRX202408279802', 'Simpanan bulanan', '2024-08-27 15:15:00', 'SAV-000009-8692', 'SAV-000013-6893'),
(99, 30, '184.08', 'transfer_in', 'fpx', 'TRX202408261372', 'Pengeluaran', '2024-08-25 16:10:00', 'SAV-932862-5917', 'SAV-000009-8692'),
(100, 30, '890.54', 'transfer_out', 'ewallet', 'TRX202402070828', 'Pengeluaran', '2024-02-06 21:44:00', 'SAV-000012-5441', 'SAV-000009-8692'),
(101, 30, '892.38', 'deposit', 'card', 'TRX202408243164', 'Pengeluaran', '2024-08-23 19:22:00', 'SAV-000013-6893', 'SAV-000013-6893'),
(102, 30, '574.90', 'withdrawal', 'ewallet', 'TRX202408233044', 'Pengeluaran', '2024-08-23 02:26:00', 'SAV-717082-0605', 'SAV-000012-5441'),
(103, 30, '103.38', 'transfer_in', 'ewallet', 'TRX202408229841', 'Pindahan keluar', '2024-08-21 17:13:00', 'SAV-000009-8692', 'SAV-000009-8692'),
(104, 30, '538.25', 'withdrawal', 'ewallet', 'TRX202402037662', 'Simpanan bulanan', '2024-02-03 13:25:00', 'SAV-940545-1665', 'SAV-000012-5441'),
(105, 30, '73.86', 'transfer_out', 'ewallet', 'TRX202408210174', 'Pindahan masuk', '2024-08-21 05:42:00', 'SAV-000009-8692', 'SAV-000013-6893'),
(106, 30, '764.34', 'transfer_in', 'card', 'TRX202411299991', 'Pengeluaran', '2024-11-29 15:38:00', 'SAV-000012-5441', 'SAV-030100-4686'),
(107, 30, '865.92', 'transfer_in', 'fpx', 'TRX202402028961', 'Simpanan bulanan', '2024-02-01 21:30:00', 'SAV-000009-8692', 'SAV-000013-6893'),
(108, 30, '267.22', 'deposit', 'card', 'TRX202408191035', 'Simpanan bulanan', '2024-08-19 09:06:00', 'SAV-000009-8692', 'SAV-000013-6893'),
(109, 30, '343.48', 'deposit', 'card', 'TRX202405296971', 'Pindahan masuk', '2024-05-28 23:26:00', 'SAV-000009-8692', 'SAV-000013-6893'),
(110, 30, '770.51', 'deposit', 'card', 'TRX202405275409', 'Pindahan keluar', '2024-05-27 08:48:00', 'SAV-000012-5441', 'SAV-000013-6893'),
(111, 30, '906.79', 'deposit', 'card', 'TRX202412133169', 'Pengeluaran', '2024-12-12 16:00:00', 'SAV-000012-5441', 'SAV-000013-6893'),
(112, 30, '323.79', 'withdrawal', 'ewallet', 'TRX202405258065', 'Pindahan keluar', '2024-05-25 01:45:00', 'SAV-000013-6893', 'SAV-000013-6893'),
(113, 30, '828.26', 'transfer_out', 'card', 'TRX202412114359', 'Pindahan masuk', '2024-12-11 01:45:00', 'SAV-000013-6893', 'SAV-000013-6893'),
(114, 30, '845.01', 'deposit', 'ewallet', 'TRX202402144640', 'Pengeluaran', '2024-02-14 15:32:00', 'SAV-000013-6893', 'SAV-000013-6893'),
(115, 30, '595.00', 'transfer_in', 'card', 'TRX202412099615', 'Simpanan bulanan', '2024-12-08 23:26:00', 'SAV-000009-8692', 'SAV-000013-6893'),
(116, 30, '447.46', 'transfer_out', 'card', 'TRX202408305492', 'Simpanan bulanan', '2024-08-30 05:38:00', 'SAV-000013-6893', 'SAV-000013-6893'),
(117, 30, '756.23', 'withdrawal', 'fpx', 'TRX202402119246', 'Simpanan bulanan', '2024-02-10 18:54:00', 'SAV-000012-5441', 'SAV-000013-6893'),
(131, 30, '1855.87', 'deposit', 'fpx', 'TRX202404199046', 'Simpanan bulanan', '2024-04-19 10:58:00', 'SAV-376551-4778', 'SAV-000013-6893'),
(132, 30, '1797.84', 'transfer_in', 'fpx', 'TRX202407280511', 'Pindahan masuk', '2024-07-27 17:43:00', 'SAV-105481-5581', 'SAV-000013-6893'),
(133, 30, '893.17', 'deposit', 'ewallet', 'TRX202404181495', 'Pindahan masuk', '2024-04-18 15:53:00', 'SAV-000012-5441', 'SAV-000013-6893'),
(134, 30, '923.57', 'transfer_in', 'card', 'TRX202407263620', 'Simpanan bulanan', '2024-07-26 13:20:00', 'SAV-000009-8692', 'SAV-000013-6893'),
(135, 30, '1844.98', 'deposit', 'card', 'TRX202411025580', 'Simpanan bulanan', '2024-11-01 20:26:00', 'SAV-781857-7505', 'SAV-000013-6893'),
(136, 30, '1763.31', 'transfer_in', 'card', 'TRX202411011301', 'Pindahan masuk', '2024-11-01 12:40:00', 'SAV-674714-6276', 'SAV-000013-6893'),
(137, 30, '1531.92', 'deposit', 'card', 'TRX202401058182', 'Pindahan masuk', '2024-01-05 08:53:00', 'SAV-000012-5441', 'SAV-000013-6893'),
(138, 30, '1780.81', 'deposit', 'card', 'TRX202404144465', 'Pindahan masuk', '2024-04-14 08:40:00', 'SAV-000009-8692', 'SAV-000013-6893'),
(139, 30, '1161.69', 'transfer_in', 'fpx', 'TRX202401035863', 'Pindahan masuk', '2024-01-03 14:59:00', 'SAV-000009-8692', 'SAV-000013-6893'),
(140, 30, '720.53', 'deposit', 'ewallet', 'TRX202404127687', 'Pindahan masuk', '2024-04-12 05:32:00', 'SAV-118233-9890', 'SAV-000013-6893'),
(141, 30, '1801.68', 'transfer_in', 'card', 'TRX202401024637', 'Pindahan masuk', '2024-01-01 22:40:00', 'SAV-302807-4363', 'SAV-000013-6893'),
(142, 30, '587.97', 'deposit', 'fpx', 'TRX202404115159', 'Simpanan bulanan', '2024-04-11 05:41:00', 'SAV-506722-5648', 'SAV-000013-6893'),
(143, 30, '1462.91', 'transfer_in', 'ewallet', 'TRX202401011335', 'Simpanan bulanan', '2024-01-01 09:21:00', 'SAV-704970-8370', 'SAV-000013-6893'),
(144, 30, '1763.76', 'deposit', 'card', 'TRX202404103660', 'Simpanan bulanan', '2024-04-10 14:32:00', 'SAV-000012-5441', 'SAV-000013-6893'),
(145, 30, '1432.05', 'deposit', 'ewallet', 'TRX202407191966', 'Simpanan bulanan', '2024-07-19 07:57:00', 'SAV-939041-5215', 'SAV-000013-6893'),
(146, 30, '1523.80', 'transfer_in', 'ewallet', 'TRX202411148076', 'Simpanan bulanan', '2024-11-13 16:11:00', 'SAV-143036-7973', 'SAV-000013-6893'),
(147, 30, '1104.47', 'deposit', 'ewallet', 'TRX202411112488', 'Pindahan masuk', '2024-11-11 14:02:00', 'SAV-947573-2781', 'SAV-000013-6893'),
(148, 30, '583.61', 'transfer_in', 'card', 'TRX202404236094', 'Pindahan masuk', '2024-04-22 21:15:00', 'SAV-000012-5441', 'SAV-000013-6893'),
(149, 30, '1881.00', 'deposit', 'ewallet', 'TRX202411095737', 'Pindahan masuk', '2024-11-09 14:02:00', 'SAV-971692-6882', 'SAV-000013-6893'),
(150, 30, '1356.20', 'deposit', 'ewallet', 'TRX202407314544', 'Simpanan bulanan', '2024-07-31 11:56:00', 'SAV-000009-8692', 'SAV-000013-6893'),
(162, 30, '1068.90', 'transfer_out', 'fpx', 'TRX202501072447', 'Pindahan keluar', '2025-01-21 16:54:00', 'SAV-000013-6893', 'SAV-000013-6893'),
(163, 30, '272.75', 'deposit', 'ewallet', 'TRX202501237034', 'Pindahan masuk', '2025-01-22 13:48:24', 'SAV-000012-5441', 'SAV-000013-6893'),
(165, 30, '370.92', 'withdrawal', 'card', 'TRX202501224752', 'Simpanan bulanan', '2025-01-13 18:39:00', 'SAV-000012-5441', 'SAV-000013-6893'),
(166, 30, '583.87', 'deposit', 'fpx', 'TRX202501238952', 'Pindahan keluar segera', '2025-01-23 04:39:05', 'SAV-000012-5441', 'SAV-000013-6893');

-- --------------------------------------------------------

--
-- Table structure for table `statements`
--

CREATE TABLE `statements` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `account_id` int NOT NULL,
  `reference_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('generated','downloaded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'generated',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `statements`
--

INSERT INTO `statements` (`id`, `member_id`, `account_id`, `reference_no`, `start_date`, `end_date`, `status`, `created_at`) VALUES
(1, 12, 29, 'STM202501182402', '2024-12-18', '2025-01-18', 'generated', '2025-01-18 14:44:27');

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `email_enabled` tinyint(1) DEFAULT '0',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_notifications`
--

INSERT INTO `user_notifications` (`id`, `member_id`, `email_enabled`, `email`, `created_at`, `updated_at`) VALUES
(3, 12, 1, 'kada.ecopioneer@gmail.com', '2025-01-22 21:18:53', '2025-01-22 21:18:53'),
(4, 13, 1, 'kada.ecopioneer@gmail.com', '2025-01-22 21:47:11', '2025-01-22 21:48:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `annual_report`
--
ALTER TABLE `annual_report`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_year` (`year`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `directors`
--
ALTER TABLE `directors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_director_id` (`director_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `loan_guarantors`
--
ALTER TABLE `loan_guarantors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_id` (`loan_id`);

--
-- Indexes for table `loan_payments`
--
ALTER TABLE `loan_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_id` (`loan_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ic_no` (`ic_no`),
  ADD UNIQUE KEY `member_id` (`member_id`);

--
-- Indexes for table `pendingloans`
--
ALTER TABLE `pendingloans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `pendingmember`
--
ALTER TABLE `pendingmember`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ic_no` (`ic_no`),
  ADD UNIQUE KEY `unique_reference_no` (`reference_no`);

--
-- Indexes for table `recurring_payments`
--
ALTER TABLE `recurring_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `rejectedloans`
--
ALTER TABLE `rejectedloans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `rejectedloans_ibfk_2` (`rejected_by`);

--
-- Indexes for table `rejectedmember`
--
ALTER TABLE `rejectedmember`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ic_no` (`ic_no`);

--
-- Indexes for table `savings_accounts`
--
ALTER TABLE `savings_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_account_number` (`account_number`),
  ADD KEY `fk_savings_admin` (`member_id`);

--
-- Indexes for table `savings_goals`
--
ALTER TABLE `savings_goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `savings_transactions`
--
ALTER TABLE `savings_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `savings_account_id` (`savings_account_id`);

--
-- Indexes for table `statements`
--
ALTER TABLE `statements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `annual_report`
--
ALTER TABLE `annual_report`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `directors`
--
ALTER TABLE `directors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `loan_guarantors`
--
ALTER TABLE `loan_guarantors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_payments`
--
ALTER TABLE `loan_payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pendingloans`
--
ALTER TABLE `pendingloans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pendingmember`
--
ALTER TABLE `pendingmember`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `recurring_payments`
--
ALTER TABLE `recurring_payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rejectedloans`
--
ALTER TABLE `rejectedloans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rejectedmember`
--
ALTER TABLE `rejectedmember`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `savings_accounts`
--
ALTER TABLE `savings_accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `savings_goals`
--
ALTER TABLE `savings_goals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `savings_transactions`
--
ALTER TABLE `savings_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT for table `statements`
--
ALTER TABLE `statements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `annual_report`
--
ALTER TABLE `annual_report`
  ADD CONSTRAINT `annual_report_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `admins` (`id`);

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `directors` (`id`);

--
-- Constraints for table `loan_guarantors`
--
ALTER TABLE `loan_guarantors`
  ADD CONSTRAINT `loan_guarantors_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `pendingloans` (`id`);

--
-- Constraints for table `loan_payments`
--
ALTER TABLE `loan_payments`
  ADD CONSTRAINT `loan_payments_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pendingloans`
--
ALTER TABLE `pendingloans`
  ADD CONSTRAINT `pendingloans_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);

--
-- Constraints for table `recurring_payments`
--
ALTER TABLE `recurring_payments`
  ADD CONSTRAINT `fk_member_recurring` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rejectedloans`
--
ALTER TABLE `rejectedloans`
  ADD CONSTRAINT `rejectedloans_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `rejectedloans_ibfk_2` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `savings_accounts`
--
ALTER TABLE `savings_accounts`
  ADD CONSTRAINT `fk_member_savings` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `savings_goals`
--
ALTER TABLE `savings_goals`
  ADD CONSTRAINT `fk_member_goals` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `savings_transactions`
--
ALTER TABLE `savings_transactions`
  ADD CONSTRAINT `savings_transactions_ibfk_1` FOREIGN KEY (`savings_account_id`) REFERENCES `savings_accounts` (`id`);

--
-- Constraints for table `statements`
--
ALTER TABLE `statements`
  ADD CONSTRAINT `statements_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `statements_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `savings_accounts` (`id`);

--
-- Constraints for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD CONSTRAINT `user_notifications_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
