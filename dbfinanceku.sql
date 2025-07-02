-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2025 at 04:27 PM
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
-- Database: `dbfinanceku`
--

-- --------------------------------------------------------

--
-- Table structure for table `balances`
--

CREATE TABLE `balances` (
  `userId` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `ending_balance` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL CHECK (`month` between 1 and 12),
  `amount` decimal(15,2) DEFAULT NULL,
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ending_balance` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id`, `userId`, `year`, `month`, `amount`, `last_updated`, `ending_balance`) VALUES
(1, 5, 2024, 6, 250000.00, '2025-04-06 09:04:08', 0.00),
(2, 5, 2024, 6, 250000.00, '2025-04-06 09:04:08', 0.00),
(3, 5, 2024, 6, 200000.00, '2025-04-06 09:04:08', 0.00),
(4, 5, 2024, 6, 200000.00, '2025-04-06 09:04:08', 0.00),
(5, 4, 2024, 6, 100000.00, '2025-04-06 09:04:08', 0.00),
(6, 6, 2024, 6, 50000.00, '2025-04-06 09:04:08', 0.00),
(7, 7, 2024, 6, 1000000.00, '2025-04-06 09:04:08', 0.00),
(8, 8, 2024, 6, 1000000.00, '2025-04-06 09:04:08', 0.00),
(9, 9, 2025, 4, 1000000.00, '2025-04-06 09:33:02', 3500000.00),
(10, 4, 2025, 4, 750000.00, '2025-04-06 13:44:28', 850000.00),
(11, 5, 2025, 4, 0.00, '2025-04-06 09:34:04', 150000.00);

-- --------------------------------------------------------

--
-- Table structure for table `expense`
--

CREATE TABLE `expense` (
  `id` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `expenseCategory` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense`
--

INSERT INTO `expense` (`id`, `userId`, `date`, `amount`, `expenseCategory`, `description`) VALUES
(1, 5, '2024-06-05', 50000.00, 'Makanan', 'Membeli Jajan cilok'),
(2, 4, '2024-06-06', 75000.00, 'Kesehatan', 'Beli Toner, Serum, Sunscreen'),
(3, 5, '2024-06-10', 50000.00, 'Lainnya', 'Beli kuota 20 gb'),
(5, 4, '2024-06-18', 300000.00, 'Pendididikan', 'Beli Temper Glass Depan Belakang'),
(6, 7, '2024-06-19', 1200000.00, 'Makanan', 'Pestanya wisnu\r\n'),
(7, 7, '2024-06-19', 10000.00, 'Transportasi', 'Have fun'),
(8, 8, '2024-06-19', 20000.00, 'Makanan', 'Ayam goreng makan siang di rara'),
(9, 8, '2024-06-19', 180000.00, 'Hiburan', 'Headset monster XKT08'),
(11, 8, '2024-06-19', 20000.00, 'Transportasi', 'Ke kampus'),
(12, 9, '2025-04-06', 150000.00, 'Pendididikan', 'Beli Kuota sebanyak 5GB'),
(13, 4, '2025-04-06', 50000.00, 'Makanan', 'Beli makan sup daging dan capjay');

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `id` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `incomeSource` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `income`
--

INSERT INTO `income` (`id`, `userId`, `date`, `amount`, `incomeSource`) VALUES
(4, 4, '2024-06-04', 100000.00, 'Bisnis'),
(5, 4, '2024-06-04', 200000.00, 'Bisnis'),
(6, 5, '2024-06-05', 350000.00, 'Gaji'),
(7, 5, '2024-06-05', 20000.00, 'Bisnis'),
(9, 6, '2024-06-17', 100000.00, 'Gaji'),
(11, 4, '2024-06-19', 100000.00, 'Gaji'),
(12, 7, '2024-06-19', 500000.00, 'Gaji'),
(13, 7, '2024-06-19', 120000.00, 'Gaji'),
(14, 7, '2024-06-19', 100000.00, 'Gaji'),
(15, 8, '2024-06-19', 50000.00, 'Hadiah orangtua'),
(16, 8, '2024-06-19', 100000.00, 'THR'),
(17, 9, '2025-04-06', 2500000.00, 'Gaji'),
(18, 9, '2025-04-06', 150000.00, 'bisnis'),
(19, 5, '2025-04-06', 150000.00, 'Gaji'),
(20, 4, '2025-04-06', 150000.00, 'Uang Saku');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `fullName` varchar(255) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `fullName`, `nickname`) VALUES
(2, 'adeva17', '$2y$10$dD9BVFi7Uy7sGMNWFYeKD.9hvYdKFUILS4lPiuQVH8nTr0Ns3nNc2', 'mhmmdadeva@gmail.com', 'Muhammad Adeva', 'Adeva'),
(4, 'wismrt', '$2y$10$qlUOxuRJPPIsfzBWyimzlelUkSufpVwUg7DRI0/DiYaVZi9W4GNXe', 'wisnumurti982@gmail.com', 'Hapsoro Wisnu', 'Wisnu'),
(5, 'wahyu', '$2y$10$TC7n950FxMFLiIO4Gxa1RuLs4FQO0Li/PIAaEXb5r9J46Lfz8FFFe', 'wahyu@gmail.com', 'Wahyu Sampurno', 'Wahyu'),
(6, 'afif22', '$2y$10$2nCsSrQFivQxFw514gEdbOwaMqXrM1BglBi3Y2ZDqcfh9yD1wlBrO', 'apip@gmail.com', 'Afifpul', 'Apip'),
(7, 'jainab', '$2y$10$rZ/thgkwc2atlRZZS6oErOqIKe14.3PaddGNPzgg3Elesf/kMUF/C', 'jainab@gmail.com', 'Jainab Murodatul', 'Jainab'),
(8, 'laili', '$2y$10$P9aZDNH8/JJlJ3ZNSWx5BeywcdYv/HiEdM9uNAjj9.DtHmlE8y6Oi', 'laili@gmail.com', 'Laili Qudriyatul', 'Laili'),
(9, 'Wilda', '$2y$10$5N4/gCasanNSfw/v3YrcheCEXMuEiGz1jSHKgGQoAY.BuFKHv4Yhi', 'wilda@gmail.com', 'Ama Maulidatul', 'Wilda');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `balances`
--
ALTER TABLE `balances`
  ADD PRIMARY KEY (`userId`,`year`,`month`);

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `expense`
--
ALTER TABLE `expense`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

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
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `expense`
--
ALTER TABLE `expense`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);

--
-- Constraints for table `expense`
--
ALTER TABLE `expense`
  ADD CONSTRAINT `expense_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);

--
-- Constraints for table `income`
--
ALTER TABLE `income`
  ADD CONSTRAINT `income_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
