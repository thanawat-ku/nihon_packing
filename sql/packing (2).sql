-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 06, 2021 at 03:38 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.2

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
(82, 1, 0, 0, '21506NSB10B002', 'FULLY', 10, 'CREATED', '2021-08-19 23:26:39', 1, '2021-09-06 01:05:10', 1),
(83, 1, 0, 0, '21506NSB10B002', 'FULLY', 10, 'CREATED', '2021-08-19 23:26:40', 1, '2021-08-19 23:26:40', 1),
(84, 1, 0, 0, '21506NSB10B003', 'FULLY', 10, 'CREATED', '2021-08-19 23:26:40', 1, '2021-08-19 23:26:40', 1),
(85, 1, 116, 0, '8850006343333', 'NONFULLY', 9, 'MERGED', '2021-08-19 23:26:40', 1, '2021-09-02 15:11:58', 1),
(86, 2, 0, 0, '21506NSB10C000', 'FULLY', 10, 'CREATED', '2021-08-19 23:26:44', 1, '2021-08-19 23:26:44', 1),
(87, 2, 0, 0, '21506NSB10C001', 'FULLY', 10, 'CREATED', '2021-08-19 23:26:44', 1, '2021-08-19 23:26:44', 1),
(88, 2, 0, 0, '21506NSB10C002', 'FULLY', 10, 'CREATED', '2021-08-19 23:26:44', 1, '2021-08-19 23:26:44', 1),
(89, 2, 0, 0, '21506NSB10C003', 'FULLY', 10, 'CREATED', '2021-08-19 23:26:44', 1, '2021-08-19 23:26:44', 1),
(90, 2, 0, 0, '21506NSB10C004', 'FULLY', 10, 'CREATED', '2021-08-19 23:26:44', 1, '2021-08-20 00:41:39', 1),
(91, 2, 116, 0, '8850006901410', 'NONFULLY', 3, 'MERGED', '2021-08-19 23:26:44', 1, '2021-09-02 15:11:58', 1),
(92, 6, 0, 0, '21513K20J11B000', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:48', 1, '2021-08-19 23:26:48', 1),
(93, 6, 0, 0, '21513K20J11B001', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:48', 1, '2021-08-19 23:26:48', 1),
(94, 6, 0, 0, '21513K20J11B002', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:48', 1, '2021-08-19 23:26:48', 1),
(95, 6, 0, 0, '21513K20J11B003', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:48', 1, '2021-08-19 23:26:48', 1),
(96, 6, 0, 0, '21513K20J11B004', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:48', 1, '2021-08-19 23:26:48', 1),
(97, 6, 0, 0, '21513K20J11B005', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(98, 6, 0, 0, '21513K20J11B006', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(99, 6, 0, 0, '21513K20J11B007', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(100, 6, 0, 0, '21513K20J11B008', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(101, 6, 0, 0, '21513K20J11B009', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(102, 6, 0, 0, '21513K20J11B010', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(103, 6, 0, 0, '21513K20J11B011', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(104, 6, 0, 0, '21513K20J11B012', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(105, 6, 0, 0, '21513K20J11B013', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(106, 6, 0, 0, '21513K20J11B014', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(107, 6, 0, 0, '21513K20J11B015', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(108, 6, 0, 0, '21513K20J11B016', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(109, 6, 0, 0, '21513K20J11B017', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(110, 6, 0, 0, '21513K20J11B018', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(111, 6, 0, 0, '21513K20J11B019', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(112, 6, 0, 0, '21513K20J11B020', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(113, 6, 0, 0, '21513K20J11B021', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(114, 6, 0, 0, '21513K20J11B022', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(115, 6, 0, 0, '21513K20J11B023', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(116, 6, 0, 0, '21513K20J11B024', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(117, 6, 0, 0, '21513K20J11B025', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(118, 6, 0, 0, '21513K20J11B026', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:49', 1, '2021-08-19 23:26:49', 1),
(119, 6, 0, 0, '21513K20J11B027', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:50', 1, '2021-08-19 23:26:50', 1),
(120, 6, 0, 0, '21513K20J11B028', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:50', 1, '2021-08-19 23:26:50', 1),
(121, 6, 0, 0, '21513K20J11B029', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:51', 1, '2021-08-19 23:26:51', 1),
(122, 6, 0, 0, '21513K20J11B030', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:51', 1, '2021-08-19 23:26:51', 1),
(123, 6, 0, 0, '21513K20J11B031', 'FULLY', 5, 'CREATED', '2021-08-19 23:26:51', 1, '2021-08-19 23:26:51', 1),
(124, 6, 0, 0, '21513K20J11B032', 'NONFULLY', 0, 'CREATED', '2021-08-19 23:26:52', 1, '2021-08-19 23:26:52', 1),
(125, 5, 0, 0, '21513K20J13B000', 'FULLY', 20, 'CREATED', '2021-08-19 23:26:57', 1, '2021-08-19 23:26:57', 1),
(126, 5, 0, 0, '21513K20J13B001', 'FULLY', 20, 'CREATED', '2021-08-19 23:26:57', 1, '2021-08-19 23:26:57', 1),
(127, 5, 0, 0, '21513K20J13B002', 'FULLY', 20, 'CREATED', '2021-08-19 23:26:57', 1, '2021-08-19 23:26:57', 1),
(128, 5, 0, 0, '21513K20J13B003', 'FULLY', 20, 'CREATED', '2021-08-19 23:26:57', 1, '2021-08-19 23:26:57', 1),
(129, 5, 0, 0, '21513K20J13B004', 'NONFULLY', 0, 'CREATED', '2021-08-19 23:26:57', 1, '2021-08-19 23:26:57', 1),
(144, 0, 86, 0, '86CHT008900000', 'MERGE_FULLY', 10, 'PACKED', '2021-08-21 00:34:19', 1, '2021-08-26 17:27:32', 1),
(145, 0, 86, 0, '86CHT008900001', 'MERGE_NONFULLY', 2, 'PACKED', '2021-08-21 00:34:26', 1, '2021-08-27 01:32:21', 1),
(146, 0, 94, 0, '94CHT008900000', 'MERGE_FULLY', 10, 'CREATED', '2021-08-21 14:26:21', 1, '2021-08-21 14:26:21', 1),
(147, 0, 94, 0, '94CHT008900001', 'MERGE_NONFULLY', 2, 'CREATED', '2021-08-21 14:26:21', 1, '2021-08-21 14:26:21', 1),
(148, 0, 95, 0, '95CHT008900000', 'MERGE_FULLY', 10, 'PACKED', '2021-08-21 14:30:04', 1, '2021-08-21 14:30:39', 1),
(149, 0, 95, 0, '95CHT008900001', 'MERGE_NONFULLY', 2, 'PACKED', '2021-08-21 14:30:04', 1, '2021-08-21 14:30:56', 1),
(150, 0, 87, 0, '87CHT008900000', 'MERGE_FULLY', 10, 'PACKED', '2021-08-21 14:50:08', 1, '2021-08-21 14:50:49', 1),
(151, 0, 87, 0, '87CHT008900001', 'MERGE_NONFULLY', 2, 'PACKED', '2021-08-21 14:50:08', 1, '2021-08-21 14:51:01', 1),
(152, 0, 98, 0, '98CHT008900000', 'MERGE_FULLY', 10, 'CREATED', '2021-08-27 22:28:54', 1, '2021-08-27 22:28:56', 1),
(153, 0, 98, 0, '98CHT008900001', 'MERGE_NONFULLY', 2, 'CREATED', '2021-08-27 22:29:00', 1, '2021-08-27 22:29:00', 1),
(154, 0, 99, 0, '8850029025292', 'MERGE_FULLY', 10, 'PACKED', '2021-08-27 22:35:39', 1, '2021-08-28 01:58:37', 1),
(155, 0, 99, 0, '8853603009978', 'MERGE_NONFULLY', 2, 'PACKED', '2021-08-27 22:35:40', 1, '2021-08-28 01:59:02', 1),
(156, 0, 101, 0, '6952060232355', 'MERGE_FULLY', 10, 'CREATED', '2021-08-28 11:55:30', 1, '2021-09-02 15:44:49', 1),
(157, 0, 101, 0, '8851989022550', 'MERGE_NONFULLY', 2, 'CREATED', '2021-08-28 11:55:31', 1, '2021-09-02 15:57:10', 1),
(158, 0, 97, 0, '97CHT008900000', 'MERGE_FULLY', 10, 'CREATED', '2021-08-28 19:38:46', 1, '2021-08-28 19:38:46', 1),
(159, 0, 97, 0, '97CHT008900001', 'MERGE_NONFULLY', 2, 'CREATED', '2021-08-28 19:38:46', 1, '2021-08-28 19:38:46', 1),
(160, 0, 112, 0, '112CHT008900000', 'MERGE_FULLY', 10, 'CREATED', '2021-08-28 20:26:12', 1, '2021-09-02 01:10:57', 1),
(161, 0, 112, 0, '112CHT008900001', 'MERGE_NONFULLY', 2, 'CREATED', '2021-08-28 20:26:12', 1, '2021-09-02 00:53:07', 1),
(162, 0, 114, 0, '114CHT008900000', 'MERGE_FULLY', 10, 'PACKED', '2021-08-29 14:29:38', 1, '2021-08-29 14:29:38', 1),
(163, 0, 114, 0, '114CHT008900001', 'MERGE_NONFULLY', 2, 'PACKED', '2021-08-29 14:29:38', 1, '2021-08-29 14:29:38', 1),
(164, 0, 115, 0, '6952060232355', 'MERGE_FULLY', 10, 'CREATED', '2021-08-29 14:34:53', 1, '2021-09-02 15:44:49', 1),
(165, 0, 115, 0, '8851989022550', 'MERGE_NONFULLY', 2, 'CREATED', '2021-08-29 14:34:54', 1, '2021-09-02 15:57:10', 1),
(166, 0, 118, 0, '8851989022550', 'MERGE_FULLY', 10, 'CREATED', '2021-09-02 08:52:03', 1, '2021-09-02 15:57:10', 1),
(167, 0, 118, 0, '6952060232355', 'MERGE_NONFULLY', 2, 'CREATED', '2021-09-02 08:52:03', 1, '2021-09-02 15:44:49', 1),
(168, 0, 116, 0, '116CHT008900000', 'MERGE_FULLY', 10, 'CREATED', '2021-09-02 15:11:58', 1, '2021-09-02 15:11:58', 1),
(169, 0, 116, 0, '116CHT008900001', 'MERGE_NONFULLY', 2, 'CREATED', '2021-09-02 15:11:58', 1, '2021-09-02 15:11:58', 1);

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
(1, '21506NSB10B', 1, 35, 1, 0, 'PRINTED', '2021-08-03 16:45:23', 0, '2021-08-19 23:26:39', 1),
(2, '21506NSB10C', 1, 43, 1, 0, 'PRINTED', '2021-08-03 16:45:38', 0, '2021-08-19 23:26:44', 1),
(5, '21513K20J13B', 2, 68, 1, 0, 'PRINTED', '2021-08-03 16:45:51', 0, '2021-08-19 23:26:57', 1),
(6, '21513K20J11B', 3, 158, 1, 0, 'PRINTED', '2021-08-03 16:45:56', 0, '2021-08-19 23:26:48', 1),
(9, '123456789', 1, 50, 1, 0, 'PRINTED', '2021-09-06 20:19:09', 1, '2021-09-06 20:19:20', 1);

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

--
-- Dumping data for table `merge_packs`
--

INSERT INTO `merge_packs` (`id`, `merge_no`, `product_id`, `merge_status`, `created_at`, `created_user_id`, `updated_at`, `updated_user_id`) VALUES
(72, 'M00018', 1, 'MERGING', '2021-08-19 23:27:54', 1, '2021-09-06 19:38:42', 1),
(73, 'M00073', 3, 'CREATED', '2021-08-20 22:00:52', 1, '2021-08-20 22:00:52', 1),
(74, 'M00074', 2, 'CREATED', '2021-08-20 22:10:11', 1, '2021-08-20 22:20:46', 1),
(75, 'M00075', 2, 'CREATED', '2021-08-20 22:31:25', 1, '2021-08-20 22:31:29', 1),
(76, 'M00076', 3, 'CREATED', '2021-08-20 22:33:06', 1, '2021-08-20 22:33:06', 1),
(77, 'MHAG002400077', 1, 'CREATED', '2021-08-20 22:37:16', 1, '2021-08-20 22:40:21', 1),
(78, 'MCHT008900078', 1, 'COMPLETE', '2021-08-20 22:40:18', 1, '2021-08-26 01:28:44', 1),
(79, 'MCHT008900079', 1, 'CREATED', '2021-08-20 22:45:57', 1, '2021-08-20 22:48:31', 1),
(80, 'M00080', 1, 'MERGING', '2021-08-20 22:48:03', 1, '2021-08-29 12:37:47', 1),
(81, 'M00081', 1, 'CREATED', '2021-08-20 22:49:41', 1, '2021-08-20 22:49:41', 1),
(82, 'MCHT008900082', 1, 'MERGING', '2021-08-20 22:50:26', 1, '2021-08-26 17:43:19', 1),
(83, 'M00083', 1, 'CREATED', '2021-08-20 22:52:15', 1, '2021-08-20 22:52:15', 1),
(84, 'MCHT008900084', 1, 'CREATED', '2021-08-20 22:53:45', 1, '2021-08-20 22:54:36', 1),
(85, 'MCHT008900085', 1, 'MERGING', '2021-08-20 23:20:26', 1, '2021-08-27 01:33:32', 1),
(86, 'MCHT008900086', 1, 'COMPLETE', '2021-08-20 23:21:26', 1, '2021-08-27 01:32:27', 1),
(87, 'MCHT008900087', 1, 'COMPLETE', '2021-08-21 11:19:11', 1, '2021-08-21 15:00:08', 1),
(88, 'MCHT008900088', 1, 'CREATED', '2021-08-21 11:19:42', 1, '2021-08-21 11:19:42', 1),
(89, 'MCHT008900089', 1, 'CREATED', '2021-08-21 11:36:06', 1, '2021-08-21 11:36:07', 1),
(90, 'MCHT008900004', 1, 'CREATED', '2021-08-21 13:57:14', 1, '2021-08-21 13:57:14', 1),
(91, 'MCHT008900004', 1, 'CREATED', '2021-08-21 14:06:15', 1, '2021-08-21 14:06:15', 1),
(92, 'MCHT008900092', 1, 'CREATED', '2021-08-21 14:12:41', 1, '2021-08-21 14:12:41', 1),
(93, 'MCHT008900093', 1, 'REGISTERING', '2021-08-21 14:17:08', 1, '2021-09-02 09:06:43', 1),
(94, 'MCHT008900094', 1, 'REGISTERING', '2021-08-21 14:23:53', 1, '2021-09-06 19:39:06', 1),
(95, 'MCHT008900095', 1, 'COMPLETE', '2021-08-21 14:27:45', 1, '2021-08-21 14:30:59', 1),
(96, 'MHAG002400096', 3, 'CREATED', '2021-08-21 14:47:43', 1, '2021-08-21 14:48:27', 1),
(97, 'MCHT008900097', 1, 'REGISTERING', '2021-08-27 19:52:59', 1, '2021-09-02 14:15:59', 1),
(98, 'MCHT008900098', 1, 'MERGING', '2021-08-27 19:54:01', 1, '2021-08-27 22:26:30', 1),
(99, 'MCHT008900099', 1, 'COMPLETE', '2021-08-27 22:30:40', 1, '2021-08-28 01:59:04', 1),
(100, 'MCHT008900100', 1, 'CREATED', '2021-08-28 02:00:39', 1, '2021-08-28 02:00:39', 1),
(101, 'MCHT008900101', 1, 'COMPLETE', '2021-08-28 11:22:43', 1, '2021-08-28 12:21:41', 1),
(102, 'MCHT008900102', 1, 'CREATED', '2021-08-28 19:41:25', 1, '2021-08-28 19:41:25', 1),
(103, 'MCHT008900103', 1, 'CREATED', '2021-08-28 19:43:48', 1, '2021-08-28 19:43:48', 1),
(104, 'MCHT008900104', 1, 'CREATED', '2021-08-28 19:54:04', 1, '2021-08-28 19:54:04', 1),
(105, 'MCHT008900105', 1, 'CREATED', '2021-08-28 20:06:17', 1, '2021-08-28 20:06:18', 1),
(106, 'MCHT008900106', 1, 'CREATED', '2021-08-28 20:08:58', 1, '2021-08-28 20:08:58', 1),
(107, 'MCHT008900107', 1, 'CREATED', '2021-08-28 20:12:35', 1, '2021-08-28 20:12:35', 1),
(108, 'MCHT008900108', 1, 'CREATED', '2021-08-28 20:12:55', 1, '2021-08-28 20:12:55', 1),
(109, 'MCHT008900109', 1, 'CREATED', '2021-08-28 20:15:25', 1, '2021-08-28 20:15:25', 1),
(110, 'MCHT008900110', 1, 'CREATED', '2021-08-28 20:17:54', 1, '2021-08-28 20:17:54', 1),
(111, 'MCHT008900111', 1, 'MERGING', '2021-08-28 20:18:34', 1, '2021-09-02 08:01:01', 1),
(112, 'MCHT008900112', 1, 'REGISTERING', '2021-08-28 20:25:02', 1, '2021-09-02 00:45:56', 1),
(113, 'MCHT008900113', 1, 'CREATED', '2021-08-29 14:21:53', 1, '2021-08-29 14:21:54', 1),
(114, 'MCHT008900114', 1, 'COMPLETE', '2021-08-29 14:24:04', 1, '2021-08-31 23:14:33', 1),
(115, 'MCHT008900115', 1, 'COMPLETE', '2021-08-29 14:31:54', 1, '2021-08-29 14:39:29', 1),
(116, 'MCHT008900116', 1, 'REGISTERING', '2021-09-02 08:44:08', 1, '2021-09-02 15:12:12', 1),
(117, 'MCHT008900117', 1, 'MERGING', '2021-09-02 08:47:25', 1, '2021-09-02 09:16:41', 1),
(118, 'MCHT008900118', 1, 'REGISTERING', '2021-09-02 08:50:19', 1, '2021-09-02 15:57:05', 1),
(119, 'MCHT008900119', 1, 'CREATED', '2021-09-02 15:56:30', 1, '2021-09-02 15:56:31', 1),
(120, 'MCHT008900120', 1, 'CREATED', '2021-09-06 19:38:31', 1, '2021-09-06 19:38:32', 1);

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

--
-- Dumping data for table `merge_pack_details`
--

INSERT INTO `merge_pack_details` (`id`, `merge_pack_id`, `label_id`, `created_at`, `created_user_id`, `updated_at`, `updated_user_id`) VALUES
(15, 72, 90, '2021-08-20 00:21:45', 1, '2021-08-20 00:21:45', 1),
(17, 72, 81, '2021-08-20 00:38:02', 1, '2021-08-20 00:38:02', 1),
(44, 0, 0, '2021-08-27 21:56:34', 1, '2021-08-27 21:56:35', 1),
(45, 0, 0, '2021-08-27 21:57:08', 1, '2021-08-27 21:57:09', 1),
(46, 98, 0, '2021-08-27 22:02:17', 1, '2021-08-27 22:02:17', 1),
(47, 98, 0, '2021-08-27 22:02:33', 1, '2021-08-27 22:02:37', 1),
(68, 111, 0, '2021-09-02 05:57:56', 1, '2021-09-02 05:57:56', 1),
(69, 111, 0, '2021-09-02 06:00:21', 1, '2021-09-02 06:00:21', 1),
(70, 111, 0, '2021-09-02 06:02:31', 1, '2021-09-02 06:02:31', 1),
(71, 111, 0, '2021-09-02 06:03:41', 1, '2021-09-02 06:03:42', 1),
(92, 118, 91, '2021-09-02 08:51:36', 1, '2021-09-02 08:51:36', 1),
(93, 118, 85, '2021-09-02 08:51:47', 1, '2021-09-02 08:51:47', 1),
(94, 116, 85, '2021-09-02 15:10:03', 1, '2021-09-02 15:10:03', 1),
(95, 116, 85, '2021-09-02 15:10:51', 1, '2021-09-02 15:10:51', 1);

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
(24, '1', 1, 'CREATED', '2021-09-06 00:39:41', 1, '2021-09-06 00:39:41', 1);

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

--
-- Dumping data for table `split_label_details`
--

INSERT INTO `split_label_details` (`id`, `split_label_id`, `label_id`, `created_at`, `created_user_id`, `updated_at`, `updated_user_id`) VALUES
(1, 1, 1, '2021-09-06 00:39:41', 1, '2021-09-06 00:39:41', '0000-00-00 00:00:00'),
(2, 1, 1, '2021-09-06 00:39:41', 1, '2021-09-06 00:39:41', '0000-00-00 00:00:00');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `lots`
--
ALTER TABLE `lots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lot_defects`
--
ALTER TABLE `lot_defects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `merge_packs`
--
ALTER TABLE `merge_packs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `merge_pack_details`
--
ALTER TABLE `merge_pack_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `split_label_details`
--
ALTER TABLE `split_label_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
