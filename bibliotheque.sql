-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2021 at 02:55 AM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bibliotheque`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `overview` text CHARACTER SET utf8 NOT NULL,
  `author` varchar(255) CHARACTER SET utf8 NOT NULL,
  `isbn` int(11) NOT NULL,
  `publisher` varchar(255) CHARACTER SET utf8 NOT NULL,
  `pages` int(11) NOT NULL,
  `publication_date` datetime NOT NULL,
  `thumbnail` varchar(255) CHARACTER SET utf8 NOT NULL,
  `loan_duration` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `overview`, `author`, `isbn`, `publisher`, `pages`, `publication_date`, `thumbnail`, `loan_duration`, `updated_at`, `created_at`) VALUES
(1, 'Le Fils du pauvre', 'Une enfance et une adolescence dans une famille kabyle, pendant l&#39;entre-deux-guerres. C&#39;est, à peine transposée, la jeunesse même de Mouloud Feraoun que nous découvrons. Ce témoignage plein de vérité et d&#39;une émotion qui se teinte volontiers d&#39;humour est d&#39;un admirable conteur, qu&#39;on a pu comparer à Jack London et à Maxime Gorki.', 'Mouloud Feraoun', 2020261995, 'Points (23 novembre 1995)', 146, '1995-11-23 00:00:00', 'data/books/Le Fils du pauvre.jpg', 20, '2021-04-25 20:40:48', '2021-04-25 20:40:48'),
(2, 'La terre et le sang', 'L&#39;histoire se situe dans un petit village de Kabylie au tout début du XXème siècle. Amer, enfant du village, s&#39;exile en France pendant quinze ans. Loin de son pays natal, accueilli par une petite communauté d&#39;hommes originaires du même village que lui, il découvre le monde des mines de charbon.', 'Mouloud Feraoun', 2147483647, 'Points (14 juin 2010)', 250, '2010-06-14 00:00:00', 'data/books/La terre et le sang.jpg', 2, '2021-04-25 20:55:41', '2021-04-25 20:55:41');

-- --------------------------------------------------------

--
-- Table structure for table `borrow`
--

CREATE TABLE `borrow` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `borrowed_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(20) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `is_admin`, `is_active`, `updated_at`, `created_at`) VALUES
(7, 'admin', 'admin@admin.com', '6e6fba1ecf298599dc4f9701373d28472822bb27', 0, 0, '2021-04-24 17:55:11', '2021-04-24 17:55:11'),
(17, 'ziniw', 'ziniw@ouliw.fr', '7c4a8d09ca3762af61e59520943dc26494f8941b', 1, 0, '2021-04-29 02:46:53', '2021-04-29 02:46:53'),
(20, 'Quentin Branch', 'xypemon@mailinator.com', 'ac748cb38ff28d1ea98458b16695739d7e90f22d', 0, 0, '2021-04-29 05:06:59', '2021-04-29 05:06:59'),
(21, 'Max Grealix', 'ahmed@ahmed.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 1, 1, '2021-04-30 01:51:04', '2021-04-30 01:51:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`),
  ADD UNIQUE KEY `isbn` (`isbn`);

--
-- Indexes for table `borrow`
--
ALTER TABLE `borrow`
  ADD PRIMARY KEY (`id`),
  ADD KEY `borrow_1` (`book_id`),
  ADD KEY `borrow_2` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `borrow`
--
ALTER TABLE `borrow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrow`
--
ALTER TABLE `borrow`
  ADD CONSTRAINT `borrow_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `borrow_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
