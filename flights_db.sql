-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 03, 2025 at 03:20 AM
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
-- Database: `flights_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `flights`
--

CREATE TABLE `flights` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `origin` varchar(10) DEFAULT NULL,
  `destination` varchar(10) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `passengers_count` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flights`
--

INSERT INTO `flights` (`id`, `user_id`, `origin`, `destination`, `date`, `passengers_count`) VALUES
(1, 1, 'MAA', 'DXB', '2025-03-15', 3),
(2, 2, 'MAA', 'DXB', '2025-03-15', 1),
(3, 1, 'MAA', 'DXB', '2025-03-15', 1),
(4, 4, 'MAA', 'DXY', '2025-03-12', 1),
(5, 2, 'MAA', 'DXY', '2025-03-21', 2),
(6, 4, 'DXY', 'MAA', '2025-03-28', 1),
(7, 4, 'GAA', 'EEE', '2025-03-19', 1),
(8, 1, 'YYY', 'RRR', '2025-04-17', 1),
(9, 6, 'QQQ', 'VGC', '2025-06-26', 1),
(10, 6, 'TTT', 'HDE', '2025-03-16', 1),
(11, 2, 'GBY', 'MAA', '2025-03-26', 1),
(12, 2, 'MAA', 'DXY', '2025-03-28', 3),
(13, 5, 'IXY', 'EDS', '2025-08-28', 1);

-- --------------------------------------------------------

--
-- Table structure for table `passengers`
--

CREATE TABLE `passengers` (
  `id` int(11) NOT NULL,
  `flight_id` int(11) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `passengers`
--

INSERT INTO `passengers` (`id`, `flight_id`, `first_name`, `last_name`, `age`, `gender`) VALUES
(1, 1, 'thanga', 'lakshmi', 46, 'female'),
(2, 1, 'bhargavi', 'uthirakumar', 24, 'female'),
(3, 1, 'saravana', 'guhan', 27, 'male'),
(4, 2, 'saravana', 'guhan', 27, 'male'),
(5, 3, 'saravana', 'guhan', 27, 'male'),
(6, 4, 'saravana ', 'guhan', 27, 'male'),
(7, 5, 'uthra', 'kumar', 60, 'male'),
(8, 5, 'thanga', 'lakshmi', 48, 'female'),
(9, 6, 'bhargavi', 'uthirakumar', 25, 'female'),
(10, 7, 'saravana', 'guhan', 27, 'male'),
(11, 8, 'thanga', 'lakshmi', 46, 'female'),
(12, 9, 'vignesh', 'kumar', 27, 'male'),
(13, 10, 'tarun', 'chander', 28, 'male'),
(14, 11, 'siva', 'shankar', 28, 'male'),
(15, 12, 'thanga', 'lakshmi', 48, 'female'),
(16, 12, 'uthra', 'kumar', 63, 'male'),
(17, 12, 'bhargavi', 'uthirakumar', 25, 'female'),
(18, 13, 'saravana', 'guhan', 27, 'male');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`) VALUES
(1, 'bhargavi', 'bhargavi@gmail.com', '$2y$10$TlDgA7ygeGbMvugIdIerrOHrH8WvHqaGxCfbFhharsrU3ZwG8kS4C'),
(2, 'saravana', 'saravana@gmail.com', '$2y$10$Py0F4e0KaCZGabNtb52t.ujQLKDA6xm0xh6qctgfLlt1IabXRdfeC'),
(4, 'thangam', 'thangam@gmail.com', '$2y$10$Q4T5GWDD.J/IgAwx.hvai.qAP06TMU.p4JAA7yFjWIicGAGntjkQm'),
(5, 'uthram', 'uthram@gmail.com', '$2y$10$ky6EVIhDY54cBwI3JyVOw.zNbpQL/.L2IBMZD8kqA6N58zo6i4lpW'),
(6, 'sandy', 'sandy@gmail.com', '$2y$10$dyQ1LREUgA36M1tHM8XssO6OC1jqjufOyy.UNq9dlLpz62fdmjaRm');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `flights`
--
ALTER TABLE `flights`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `passengers`
--
ALTER TABLE `passengers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `flight_id` (`flight_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `flights`
--
ALTER TABLE `flights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `passengers`
--
ALTER TABLE `passengers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `flights`
--
ALTER TABLE `flights`
  ADD CONSTRAINT `flights_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `passengers`
--
ALTER TABLE `passengers`
  ADD CONSTRAINT `passengers_ibfk_1` FOREIGN KEY (`flight_id`) REFERENCES `flights` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
