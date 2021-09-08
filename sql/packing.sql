-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 08, 2021 at 04:34 PM
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
  `status` enum('CREATED','PACKED','USED','VOID','MERGED','MERGING') NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_user_id` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `labels`
--

INSERT INTO `labels` (`id`, `lot_id`, `merge_pack_id`, `split_label_id`, `label_no`, `label_type`, `quantity`, `status`, `created_at`, `created_user_id`, `updated_at`, `updated_user_id`) VALUES
(224, 15, 0, 27, 'test_v001000', 'FULLY', 10, 'VOID', '2021-09-08 17:59:22', 1, '2021-09-08 18:45:38', 1),
(225, 15, 0, 0, 'test_v001001', 'FULLY', 0, 'VOID', '2021-09-08 17:59:22', 1, '2021-09-08 18:13:28', 1),
(226, 15, 0, 0, 'test_v001002', 'FULLY', 10, 'PACKED', '2021-09-08 17:59:22', 1, '2021-09-08 18:07:29', 1),
(227, 15, 0, 0, 'test_v001003', 'NONFULLY', 8, 'PACKED', '2021-09-08 17:59:22', 1, '2021-09-08 18:12:45', 1),
(234, 18, 0, 0, 'TPR01v2000', 'FULLY', 10, 'PACKED', '2021-09-08 18:36:12', 1, '2021-09-08 18:36:55', 1),
(235, 18, 0, 0, 'TPR01v2001', 'FULLY', 10, 'PACKED', '2021-09-08 18:36:12', 1, '2021-09-08 18:37:13', 1),
(236, 18, 0, 0, 'TPR01v2002', 'FULLY', 0, 'VOID', '2021-09-08 18:36:12', 1, '2021-09-08 18:38:24', 1),
(237, 18, 0, 0, 'TPR01v2003', 'NONFULLY', 2, 'PACKED', '2021-09-08 18:36:12', 1, '2021-09-08 18:37:52', 1),
(238, 15, 0, 27, 'LBSP2242700', 'NONFULLY', 5, 'PACKED', '2021-09-08 18:45:39', 1, '2021-09-08 19:17:27', 1),
(239, 15, 0, 27, 'LBSP2242701', 'NONFULLY', 3, 'PACKED', '2021-09-08 18:45:39', 1, '2021-09-08 19:22:27', 1);

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
(15, 'test_v001', 1, 28, 1, 1, 'PACKED', '2021-09-08 17:58:05', 1, '2021-09-08 18:13:29', 1),
(18, 'TPR01v2', 1, 22, 1, 1, 'PACKED', '2021-09-08 18:36:03', 1, '2021-09-08 18:38:26', 1);

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
(1, 1, 1, 3, '0000-00-00 00:00:00', 1, '0000-00-00 00:00:00', 1),
(2, 1, 1, 2, '2021-08-06 23:23:29', 1, '2021-08-06 23:23:29', 1),
(3, 1, 3, 8, '2021-08-06 23:34:27', 1, '2021-08-06 23:34:27', 1),
(4, 2, 4, 3, '2021-08-07 17:09:15', 1, '2021-08-07 17:09:15', 1),
(5, 1, 1, 12, '2021-08-10 14:45:52', 1, '2021-08-10 14:45:52', 1),
(6, 5, 3, 4, '2021-08-10 21:23:17', 1, '2021-08-10 21:23:17', 1),
(7, 1, 1, 10, '2021-08-11 00:55:00', 1, '2021-08-11 00:55:00', 1),
(8, 1, 1, 10, '2021-08-11 00:55:09', 1, '2021-08-11 00:55:09', 1),
(9, 1, 3, 10, '2021-08-11 02:01:12', 1, '2021-08-11 02:01:12', 1),
(10, 1, 1, 12, '2021-08-11 02:04:11', 1, '2021-08-11 02:04:11', 1),
(11, 0, 0, 0, '2021-08-13 21:21:58', 1, '2021-08-13 21:21:58', 1),
(12, 0, 0, 0, '2021-08-13 21:24:45', 1, '2021-08-13 21:24:45', 1),
(13, 5, 3, 2, '2021-08-13 21:28:57', 1, '2021-08-13 21:28:57', 1),
(14, 0, 0, 0, '2021-08-13 21:29:48', 1, '2021-08-13 21:29:48', 1),
(15, 0, 0, 0, '2021-08-13 21:31:47', 1, '2021-08-13 21:31:47', 1),
(16, 0, 0, 0, '2021-08-13 21:33:34', 1, '2021-08-13 21:33:34', 1),
(17, 6, 1, 3, '2021-08-13 21:34:34', 1, '2021-08-13 21:34:34', 1),
(18, 0, 0, 0, '2021-08-13 21:43:53', 1, '2021-08-13 21:43:53', 1),
(19, 1, 1, 7, '2021-08-13 21:44:35', 1, '2021-08-13 21:44:35', 1),
(20, 6, 2, 2, '2021-08-13 21:51:55', 1, '2021-08-13 21:51:55', 1),
(21, 6, 4, 2, '2021-08-13 21:52:35', 1, '2021-08-13 21:52:35', 1),
(22, 0, 0, 0, '2021-08-13 21:53:45', 1, '2021-08-13 21:53:45', 1),
(23, 0, 0, 0, '2021-08-13 21:54:07', 1, '2021-08-13 21:54:07', 1),
(24, 0, 0, 0, '2021-08-13 21:57:45', 1, '2021-08-13 21:57:45', 1),
(25, 0, 0, 0, '2021-08-13 22:37:53', 1, '2021-08-13 22:37:53', 1),
(26, 0, 0, 0, '2021-08-13 22:39:43', 1, '2021-08-13 22:39:43', 1),
(27, 0, 0, 0, '2021-08-13 22:45:13', 1, '2021-08-13 22:45:13', 1),
(28, 0, 0, 0, '2021-08-13 22:45:15', 1, '2021-08-13 22:45:15', 1),
(29, 1, 1, 3, '2021-08-13 22:48:57', 1, '2021-08-13 22:48:57', 1),
(30, 1, 1, 3, '2021-08-13 22:49:47', 1, '2021-08-13 22:49:47', 1),
(31, 6, 1, 3, '2021-08-13 22:51:33', 1, '2021-08-13 22:51:33', 1),
(32, 0, 0, 0, '2021-08-13 22:55:22', 1, '2021-08-13 22:55:22', 1),
(33, 2, 1, 3, '2021-08-13 23:08:49', 1, '2021-08-13 23:08:49', 1);

-- --------------------------------------------------------

--
-- Table structure for table `merge_packs`
--

CREATE TABLE `merge_packs` (
  `id` int(11) NOT NULL,
  `merge_no` varchar(20) NOT NULL,
  `product_id` int(11) NOT NULL,
  `merge_status` enum('CREATED','MERGE','MERGING','REGISTERING','COMPLETE') NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_user_id` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `merge_pack_details`
--

CREATE TABLE `merge_pack_details` (
  `id` int(11) NOT NULL,
  `merge_pack_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_user_id` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_user_id` int(11) NOT NULL
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

--
-- Dumping data for table `split_labels`
--

INSERT INTO `split_labels` (`id`, `split_label_no`, `label_id`, `status`, `created_at`, `created_user_id`, `updated_at`, `updated_user_id`) VALUES
(27, 'SP27LB224', 224, 'PACKED', '2021-09-08 18:45:38', 1, '2021-09-08 19:22:31', 1);

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
  `updated_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `split_label_details`
--

INSERT INTO `split_label_details` (`id`, `split_label_id`, `label_id`, `created_at`, `created_user_id`, `updated_at`, `updated_user_id`) VALUES
(7, 27, 238, '2021-09-08 18:45:39', 1, '2021-09-08 18:45:39', 0),
(8, 27, 239, '2021-09-08 18:45:39', 1, '2021-09-08 18:45:39', 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=246;

--
-- AUTO_INCREMENT for table `lots`
--
ALTER TABLE `lots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `lot_defects`
--
ALTER TABLE `lot_defects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `merge_packs`
--
ALTER TABLE `merge_packs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `merge_pack_details`
--
ALTER TABLE `merge_pack_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `split_label_details`
--
ALTER TABLE `split_label_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
