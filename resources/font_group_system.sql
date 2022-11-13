-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 13, 2022 at 05:44 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `font_group_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `font`
--

CREATE TABLE `font` (
  `id` int(11) NOT NULL,
  `font_name` varchar(256) NOT NULL,
  `file_size` varchar(256) NOT NULL,
  `file_path` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `font`
--

INSERT INTO `font` (`id`, `font_name`, `file_size`, `file_path`) VALUES
(18, 'Cabin-Regular.ttf', '163.068KB', 'src\\Assets\\Cabin-Regular.ttf'),
(19, 'Cabin-Bold.ttf', '151.256KB', 'src\\Assets\\Cabin-Bold.ttf'),
(20, 'Cabin-Italic.ttf', '167.096KB', 'src\\Assets\\Cabin-Italic.ttf');

-- --------------------------------------------------------

--
-- Table structure for table `font_for_font_group`
--

CREATE TABLE `font_for_font_group` (
  `id` int(11) NOT NULL,
  `font_name` varchar(256) NOT NULL,
  `font_group_id` int(11) NOT NULL,
  `font_id` int(11) NOT NULL,
  `specific_size` double NOT NULL,
  `price_change` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `font_for_font_group`
--

INSERT INTO `font_for_font_group` (`id`, `font_name`, `font_group_id`, `font_id`, `specific_size`, `price_change`) VALUES
(38, 'Cabin-Regular.ttf', 16, 18, 12.5, 10),
(39, 'Cabin-Bold.ttf', 16, 19, 16.5, 20),
(40, 'Cabin-Regular.ttf', 17, 18, 17, 20),
(41, 'Cabin-Italic.ttf', 17, 20, 12.5, 15);

-- --------------------------------------------------------

--
-- Table structure for table `font_group`
--

CREATE TABLE `font_group` (
  `id` int(11) NOT NULL,
  `font_group_name` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `font_group`
--

INSERT INTO `font_group` (`id`, `font_group_name`) VALUES
(16, 'my-font-group'),
(17, 'my-font-group');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `font`
--
ALTER TABLE `font`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `font_for_font_group`
--
ALTER TABLE `font_for_font_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `font_group_id` (`font_group_id`,`font_id`),
  ADD KEY `font_id` (`font_id`);

--
-- Indexes for table `font_group`
--
ALTER TABLE `font_group`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `font`
--
ALTER TABLE `font`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `font_for_font_group`
--
ALTER TABLE `font_for_font_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `font_group`
--
ALTER TABLE `font_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `font_for_font_group`
--
ALTER TABLE `font_for_font_group`
  ADD CONSTRAINT `font_for_font_group_ibfk_1` FOREIGN KEY (`font_id`) REFERENCES `font` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `font_for_font_group_ibfk_2` FOREIGN KEY (`font_group_id`) REFERENCES `font_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
