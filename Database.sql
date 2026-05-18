-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2026 at 04:22 AM
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
-- Database: `online_book_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `author` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `description`, `price`, `category_id`, `image_path`, `stock`, `created_at`) VALUES
(1, 'The Alchemist', 'Paulo Coelho', 'A story about following your dreams.', 500.00, 1, NULL, 50, '2026-05-17 10:10:35'),
(2, 'Devdas', 'Sarat Chandra', 'Classic Bengali literature.', 300.00, 2, NULL, 30, '2026-05-17 10:10:35'),
(3, 'Dune', 'Frank Herbert', 'Epic sci-fi set in a desert planet.', 750.00, 3, NULL, 20, '2026-05-17 10:10:35'),
(4, 'Sapiens', 'Yuval Noah Harari', 'A brief history of humankind.', 600.00, 4, NULL, 40, '2026-05-17 10:10:35'),
(5, 'Atomic Habits', 'James Clear', 'Build good habits, break bad ones.', 450.00, 5, NULL, 60, '2026-05-17 10:10:35');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `book_id`, `quantity`, `added_at`) VALUES
(3, 3, 2, 1, '2026-05-17 10:10:35'),
(5, 3, 4, 1, '2026-05-17 10:10:35');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Novel', '2026-05-17 10:10:35'),
(2, 'Literature', '2026-05-17 10:10:35'),
(3, 'Sci-Fi', '2026-05-17 10:10:35'),
(4, 'History', '2026-05-17 10:10:35'),
(5, 'Self-Help', '2026-05-17 10:10:35');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `payment_method`, `order_date`) VALUES
(1, 2, 1750.00, 'pending', 'bKash', '2026-05-17 10:10:35'),
(2, 3, 300.00, 'pending', 'Cash on Delivery', '2026-05-17 10:10:35'),
(4, 2, 600.00, 'pending', 'Credit Card', '2026-05-17 10:10:35'),
(5, 3, 450.00, 'pending', 'Bank Transfer', '2026-05-17 10:10:35'),
(6, 2, 1750.00, 'delivered', 'bKash', '2026-05-17 10:31:19'),
(7, 2, 500.00, 'shipped', 'bKash', '2026-05-17 12:31:14');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `book_id`, `quantity`, `unit_price`) VALUES
(1, 1, 1, 2, 500.00),
(2, 1, 3, 1, 750.00),
(3, 2, 2, 1, 300.00),
(5, 4, 4, 1, 600.00),
(6, 6, 1, 2, 500.00),
(7, 6, 3, 1, 750.00),
(8, 7, 1, 1, 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `amount`, `payment_method`, `transaction_id`, `payment_date`) VALUES
(1, 1, 1750.00, 'bKash', 'TXN100001', '2026-05-17 10:10:35'),
(2, 2, 300.00, 'Cash on Delivery', 'TXN100002', '2026-05-17 10:10:35'),
(4, 4, 600.00, 'Credit Card', 'TXN100004', '2026-05-17 10:10:35'),
(5, 5, 450.00, 'Bank Transfer', 'TXN100005', '2026-05-17 10:10:35'),
(6, 6, 1750.00, 'bKash', 'TXN17790138792', '2026-05-17 10:31:19'),
(7, 7, 500.00, 'bKash', 'TXN17790210742', '2026-05-17 12:31:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer',
  `profile_picture` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `profile_picture`, `address`, `phone`, `created_at`, `remember_token`) VALUES
(1, 'Rahim Admin', 'rahim@admin.com', '$2y$10$Tgtl.FnAkpkR/dDndJGkqetUX3R5.vfprQNVEXGHDDiVUXge8LS62', 'admin', NULL, 'Dhaka, Bangladesh', '01711111111', '2026-05-17 10:10:35', NULL),
(2, 'Karim Customer', 'karim@gmail.com', '$2y$10$3Y6EpCCpwjv4cDp7HhcMa.3uSbDrWDqi3T8/K.8uLYNqQRYbgv9UG', 'customer', NULL, 'uu', '01722222222', '2026-05-17 10:10:35', NULL),
(3, 'Sumaiya Ahmed', 'sumaiya@gmail.com', '$2y$10$abcdefghijklmnopqrstuuVGZzQ1234567890abcdefghijklmnop', 'customer', NULL, 'Sylhet, Bangladesh', '01733333333', '2026-05-17 10:10:35', NULL),
(5, 'Nadia Islam', 'nadia@admin.com', '$2y$10$abcdefghijklmnopqrstuuVGZzQ1234567890abcdefghijklmnop', 'admin', NULL, 'Khulna, Bangladesh', '01755555555', '2026-05-17 10:10:35', NULL),
(9, 'asdfsf', 'r@admin.com', '$2y$10$nr99mR.e9mHafA9cdPa2me7HV1BArm9WnPDHquasElhJAq5bDKhIa', 'customer', NULL, 'sadfdf', '01988888888', '2026-05-17 21:20:11', 'eace0bb26588eb98ff44dd21baf817a483884f663aef907197b675d43c500a5'),
(10, 'Aim Less Solution', 'rhim@admin.com', '$2y$10$mFvNlj5ALeUaNKizoVjg8OVHdiDpNXAW60ucsrJXUm7hWDdq4cAs2', 'customer', NULL, 'Sajjanknada,Rajbari', '01988888888', '2026-05-18 01:23:45', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
