-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Jun 13, 2025 at 10:02 AM
-- Server version: 8.0.42
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stellarleague`
--

-- --------------------------------------------------------

--
-- Table structure for table `classement`
--

CREATE TABLE `classement` (
  `id` int NOT NULL,
  `tournament_id` int NOT NULL,
  `user_id_continue` int DEFAULT NULL,
  `user_id_stop` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `classement`
--

INSERT INTO `classement` (`id`, `tournament_id`, `user_id_continue`, `user_id_stop`) VALUES
(21, 2, NULL, 2),
(22, 2, 0, 3),
(23, 2, NULL, 2),
(24, 2, NULL, 1),
(25, 2, NULL, 3),
(26, 2, NULL, 2),
(27, 2, NULL, 2),
(28, 2, 0, 2),
(29, 2, 0, 3),
(30, 1, NULL, 3),
(31, 1, NULL, 2),
(32, 1, 0, 2),
(33, 1, 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE `game` (
  `id` int NOT NULL,
  `tournament_id` int NOT NULL,
  `user_1_id` int NOT NULL,
  `user_2_id` int NOT NULL,
  `round_number` int NOT NULL,
  `winner_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `game`
--

INSERT INTO `game` (`id`, `tournament_id`, `user_1_id`, `user_2_id`, `round_number`, `winner_id`) VALUES
(1, 2, 3, 2, 1, 3),
(2, 2, 3, 2, 1, 3),
(3, 2, 2, 3, 1, 2),
(4, 1, 3, 2, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `pending_list`
--

CREATE TABLE `pending_list` (
  `id` int NOT NULL,
  `tournament_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pending_list`
--

INSERT INTO `pending_list` (`id`, `tournament_id`) VALUES
(1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tournament`
--

CREATE TABLE `tournament` (
  `id` int NOT NULL,
  `nameTournament` varchar(255) NOT NULL,
  `startAt` int DEFAULT NULL,
  `endAt` int DEFAULT NULL,
  `userLimit` int NOT NULL,
  `prize` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tournament`
--

INSERT INTO `tournament` (`id`, `nameTournament`, `startAt`, `endAt`, `userLimit`, `prize`) VALUES
(1, 'lol', 1750291200, NULL, 16, 'lol'),
(2, 'popop', 1750338000, NULL, 16, 'pop');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `pseudo` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `userRole` varchar(255) NOT NULL,
  `imgAvatar` varchar(255) NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tokenValidate` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `pseudo`, `email`, `pass`, `userRole`, `imgAvatar`, `token`, `tokenValidate`) VALUES
(1, 'courgette', 'courgette@pioupiou.net', '$argon2i$v=19$m=65536,t=4,p=1$WkVPcW1rOGc1b1dFOVYubQ$8GVywuwXFbvftHvWt23NxpMzlyrsVAq0el1qrsf/6Tg', 'admin', 'default_avatar.jpg', NULL, NULL),
(2, 'p', 'p@p.p', '$argon2i$v=19$m=65536,t=4,p=1$SDRjVVcvLkRyeThiQ3pLZA$NeHNOH+gBMsR2GXWoY1bR8JWUvIcvdCoK3UTg3aSOfA', 'user', 'default_avatar.jpg', NULL, NULL),
(3, 'o', 'o@o.o', '$argon2i$v=19$m=65536,t=4,p=1$UDRzTVlZRkouTm5CYWk1QQ$0JhdCiwdgyVQwoOnU5sOx3JVF//Qz6zekfgMjxPzhqo', 'user', 'default_avatar.jpg', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `usersPending_list`
--

CREATE TABLE `usersPending_list` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `pending_list_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usersTournament`
--

CREATE TABLE `usersTournament` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `tournament_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `usersTournament`
--

INSERT INTO `usersTournament` (`id`, `user_id`, `tournament_id`) VALUES
(21, 2, 2),
(22, 3, 2),
(23, 2, 2),
(24, 1, 2),
(25, 1, 2),
(26, 3, 2),
(27, 2, 2),
(28, 2, 2),
(29, 2, 2),
(30, 3, 2),
(31, 3, 1),
(32, 2, 1),
(33, 2, 1),
(34, 3, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classement`
--
ALTER TABLE `classement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pending_list`
--
ALTER TABLE `pending_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tournament`
--
ALTER TABLE `tournament`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usersPending_list`
--
ALTER TABLE `usersPending_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usersTournament`
--
ALTER TABLE `usersTournament`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classement`
--
ALTER TABLE `classement`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `game`
--
ALTER TABLE `game`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pending_list`
--
ALTER TABLE `pending_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tournament`
--
ALTER TABLE `tournament`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `usersPending_list`
--
ALTER TABLE `usersPending_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `usersTournament`
--
ALTER TABLE `usersTournament`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
