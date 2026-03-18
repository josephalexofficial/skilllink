-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2026 at 10:27 PM
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
-- Database: `skilllink_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `cat_name` varchar(50) NOT NULL COMMENT 'Display name (e.g., Plumbing)',
  `cat_icon` varchar(50) NOT NULL COMMENT 'FontAwesome class (e.g., fa-wrench)',
  `sort_order` int(11) DEFAULT 0 COMMENT 'Controls UI display sequence',
  `status` enum('active','hidden') DEFAULT 'active' COMMENT 'Allows temporary hiding of categories',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `cat_name`, `cat_icon`, `sort_order`, `status`, `created_at`) VALUES
(1, 'Plumbing', 'fa-faucet-drip', 1, 'hidden', '2026-03-05 14:56:05'),
(2, 'Electrical', 'fa-bolt-lightning', 2, 'active', '2026-03-05 14:56:05'),
(3, 'Painting', 'fa-paint-roller', 3, 'active', '2026-03-05 14:56:05'),
(4, 'Carpentry', 'fa-hammer', 4, 'active', '2026-03-05 14:56:05'),
(5, 'Cleaning', 'fa-sparkles', 5, 'active', '2026-03-05 14:56:05'),
(6, 'Appliances', 'fa-gears', 6, 'active', '2026-03-05 14:56:05');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `worker_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `task_id`, `worker_id`, `client_id`, `rating`, `comment`, `created_at`) VALUES
(1, 7, 2, 10, 5, 'Perfect Work .\\r\\nArrival on time', '2026-03-18 05:19:54'),
(2, 4, 13, 10, 5, 'Work Perfectly Done', '2026-03-18 06:01:15'),
(3, 6, 2, 1, 5, 'Well done', '2026-03-18 06:43:29');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location_name` varchar(255) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `budget` decimal(10,2) DEFAULT 0.00,
  `budget_type` enum('fixed','negotiable') DEFAULT 'fixed',
  `status` enum('open','assigned','in_progress','completed','finalized','cancelled') NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `client_id`, `category_id`, `worker_id`, `title`, `description`, `location_name`, `latitude`, `longitude`, `budget`, `budget_type`, `status`, `created_at`, `updated_at`) VALUES
(1, 10, 2, 2, 'Fix  Electronic (Fridge)', 'Fix  Electronic (Fridge)', 'Nairobi', NULL, NULL, 500.00, 'negotiable', 'completed', '2026-03-06 08:21:37', '2026-03-06 15:02:04'),
(2, 10, 4, NULL, 'Bed fixing', 'I want the bed fixed on the edges', 'Kisumu', NULL, NULL, 1000.00, 'negotiable', 'open', '2026-03-06 08:47:23', '2026-03-06 08:47:23'),
(3, 10, 3, NULL, 'Wall Painting', 'Panting of  a 6 Feet Wall', 'Nakuru', NULL, NULL, 2000.00, 'fixed', 'open', '2026-03-06 08:50:41', '2026-03-06 08:50:41'),
(4, 10, 6, 13, 'Appliance Connection and Fixing', 'Fixing of my expensive television which i bought with my own Money', 'Siaya', NULL, NULL, 1500.00, 'fixed', 'finalized', '2026-03-06 08:53:23', '2026-03-18 06:01:15'),
(5, 1, 1, NULL, 'Tap Fixing', 'Fixing of a Tap', 'Machakos', NULL, NULL, 3000.00, 'negotiable', 'open', '2026-03-06 09:26:51', '2026-03-06 09:26:51'),
(6, 1, 2, 2, 'Laundry Machine Electronic Fixing', 'I want my laundry machine to be fixed electronically', 'Milimani', NULL, NULL, 3000.00, 'fixed', 'finalized', '2026-03-11 20:46:59', '2026-03-18 06:43:29'),
(7, 10, 2, 2, 'Electronic Kettle Fixing', 'The charging part of the electronic kettle fixed', 'Siaya', NULL, NULL, 800.00, 'negotiable', 'finalized', '2026-03-11 20:54:01', '2026-03-18 05:19:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `county` varchar(100) NOT NULL,
  `area` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('client','worker','admin') NOT NULL,
  `status` enum('active','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `county`, `area`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'ALEX JOSEPH', 'alexjoseph@gmail.com', '0769591223', 'Kisumu', 'Milimani', '$2y$10$CcRNjaly8Y3.52Ac6Lw9dOAPabDsLgG/CK.eNyL6Lts0aykFj.PtO', 'client', 'active', '2026-02-13 22:08:36'),
(2, 'Whimsey', 'josephalex@gmail.com', '0715036332', 'Kisumu', 'Milimani', '$2y$10$npodUy8L.K95tyn0qKfZs.w6S18Ud955d47CAFg2wdwitDCBj5BPG', 'worker', 'active', '2026-02-13 22:10:21'),
(9, 'SkillLink Admin', 'skill@gmail.com', '0700000000', 'Kisumu', 'Maseno', '$2y$10$e.bD4HaFAGb8zU6Xl6zvr.v/FhASkUsgNPT7QqFh0Lq0px7okJ5FS', 'admin', 'active', '2026-02-16 20:59:03'),
(10, 'Silas Omondi', 'silas.omondi@email.com', '0712345678', 'Kisumu', 'Maseno Main', '$2y$10$FDZV1VL.F8RhtdaBoNXF.uczwjxvQwqe/7QeCb5f6inXhlSTEPYLK', 'client', 'active', '2026-03-05 13:58:03'),
(11, 'Mary Anyango', 'mary.anyango@email.com', '0722112233', 'Siaya', 'Bondo Town', '$2y$10$0yjH7gTz5MIg3Mf5xA/.mue6WbZ1o5cKD.JxL5a.HoRCIoAJhvfYK', 'worker', 'active', '2026-03-05 13:59:17'),
(12, 'Isaac Simiyu', 'isaac.simiyu@email.com', '0733445566', 'Bungoma', 'Kanduyi', '$2y$10$M.8OsWSA.LYDt6/U9P1okOmPKS/Br2qz6yNshBVA3zkhnhynRkkBW', 'worker', 'active', '2026-03-05 14:00:18'),
(13, 'Catherine Mutheu', 'catherine.mutheu@email.com', '0700123456', 'Machakos', 'Syokimau', '$2y$10$aTk2ZnWsRwuYceX92sMmSePsWEBc8y687bbf311dYC4gDu.bB5tVq', 'worker', 'active', '2026-03-05 14:01:23'),
(14, 'Benard Cheruiyot', 'ben.cheruiyot@email.com', '0755998877', 'Nakuru', 'Milimani', '$2y$10$T6YfQAQlPOsGZW8ubnHfK.YigciHvqbFQ2smGtqPdF9F023ZQbxA6', 'worker', 'active', '2026-03-05 14:02:23'),
(15, 'Khamis Bakari', 'khamis.bakari@email.com', '0799887766', 'Siaya', 'Ugenya', '$2y$10$K03nw9/oqb7pEOEREr6Hxe4Dd96mVtPMGrs/F9wFNvbL37ZSolKse', 'client', 'active', '2026-03-05 14:03:47'),
(16, 'David Odhiambo', 'david.odhiambo@email.com', '0722334455', 'Bungoma', 'Mabungo', '$2y$10$T7KQlX2BOxnqFbDBq9LXWe7sblpmwNx09A.gE8k.u5BYjgvWSMbku', 'client', 'active', '2026-03-05 14:04:51'),
(17, 'George Nasibi', 'george.nasibi@email.com', '0733123456', 'Machakos', 'Athiriver', '$2y$10$h./PhpnriGXS11FeOoxZKeD9zj3j5oRwEHCG4jhV6g5KFbDEyuz1q', 'client', 'active', '2026-03-05 14:05:45'),
(18, 'Janet Bosibori', 'janet.bosibori@email.com', '0701987654', 'Nakuru', 'Vegas', '$2y$10$AAZhD9rjp8edTFZ4n4GAp.DU2Ek3eOZHkbCm6nb4VU0Y4JFOFJOn2', 'client', 'active', '2026-03-05 14:07:26'),
(19, 'Kelvin Kosma', 'kelvinkosma@gmail.com', '0712341234', 'Nairobi', 'Kilimani', '$2y$10$/VpqkXRH2O1FbFkh174CQes5LzDwqp9sireXhSnYXs87v2z7xSPM6', 'worker', 'active', '2026-03-05 16:40:33'),
(20, 'Beatrice Kosma', 'beatricekosma@gmail.com', '0745461234', 'Nairobi', 'Upperhill', '$2y$10$jWTO6ca8AqBufE5LVnJDxupN9MsqvnOJdQw2RTj19dzSFyoTKOfnK', 'client', 'active', '2026-03-05 16:43:21');

-- --------------------------------------------------------

--
-- Table structure for table `worker_profiles`
--

CREATE TABLE `worker_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Links to the shared users table',
  `category_id` int(11) NOT NULL COMMENT 'Links to the Blueprint (categories) table',
  `bio` text NOT NULL COMMENT 'The professional summary for clients',
  `profile_photo` varchar(255) DEFAULT 'default-pro.png' COMMENT 'Path to the headshot',
  `onboarded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `hourly_rate` decimal(10,2) DEFAULT 0.00,
  `service_radius` int(11) DEFAULT 10,
  `is_live` tinyint(1) DEFAULT 1,
  `id_proof_path` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `worker_profiles`
--

INSERT INTO `worker_profiles` (`profile_id`, `user_id`, `category_id`, `bio`, `profile_photo`, `onboarded_at`, `hourly_rate`, `service_radius`, `is_live`, `id_proof_path`, `is_verified`) VALUES
(1, 2, 2, 'Certified Electrician Professional', 'pro_2_1772736276.jpg', '2026-03-05 15:25:39', 0.00, 10, 1, NULL, 1),
(2, 11, 5, 'Certified Cleaner', 'default-pro.png', '2026-03-05 15:27:52', 0.00, 10, 1, NULL, 0),
(3, 14, 4, 'Certified Carpenter', 'default-pro.png', '2026-03-05 16:20:25', 0.00, 10, 1, NULL, 0),
(4, 13, 6, 'Certified in Appliances', 'pro_13_1772728203.jpg', '2026-03-05 16:30:03', 0.00, 10, 1, 'id_13_1772740899.pdf', 0),
(5, 12, 1, 'Professional Plumber', 'default-pro.png', '2026-03-05 16:36:46', 0.00, 10, 1, NULL, 0),
(6, 19, 3, 'Proficient Painter', 'default-pro.png', '2026-03-05 16:41:13', 0.00, 10, 1, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cat_name` (`cat_name`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `worker_id` (`worker_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_client` (`client_id`),
  ADD KEY `fk_category` (`category_id`),
  ADD KEY `fk_worker` (`worker_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `worker_profiles`
--
ALTER TABLE `worker_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `fk_profile_category` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `worker_profiles`
--
ALTER TABLE `worker_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`worker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_client` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_worker` FOREIGN KEY (`worker_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `worker_profiles`
--
ALTER TABLE `worker_profiles`
  ADD CONSTRAINT `fk_profile_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_profile_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
