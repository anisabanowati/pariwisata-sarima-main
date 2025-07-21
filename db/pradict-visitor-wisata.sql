-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 14, 2025 at 01:18 PM
-- Server version: 5.7.24
-- PHP Version: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pradict-visitor-wisata`
--

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `permission` varchar(255) CHARACTER SET latin1 NOT NULL,
  `createuser` varchar(255) DEFAULT NULL,
  `deleteuser` varchar(255) DEFAULT NULL,
  `createbid` varchar(255) DEFAULT NULL,
  `updatebid` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `permission`, `createuser`, `deleteuser`, `createbid`, `updatebid`) VALUES
(1, 'Superuser', '1', '1', '1', '1'),
(2, 'Admin', '1', NULL, '1', '1'),
(3, 'User', NULL, NULL, '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `sarima_predictions`
--

CREATE TABLE `sarima_predictions` (
  `id` int(11) NOT NULL,
  `nama_wisata` varchar(100) NOT NULL,
  `tahun_prediksi` int(4) NOT NULL,
  `bulan` int(2) NOT NULL,
  `prediksi_pengunjung` int(11) NOT NULL,
  `lower_bound` int(11) DEFAULT NULL,
  `upper_bound` int(11) DEFAULT NULL,
  `model_params` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sarima_predictions`
--

INSERT INTO `sarima_predictions` (`id`, `nama_wisata`, `tahun_prediksi`, `bulan`, `prediksi_pengunjung`, `lower_bound`, `upper_bound`, `model_params`, `created_at`, `created_by`) VALUES
(49, 'Pantai Depok', 2024, 1, 144473, 57775, 231170, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1333083764841,\"bic\":855.244004499463,\"mae\":30396.01801394593,\"is_stationary\":false,\"adf_pvalue\":0.08432355408425835,\"training_data_points\":36}', '2025-07-14 12:34:46', 2),
(50, 'Pantai Depok', 2024, 2, 131282, 14137, 248427, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1333083764841,\"bic\":855.244004499463,\"mae\":30396.01801394593,\"is_stationary\":false,\"adf_pvalue\":0.08432355408425835,\"training_data_points\":36}', '2025-07-14 12:34:46', 2),
(51, 'Pantai Depok', 2024, 3, 119295, -17992, 256583, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1333083764841,\"bic\":855.244004499463,\"mae\":30396.01801394593,\"is_stationary\":false,\"adf_pvalue\":0.08432355408425835,\"training_data_points\":36}', '2025-07-14 12:34:46', 2),
(52, 'Pantai Depok', 2024, 4, 108403, -43517, 260323, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1333083764841,\"bic\":855.244004499463,\"mae\":30396.01801394593,\"is_stationary\":false,\"adf_pvalue\":0.08432355408425835,\"training_data_points\":36}', '2025-07-14 12:34:46', 2),
(53, 'Pantai Depok', 2024, 5, 98505, -64510, 261520, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1333083764841,\"bic\":855.244004499463,\"mae\":30396.01801394593,\"is_stationary\":false,\"adf_pvalue\":0.08432355408425835,\"training_data_points\":36}', '2025-07-14 12:34:46', 2),
(54, 'Pantai Depok', 2024, 6, 89511, -82125, 261148, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1333083764841,\"bic\":855.244004499463,\"mae\":30396.01801394593,\"is_stationary\":false,\"adf_pvalue\":0.08432355408425835,\"training_data_points\":36}', '2025-07-14 12:34:46', 2),
(55, 'Pantai Depok', 2024, 7, 81339, -97104, 259781, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1333083764841,\"bic\":855.244004499463,\"mae\":30396.01801394593,\"is_stationary\":false,\"adf_pvalue\":0.08432355408425835,\"training_data_points\":36}', '2025-07-14 12:34:46', 2),
(56, 'Pantai Depok', 2024, 8, 73912, -109960, 257784, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1333083764841,\"bic\":855.244004499463,\"mae\":30396.01801394593,\"is_stationary\":false,\"adf_pvalue\":0.08432355408425835,\"training_data_points\":36}', '2025-07-14 12:34:46', 2),
(57, 'Pantai Depok', 2024, 9, 67164, -121074, 255402, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1333083764841,\"bic\":855.244004499463,\"mae\":30396.01801394593,\"is_stationary\":false,\"adf_pvalue\":0.08432355408425835,\"training_data_points\":36}', '2025-07-14 12:34:46', 2),
(58, 'Pantai Depok', 2024, 10, 61031, -130736, 252799, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1333083764841,\"bic\":855.244004499463,\"mae\":30396.01801394593,\"is_stationary\":false,\"adf_pvalue\":0.08432355408425835,\"training_data_points\":36}', '2025-07-14 12:34:46', 2),
(59, 'Pantai Depok', 2024, 11, 55459, -139175, 250093, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1333083764841,\"bic\":855.244004499463,\"mae\":30396.01801394593,\"is_stationary\":false,\"adf_pvalue\":0.08432355408425835,\"training_data_points\":36}', '2025-07-14 12:34:46', 2),
(60, 'Pantai Depok', 2024, 12, 50395, -146574, 247365, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1333083764841,\"bic\":855.244004499463,\"mae\":30396.01801394593,\"is_stationary\":false,\"adf_pvalue\":0.08432355408425835,\"training_data_points\":36}', '2025-07-14 12:34:46', 2),
(205, 'Pantai Parangtritis', 2024, 1, 144472, 57776, 231169, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1323865382473,\"bic\":855.2430826612261,\"mae\":30395.79358385747,\"is_stationary\":false,\"adf_pvalue\":0.08432350996183557,\"training_data_points\":36}', '2025-07-14 13:15:00', 2),
(206, 'Pantai Parangtritis', 2024, 2, 131282, 14139, 248426, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1323865382473,\"bic\":855.2430826612261,\"mae\":30395.79358385747,\"is_stationary\":false,\"adf_pvalue\":0.08432350996183557,\"training_data_points\":36}', '2025-07-14 13:15:00', 2),
(207, 'Pantai Parangtritis', 2024, 3, 119296, -17990, 256582, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1323865382473,\"bic\":855.2430826612261,\"mae\":30395.79358385747,\"is_stationary\":false,\"adf_pvalue\":0.08432350996183557,\"training_data_points\":36}', '2025-07-14 13:15:00', 2),
(208, 'Pantai Parangtritis', 2024, 4, 108405, -43514, 260323, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1323865382473,\"bic\":855.2430826612261,\"mae\":30395.79358385747,\"is_stationary\":false,\"adf_pvalue\":0.08432350996183557,\"training_data_points\":36}', '2025-07-14 13:15:00', 2),
(209, 'Pantai Parangtritis', 2024, 5, 98507, -64507, 261522, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1323865382473,\"bic\":855.2430826612261,\"mae\":30395.79358385747,\"is_stationary\":false,\"adf_pvalue\":0.08432350996183557,\"training_data_points\":36}', '2025-07-14 13:15:00', 2),
(210, 'Pantai Parangtritis', 2024, 6, 89514, -82123, 261150, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1323865382473,\"bic\":855.2430826612261,\"mae\":30395.79358385747,\"is_stationary\":false,\"adf_pvalue\":0.08432350996183557,\"training_data_points\":36}', '2025-07-14 13:15:00', 2),
(211, 'Pantai Parangtritis', 2024, 7, 81341, -97101, 259783, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1323865382473,\"bic\":855.2430826612261,\"mae\":30395.79358385747,\"is_stationary\":false,\"adf_pvalue\":0.08432350996183557,\"training_data_points\":36}', '2025-07-14 13:15:00', 2),
(212, 'Pantai Parangtritis', 2024, 8, 73915, -109958, 257787, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1323865382473,\"bic\":855.2430826612261,\"mae\":30395.79358385747,\"is_stationary\":false,\"adf_pvalue\":0.08432350996183557,\"training_data_points\":36}', '2025-07-14 13:15:00', 2),
(213, 'Pantai Parangtritis', 2024, 9, 67166, -121072, 255404, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1323865382473,\"bic\":855.2430826612261,\"mae\":30395.79358385747,\"is_stationary\":false,\"adf_pvalue\":0.08432350996183557,\"training_data_points\":36}', '2025-07-14 13:15:00', 2),
(214, 'Pantai Parangtritis', 2024, 10, 61034, -130734, 252802, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1323865382473,\"bic\":855.2430826612261,\"mae\":30395.79358385747,\"is_stationary\":false,\"adf_pvalue\":0.08432350996183557,\"training_data_points\":36}', '2025-07-14 13:15:00', 2),
(215, 'Pantai Parangtritis', 2024, 11, 55462, -139173, 250097, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1323865382473,\"bic\":855.2430826612261,\"mae\":30395.79358385747,\"is_stationary\":false,\"adf_pvalue\":0.08432350996183557,\"training_data_points\":36}', '2025-07-14 13:15:00', 2),
(216, 'Pantai Parangtritis', 2024, 12, 50398, -146572, 247369, '{\"order\":[1,0,0],\"seasonal_order\":[0,0,0,12],\"aic\":852.1323865382473,\"bic\":855.2430826612261,\"mae\":30395.79358385747,\"is_stationary\":false,\"adf_pvalue\":0.08432350996183557,\"training_data_points\":36}', '2025-07-14 13:15:00', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tourism_data`
--

CREATE TABLE `tourism_data` (
  `ID` int(11) NOT NULL,
  `NamaWisata` varchar(100) NOT NULL,
  `JumlahPengunjung` int(11) NOT NULL,
  `Pendapatan` decimal(15,2) NOT NULL,
  `RentangWaktu` varchar(50) NOT NULL,
  `Tanggal` date NOT NULL,
  `CreatedBy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tourism_data`
--

INSERT INTO `tourism_data` (`ID`, `NamaWisata`, `JumlahPengunjung`, `Pendapatan`, `RentangWaktu`, `Tanggal`, `CreatedBy`) VALUES
(12, 'Pantai Parangtritis', 99281, '967989750.00', 'Bulanan', '2023-04-28', NULL),
(13, 'Pantai Parangtritis', 111515, '1087271250.00', 'Bulanan', '2023-01-28', NULL),
(14, 'Pantai Parangtritis', 77497, '755425125.00', 'Bulanan', '2023-02-28', NULL),
(15, 'Pantai Parangtritis', 64901, '632789625.00', 'Bulanan', '2023-03-28', NULL),
(16, 'Pantai Parangtritis', 96677, '942600750.00', 'Bulanan', '2023-05-28', NULL),
(17, 'Pantai Parangtritis', 97941, '954924750.00', 'Bulanan', '2023-06-28', NULL),
(18, 'Pantai Parangtritis', 97372, '949377000.00', 'Bulanan', '2023-07-28', NULL),
(19, 'Pantai Parangtritis', 60039, '585380250.00', 'Bulanan', '2023-08-28', NULL),
(20, 'Pantai Parangtritis', 79580, '775909875.00', 'Bulanan', '2023-09-28', NULL),
(21, 'Pantai Parangtritis', 86527, '843639250.00', 'Bulanan', '2023-10-28', NULL),
(22, 'Pantai Parangtritis', 79639, '776470500.00', 'Bulanan', '2023-11-28', NULL),
(23, 'Pantai Parangtritis', 158988, '1550137875.00', 'Bulanan', '2023-12-28', NULL),
(24, 'Pantai Depok', 111515, '1087271250.00', 'Bulanan', '2023-01-28', NULL),
(25, 'Pantai Depok', 77462, '755425125.00', 'Bulanan', '2023-02-28', NULL),
(26, 'Pantai Depok', 64902, '632789625.00', 'Bulanan', '2023-03-28', NULL),
(27, 'Pantai Depok', 99281, '967989750.00', 'Bulanan', '2023-04-28', NULL),
(28, 'Pantai Depok', 96677, '942600750.00', 'Bulanan', '2023-05-28', NULL),
(29, 'Pantai Depok', 97941, '954924750.00', 'Bulanan', '2023-06-28', NULL),
(30, 'Pantai Depok', 97372, '949377000.00', 'Bulanan', '2023-07-28', NULL),
(31, 'Pantai Depok', 60039, '585380250.00', 'Bulanan', '2023-08-28', NULL),
(32, 'Pantai Depok', 79581, '775909875.00', 'Bulanan', '2023-09-28', NULL),
(33, 'Pantai Depok', 86527, '843638250.00', 'Bulanan', '2023-10-28', NULL),
(34, 'Pantai Depok', 79638, '776470500.00', 'Bulanan', '2023-11-28', NULL),
(35, 'Pantai Depok', 158989, '1550137875.00', 'Bulanan', '2023-12-28', NULL),
(36, 'Pantai Samas', 18617, '181515750.00', 'Bulanan', '2023-01-28', NULL),
(37, 'Pantai Samas', 19068, '185913000.00', 'Bulanan', '2023-02-28', NULL),
(38, 'Pantai Samas', 18934, '184606500.00', 'Bulanan', '2023-03-28', NULL),
(39, 'Pantai Samas', 38136, '371826000.00', 'Bulanan', '2023-04-28', NULL),
(40, 'Pantai Samas', 31312, '305292000.00', 'Bulanan', '2023-05-28', NULL),
(41, 'Pantai Samas', 28392, '276822000.00', 'Bulanan', '2023-06-28', NULL),
(42, 'Pantai Samas', 28646, '279298500.00', 'Bulanan', '2023-07-28', NULL),
(43, 'Pantai Samas', 17469, '170322750.00', 'Bulanan', '2023-08-28', NULL),
(44, 'Pantai Samas', 23873, '232761750.00', 'Bulanan', '2023-09-28', NULL),
(45, 'Pantai Samas', 24402, '237919500.00', 'Bulanan', '2023-10-28', NULL),
(47, 'Pantai Samas', 21054, '205276500.00', 'Bulanan', '2023-11-28', NULL),
(48, 'Pantai Samas', 33157, '323280750.00', 'Bulanan', '2023-12-28', NULL),
(49, 'Pantai Goa Cemara', 4314, '42061500.00', 'Bulanan', '2023-01-28', NULL),
(50, 'Pantai Goa Cemara', 1958, '19090500.00', 'Bulanan', '2023-02-28', NULL),
(51, 'Pantai Goa Cemara', 1143, '11144250.00', 'Bulanan', '2023-03-28', NULL),
(52, 'Pantai Goa Cemara', 5630, '54892500.00', 'Bulanan', '2023-04-28', NULL),
(53, 'Pantai Goa Cemara', 2976, '29016000.00', 'Bulanan', '2023-05-28', NULL),
(54, 'Pantai Goa Cemara', 1779, '17345250.00', 'Bulanan', '2023-06-28', NULL),
(55, 'Pantai Goa Cemara', 2171, '21167250.00', 'Bulanan', '2023-07-28', NULL),
(56, 'Pantai Goa Cemara', 1365, '13308750.00', 'Bulanan', '2023-08-28', NULL),
(57, 'Pantai Goa Cemara', 1862, '18154500.00', 'Bulanan', '2023-09-28', NULL),
(58, 'Pantai Goa Cemara', 2295, '22376250.00', 'Bulanan', '2023-10-28', NULL),
(59, 'Pantai Goa Cemara', 2137, '20835750.00', 'Bulanan', '2023-11-28', NULL),
(60, 'Pantai Goa Cemara', 4419, '43085250.00', 'Bulanan', '2023-12-28', NULL),
(61, 'Pantai Kwaru', 0, '0.00', 'Bulanan', '2023-01-28', NULL),
(62, 'Pantai Kwaru', 760, '7410000.00', 'Bulanan', '2023-02-28', NULL),
(63, 'Pantai Kwaru', 1081, '10539750.00', 'Bulanan', '2023-03-28', NULL),
(64, 'Pantai Kwaru', 5161, '50319750.00', 'Bulanan', '2023-04-28', NULL),
(65, 'Pantai Kwaru', 2157, '21030750.00', 'Bulanan', '2023-05-28', NULL),
(66, 'Pantai Kwaru', 1366, '13318500.00', 'Bulanan', '2023-06-28', NULL),
(67, 'Pantai Kwaru', 2000, '19500000.00', 'Bulanan', '2023-07-28', NULL),
(68, 'Pantai Kwaru', 1070, '10432500.00', 'Bulanan', '2023-08-28', NULL),
(69, 'Pantai Kwaru', 1688, '16458000.00', 'Bulanan', '2023-09-28', NULL),
(70, 'Pantai Kwaru', 1794, '17491500.00', 'Bulanan', '2023-10-28', NULL),
(71, 'Pantai Kwaru', 1260, '12285000.00', 'Bulanan', '2023-11-28', NULL),
(72, 'Pantai Kwaru', 2770, '27007500.00', 'Bulanan', '2023-12-28', NULL),
(73, 'Pantai Pandansimo', 10502, '102394500.00', 'Bulanan', '2023-01-28', NULL),
(74, 'Pantai Pandansimo', 5343, '52094250.00', 'Bulanan', '2023-02-28', NULL),
(75, 'Pantai Pandansimo', 4437, '43260750.00', 'Bulanan', '2023-03-28', NULL),
(76, 'Pantai Pandansimo', 19305, '188223750.00', 'Bulanan', '2023-04-28', NULL),
(77, 'Pantai Pandansimo', 10536, '102726000.00', 'Bulanan', '2023-05-28', NULL),
(78, 'Pantai Pandansimo', 8972, '87477000.00', 'Bulanan', '2023-06-28', NULL),
(79, 'Pantai Pandansimo', 8627, '84113250.00', 'Bulanan', '2023-07-28', NULL),
(80, 'Pantai Pandansimo', 4403, '42929250.00', 'Bulanan', '2023-08-28', NULL),
(81, 'Pantai Pandansimo', 6187, '60323250.00', 'Bulanan', '2023-09-28', NULL),
(82, 'Pantai Pandansimo', 6425, '62643750.00', 'Bulanan', '2023-10-28', NULL),
(83, 'Pantai Pandansimo', 5379, '52445250.00', 'Bulanan', '2023-11-28', NULL),
(84, 'Pantai Pandansimo', 11824, '115284000.00', 'Bulanan', '2023-12-28', NULL),
(85, 'Kawasan Goa Selarong', 2503, '14392250.00', 'Bulanan', '2023-01-28', NULL),
(86, 'Kawasan Goa Selarong', 1253, '7204750.00', 'Bulanan', '2023-02-28', NULL),
(87, 'Kawasan Goa Selarong', 1105, '6353750.00', 'Bulanan', '2023-03-28', NULL),
(88, 'Kawasan Goa Selarong', 1576, '9062000.00', 'Bulanan', '2023-04-28', NULL),
(89, 'Kawasan Goa Selarong', 1850, '10637500.00', 'Bulanan', '2023-05-28', NULL),
(90, 'Kawasan Goa Selarong', 1475, '8481250.00', 'Bulanan', '2023-06-28', NULL),
(91, 'Kawasan Goa Selarong', 1663, '9562250.00', 'Bulanan', '2023-07-28', NULL),
(92, 'Kawasan Goa Selarong', 941, '5410750.00', 'Bulanan', '2023-08-28', NULL),
(93, 'Kawasan Goa Selarong', 1835, '10551250.00', 'Bulanan', '2023-09-28', NULL),
(94, 'Kawasan Goa Selarong', 1264, '7268000.00', 'Bulanan', '2023-10-28', NULL),
(95, 'Kawasan Goa Selarong', 1171, '6733250.00', 'Bulanan', '2023-11-28', NULL),
(96, 'Kawasan Goa Selarong', 1953, '11229750.00', 'Bulanan', '2023-12-28', NULL),
(97, 'Kawasan Goa Cerme', 1657, '9527750.00', 'Bulanan', '2023-01-28', NULL),
(98, 'Kawasan Goa Cerme', 251, '1443250.00', 'Bulanan', '2023-02-28', NULL),
(99, 'Kawasan Goa Cerme', 282, '1621500.00', 'Bulanan', '2023-03-28', NULL),
(100, 'Kawasan Goa Cerme', 365, '2098750.00', 'Bulanan', '2023-04-28', NULL),
(101, 'Kawasan Goa Cerme', 344, '1978000.00', 'Bulanan', '2023-05-28', NULL),
(102, 'Kawasan Goa Cerme', 396, '2277000.00', 'Bulanan', '2023-06-28', NULL),
(103, 'Kawasan Goa Cerme', 348, '2001000.00', 'Bulanan', '2023-07-28', NULL),
(104, 'Kawasan Goa Cerme', 323, '1857250.00', 'Bulanan', '2023-08-28', NULL),
(105, 'Kawasan Goa Cerme', 310, '1782500.00', 'Bulanan', '2023-09-28', NULL),
(106, 'Kawasan Goa Cerme', 340, '1955000.00', 'Bulanan', '2023-10-28', NULL),
(107, 'Kawasan Goa Cerme', 311, '1788250.00', 'Bulanan', '2023-11-28', NULL),
(108, 'Kawasan Goa Cerme', 290, '1667500.00', 'Bulanan', '2023-12-28', NULL),
(109, 'Pantai Parangtritis', 130900, '1276275000.00', 'Bulanan', '2022-01-28', NULL),
(110, 'Pantai Parangtritis', 93975, '916256250.00', 'Bulanan', '2022-02-22', NULL),
(111, 'Pantai Parangtritis', 96275, '938681250.00', 'Bulanan', '2022-03-27', NULL),
(112, 'Pantai Parangtritis', 21700, '211575000.00', 'Bulanan', '2022-04-28', NULL),
(113, 'Pantai Parangtritis', 194650, '1897837500.00', 'Bulanan', '2022-05-28', NULL),
(114, 'Pantai Parangtritis', 112977, '1101525750.00', 'Bulanan', '2022-06-21', NULL),
(115, 'Pantai Parangtritis', 115984, '1130848875.00', 'Bulanan', '2022-07-28', NULL),
(116, 'Pantai Parangtritis', 78250, '762937500.00', 'Bulanan', '2022-08-16', NULL),
(117, 'Pantai Parangtritis', 74388, '725287875.00', 'Bulanan', '2022-09-22', NULL),
(118, 'Pantai Parangtritis', 80862, '788404500.00', 'Bulanan', '2022-10-28', NULL),
(119, 'Pantai Parangtritis', 63248, '616668000.00', 'Bulanan', '2022-11-28', NULL),
(120, 'Pantai Parangtritis', 118780, '1157715000.00', 'Bulanan', '2022-12-20', NULL),
(121, 'Pantai Depok', 130900, '1276275000.00', 'Bulanan', '2022-01-28', NULL),
(122, 'Pantai Depok', 93975, '916256250.00', 'Bulanan', '2022-02-28', NULL),
(123, 'Pantai Depok', 96275, '938681250.00', 'Bulanan', '2022-03-28', NULL),
(124, 'Pantai Depok', 21700, '211575000.00', 'Bulanan', '2022-04-28', NULL),
(125, 'Pantai Depok', 194650, '1897837500.00', 'Bulanan', '2022-05-28', NULL),
(126, 'Pantai Depok', 112977, '1101525750.00', 'Bulanan', '2022-06-28', NULL),
(127, 'Pantai Depok', 115985, '1130848875.00', 'Bulanan', '2022-07-28', NULL),
(128, 'Pantai Depok', 78250, '762937500.00', 'Bulanan', '2022-08-28', NULL),
(129, 'Pantai Depok', 74389, '725287875.00', 'Bulanan', '2022-09-08', NULL),
(130, 'Pantai Depok', 80862, '788404500.00', 'Bulanan', '2022-10-14', NULL),
(131, 'Pantai Depok', 63248, '616668000.00', 'Bulanan', '2022-11-22', NULL),
(132, 'Pantai Depok', 118780, '1157715000.00', 'Bulanan', '2022-12-10', NULL),
(133, 'Pantai Samas', 23600, '230100000.00', 'Bulanan', '2022-01-14', NULL),
(134, 'Pantai Samas', 14500, '141375000.00', 'Bulanan', '2022-02-28', NULL),
(135, 'Pantai Samas', 15500, '151125000.00', 'Bulanan', '2022-03-29', NULL),
(136, 'Pantai Samas', 3100, '30225000.00', 'Bulanan', '2022-04-27', NULL),
(137, 'Pantai Samas', 50400, '491400000.00', 'Bulanan', '2022-05-28', NULL),
(138, 'Pantai Samas', 16700, '162825000.00', 'Bulanan', '2022-06-28', NULL),
(139, 'Pantai Samas', 15800, '154050000.00', 'Bulanan', '2022-07-28', NULL),
(140, 'Pantai Samas', 15950, '155512500.00', 'Bulanan', '2022-08-28', NULL),
(141, 'Pantai Samas', 11850, '115537500.00', 'Bulanan', '2022-09-28', NULL),
(142, 'Pantai Samas', 12300, '119925000.00', 'Bulanan', '2022-10-28', NULL),
(143, 'Pantai Samas', 12630, '123142500.00', 'Bulanan', '2022-11-28', NULL),
(144, 'Pantai Samas', 15781, '153864750.00', 'Bulanan', '2022-12-28', NULL),
(145, 'Pantai Goa Cemara', 1785, '17403750.00', 'Bulanan', '2022-01-28', NULL),
(146, 'Pantai Goa Cemara', 1055, '10286250.00', 'Bulanan', '2022-02-28', NULL),
(147, 'Pantai Goa Cemara', 1165, '11358750.00', 'Bulanan', '2022-03-28', NULL),
(148, 'Pantai Goa Cemara', 311, '3032250.00', 'Bulanan', '2022-04-28', NULL),
(149, 'Pantai Goa Cemara', 8324, '81159000.00', 'Bulanan', '2022-05-28', NULL),
(150, 'Pantai Goa Cemara', 1271, '12392250.00', 'Bulanan', '2022-06-28', NULL),
(151, 'Pantai Goa Cemara', 1026, '10003500.00', 'Bulanan', '2022-07-28', NULL),
(152, 'Pantai Goa Cemara', 872, '8502000.00', 'Bulanan', '2022-08-28', NULL),
(153, 'Pantai Goa Cemara', 862, '8404500.00', 'Bulanan', '2022-09-28', NULL),
(154, 'Pantai Goa Cemara', 1247, '12158250.00', 'Bulanan', '2022-10-28', NULL),
(155, 'Pantai Goa Cemara', 833, '8121750.00', 'Bulanan', '2022-11-28', NULL),
(156, 'Pantai Goa Cemara', 2833, '27621750.00', 'Bulanan', '2022-12-28', NULL),
(157, 'Pantai Kwaru', 2871, '27992250.00', 'Bulanan', '2022-01-28', NULL),
(158, 'Pantai Kwaru', 1346, '13123500.00', 'Bulanan', '2022-02-28', NULL),
(159, 'Pantai Kwaru', 1416, '13806000.00', 'Bulanan', '2022-03-28', NULL),
(160, 'Pantai Kwaru', 412, '4017000.00', 'Bulanan', '2022-04-28', NULL),
(161, 'Pantai Kwaru', 6510, '63472500.00', 'Bulanan', '2022-05-28', NULL),
(162, 'Pantai Kwaru', 1334, '13006500.00', 'Bulanan', '2022-06-28', NULL),
(163, 'Pantai Kwaru', 1260, '12285000.00', 'Bulanan', '2022-07-28', NULL),
(164, 'Pantai Kwaru', 986, '9613500.00', 'Bulanan', '2022-08-28', NULL),
(165, 'Pantai Kwaru', 964, '9399000.00', 'Bulanan', '2022-09-28', NULL),
(166, 'Pantai Kwaru', 1915, '18671250.00', 'Bulanan', '2022-10-28', NULL),
(167, 'Pantai Kwaru', 850, '8287500.00', 'Bulanan', '2022-11-28', NULL),
(168, 'Pantai Kwaru', 950, '9262500.00', 'Bulanan', '2022-12-28', NULL),
(169, 'Pantai Pandansimo', 11100, '108225000.00', 'Bulanan', '2022-01-28', NULL),
(170, 'Pantai Pandansimo', 6400, '62400000.00', 'Bulanan', '2022-02-28', NULL),
(171, 'Pantai Pandansimo', 6300, '61425000.00', 'Bulanan', '2022-03-28', NULL),
(172, 'Pantai Pandansimo', 1380, '13455000.00', 'Bulanan', '2022-04-28', NULL),
(173, 'Pantai Pandansimo', 25960, '253110000.00', 'Bulanan', '2022-05-28', NULL),
(174, 'Pantai Pandansimo', 6880, '67080000.00', 'Bulanan', '2022-06-28', NULL),
(175, 'Pantai Pandansimo', 7155, '69761250.00', 'Bulanan', '2022-07-28', NULL),
(176, 'Pantai Pandansimo', 4065, '39633750.00', 'Bulanan', '2022-08-28', NULL),
(177, 'Pantai Pandansimo', 4060, '39585000.00', 'Bulanan', '2022-09-28', NULL),
(178, 'Pantai Pandansimo', 4457, '43455750.00', 'Bulanan', '2022-10-28', NULL),
(179, 'Pantai Pandansimo', 3763, '36689250.00', 'Bulanan', '2022-11-28', NULL),
(180, 'Pantai Pandansimo', 6480, '63180000.00', 'Bulanan', '2022-12-28', NULL),
(181, 'Kawasan Goa Selarong', 2390, '13742500.00', 'Bulanan', '2022-01-28', NULL),
(182, 'Kawasan Goa Selarong', 1558, '8958500.00', 'Bulanan', '2022-02-28', NULL),
(183, 'Kawasan Goa Selarong', 1604, '9223000.00', 'Bulanan', '2022-03-28', NULL),
(184, 'Kawasan Goa Selarong', 399, '2294250.00', 'Bulanan', '2022-04-28', NULL),
(185, 'Kawasan Goa Selarong', 3675, '21131250.00', 'Bulanan', '2022-05-28', NULL),
(186, 'Kawasan Goa Selarong', 2138, '12293500.00', 'Bulanan', '2022-06-28', NULL),
(187, 'Kawasan Goa Selarong', 2093, '12034750.00', 'Bulanan', '2022-07-28', NULL),
(188, 'Kawasan Goa Selarong', 1387, '7975250.00', 'Bulanan', '2022-08-28', NULL),
(189, 'Kawasan Goa Selarong', 1087, '6250250.00', 'Bulanan', '2022-09-28', NULL),
(190, 'Kawasan Goa Selarong', 3284, '18883000.00', 'Bulanan', '2022-10-28', NULL),
(191, 'Kawasan Goa Selarong', 1163, '6687250.00', 'Bulanan', '2022-11-28', NULL),
(192, 'Kawasan Goa Selarong', 1624, '9338000.00', 'Bulanan', '2022-12-28', NULL),
(193, 'Kawasan Goa Cerme', 674, '1937750.00', 'Bulanan', '2022-01-28', NULL),
(194, 'Kawasan Goa Cerme', 536, '1541000.00', 'Bulanan', '2022-02-28', NULL),
(195, 'Kawasan Goa Cerme', 726, '2087250.00', 'Bulanan', '2022-03-28', NULL),
(196, 'Kawasan Goa Cerme', 238, '684250.00', 'Bulanan', '2022-04-28', NULL),
(197, 'Kawasan Goa Cerme', 850, '2443750.00', 'Bulanan', '2022-05-28', NULL),
(198, 'Kawasan Goa Cerme', 396, '1138500.00', 'Bulanan', '2022-06-28', NULL),
(199, 'Kawasan Goa Cerme', 440, '1265000.00', 'Bulanan', '2022-07-28', NULL),
(200, 'Kawasan Goa Cerme', 436, '1253500.00', 'Bulanan', '2022-08-28', NULL),
(201, 'Kawasan Goa Cerme', 376, '1081000.00', 'Bulanan', '2022-09-29', NULL),
(202, 'Kawasan Goa Cerme', 392, '1127000.00', 'Bulanan', '2022-10-28', NULL),
(203, 'Kawasan Goa Cerme', 700, '2012500.00', 'Bulanan', '2022-11-28', NULL),
(204, 'Kawasan Goa Cerme', 646, '1857250.00', 'Bulanan', '2022-12-28', NULL),
(205, 'Pantai Parangtritis', 54120, '527670000.00', 'Bulanan', '2021-01-31', NULL),
(206, 'Pantai Parangtritis', 39380, '383955000.00', 'Bulanan', '2021-02-28', NULL),
(207, 'Pantai Parangtritis', 65300, '636675000.00', 'Bulanan', '2021-03-31', NULL),
(208, 'Pantai Parangtritis', 53030, '517042500.00', 'Bulanan', '2021-04-30', NULL),
(209, 'Pantai Parangtritis', 90670, '884037375.00', 'Bulanan', '2021-05-31', NULL),
(210, 'Pantai Parangtritis', 53829, '524837625.00', 'Bulanan', '2021-06-30', NULL),
(211, 'Pantai Parangtritis', 1521, '14829750.00', 'Bulanan', '2021-07-31', NULL),
(212, 'Pantai Parangtritis', 0, '0.00', 'Bulanan', '2021-08-31', NULL),
(213, 'Pantai Parangtritis', 0, '0.00', 'Bulanan', '2021-09-30', NULL),
(214, 'Pantai Parangtritis', 41011, '399862125.00', 'Bulanan', '2021-10-31', NULL),
(215, 'Pantai Parangtritis', 72593, '707786625.00', 'Bulanan', '2021-11-30', NULL),
(216, 'Pantai Parangtritis', 119544, '1165554000.00', 'Bulanan', '2021-12-31', NULL),
(217, 'Pantai Depok', 54120, '527670000.00', 'Bulanan', '2021-01-31', NULL),
(218, 'Pantai Depok', 39380, '383955000.00', 'Bulanan', '2021-02-28', NULL),
(219, 'Pantai Depok', 65300, '636675000.00', 'Bulanan', '2021-03-31', NULL),
(220, 'Pantai Depok', 53030, '517042500.00', 'Bulanan', '2021-04-30', NULL),
(221, 'Pantai Depok', 90671, '884037375.00', 'Bulanan', '2021-05-31', NULL),
(222, 'Pantai Depok', 53830, '524837625.00', 'Bulanan', '2021-06-30', NULL),
(223, 'Pantai Depok', 1521, '14829750.00', 'Bulanan', '2021-07-31', 2),
(224, 'Pantai Depok', 0, '0.00', 'Bulanan', '2021-08-31', 2),
(225, 'Pantai Depok', 0, '0.00', 'Bulanan', '2021-09-30', 2),
(226, 'Pantai Depok', 41012, '399862125.00', 'Bulanan', '2021-10-31', 2),
(227, 'Pantai Depok', 72594, '707786625.00', 'Bulanan', '2021-11-30', 2),
(228, 'Pantai Depok', 119544, '1165554000.00', 'Bulanan', '2021-12-31', 2),
(229, 'Pantai Samas', 10000, '97500000.00', 'Bulanan', '2021-01-31', 2),
(230, 'Pantai Samas', 6200, '60450000.00', 'Bulanan', '2021-02-28', 2),
(231, 'Pantai Samas', 10250, '99937500.00', 'Bulanan', '2021-03-31', 2),
(232, 'Pantai Samas', 7290, '71077500.00', 'Bulanan', '2021-04-30', 2),
(233, 'Pantai Samas', 24315, '237071250.00', 'Bulanan', '2021-05-31', 2),
(234, 'Pantai Samas', 9800, '95550000.00', 'Bulanan', '2021-06-30', 2),
(235, 'Pantai Samas', 200, '1950000.00', 'Bulanan', '2021-07-31', 2),
(236, 'Pantai Samas', 0, '0.00', 'Bulanan', '2021-08-31', 2),
(237, 'Pantai Samas', 0, '0.00', 'Bulanan', '2021-09-30', 2),
(238, 'Pantai Samas', 7900, '77025000.00', 'Bulanan', '2021-10-31', 2),
(239, 'Pantai Samas', 12745, '124263750.00', 'Bulanan', '2021-11-30', 2),
(240, 'Pantai Samas', 16800, '163800000.00', 'Bulanan', '2021-12-31', 2),
(241, 'Pantai Parangtritis', 2345, '20000000.00', 'Bulanan', '2025-07-02', 38);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `Staffid` varchar(255) DEFAULT NULL,
  `AdminName` varchar(120) DEFAULT NULL,
  `UserName` varchar(120) DEFAULT NULL,
  `FirstName` varchar(255) DEFAULT NULL,
  `LastName` varchar(255) DEFAULT NULL,
  `MobileNumber` varchar(255) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Status` int(11) NOT NULL DEFAULT '1',
  `Photo` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT 'avatar15.jpg',
  `Password` varchar(120) DEFAULT NULL,
  `AdminRegdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `Staffid`, `AdminName`, `UserName`, `FirstName`, `LastName`, `MobileNumber`, `Email`, `Status`, `Photo`, `Password`, `AdminRegdate`) VALUES
(2, 'C001', 'Admin', 'admin', 'Super', 'Admin ', 'wisdom', 'useradmin', 1, 'user.png', '21232f297a57a5a743894a0e4a801fc3', '2020-07-21 10:18:39'),
(38, '2', 'User', 'nisa', 'nisa', 'banowati', 'Pantai Parangtritis', 'nisa', 1, 'avatar15.jpg', '827ccb0eea8a706c4c34a16891f84e7b', '2025-06-30 02:30:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sarima_predictions`
--
ALTER TABLE `sarima_predictions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wisata_tahun` (`nama_wisata`,`tahun_prediksi`);

--
-- Indexes for table `tourism_data`
--
ALTER TABLE `tourism_data`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sarima_predictions`
--
ALTER TABLE `sarima_predictions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=217;

--
-- AUTO_INCREMENT for table `tourism_data`
--
ALTER TABLE `tourism_data`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=242;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
