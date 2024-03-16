-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2024 at 09:34 AM
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
-- Database: `cloud`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `session_id` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banned`
--

CREATE TABLE `banned` (
  `id` varchar(255) NOT NULL,
  `duedate` datetime NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` varchar(50) NOT NULL,
  `header` varchar(50) DEFAULT NULL COMMENT 'folder id or userid',
  `name` varchar(100) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `size` bigint(255) DEFAULT 0,
  `uploaded_by` varchar(20) DEFAULT NULL,
  `uploaded_on` datetime DEFAULT current_timestamp(),
  `deleted_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Table structure for table `format`
--

CREATE TABLE `format` (
  `format` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `format`
--

INSERT INTO `format` (`format`) VALUES
('ai'),
('dll'),
('exe'),
('gz'),
('jpeg'),
('jpg'),
('js'),
('pdf'),
('php'),
('png'),
('rar'),
('svg'),
('tif'),
('tiff'),
('zip');

-- --------------------------------------------------------

--
-- Table structure for table `group_folder`
--

CREATE TABLE `group_folder` (
  `id` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `usage` bigint(255) NOT NULL DEFAULT 0,
  `limit` bigint(255) NOT NULL DEFAULT 0,
  `created_by` varchar(20) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- --------------------------------------------------------

--
-- Table structure for table `shared`
--

CREATE TABLE `shared` (
  `rowid` bigint(255) NOT NULL,
  `folder` varchar(255) NOT NULL,
  `userid` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userid` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `otp` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `usage` bigint(255) DEFAULT 0,
  `limit` bigint(255) DEFAULT 0,
  `group_folder` bigint(255) DEFAULT 0,
  `last_log` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `password`, `otp`, `name`, `phone`, `email`, `picture`, `usage`, `limit`, `group_folder`, `last_log`) VALUES
('hakimi', '9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684', 'dbdee39fdeb71e9b53bb5231b0943748c8c38442', 'HAKIMI', '0189420292', NULL, NULL, 4006123, 5000000000, 4994757120, '2024-03-16 16:34:03'),
('hakimi_guest', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'a0c7dbb4d41a8877132a943c10bdd0b588cfec32', 'hakimi guest', '0189420292', 'hakimi@peritone-health.com', NULL, 0, 0, 0, '2024-03-14 11:00:40');

-- --------------------------------------------------------

--
-- Table structure for table `user_group_folder`
--

CREATE TABLE `user_group_folder` (
  `rowid` bigint(20) NOT NULL,
  `id` varchar(255) NOT NULL,
  `userid` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_group_folder`
--

INSERT INTO `user_group_folder` (`rowid`, `id`, `userid`) VALUES
(9, '1936b08c7d25ef2cf3cf41ce83d4d416843115b5', 'hakimi_guest');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `format`
--
ALTER TABLE `format`
  ADD PRIMARY KEY (`format`);

--
-- Indexes for table `group_folder`
--
ALTER TABLE `group_folder`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shared`
--
ALTER TABLE `shared`
  ADD PRIMARY KEY (`rowid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `user_group_folder`
--
ALTER TABLE `user_group_folder`
  ADD PRIMARY KEY (`rowid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shared`
--
ALTER TABLE `shared`
  MODIFY `rowid` bigint(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_group_folder`
--
ALTER TABLE `user_group_folder`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
