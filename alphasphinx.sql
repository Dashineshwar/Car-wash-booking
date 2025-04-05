-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2025 at 05:32 PM
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
-- Database: `alphasphinx`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertTimeSlots` ()   BEGIN
    DECLARE start_time TIME;
    DECLARE end_time TIME;
    DECLARE city VARCHAR(255);

    -- Initialize time variables
    SET start_time = '08:00:00';
    SET end_time = '09:00:00';

    -- Loop for each hour slot from 8:00 AM to 12:00 AM
    WHILE start_time < '24:00:00' DO
        
        -- Insert slots for each city in Penang
        SET city = 'George Town';
        INSERT INTO TimeSlots (start_time, end_time, city, available_slots, total_slots, is_full)
        VALUES (CONCAT(CURDATE(), ' ', start_time), CONCAT(CURDATE(), ' ', end_time), city, 0, 0, FALSE);
        
        SET city = 'Bayan Lepas';
        INSERT INTO TimeSlots (start_time, end_time, city, available_slots, total_slots, is_full)
        VALUES (CONCAT(CURDATE(), ' ', start_time), CONCAT(CURDATE(), ' ', end_time), city, 0, 0, FALSE);
        
        SET city = 'Butterworth';
        INSERT INTO TimeSlots (start_time, end_time, city, available_slots, total_slots, is_full)
        VALUES (CONCAT(CURDATE(), ' ', start_time), CONCAT(CURDATE(), ' ', end_time), city, 0, 0, FALSE);
        
        SET city = 'Bukit Mertajam';
        INSERT INTO TimeSlots (start_time, end_time, city, available_slots, total_slots, is_full)
        VALUES (CONCAT(CURDATE(), ' ', start_time), CONCAT(CURDATE(), ' ', end_time), city, 0, 0, FALSE);
        
        SET city = 'Seberang Jaya';
        INSERT INTO TimeSlots (start_time, end_time, city, available_slots, total_slots, is_full)
        VALUES (CONCAT(CURDATE(), ' ', start_time), CONCAT(CURDATE(), ' ', end_time), city, 0, 0, FALSE);

        -- Increment the time slots by 1 hour
        SET start_time = ADDTIME(start_time, '01:00:00');
        SET end_time = ADDTIME(end_time, '01:00:00');
        
    END WHILE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `wallet` varchar(255) NOT NULL,
  `address_line_1` varchar(255) NOT NULL,
  `address_line_2` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `phone_no` varchar(255) NOT NULL,
  `acc_status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `email`, `wallet`, `address_line_1`, `address_line_2`, `postcode`, `city`, `state`, `country`, `phone_no`, `acc_status`) VALUES
('1', 'dashinesh', '$2y$10$UvZRrSnrQjNNw5/HTlKIHOaRKNaZ5GKAnpYPn7gxOw1NV7JTJ9XKW', 'dashinesh08@gmail.com', '', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang', 'Malaysia', '0134899379', '0');

-- --------------------------------------------------------

--
-- Table structure for table `booked_slots`
--

CREATE TABLE `booked_slots` (
  `slot_id` int(11) NOT NULL,
  `rider_id` varchar(255) NOT NULL,
  `slot_time` time NOT NULL,
  `slot_date` date NOT NULL,
  `booking_id` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booked_slots`
--

INSERT INTO `booked_slots` (`slot_id`, `rider_id`, `slot_time`, `slot_date`, `booking_id`, `postcode`) VALUES
(1, '1', '08:00:00', '2024-09-10', '11', '13700'),
(2, '1', '08:00:00', '2024-09-11', '12', '13700'),
(3, '1', '09:00:00', '2024-09-10', '13', '13700'),
(4, '1', '08:00:00', '2024-09-13', '14', '13700'),
(5, '1', '12:00:00', '2024-09-12', '15', '13700'),
(6, '2', '12:00:00', '2024-09-12', '16', '13800'),
(7, '1', '15:00:00', '2024-09-12', '17', '13700'),
(8, '1', '16:00:00', '2024-09-12', '18', '13700'),
(9, '1', '08:00:00', '2024-09-20', '19', '13700'),
(10, '1', '09:00:00', '2024-09-20', '20', '13700'),
(11, '1', '10:00:00', '2024-09-20', '21', '13700'),
(12, '1', '11:00:00', '2024-09-20', '22', '13700'),
(13, '1', '12:00:00', '2024-09-20', '23', '13700'),
(14, '1', '08:00:00', '2024-09-23', '24', '13700'),
(15, '1', '08:00:00', '2024-09-21', '25', '13700'),
(16, '1', '17:00:00', '2024-09-21', '26', '13700'),
(17, '1', '17:15:00', '2024-09-21', '27', '13700'),
(18, '1', '17:30:00', '2024-09-21', '28', '13700'),
(19, '1', '17:00:00', '2024-11-06', '29', '13700'),
(20, '1', '17:15:00', '2024-11-06', '30', '13700'),
(21, '1', '17:30:00', '2024-11-06', '31', '13700'),
(22, '1', '17:45:00', '2024-11-06', '32', '13700'),
(23, '1', '08:00:00', '2024-11-07', '33', '13700'),
(24, '1', '14:30:00', '2024-11-11', '34', '13700'),
(25, '1', '14:45:00', '2024-11-11', '35', '13700'),
(26, '1', '18:30:00', '2024-11-18', '36', '13700'),
(27, '1', '18:00:00', '2024-11-18', '37', '13700'),
(28, '1', '19:00:00', '2024-11-18', '38', '13700'),
(29, '1', '19:30:00', '2024-11-18', '39', '13700'),
(30, '1', '19:15:00', '2024-11-18', '40', '13700'),
(31, '1', '19:45:00', '2024-11-18', '41', '13700'),
(32, '2', '19:45:00', '0000-00-00', '41', ''),
(41, '1', '15:15:00', '0000-00-00', '44', ''),
(42, '1', '12:00:00', '2024-12-05', '45', '13700'),
(43, '1', '12:15:00', '2024-12-05', '46', '13700'),
(46, '1', '12:30:00', '0000-00-00', '47', ''),
(48, '2', '15:30:00', '0000-00-00', '48', ''),
(50, '2', '17:45:00', '0000-00-00', '49', ''),
(52, '1', '14:15:00', '0000-00-00', '50', ''),
(53, '2', '08:00:00', '2025-02-23', '51', '13800'),
(55, '2', '08:30:00', '2025-03-04', '53', '13800'),
(56, '2', '08:00:00', '2025-03-04', '54', '13800'),
(64, '2', '10:45:00', '2025-03-06', '56', '13800'),
(65, '2', '11:15:00', '2025-03-06', '57', '13800'),
(68, '1', '10:30:00', '0000-00-00', '58', ''),
(69, '2', '08:00:00', '2025-04-05', '55', '13800');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_type` varchar(255) NOT NULL,
  `booking_type` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `booking_time` datetime NOT NULL,
  `phone_no` varchar(255) NOT NULL,
  `payment_status` varchar(255) NOT NULL,
  `rider_id` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `address_line_1` varchar(255) NOT NULL,
  `address_line_2` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `user_id`, `service_type`, `booking_type`, `price`, `booking_time`, `phone_no`, `payment_status`, `rider_id`, `status`, `address_line_1`, `address_line_2`, `postcode`, `city`, `state`, `country`) VALUES
(11, 9, 'Basic Wash', 'Express', '70', '2024-09-10 08:00:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(12, 9, 'Basic Wash', 'Express', '70', '2024-09-11 08:00:00', '0134899379', 'paid', '1', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(13, 9, 'Basic Wash', 'Express', '70', '2024-09-10 09:00:00', '0134899379', 'paid', '1', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(14, 9, 'Basic Wash', 'Express', '70', '2024-09-13 08:00:00', '0134899379', 'paid', '1', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(15, 9, 'Basic Wash', 'Express', '70', '2024-09-12 12:00:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(16, 10, 'Premium Wash', 'Express', '100', '2024-09-12 12:00:00', '0134899379', 'paid', '2', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13800', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(17, 9, 'Basic Wash', 'Express', '70', '2024-09-12 15:00:00', '0134899379', 'paid', '1', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(18, 9, 'Basic Wash', 'Express', '70', '2024-09-12 16:00:00', '0134899379', 'paid', '1', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(19, 9, 'Premium Wash', 'Express', '100', '2024-09-20 08:00:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(20, 9, 'Basic Wash', 'Express', '70', '2024-09-20 09:00:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(21, 9, 'Premium Wash', 'Express', '100', '2024-09-20 10:00:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(22, 9, 'Premium Wash', 'Express', '100', '2024-09-20 11:00:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(23, 9, 'Basic Wash', 'Express', '70', '2024-09-20 12:00:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(24, 9, 'Basic Wash', 'Express', '70', '2024-09-23 08:00:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(25, 9, 'Basic Wash', 'Express', '70', '2024-09-21 08:00:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(26, 9, 'Premium Wash', 'Normal', '55', '2024-09-21 17:00:00', '0134899379', 'paid', '1', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(27, 9, 'Normal Wash', 'Express', '55', '2024-09-21 17:15:00', '0134899379', 'paid', '1', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(28, 9, 'Normal Wash', 'Express', '55', '2024-09-21 17:30:00', '0134899379', 'paid', '1', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(29, 9, 'Normal Wash', 'Express', '55', '2024-11-06 17:00:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(30, 9, 'Normal Wash', 'Express', '55', '2024-11-06 17:15:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(31, 9, 'Normal Wash', 'Express', '55', '2024-11-06 17:30:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(32, 9, 'Normal Wash', 'Express', '55', '2024-11-06 17:45:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(33, 9, 'Normal Wash', 'Express', '55', '2024-11-07 08:00:00', '0134899379', 'paid', '1', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(34, 9, 'Normal Wash', 'Express', '55', '2024-11-11 14:30:00', '0134899379', 'paid', '1', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(35, 9, 'Normal Wash', 'Express', '55', '2024-11-11 14:45:00', '0134899379', 'paid', '1', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(36, 9, 'Normal Wash', 'Express', '55', '2024-11-18 18:30:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(37, 9, 'Normal Wash', 'Express', '55', '2024-11-18 18:00:00', '0134899379', 'paid', '2', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(38, 9, 'Normal Wash', 'Express', '55', '2024-11-18 19:00:00', '0134899379', 'paid', '2', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(39, 9, 'Normal Wash', 'Express', '55', '2024-11-18 19:30:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(40, 9, 'Premium Wash', 'Express', '75', '2024-11-18 19:15:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(41, 9, 'Normal Wash', 'Express', '55', '2024-11-18 19:45:00', '0134899379', 'paid', '2', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(42, 9, 'Premium Car Wash', 'Express', '75', '2024-11-26 10:45:00', '0134899379', 'paid', '1', 'cancelled', 'No 6, Lorong Sutera Prima 4', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(43, 9, 'Basic Car Wash', 'Express', '55', '2024-11-26 12:00:00', '0134899379', 'paid', '1', 'cancelled', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(44, 9, 'Basic Car Wash', 'Express', '55', '2024-12-02 15:15:00', '0134899379', 'paid', '1', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(45, 9, 'Basic Car Wash', 'Normal', '35', '2024-12-05 12:00:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(46, 9, 'Basic Car Wash', 'Normal', '35', '2024-12-05 12:15:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(47, 9, 'Basic Car Wash', 'Normal', '35', '2024-12-05 12:30:00', '0134899379', 'paid', '1', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(48, 9, 'Basic Car Wash', 'Express', '55', '2024-12-05 15:30:00', '0134899379', 'paid', '2', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(49, 9, 'Basic Car Wash', 'Express', '55', '2024-12-05 17:45:00', '0134899379', 'paid', '2', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(50, 10, 'Basic Car Wash', 'Normal', '35', '2025-01-06 14:15:00', '0134899379', 'paid', '1', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13800', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(51, 10, 'Basic Car Wash', 'Express', '55', '2025-02-23 08:00:00', '0134899379', 'paid', '2', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13800', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(52, 10, 'Basic Car Wash', 'Express', '55', '2025-03-04 08:00:00', '0134899379', 'paid', '2', 'cancelled', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13800', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(54, 10, 'Basic Car Wash', 'Express', '55', '2025-03-04 11:00:00', '0134899379', 'paid', '2', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13800', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(55, 10, 'Basic Car Wash', 'Express', '55', '2025-04-05 08:00:00', '0134899379', 'paid', '2', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13800', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(56, 10, 'Basic Car Wash', 'Express', '55', '2025-03-06 10:45:00', '0134899379', 'paid', '2', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13800', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(57, 10, 'Basic Car Wash', 'Express', '55', '2025-03-06 11:15:00', '0134899379', 'paid', '2', 'done', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13800', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia'),
(58, 10, 'Basic Car Wash', 'Express', '55', '2025-03-06 10:30:00', '0134899379', 'paid', '1', 'pending', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13800', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia');

-- --------------------------------------------------------

--
-- Table structure for table `company_transactions`
--

CREATE TABLE `company_transactions` (
  `transaction_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `rider_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `previous_amount` decimal(10,2) DEFAULT NULL,
  `current_amount` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `transaction_time` datetime DEFAULT current_timestamp(),
  `service_type` varchar(50) DEFAULT NULL,
  `booking_type` varchar(50) DEFAULT NULL,
  `com_pre_wallet` decimal(10,2) DEFAULT NULL,
  `com_curr_wallet` decimal(10,2) DEFAULT NULL,
  `agent_amount` decimal(10,2) DEFAULT NULL,
  `company_amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_transactions`
--

INSERT INTO `company_transactions` (`transaction_id`, `booking_id`, `rider_id`, `user_id`, `amount`, `previous_amount`, `current_amount`, `payment_status`, `transaction_time`, `service_type`, `booking_type`, `com_pre_wallet`, `com_curr_wallet`, `agent_amount`, `company_amount`) VALUES
(1, 47, 1, 9, 35.00, 95.00, 102.00, '0', '2024-12-05 13:45:16', 'Basic Car Wash', 'Normal', 95.00, 102.00, 28.00, 7.00),
(2, 48, 2, 9, 55.00, 102.00, 113.00, '0', '2024-12-05 14:52:02', 'Basic Car Wash', 'Express', 102.00, 113.00, 44.00, 11.00);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL CHECK (`rating` between 1 and 5),
  `review_text` text DEFAULT NULL,
  `review_status` enum('pending','completed') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rider`
--

CREATE TABLE `rider` (
  `rider_id` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `wallet` varchar(255) NOT NULL,
  `address_line_1` varchar(255) NOT NULL,
  `address_line_2` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `vehicle_number` varchar(255) NOT NULL,
  `phone_no` varchar(255) NOT NULL,
  `current_location` varchar(255) NOT NULL,
  `available` varchar(255) NOT NULL,
  `serving_postcode` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rider`
--

INSERT INTO `rider` (`rider_id`, `username`, `password`, `email`, `wallet`, `address_line_1`, `address_line_2`, `postcode`, `city`, `state`, `country`, `vehicle_number`, `phone_no`, `current_location`, `available`, `serving_postcode`) VALUES
(1, 'dashinesh', '$2y$10$UvZRrSnrQjNNw5/HTlKIHOaRKNaZ5GKAnpYPn7gxOw1NV7JTJ9XKW', 'dashinesh08@gmail.com', '522', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang', 'Malaysia', 'PKD 8936', '0134899379', '', '1', '13700'),
(2, 'dashinesh2', '$2y$10$UvZRrSnrQjNNw5/HTlKIHOaRKNaZ5GKAnpYPn7gxOw1NV7JTJ9XKW', 'dashinesh08@gmail.com', '220', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13800', 'Seberang Jaya', 'Pulau Pinang', 'Malaysia', 'PKD 8936', '0134899379', '', '1', '13800');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `duration` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `normal_price` varchar(255) NOT NULL,
  `express_price` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `duration`, `description`, `normal_price`, `express_price`) VALUES
(1, 'Basic Car Wash', '35', 'Wash car normally', '1', '1'),
(2, 'Premium Car Wash', '35', 'Wash car premium', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `service_vehicle_pricing`
--

CREATE TABLE `service_vehicle_pricing` (
  `id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `vehicle_type` enum('Sedan','SUV','MPV','Van','Lorry') DEFAULT NULL,
  `normal_price` decimal(10,2) DEFAULT NULL,
  `express_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_vehicle_pricing`
--

INSERT INTO `service_vehicle_pricing` (`id`, `service_id`, `vehicle_type`, `normal_price`, `express_price`, `created_at`) VALUES
(1, 1, 'Sedan', 35.00, 45.00, '2024-11-23 10:10:17'),
(2, 2, 'Sedan', 55.00, 75.00, '2024-11-23 10:10:17');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `rider_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `previous_amount` varchar(255) NOT NULL,
  `current_amount` varchar(255) NOT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `transaction_time` datetime DEFAULT current_timestamp(),
  `service_type` varchar(50) DEFAULT NULL,
  `booking_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `booking_id`, `rider_id`, `user_id`, `amount`, `previous_amount`, `current_amount`, `payment_status`, `transaction_time`, `service_type`, `booking_type`) VALUES
(1, 19, 1, 9, 100.00, '', '', 'paid', '2024-09-19 23:46:17', 'Premium Wash', 'Express'),
(2, 20, 1, NULL, 70.00, '', '', NULL, '2024-09-19 23:51:26', NULL, NULL),
(3, 20, 1, NULL, 70.00, '', '', NULL, '2024-09-19 23:51:35', NULL, NULL),
(4, 20, 1, NULL, 70.00, '', '', NULL, '2024-09-19 23:52:01', NULL, NULL),
(5, 21, 1, NULL, 100.00, '', '', NULL, '2024-09-19 23:55:19', NULL, NULL),
(6, 22, 1, 9, 100.00, '', '', 'paid', '2024-09-20 00:00:00', 'Premium Wash', 'Express'),
(7, 23, 1, 9, 70.00, '510', '580', 'paid', '2024-09-20 00:04:28', 'Basic Wash', 'Express'),
(8, 24, 1, 9, 70.00, '0', '70', 'paid', '2024-09-20 18:40:20', 'Basic Wash', 'Express'),
(9, 25, 1, 9, 70.00, '0', '70', 'paid', '2024-09-20 19:14:11', 'Basic Wash', 'Express'),
(10, 29, 1, 9, 55.00, '70', '125', 'paid', '2024-11-06 16:22:46', 'Normal Wash', 'Express'),
(11, 30, 1, 9, 55.00, '125', '180', 'paid', '2024-11-06 16:31:55', 'Normal Wash', 'Express'),
(12, 32, 1, 9, 55.00, '180', '235', 'paid', '2024-11-06 16:44:07', 'Normal Wash', 'Express'),
(13, 31, 1, 9, 55.00, '235', '290', 'paid', '2024-11-06 16:46:02', 'Normal Wash', 'Express'),
(14, 1, 1, 1, 100.00, '200.00', '300.00', 'paid', '2024-12-05 12:11:42', 'car wash', 'normal'),
(15, 46, 1, 9, 35.00, '466', '494', 'paid', '2024-12-05 12:14:22', 'Basic Car Wash', 'Normal'),
(16, 47, 1, 9, 28.00, '494', '522', 'paid', '2024-12-05 13:45:16', 'Basic Car Wash', 'Normal'),
(17, 48, 2, 9, 44.00, '176', '220', 'paid', '2024-12-05 14:52:02', 'Basic Car Wash', 'Express');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address_line_1` varchar(255) NOT NULL,
  `address_line_2` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `phone_no` varchar(255) NOT NULL,
  `acc_status` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `address_line_1`, `address_line_2`, `postcode`, `city`, `state`, `country`, `phone_no`, `acc_status`, `full_name`, `created_at`) VALUES
(9, 'dashinesh', '$2y$10$ic8B4rbGc92Yxvusx0Lym.PITHeSOXECJpuKIJ7qYOw5CMkjrqxju', 'dashineshwar@eighty8.com.my', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13700', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia', '0134899379', '0', '', '2024-12-06 09:46:15'),
(10, 'dashinesh2', '$2y$10$UvZRrSnrQjNNw5/HTlKIHOaRKNaZ5GKAnpYPn7gxOw1NV7JTJ9XKW', 'dashinesh08@gmail.com', 'No 6, Lorong Sutera Prima 2', 'Taman Sutera Prima ', '13800', 'Seberang Jaya', 'Pulau Pinang ', 'Malaysia', '0134899379', '0', '', '2024-12-06 09:46:15'),
(12, 'dashineshwar', '', 'eighty431@gmail.com', 'No 6, Lorong Sutera Prima 2,', ' Taman Sutera Prima', '13700', 'Seberang Jaya', 'Penang', 'Malaysia', '013348937', '', 'Eighty8', '2025-04-03 10:18:23');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle`
--

CREATE TABLE `vehicle` (
  `id` bigint(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `number_plate` varchar(255) NOT NULL,
  `brand` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `registered_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle`
--

INSERT INTO `vehicle` (`id`, `username`, `type`, `number_plate`, `brand`, `model`, `registered_date`) VALUES
(12, 'dashinesh2', 'Sedan', 'DJ88', 'BMW', 'E55', '2025-04-03 10:52:07');

-- --------------------------------------------------------

--
-- Table structure for table `wallet`
--

CREATE TABLE `wallet` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `wallet` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallet`
--

INSERT INTO `wallet` (`id`, `name`, `email`, `wallet`) VALUES
('1', 'company', 'dashineshwar@eighty8.com.my', '113');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booked_slots`
--
ALTER TABLE `booked_slots`
  ADD PRIMARY KEY (`slot_id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`);

--
-- Indexes for table `company_transactions`
--
ALTER TABLE `company_transactions`
  ADD PRIMARY KEY (`transaction_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`);

--
-- Indexes for table `rider`
--
ALTER TABLE `rider`
  ADD PRIMARY KEY (`rider_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_vehicle_pricing`
--
ALTER TABLE `service_vehicle_pricing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_service_vehicle` (`service_id`,`vehicle_type`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booked_slots`
--
ALTER TABLE `booked_slots`
  MODIFY `slot_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `company_transactions`
--
ALTER TABLE `company_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rider`
--
ALTER TABLE `rider`
  MODIFY `rider_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `service_vehicle_pricing`
--
ALTER TABLE `service_vehicle_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `vehicle`
--
ALTER TABLE `vehicle`
  MODIFY `id` bigint(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
