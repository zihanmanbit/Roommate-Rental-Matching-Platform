-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2025 at 08:53 PM
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
-- Database: `roommate_rental_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `preferences`
--

CREATE TABLE `preferences` (
  `preference_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `budget_min` decimal(10,2) DEFAULT NULL,
  `budget_max` decimal(10,2) DEFAULT NULL,
  `pets_allowed` tinyint(1) DEFAULT NULL,
  `smoking` tinyint(1) DEFAULT NULL,
  `cleanliness_level` tinyint(4) DEFAULT NULL,
  `sleep_schedule` enum('Early Bird','Night Owl','Flexible') DEFAULT NULL,
  `preferred_location` varchar(100) DEFAULT NULL,
  `gender_preference` enum('Male','Female','Any') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `preferences`
--

INSERT INTO `preferences` (`preference_id`, `user_id`, `budget_min`, `budget_max`, `pets_allowed`, `smoking`, `cleanliness_level`, `sleep_schedule`, `preferred_location`, `gender_preference`) VALUES
(1, 1, 6000.00, 10000.00, 0, 0, 4, 'Night Owl', 'Dhanmondi', 'Female'),
(2, 3, 8000.00, 12000.00, 1, 0, 4, 'Flexible', 'Mirpur', 'Male'),
(3, 5, 9000.00, 14000.00, 1, 1, 3, 'Night Owl', 'Mohammadpur', 'Any'),
(4, 6, 10000.00, 16000.00, 1, 0, 5, 'Early Bird', 'Gulshan', 'Female'),
(5, 10, 12000.00, 17000.00, 1, 0, 0, 'Flexible', 'Banani', 'Male');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `property_id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `rent` decimal(10,2) DEFAULT NULL,
  `available_from` date DEFAULT NULL,
  `gender_preference` enum('Male','Female','Any') DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `status` enum('Available','Occupied') DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `owner_id`, `title`, `description`, `address`, `location`, `rent`, `available_from`, `gender_preference`, `amenities`, `status`, `created_at`) VALUES
(1, 2, 'Cozy 2BHK in Dhanmondi', 'Spacious 2-bedroom apartment with kitchen and balcony.', 'House #7, Dhanmondi, Dhaka', 'Dhanmondi', 8500.00, '2025-07-20', 'Female', 'WiFi, Washing Machine, Kitchen', 'Available', '2025-08-02 17:27:38'),
(2, 4, '3BHK House in Bashundhara', 'Well-maintained 3-bedroom house with all modern amenities', 'House #21, Block D, Bashundhara, Dhaka.', 'Bashundhara', 16000.00, '2025-09-01', 'Any', 'WiFi, Generator, Kitchen', 'Available', '2025-08-02 17:41:43'),
(3, 7, 'Family Apartment for Rent', '3-bedroom flat suitable for small family or group of friends.', 'House 22, Block C, Mirpur-10', 'Mirpur', 18000.00, '2025-09-15', 'Any', 'Parking, Lift, 24/7 Security', 'Available', '2025-08-07 23:24:36'),
(4, 8, 'Fully Furnished Flat in Uttara Sector 10', 'Newly built 2-bedroom flat with wide balconies and children’s play area.', 'House 7, Road 14, Sector 10, Uttara', 'Uttara', 14000.00, '2025-09-01', 'Male', 'Lift, Generator Backup, CCTV', 'Available', '2025-08-07 23:33:06'),
(5, 9, 'Medium-Sized Flat for Family in Banasree', '2-bedroom flat in a quiet residential area, best suited for families.', 'Block G, Road 9, Banasree', 'Banasree', 15000.00, '2025-08-15', 'Any', 'Attached Bathroom, Balcony, Security', 'Available', '2025-08-08 12:36:39');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL,
  `reviewer_id` int(11) DEFAULT NULL,
  `reviewed_user_id` int(11) DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `rated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`rating_id`, `reviewer_id`, `reviewed_user_id`, `rating`, `comment`, `rated_at`) VALUES
(3, 3, 4, 3, 'Average', '2025-08-04 23:25:02'),
(4, 5, 4, 4, 'Good', '2025-08-05 02:43:10'),
(5, 10, 8, 3, '', '2025-08-08 14:41:34'),
(6, 6, 7, 1, 'Rent amount too much', '2025-08-08 19:14:43');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `request_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('Pending','Accepted','Rejected') DEFAULT 'Pending',
  `reply` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`request_id`, `sender_id`, `receiver_id`, `property_id`, `message`, `status`, `reply`, `sent_at`) VALUES
(1, 3, 4, 2, 'Is it available?', '', 'No', '2025-08-02 18:07:32'),
(3, 5, 4, 2, 'Negotiable?', '', 'NO', '2025-08-05 02:41:06'),
(4, 12, 7, 3, NULL, 'Rejected', NULL, '2025-08-08 19:11:48'),
(5, 6, 7, 3, NULL, 'Accepted', NULL, '2025-08-08 19:13:59'),
(6, 11, 8, 4, NULL, 'Pending', NULL, '2025-08-08 21:53:50'),
(7, 10, 9, 5, 'Bachelors allowed?', '', 'Yes', '2025-08-08 23:01:02');

-- --------------------------------------------------------

--
-- Table structure for table `roommate_requests`
--

CREATE TABLE `roommate_requests` (
  `request_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `status` enum('Pending','Accepted','Declined') DEFAULT 'Pending',
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roommate_requests`
--

INSERT INTO `roommate_requests` (`request_id`, `sender_id`, `receiver_id`, `status`, `sent_at`) VALUES
(3, 3, 1, 'Pending', '2025-08-05 02:04:57'),
(4, 5, 3, 'Accepted', '2025-08-05 02:13:39'),
(5, 5, 1, 'Pending', '2025-08-05 02:33:17'),
(6, 10, 5, 'Declined', '2025-08-08 12:43:54'),
(7, 10, 3, 'Pending', '2025-08-08 12:44:09'),
(8, 5, 6, 'Pending', '2025-08-08 13:30:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `role` enum('tenant','owner') DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `age`, `gender`, `role`, `contact`, `created_at`) VALUES
(1, 'Alice Tenant', 'alice@example.com', 'password123', 22, 'Female', 'tenant', '01711111111', '2025-08-02 17:27:38'),
(2, 'Bob Owner', 'bob@example.com', 'password123', 30, 'Male', 'owner', '01822222222', '2025-08-02 17:27:38'),
(3, 'Sakib Abrar', 'sa@gmail.com', '$2y$10$1VD1QZV224GrbMEFH6yCOen6wCRhsw0PRd/LJybd9wft7k7KIstku', 23, 'Male', 'tenant', '01712345678', '2025-08-02 17:30:01'),
(4, 'Abul Khan Anam', 'aka@gmail.com', '$2y$10$qe7uayX5Rxn0hbhLHSQFxONKtqexLHjh2ibGw.8GsqtGsQxBZPRtG', 42, 'Male', 'owner', '01912345678', '2025-08-02 17:32:13'),
(5, 'Arif Jawad', 'aj@gmail.com', '$2y$10$N6fhp8VOhmsCAy0oDqhbjOyzvBYnESOhrpY07c9lZtjoXe2ei4bgq', 22, 'Male', 'tenant', '01819354687', '2025-08-05 01:51:03'),
(6, 'Nafisa Fatema', 'nafisa@gmail.com', '$2y$10$ivmvt.2smTvr833phUAXMOYcFVHt94URNC9W8It1l9qcSxBv9TSOW', 21, 'Female', 'tenant', '01911987654', '2025-08-07 23:11:03'),
(7, 'Johura Begum', 'johura@gmail.com', '$2y$10$.UU6/Tqr1jcGczKtO0xLxOrIisx1b/pHBk2sgSSdtY9G.1inrzKm.', 44, 'Female', 'owner', '01613234567', '2025-08-07 23:16:19'),
(8, 'Md. Idris Ali', 'ali@gmail.com', '$2y$10$hl11zswJfWijCYK.PEHVSOyqO5fHqcY.Zua7NpnSGpo.pf8EDLI4y', 50, 'Male', 'owner', '01523567891', '2025-08-07 23:27:59'),
(9, 'Kazi Rahmat Miah', 'rahmat@gmail.com', '$2y$10$k1SmmOqt/hjnH02/Od149OdxS5vTlMOy.NCVBS8pIK8Eks7rjwQaa', 52, 'Male', 'owner', '01819345123', '2025-08-08 12:29:36'),
(10, 'Rafis Ahmed', 'rafis@gmail.com', '$2y$10$DHWEBJmwz8PX7HycDxD77.Tn1WvoYtT4nQA6atLd.UxOE1C0NLYu6', 31, 'Male', 'tenant', '01619546734', '2025-08-08 12:38:23'),
(11, 'Tanjim Rahman', 'tanjim@gmail.com', '$2y$10$pWEeDBthfwAcjB/k.d50XuDayJyxk3RHqUeoNDKA.2DyTvyYqIkna', 19, 'Male', 'tenant', '01519467399', '2025-08-08 13:39:58'),
(12, 'Sharmila Khatun', 'sk@gmail.com', '$2y$10$trJ.bjvpAnUnxXEqG6mTSOb.BS0vrG0rSQ9wSQ9XynrsQ55DhKS56', 40, 'Female', 'owner', '01312765432', '2025-08-08 18:44:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `preferences`
--
ALTER TABLE `preferences`
  ADD PRIMARY KEY (`preference_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`property_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `reviewed_user_id` (`reviewed_user_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `roommate_requests`
--
ALTER TABLE `roommate_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `preferences`
--
ALTER TABLE `preferences`
  MODIFY `preference_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `roommate_requests`
--
ALTER TABLE `roommate_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `preferences`
--
ALTER TABLE `preferences`
  ADD CONSTRAINT `preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`reviewed_user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `requests_ibfk_3` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`);

--
-- Constraints for table `roommate_requests`
--
ALTER TABLE `roommate_requests`
  ADD CONSTRAINT `roommate_requests_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `roommate_requests_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
