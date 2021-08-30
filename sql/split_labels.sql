-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2021 at 12:24 PM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `packing`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `tel_no` varchar(30) NOT NULL,
  `address` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_user_id` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_name`, `tel_no`, `address`, `created_at`, `created_user_id`, `updated_at`, `updated_user_id`) VALUES
(2, 'DENSO (GUANGZHOU NANSHA) CO.,LTD.', '-', '-', NULL, 0, NULL, 0),
(3, 'HITACHI AUTOMOTIVE SYSTEMS AMERICAS,INC.-MONROE,GA', '-', '-', NULL, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `defects`
--

CREATE TABLE `defects` (
  `id` int(11) NOT NULL,
  `defect_code` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  `created_user_id` int(11) NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `defects`
--

INSERT INTO `defects` (`id`, `defect_code`, `created_at`, `created_user_id`, `updated_at`, `updated_user_id`) VALUES
(1, 'A1', '2021-08-02 15:51:56', 1, '2021-08-02 15:51:56', 1),
(2, 'B2', '2021-08-02 15:51:57', 1, '2021-08-02 15:51:57', 1),
(3, 'C3', '2021-08-02 15:52:21', 1, '2021-08-02 15:52:21', 1),
(4, 'D4', '2021-08-02 15:52:21', 1, '2021-08-02 15:52:21', 1);

-- --------------------------------------------------------

--
-- Table structure for table `labels`
--

CREATE TABLE `labels` (
  `id` int(11) NOT NULL,
  `lot_id` int(11) NOT NULL,
  `merge_pack_id` int(11) NOT NULL,
  `split_label_id` int(11) NOT NULL,
  `label_no` varchar(100) NOT NULL,
  `label_type` enum('FULLY','NONFULLY','MERGE_FULLY','MERGE_NONFULLY') NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('CREATED','PACKED','USED','VOID','MERGED','SPLIT') NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_user_id` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `labels`
--

INSERT INTO `labels` (`id`, `lot_id`, `merge_pack_id`, `split_label_id`, `label_no`, `label_type`, `quantity`, `status`, `created_at`, `created_user_id`, `updated_at`, `updated_user_id`) VALUES
(5, 10, 0, 0, 'TestLabel000', 'FULLY', 10, 'PACKED', '2021-08-27 21:21:50', 1, '2021-08-29 21:42:09', 1),
(6, 10, 0, 0, 'TestLabel001', 'FULLY', 0, 'CREATED', '2021-08-27 21:21:50', 1, '2021-08-29 21:10:11', 1),
(7, 10, 0, 0, 'TestLabel002', 'NONFULLY', 5, 'PACKED', '2021-08-27 21:21:50', 1, '2021-08-29 21:09:45', 1);

-- --------------------------------------------------------

--
-- Table structure for table `lots`
--

CREATE TABLE `lots` (
  `id` int(11) NOT NULL,
  `lot_no` varchar(100) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `printed_user_id` int(11) DEFAULT NULL,
  `packed_user_id` int(11) NOT NULL,
  `status` enum('CREATED','PRINTED','PACKING','PACKED') NOT NULL DEFAULT 'CREATED',
  `created_at` datetime DEFAULT NULL,
  `created_user_id` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lots`
--

INSERT INTO `lots` (`id`, `lot_no`, `product_id`, `quantity`, `printed_user_id`, `packed_user_id`, `status`, `created_at`, `created_user_id`, `updated_at`, `updated_user_id`) VALUES
(1, '21506NSB10B', 1, 35, 1, 0, 'CREATED', NULL, 0, '2021-07-28 22:48:01', 1),
(2, '21506NSB10C', 1, 43, 1, 0, 'CREATED', NULL, 0, '2021-07-28 22:30:07', 1),
(5, '21513K20J13B', 2, 68, 1, 0, 'CREATED', NULL, 0, '2021-07-28 22:34:34', 1),
(6, '21513K20J11B', 3, 15, 1, 0, 'CREATED', NULL, 0, '2021-07-28 22:34:03', 1),
(10, 'TestLabel', 1, 20, 1, 0, 'PACKING', '2021-08-27 21:21:46', 1, '2021-08-29 21:42:10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `lot_defects`
--

CREATE TABLE `lot_defects` (
  `id` int(11) NOT NULL,
  `lot_id` int(11) NOT NULL,
  `defect_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `created_user_id` int(11) NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lot_defects`
--

INSERT INTO `lot_defects` (`id`, `lot_id`, `defect_id`, `quantity`, `created_at`, `created_user_id`, `updated_at`, `updated_user_id`) VALUES
(1, 9, 1, 10, '2021-08-21 13:48:07', 1, '2021-08-21 13:48:07', 1),
(2, 0, 0, 0, '2021-08-28 22:46:14', 1, '2021-08-28 22:46:14', 1);

-- --------------------------------------------------------

--
-- Table structure for table `merge_packs`
--

CREATE TABLE `merge_packs` (
  `id` int(11) NOT NULL,
  `merge_no` varchar(20) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `merge_pack_details`
--

CREATE TABLE `merge_pack_details` (
  `id` int(11) NOT NULL,
  `merge_pack_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pos`
--

CREATE TABLE `pos` (
  `id` int(11) NOT NULL,
  `po_no` varchar(100) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `po_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `po_details`
--

CREATE TABLE `po_details` (
  `id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_code` varchar(20) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` float NOT NULL,
  `std_pack` int(11) NOT NULL,
  `std_box` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_user_id` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_code`, `product_name`, `price`, `std_pack`, `std_box`, `created_at`, `created_user_id`, `updated_at`, `updated_user_id`) VALUES
(1, 'CHT0089', 'SHAFT,LOCK,PUSH', 100, 10, 30, NULL, 0, NULL, 0),
(2, 'CHT4291', 'MAIN CAM SHAFT (T99001)', 300, 20, 20, NULL, 0, NULL, 0),
(3, 'HAG0024', 'RETAINER-BALL', 200, 5, 40, NULL, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `split_labels`
--

CREATE TABLE `split_labels` (
  `id` int(11) NOT NULL,
  `split_label_no` varchar(20) NOT NULL,
  `label_id` int(11) NOT NULL,
  `status` enum('CREATED','PRINTED','PACKING','PACKED') NOT NULL,
  `created_at` datetime NOT NULL,
  `created_user_id` int(11) NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `split_label_details`
--

CREATE TABLE `split_label_details` (
  `id` int(11) NOT NULL,
  `split_label_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `created_user_id` int(11) NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_user_id` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `tag_no` varchar(20) NOT NULL,
  `po_detail_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('CREATED','BOXED','SHIPPED','VOID') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tag_labels`
--

CREATE TABLE `tag_labels` (
  `id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `user_role_id` int(11) NOT NULL,
  `locale` varchar(100) DEFAULT NULL,
  `enabled` tinyint(4) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_user_id` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `first_name`, `last_name`, `user_role_id`, `locale`, `enabled`, `created_at`, `created_user_id`, `updated_at`, `updated_user_id`) VALUES
(1, 'admin', '$2y$10$AG.ltxsSn6jfR1wveS7jreu9eWIz.qou9sBjA3OLa3FWUnKKg/5Ta', NULL, NULL, 1, NULL, 0, NULL, 0, NULL, 0),
(2, 'user', '25d55ad283aa400af464c76d713c07ad', NULL, NULL, 2, NULL, 0, NULL, 0, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `defects`
--
ALTER TABLE `defects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `labels`
--
ALTER TABLE `labels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lots`
--
ALTER TABLE `lots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lot_no` (`lot_no`);

--
-- Indexes for table `lot_defects`
--
ALTER TABLE `lot_defects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `merge_packs`
--
ALTER TABLE `merge_packs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `merge_pack_details`
--
ALTER TABLE `merge_pack_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pos`
--
ALTER TABLE `pos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `po_details`
--
ALTER TABLE `po_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_code` (`product_code`);

--
-- Indexes for table `split_labels`
--
ALTER TABLE `split_labels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `split_label_details`
--
ALTER TABLE `split_label_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tag_labels`
--
ALTER TABLE `tag_labels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `defects`
--
ALTER TABLE `defects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `labels`
--
ALTER TABLE `labels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `lots`
--
ALTER TABLE `lots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `lot_defects`
--
ALTER TABLE `lot_defects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `merge_packs`
--
ALTER TABLE `merge_packs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `merge_pack_details`
--
ALTER TABLE `merge_pack_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pos`
--
ALTER TABLE `pos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `po_details`
--
ALTER TABLE `po_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `split_labels`
--
ALTER TABLE `split_labels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `split_label_details`
--
ALTER TABLE `split_label_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tag_labels`
--
ALTER TABLE `tag_labels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
