-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2021 at 06:31 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.3

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
  `create_user_id` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_name`, `tel_no`, `address`, `created_at`, `create_user_id`, `updated_at`, `updated_user_id`) VALUES
(1, 'CANON HI-TECH (THAILAND)LTD.', '-', '-', NULL, 0, NULL, 0),
(2, 'DENSO (GUANGZHOU NANSHA) CO.,LTD.', '-', '-', NULL, 0, NULL, 0),
(3, 'HITACHI AUTOMOTIVE SYSTEMS AMERICAS,INC.-MONROE,GA', '-', '-', NULL, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `labels`
--

CREATE TABLE `labels` (
  `id` int(11) NOT NULL,
  `lot_id` int(11) NOT NULL,
  `merge_pack_id` int(11) NOT NULL,
  `label_no` varchar(100) NOT NULL,
  `label_type` enum('FULLY','NONFULLY','MERGE_FULLY','MERGE_NONFULLY') NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('CREATE','PACKED','USED','VOID','MERGED') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `labels`
--

INSERT INTO `labels` (`id`, `lot_id`, `merge_pack_id`, `label_no`, `label_type`, `quantity`, `status`) VALUES
(1, 1, 0, 'PK0000001', 'FULLY', 10, 'USED'),
(2, 1, 0, 'PK0000002', 'FULLY', 10, 'PACKED'),
(3, 1, 0, 'PK0000003', 'FULLY', 10, 'USED'),
(4, 1, 0, 'PK0000004', 'FULLY', 10, 'VOID'),
(5, 1, 0, 'PK0000005', 'NONFULLY', 8, 'MERGED'),
(6, 2, 0, 'PK0000006', 'FULLY', 10, 'PACKED'),
(7, 2, 0, 'PK0000007', 'FULLY', 10, 'PACKED'),
(8, 2, 0, 'PK0000008', 'FULLY', 10, 'PACKED'),
(9, 2, 0, 'PK0000009', 'FULLY', 10, 'PACKED'),
(10, 2, 0, 'PK0000010', 'FULLY', 10, 'VOID'),
(11, 2, 0, 'PK0000011', 'NONFULLY', 5, 'MERGED'),
(12, 5, 0, 'PK0000012', 'FULLY', 20, 'USED'),
(13, 5, 0, 'PK0000013', 'FULLY', 20, 'USED'),
(14, 5, 0, 'PK0000014', 'FULLY', 20, 'VOID'),
(15, 5, 0, 'PK0000015', 'FULLY', 20, 'PACKED'),
(16, 5, 0, 'PK0000016', 'NONFULLY', 4, 'PACKED'),
(17, 6, 0, 'PK0000017', 'FULLY', 5, 'USED'),
(18, 6, 0, 'PK0000018', 'FULLY', 5, 'USED'),
(19, 6, 0, 'PK0000019', 'FULLY', 5, 'PACKED'),
(20, 6, 0, 'PK0000020', 'NONFULLY', 0, 'VOID'),
(21, 0, 1, 'PK0000021', 'MERGE_FULLY', 10, 'PACKED'),
(22, 0, 1, 'PK0000022', 'MERGE_NONFULLY', 2, 'PACKED');

-- --------------------------------------------------------

--
-- Table structure for table `lots`
--

CREATE TABLE `lots` (
  `id` int(11) NOT NULL,
  `lot_no` varchar(100) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lots`
--

INSERT INTO `lots` (`id`, `lot_no`, `product_id`, `quantity`) VALUES
(1, '21506NSB10B', 1, 38),
(2, '21506NSB10C', 1, 43),
(5, '21513K20J13B', 2, 68),
(6, '21513K20J11B', 3, 15);

-- --------------------------------------------------------

--
-- Table structure for table `merge_packs`
--

CREATE TABLE `merge_packs` (
  `id` int(11) NOT NULL,
  `merge_no` varchar(20) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `merge_packs`
--

INSERT INTO `merge_packs` (`id`, `merge_no`, `product_id`) VALUES
(1, 'M0001', 1);

-- --------------------------------------------------------

--
-- Table structure for table `merge_pack_details`
--

CREATE TABLE `merge_pack_details` (
  `id` int(11) NOT NULL,
  `merge_pack_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `merge_pack_details`
--

INSERT INTO `merge_pack_details` (`id`, `merge_pack_id`, `label_id`) VALUES
(1, 1, 5),
(2, 1, 11);

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

--
-- Dumping data for table `pos`
--

INSERT INTO `pos` (`id`, `po_no`, `customer_id`, `po_date`) VALUES
(1, 'PO0001', 1, '2021-07-10'),
(2, 'PO0002', 2, '2021-07-10');

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

--
-- Dumping data for table `po_details`
--

INSERT INTO `po_details` (`id`, `po_id`, `product_id`, `quantity`) VALUES
(1, 1, 1, 20),
(2, 1, 2, 40),
(3, 2, 3, 10);

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
  `create_user_id` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_code`, `product_name`, `price`, `std_pack`, `std_box`, `created_at`, `create_user_id`, `updated_at`, `updated_user_id`) VALUES
(1, 'CHT0089', 'SHAFT,LOCK,PUSH', 100, 10, 30, NULL, 0, NULL, 0),
(2, 'CHT4291', 'MAIN CAM SHAFT (T99001)', 300, 20, 20, NULL, 0, NULL, 0),
(3, 'HAG0024', 'RETAINER-BALL', 200, 5, 40, NULL, 0, NULL, 0);

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

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `tag_no`, `po_detail_id`, `quantity`, `status`) VALUES
(1, 'PT0001', 1, 20, 'SHIPPED'),
(2, 'PT0002', 2, 20, 'SHIPPED'),
(4, 'PT0003', 2, 20, 'SHIPPED'),
(5, 'PT0004', 3, 10, 'SHIPPED');

-- --------------------------------------------------------

--
-- Table structure for table `tag_labels`
--

CREATE TABLE `tag_labels` (
  `id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tag_labels`
--

INSERT INTO `tag_labels` (`id`, `tag_id`, `label_id`) VALUES
(1, 1, 1),
(2, 1, 3),
(3, 2, 13),
(4, 4, 12),
(5, 5, 18),
(6, 5, 17);

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
-- AUTO_INCREMENT for table `labels`
--
ALTER TABLE `labels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `lots`
--
ALTER TABLE `lots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `merge_packs`
--
ALTER TABLE `merge_packs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `merge_pack_details`
--
ALTER TABLE `merge_pack_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pos`
--
ALTER TABLE `pos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `po_details`
--
ALTER TABLE `po_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tag_labels`
--
ALTER TABLE `tag_labels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
