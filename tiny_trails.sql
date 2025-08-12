-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 12, 2025 at 08:11 AM
-- Server version: 10.6.22-MariaDB-cll-lve-log
-- PHP Version: 8.3.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zanagyrh_tiny_trails`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `state_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `state_id`, `name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Gillianhaven', 'active', '2025-08-03 01:21:58', '2025-08-03 01:21:58', NULL),
(2, 2, 'West Mavis', 'active', '2025-08-03 01:21:58', '2025-08-03 01:21:58', NULL),
(3, 3, 'New Louisa', 'active', '2025-08-03 01:21:58', '2025-08-03 01:21:58', NULL),
(4, 4, 'North Dorthy', 'active', '2025-08-03 01:21:58', '2025-08-03 01:21:58', NULL),
(5, 1, 'Balranald', 'active', '2025-08-04 16:36:35', '2025-08-04 16:36:35', NULL),
(6, 1, 'Admin', 'active', '2025-08-04 17:19:49', '2025-08-04 17:19:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Australia', 'active', '2025-08-03 01:21:58', '2025-08-03 01:21:58', NULL),
(2, 'FC Barcelona', 'active', '2025-08-04 16:25:54', '2025-08-04 16:28:11', '2025-08-04 16:28:11');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `driving_license_number` varchar(255) DEFAULT NULL,
  `driving_license_expiry` date DEFAULT NULL,
  `driving_license_image` varchar(255) DEFAULT NULL,
  `car_model` varchar(255) DEFAULT NULL,
  `car_make` varchar(255) DEFAULT NULL,
  `car_year` year(4) DEFAULT NULL,
  `car_color` varchar(255) DEFAULT NULL,
  `car_plate_number` varchar(255) DEFAULT NULL,
  `car_image` varchar(255) DEFAULT NULL,
  `face_embedding` text DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `device_token` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `user_id`, `driving_license_number`, `driving_license_expiry`, `driving_license_image`, `car_model`, `car_make`, `car_year`, `car_color`, `car_plate_number`, `car_image`, `face_embedding`, `is_verified`, `device_token`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'PYU42PBGSO', '2027-08-14', NULL, 'Ford', 'illo', '2020', 'blue', 'UGQFQI', NULL, NULL, 0, NULL, 'active', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(2, 3, 'I1DXSUB80W', '2028-06-17', NULL, 'Honda', 'sed', '1977', 'yellow', 'JRGVDE', NULL, NULL, 0, NULL, 'active', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(3, 4, 'LE8FKXFVY8', '2030-06-19', NULL, 'Toyota', 'tempora', '2000', 'aqua', 'MNHENB', NULL, NULL, 0, NULL, 'active', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(4, 5, 'HBVRK9ZEBG', '2029-09-12', NULL, 'Honda', 'sed', '2013', 'lime', 'VY7BLS', NULL, NULL, 0, NULL, 'active', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(5, 6, 'PLH22KECMQ', '2029-09-17', NULL, 'Ford', 'ducimus', '1973', 'green', 'TMBHKZ', NULL, NULL, 0, NULL, 'active', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(6, 7, 'QD26N9U3IL', '2029-09-05', NULL, 'Honda', 'voluptas', '1982', 'gray', '8GQOQ9', NULL, NULL, 0, NULL, 'active', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(7, 8, 'LOI9HXY06W', '2028-01-02', NULL, 'Ford', 'sit', '2022', 'blue', 'HIH8A9', NULL, NULL, 0, NULL, 'active', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(8, 9, 'ASSPU0EDX5', '2030-01-24', NULL, 'Toyota', 'quam', '1980', 'fuchsia', 'GGIL0H', NULL, NULL, 0, NULL, 'active', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(9, 10, 'NVJIIKLDIS', '2029-12-18', NULL, 'Ford', 'quia', '1977', 'purple', 'NFEFV2', NULL, NULL, 0, NULL, 'active', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(10, 11, 'ZHLFF7OJOY', '2030-04-29', NULL, 'Honda', 'perspiciatis', '2011', 'blue', '0E4WPD', NULL, NULL, 0, NULL, 'active', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(11, 23, '123fgd45gh', '2027-06-09', 'uploads/driver/23/175423427059.jpg', '201267', 'Honda', '2012', 'red', '2012654', 'uploads/driver/23/175423427030.jpg', NULL, 0, NULL, 'active', '2025-08-03 09:17:50', '2025-08-03 09:17:50', NULL),
(12, 24, '123fgd45gh111', '2027-08-03', 'uploads/driver/24/175423649987.png', '20126777', 'Honda', '2021', 'Green', '201265466', 'uploads/driver/24/175423649996.jpg', NULL, 0, NULL, 'active', '2025-08-03 09:54:59', '2025-08-03 09:54:59', NULL),
(13, 25, '123fglatest', '2029-08-04', 'uploads/driver/25/175427336878.jpg', '2012latest', 'Honda', '2021', 'Green', '2012latest', 'uploads/driver/25/175427336841.jpg', NULL, 0, NULL, 'active', '2025-08-03 20:08:59', '2025-08-03 20:09:28', NULL),
(14, 32, '12345', '2222-11-11', 'uploads/driver/32/175430928418.jpeg', 'Mercedes-benz.', 'none', '2025', 'Red', '2345', 'uploads/driver/32/175430928461.jpeg', NULL, 0, NULL, 'active', '2025-08-04 16:04:17', '2025-08-04 16:08:04', NULL),
(15, 36, '12345', '2034-02-28', 'uploads/driver/36/17546777496.jpeg', 'Mercedes-benz.', 'Mercedes', '2023', 'black', '2345', 'uploads/driver/36/175467774973.jpeg', NULL, 0, NULL, 'active', '2025-08-08 22:29:09', '2025-08-08 22:29:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kids`
--

CREATE TABLE `kids` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `school_name` varchar(255) DEFAULT NULL,
  `school_address` varchar(255) DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kids`
--

INSERT INTO `kids` (`id`, `user_id`, `first_name`, `last_name`, `dob`, `gender`, `height_cm`, `weight_kg`, `photo`, `school_name`, `school_address`, `emergency_contact`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 12, 'Filiberto', 'Denesik', '2007-07-04', 'other', 140.82, 64.24, NULL, 'Emmerich-Green', '3945 Hayden Coves\nHuelchester, WY 99833', '+1.530.282.9848', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(2, 13, 'Sylvester', 'Douglas', '1992-10-22', 'female', 126.21, 65.86, NULL, 'Quitzon-Ryan', '62013 Marvin Cliff Apt. 957\nNorth Keshaun, VT 79554-5404', '1-503-440-7985', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(3, 14, 'Rhiannon', 'Jerde', '2009-11-25', 'male', 122.62, 63.11, NULL, 'Rau-Abshire', '65388 Heidenreich Port Apt. 926\nCaseyberg, AL 44882-7877', '1-469-201-4423', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(4, 15, 'Maxine', 'Champlin', '1985-05-07', 'male', 129.32, 45.57, NULL, 'Bednar-Nolan', '9859 Feest Stravenue\nNorth Brendon, NE 73869-3581', '+17603099809', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(5, 16, 'Rogers', 'Ledner', '1993-10-18', 'female', 158.31, 39.32, NULL, 'Stamm, Lemke and Beier', '6709 Deonte Centers\nPfefferland, IA 68352', '281-764-5471', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(6, 17, 'Ronaldo', 'Bernier', '2019-03-16', 'female', 97.24, 24.49, NULL, 'Casper, Howe and Gutmann', '554 O\'Kon Lock\nLangoshport, ND 35331', '+1.480.361.4826', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(7, 18, 'Ara', 'Kris', '2024-09-29', 'female', 125.59, 16.40, NULL, 'Murphy-Lockman', '20046 D\'Amore Port\nSouth Kyler, DC 30732-0400', '318.875.3238', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(8, 19, 'Eda', 'Hudson', '2024-10-07', 'other', 157.46, 43.52, NULL, 'Kessler, Ankunding and Cummerata', '93320 Johnson Junction Suite 707\nPort Burnice, KS 95337', '(859) 453-6884', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(9, 20, 'Bennie', 'Okuneva', '1980-06-21', 'male', 137.72, 65.23, NULL, 'Hickle, Jenkins and Kautzer', '49180 Favian Alley\nDarrelshire, KS 83672-6953', '757.566.4100', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(10, 21, 'Marion', 'Reichel', '1971-11-19', 'female', 162.08, 32.43, NULL, 'Balistreri-Leuschke', '120 Makenzie Ridges\nPort Jacyntheland, AZ 29354', '947-283-6062', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(11, 20, 'sfds', 'fgd', '2012-03-31', 'female', 1.00, 45.00, 'uploads/kid/175460976493.jpg', 'Balistreri-Leuschke', 'address', '947-283-6062', '2025-08-08 03:36:04', '2025-08-08 03:36:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_07_31_111046_create_permission_tables', 1),
(5, '2025_08_01_172407_add_soft_deletes_to_roles_users_permissions_tables', 1),
(6, '2025_08_02_152757_create_pickup_types_table', 1),
(7, '2025_08_02_175026_create_countries_table', 1),
(8, '2025_08_02_175027_create_states_table', 1),
(9, '2025_08_02_175028_create_cities_table', 1),
(10, '2025_08_03_064844_create_drivers_table', 1),
(11, '2025_08_03_064857_create_kids_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(1, 'App\\Models\\User', 27),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 4),
(2, 'App\\Models\\User', 5),
(2, 'App\\Models\\User', 6),
(2, 'App\\Models\\User', 7),
(2, 'App\\Models\\User', 8),
(2, 'App\\Models\\User', 9),
(2, 'App\\Models\\User', 10),
(2, 'App\\Models\\User', 11),
(2, 'App\\Models\\User', 23),
(2, 'App\\Models\\User', 24),
(2, 'App\\Models\\User', 25),
(2, 'App\\Models\\User', 32),
(2, 'App\\Models\\User', 36),
(3, 'App\\Models\\User', 12),
(3, 'App\\Models\\User', 13),
(3, 'App\\Models\\User', 14),
(3, 'App\\Models\\User', 15),
(3, 'App\\Models\\User', 16),
(3, 'App\\Models\\User', 17),
(3, 'App\\Models\\User', 19),
(3, 'App\\Models\\User', 20),
(3, 'App\\Models\\User', 21),
(3, 'App\\Models\\User', 26),
(3, 'App\\Models\\User', 28),
(3, 'App\\Models\\User', 31),
(3, 'App\\Models\\User', 33),
(3, 'App\\Models\\User', 35),
(10, 'App\\Models\\User', 30);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', 'web', '2025-08-04 16:13:58', '2025-08-04 16:13:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'api_token', '059cad857e68abe97e5f3037749812a13bb999dbd7af0868b80b2e1e050b8991', '[\"*\"]', NULL, NULL, '2025-08-08 11:03:11', '2025-08-08 11:03:11'),
(2, 'App\\Models\\User', 1, 'api_token', 'c0510bb3519b6968221cf15f99307cb326f9b664bb1776dd1b2efdbf9b9490b4', '[\"*\"]', NULL, NULL, '2025-08-08 19:39:01', '2025-08-08 19:39:01'),
(3, 'App\\Models\\User', 1, 'api_token', '2dced89aaf214a2004ec78ee347c33438f8342d44e3abe90acad15f9f60875ef', '[\"*\"]', NULL, NULL, '2025-08-08 19:42:28', '2025-08-08 19:42:28'),
(4, 'App\\Models\\User', 1, 'api_token', '22c87c2df192266f1f36d5716157dfc1a826c4ff16cc91fb63dcbcee91d82feb', '[\"*\"]', NULL, NULL, '2025-08-08 19:43:04', '2025-08-08 19:43:04'),
(5, 'App\\Models\\User', 1, 'api_token', '7bc8f75e34a7de493a8da747207e7c3ceac371dedc1c339bd4f60eddffe28f0d', '[\"*\"]', NULL, NULL, '2025-08-08 20:50:28', '2025-08-08 20:50:28'),
(6, 'App\\Models\\User', 1, 'api_token', 'dc4832b4f0aeb7dfaddcc9dc42c6387087066863e8745cb26453b2b25372aa0f', '[\"*\"]', NULL, NULL, '2025-08-08 20:56:26', '2025-08-08 20:56:26'),
(7, 'App\\Models\\User', 1, 'api_token', '5769fc519535b3b13d560e1b292d6a705d503871fc0345246142f8892af1b6c7', '[\"*\"]', NULL, NULL, '2025-08-08 20:57:09', '2025-08-08 20:57:09'),
(8, 'App\\Models\\User', 1, 'api_token', '93b081f667874aa82f515589d184f07baae6a966195c66fb889bd532ae0a8a86', '[\"*\"]', NULL, NULL, '2025-08-08 20:57:13', '2025-08-08 20:57:13'),
(11, 'App\\Models\\User', 1, 'api_token', '6d79ae2901a1b6ad100934874c809bd15dc0d8416cd1d2062d7948745ec2b9d5', '[\"*\"]', NULL, NULL, '2025-08-08 21:20:53', '2025-08-08 21:20:53'),
(12, 'App\\Models\\User', 23, 'api_token', 'b6ab32a11c17b7421ba7aeee2b5bf0f267126901561d2936f60d1c199b75b63c', '[\"*\"]', NULL, NULL, '2025-08-08 21:20:53', '2025-08-08 21:20:53'),
(13, 'App\\Models\\User', 23, 'api_token', '8653e98cb0852d4f0ca17ff12a1dad68684a19fa562b36c6ce7e47216738d08a', '[\"*\"]', '2025-08-08 21:30:49', NULL, '2025-08-08 21:20:55', '2025-08-08 21:30:49'),
(14, 'App\\Models\\User', 23, 'api_token', '3dc99b001e9a8e51240d68627ba83f8c1f2167933d511fb45d50b0d245188058', '[\"*\"]', NULL, NULL, '2025-08-08 21:21:05', '2025-08-08 21:21:05'),
(18, 'App\\Models\\User', 36, 'api_token', '4f992323841cfc89d5cc933440cdb45bb93ec9d5d7ecc9d42247e143cf159172', '[\"*\"]', '2025-08-08 22:29:40', NULL, '2025-08-08 22:29:34', '2025-08-08 22:29:40'),
(19, 'App\\Models\\User', 36, 'api_token', '9dce7734fc55c2dded9e87b1ac34d7eaab24c4d0a7d493ed5bbe0a697922e0e7', '[\"*\"]', '2025-08-10 23:50:33', NULL, '2025-08-10 23:41:02', '2025-08-10 23:50:33'),
(20, 'App\\Models\\User', 36, 'api_token', '7d657fc7df040a29906b218bbf179a7c7fcfa20be3d2f8d75e0446710643f70e', '[\"*\"]', '2025-08-10 23:56:33', NULL, '2025-08-10 23:56:14', '2025-08-10 23:56:33'),
(21, 'App\\Models\\User', 36, 'api_token', 'b9f538c7cdb338a607e26b173d8ebf7e80d1edd7670616cadd7f3208e8bad8dc', '[\"*\"]', '2025-08-11 00:03:22', NULL, '2025-08-11 00:03:15', '2025-08-11 00:03:22'),
(22, 'App\\Models\\User', 36, 'api_token', '759cda8e5e5c266b998fea38c09bf31d54eed522caae33590d3c15286208df08', '[\"*\"]', '2025-08-11 00:20:11', NULL, '2025-08-11 00:20:05', '2025-08-11 00:20:11'),
(23, 'App\\Models\\User', 36, 'api_token', '8f0fc76b62dc1ec9a38dd03e745ab0224588ff8991613e9ecc917236093c246c', '[\"*\"]', '2025-08-12 14:52:47', NULL, '2025-08-12 14:52:40', '2025-08-12 14:52:47'),
(24, 'App\\Models\\User', 36, 'api_token', 'b1756338012d0af45ca45ccd52e0e50d2a4db6189eb10365f55eb22ca21de1f3', '[\"*\"]', '2025-08-12 14:55:45', NULL, '2025-08-12 14:55:29', '2025-08-12 14:55:45'),
(25, 'App\\Models\\User', 36, 'api_token', '75816f2965d94aeff0242fd148af49806bd14f871879a923fb79778bb4f716ff', '[\"*\"]', '2025-08-12 14:58:28', NULL, '2025-08-12 14:58:21', '2025-08-12 14:58:28'),
(26, 'App\\Models\\User', 36, 'api_token', 'ee2b623a69efd35f4322824040f784e17ef6419a12c957c8bc5239f78d38810a', '[\"*\"]', '2025-08-12 15:04:28', NULL, '2025-08-12 15:04:20', '2025-08-12 15:04:28'),
(27, 'App\\Models\\User', 36, 'api_token', '95bf59d72ab740bd0d66b61e74ee1393bc3554c76b992132e61cfd6afca4bbc8', '[\"*\"]', NULL, NULL, '2025-08-12 15:11:11', '2025-08-12 15:11:11'),
(28, 'App\\Models\\User', 36, 'api_token', '3b6ba8239beec838580c61a6ac801bbd910d95be5e180707dc8fce18f41f1238', '[\"*\"]', '2025-08-12 15:15:29', NULL, '2025-08-12 15:15:18', '2025-08-12 15:15:29');

-- --------------------------------------------------------

--
-- Table structure for table `pickup_types`
--

CREATE TABLE `pickup_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `min_notice_minutes` int(11) NOT NULL DEFAULT 0,
  `requires_instant_notification` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pickup_types`
--

INSERT INTO `pickup_types` (`id`, `name`, `amount`, `min_notice_minutes`, `requires_instant_notification`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Scheduled', 23.58, 44, 0, 'active', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(2, 'Express', 37.95, 36, 1, 'active', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(3, 'Express', 15.19, 52, 1, 'active', '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(4, 'Standard', 33.51, 26, 0, 'active', '2025-08-03 01:22:08', '2025-08-03 01:22:08', NULL),
(5, 'Express', 34.51, 48, 1, 'active', '2025-08-03 01:22:08', '2025-08-03 01:22:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', 'web', '2025-08-03 01:21:58', '2025-08-03 01:21:58', NULL),
(2, 'driver', 'web', '2025-08-03 01:21:58', '2025-08-04 16:12:15', NULL),
(3, 'parent', 'web', '2025-08-03 01:21:58', '2025-08-03 01:21:58', NULL),
(10, 'Noman', 'web', '2025-08-03 21:18:14', '2025-08-03 21:18:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('7SLuM0HQGOhai3i6haEXOEiF0tCX3sHYOjycODx2', NULL, '198.235.24.38', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiODRkNjMydmRFTWRCaGR5Y0ViMHR0S3hnSWVLQUdLZ1F4ZHVnNnpIbCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHBzOi8vdGlueXRyYWlscy5hbGFtaW5zZS5vbmxpbmUvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1754776034),
('aGNigKsWgPvWXu4EPSU55tRBixeGFMedaFqYzQiv', NULL, '37.111.206.182', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaTIzM3dwZFlrZ2tSQzZGN0FkdmFkNHNlYjVxemxhUWo1aU9zRW5rTyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHBzOi8vdGlueXRyYWlscy5hbGFtaW5zZS5vbmxpbmUvbG9naW4iO319', 1754855762),
('J7V2Bk9c43UzhgwdryzYyHEc6p3JZ69wrjsVxryc', NULL, '157.143.53.238', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTkY1cXZuR1lPckJiMWE4dGZqeWFaWGlNRG12bm1YOExsN3N5bzVRMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vdGlueXRyYWlscy5hbGFtaW5zZS5vbmxpbmUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1754762557),
('Ji2W2zKevEigYwhHtQ9dbenKPiRSxnvATtXdjwEX', NULL, '103.72.212.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNUJ4Q2t5OGY4N1JBeVU4bHRBZFBGaGU3ZVJVSThqeGxpMEt0c1c5VyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NDoiaHR0cHM6Ly90aW55dHJhaWxzLmFsYW1pbnNlLm9ubGluZS9kYXNoYm9hcmQiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo0MDoiaHR0cHM6Ly90aW55dHJhaWxzLmFsYW1pbnNlLm9ubGluZS9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754718772),
('kBI81grVJc9l28n2WtpQbpxN8amyyOgjKImxj8qO', NULL, '199.45.155.70', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNWlPUnNLTHRzUTZDVGRzWDVSMnhoM0pFcnpvS2c4Um9kcks0em5RbSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vd3d3LnRpbnl0cmFpbHMuYWxhbWluc2Uub25saW5lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1754742818),
('KRFyNzhpmB48aVeAbKCIkDlKlRbbT8vKkY5CzgSC', NULL, '23.27.145.205', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidFFqRjJpbXlpM1dPdW11Y05ZcXNCRnlaSzBDRlJDb2U1NXF5OXk0aSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vd3d3LnRpbnl0cmFpbHMuYWxhbWluc2Uub25saW5lL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1754783578),
('Mt0QJwAocciRgxZDrRJ84zcdQaURVPIwlrXRTXVd', NULL, '205.210.31.141', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV3B3NWkyeEYxRUg4TFVST1dqOWxQS0N0NGNQbXd3aXZOOTlvNTMzSCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vd3d3LnRpbnl0cmFpbHMuYWxhbWluc2Uub25saW5lL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1754751252),
('o3IwWEniRUI086yivc8u8sJgTWFNl98kFtoflkKb', NULL, '205.210.31.141', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMFF2MHZVNkh0OXM0MGkzZjBGa1dsZGJIWjc4VXNIU0JyZUQzSXl6YSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vd3d3LnRpbnl0cmFpbHMuYWxhbWluc2Uub25saW5lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1754751251),
('qN2vpLzz0LIxo8LQ5MLWfcNZShaGkyWNQbOfvIQS', NULL, '119.30.38.69', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSkRXM0hFMGFjOWJLWmdqR251VmRBdFpqaFNKUERCQ2k5akV2ZW1SWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHBzOi8vdGlueXRyYWlscy5hbGFtaW5zZS5vbmxpbmUvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1754745053),
('R9Rg66WCbPSS1jPl7BN8bWNOgJktgtxZkCDDdSso', NULL, '198.235.24.170', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUHFxVHFoNnM4MlZ6NlNzcnJoeEJuMkNTdkE3Wk1JSFBZc3JNSGg4NyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vdGlueXRyYWlscy5hbGFtaW5zZS5vbmxpbmUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1754734301),
('rM03pumE85M85TmxoBVfRBN7xq79hvyQcZlmWffJ', NULL, '23.27.145.172', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieGhtYWV1THFPa3JMUEdkSVAwdjdtY0xiVzA1UzJaMVFHNHVrY0xONiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHBzOi8vdGlueXRyYWlscy5hbGFtaW5zZS5vbmxpbmUvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1754785316),
('sDA5CEKVIq2DKpieB2sqT3vbwaoX9Ssg2zQc7Ly9', NULL, '23.27.145.33', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ0VCcmMxZWdrc21XQUk2ZmIxalZua3BmU2QwNjY4SmpMQUZLYjRxUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHBzOi8vdGlueXRyYWlscy5hbGFtaW5zZS5vbmxpbmUvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1754774198),
('Taxi1mfVV5XxYg3YHAlLp68hXtiWuzDq2nx5nEcW', NULL, '198.235.24.170', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRzZycHdMbUFUcklWTUpkZVVkUGRkY0R2YVc1d2NsRkRlMmVKcXNkZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHBzOi8vdGlueXRyYWlscy5hbGFtaW5zZS5vbmxpbmUvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1754734301),
('TdjUiGDDLk4OCpnI9ys2TvCKaq3a9y05kTJQSlPD', NULL, '205.210.31.138', 'Hello from Palo Alto Networks, find out more about our scans in https://docs-cortex.paloaltonetworks.com/r/1/Cortex-Xpanse/Scanning-activity', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRWQ5c1VpT3Z3RkhtdnpEMUsyUmVrM3Y0YzhLWHdlb29RbVdKVmxaWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vdGlueXRyYWlscy5hbGFtaW5zZS5vbmxpbmUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1754975521),
('vVMtXFO10oOUvYTvzq6wt0ZVMBAAGGOO9rtO4HY8', NULL, '157.143.53.238', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibGZwM3FuWk1NSnlWR29WWjREdnNXU1BFNXBaOXpHcFhnc1l2QnlRUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vd3d3LnRpbnl0cmFpbHMuYWxhbWluc2Uub25saW5lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1754762344),
('W6p0GqxcQAlS97sZEJTI29VBWfAdOOWcLkotZsK7', NULL, '198.235.24.38', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMDg0bjNHY0RwWkZKeWFSb2o0eEpiNWhlU3pZczVUVmhYUDk3dnBLQSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vdGlueXRyYWlscy5hbGFtaW5zZS5vbmxpbmUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1754776034),
('XC6TjmYYuP2hRaMAXMHi3taTo8nuW51gDrfGjJXO', NULL, '23.27.145.126', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMDNYSG83RXFYS2FtM0EzT3Y2T0lsR2RyME4zY0lpbGRhSWt5N2kwciI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vd3d3LnRpbnl0cmFpbHMuYWxhbWluc2Uub25saW5lL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1754774203),
('XsMR6ZnEN9chGv8aTXASxbkvg5XVuNutoAZQnNtA', NULL, '199.45.155.70', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieHQ1WnRvQzFQc1FpdEZjb2NzMVh1dERrZUtraVh1am54ZWlacVp6ZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vd3d3LnRpbnl0cmFpbHMuYWxhbWluc2Uub25saW5lL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1754742839),
('zOuGLVfEWTNIOk1SoeebKcSF8PcQNlUeYmVR3jOm', NULL, '203.123.65.97', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) EdgiOS/138.0.3351.121 Version/18.0 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWllDcmFnd0NSWjJxOERJcUpjTXUwVWo1OEFUZmxGNFNucjhjRFhYZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHBzOi8vdGlueXRyYWlscy5hbGFtaW5zZS5vbmxpbmUvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1754839757);

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `country_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `country_id`, `name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'New South Wales', 'active', '2025-08-03 01:21:58', '2025-08-03 01:21:58', NULL),
(2, 1, 'Victoria', 'active', '2025-08-03 01:21:58', '2025-08-03 01:21:58', NULL),
(3, 1, 'Queensland', 'active', '2025-08-03 01:21:58', '2025-08-03 01:21:58', NULL),
(4, 1, 'Tasmania', 'active', '2025-08-03 01:21:58', '2025-08-03 01:21:58', NULL),
(5, 1, 'Tamim', 'active', '2025-08-04 16:30:47', '2025-08-04 16:30:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `country_id` varchar(255) DEFAULT NULL,
  `state_id` varchar(255) DEFAULT NULL,
  `city_id` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `phone`, `dob`, `gender`, `height_cm`, `weight_kg`, `photo`, `address`, `country_id`, `state_id`, `city_id`, `status`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Super', 'Admin', 'admin@gmail.com', '$2y$12$DVkxBb8nhpZJvGxWDFe/yu9kyPa3mHO/1mNQlKUmO8bk9unh2p54i', NULL, '1990-01-01', 'male', NULL, NULL, NULL, 'Admin Address', '1', '1', '1', 'active', NULL, NULL, '2025-08-03 01:21:59', '2025-08-03 01:21:59', NULL),
(2, 'Devyn', 'Johnson', 'muller.emely@example.net', '$2y$12$R1CRgZlQB2pU9SxDOl9lGuXM6/wv2eXup9DBWibrfLrmSykq.Uo46', NULL, '1996-11-08', 'male', NULL, NULL, NULL, '171 Paris Ville\nNorth Hermann, WA 54820', '1', '9', '8', 'active', '2025-08-03 01:21:59', NULL, '2025-08-03 01:21:59', '2025-08-03 01:21:59', NULL),
(3, 'Cyril', 'Trantow', 'rusty.hirthe@example.net', '$2y$12$f7hmpAhm3khD0dG34DCyoOkECosblGw9uWg6cfHFH1fhZhVQzkN4u', NULL, '1989-04-27', 'female', NULL, NULL, NULL, '245 Franecki Roads Suite 161\nGildaburgh, FL 88197-3279', '1', '1', '3', 'active', '2025-08-03 01:21:59', NULL, '2025-08-03 01:22:00', '2025-08-03 01:22:00', NULL),
(4, 'Jefferey', 'Collier', 'misty55@example.com', '$2y$12$Tj.eoCCUFMrN.GGFIuwSkeAUWkuMASdxP.tNFW9C23JrDxLQC6hrC', NULL, '2012-10-31', 'female', NULL, NULL, NULL, '58500 Jared Lake Suite 589\nBritneymouth, AL 88578-9107', '1', '8', '7', 'active', '2025-08-03 01:22:00', NULL, '2025-08-03 01:22:00', '2025-08-03 01:22:00', NULL),
(5, 'Nakia', 'Jast', 'maia00@example.com', '$2y$12$nj2KqxuDNpwPUDJ9M29FiuRKjH5fWSOTWhASzUP4ZgTrjntVCZkkC', NULL, '1986-04-03', 'female', NULL, NULL, NULL, '9357 Sanford Alley\nDoyleside, IN 40792', '1', '8', '4', 'active', '2025-08-03 01:22:00', NULL, '2025-08-03 01:22:01', '2025-08-03 01:22:01', NULL),
(6, 'Allie', 'Smith', 'vrunolfsson@example.org', '$2y$12$a2Fc9eglpoxe79hKb2Z/1e.w4oPTt4ZZ0EKg4D4X/iML7e2IgjtTy', NULL, '1976-11-28', 'male', NULL, NULL, NULL, '15690 Kohler Club\nNew Andre, LA 09659', '1', '5', '5', 'active', '2025-08-03 01:22:01', NULL, '2025-08-03 01:22:01', '2025-08-03 01:22:01', NULL),
(7, 'Candice', 'Homenick', 'rhettinger@example.com', '$2y$12$hB09GDJrH.1tlGH67D3H6.NlF1w73mKeOJ6RWbyFtfZDQxUETOTSC', NULL, '1986-09-21', 'female', NULL, NULL, NULL, '9869 Sonya Estate Suite 870\nNorth Leolafort, KY 05620-7755', '1', '5', '7', 'active', '2025-08-03 01:22:01', NULL, '2025-08-03 01:22:01', '2025-08-03 01:22:01', NULL),
(8, 'Forest', 'Crist', 'abbie.johns@example.com', '$2y$12$I6bgp2ccMpNx2wmAmazdtehoXzpwqvG1x1UPex7Ctnz/STpnz04Du', NULL, '2011-06-07', 'female', NULL, NULL, NULL, '1290 Will Land\nLarsonport, UT 69240-8236', '1', '4', '8', 'active', '2025-08-03 01:22:01', NULL, '2025-08-03 01:22:02', '2025-08-03 01:22:02', NULL),
(9, 'Seamus', 'Thompson', 'stefanie.rutherford@example.com', '$2y$12$XDyDGn5EhnBTrQzOZe/p3uhuqZRTgL6Dqku6ZG8BAX3AVIeCgQRVi', NULL, '2008-01-07', 'male', NULL, NULL, NULL, '3123 Grayson Highway Suite 654\nLake Kathryneborough, SC 43570', '1', '6', '5', 'active', '2025-08-03 01:22:02', NULL, '2025-08-03 01:22:02', '2025-08-03 01:22:02', NULL),
(10, 'Harley', 'Labadie', 'dietrich.sophia@example.net', '$2y$12$.hVVi8tpmdfCVAFx3G8gQ.hiE2vbaaY1IXsVIsuOLX0QdhKvT/PoS', NULL, '1981-08-03', 'male', NULL, NULL, NULL, '3089 Javier Heights Apt. 527\nWeissnatfurt, OK 17684', '1', '2', '1', 'active', '2025-08-03 01:22:02', NULL, '2025-08-03 01:22:03', '2025-08-03 01:22:03', NULL),
(11, 'Eloise', 'Bayer', 'marks.declan@example.org', '$2y$12$4ZSvgwtijnEYkcEgfWSg2OfL.vrG5yBv8GIRKiJF9LYag8ayr.g7a', NULL, '2009-12-26', 'male', NULL, NULL, NULL, '86293 Schuppe Expressway\nDiegostad, AR 72681', '1', '5', '8', 'active', '2025-08-03 01:22:03', NULL, '2025-08-03 01:22:03', '2025-08-03 01:22:03', NULL),
(12, 'Norberto', 'Bartell', 'unicolas@example.net', '$2y$12$kq5dmxG2rJBiD3nc1qdBwOQuok..5GjRKByK/jmbv.ttJouIpCg8m', NULL, '1989-05-12', 'female', NULL, NULL, NULL, '21421 Nicolas Cove\nGustaveberg, NY 85830', '1', '5', '3', 'active', '2025-08-03 01:22:03', NULL, '2025-08-03 01:22:03', '2025-08-03 01:22:03', NULL),
(13, 'Albert', 'Muller', 'savannah.will@example.com', '$2y$12$o0n6oZ7aAzqv1y/L1T/AdOAb0WECPkrbSyRiSJ.I8xR3aWMkzkPcW', NULL, '1982-07-25', 'male', NULL, NULL, NULL, '3959 Efrain Cape Apt. 905\nJalynmouth, WV 09206', '1', '1', '4', 'active', '2025-08-03 01:22:03', NULL, '2025-08-03 01:22:04', '2025-08-03 01:22:04', NULL),
(14, 'Natalia', 'Murray', 'bosco.murphy@example.net', '$2y$12$ZsjmfsdHoahgzAf61XIwe.u4g5E1nPMVROE6sLrM1kWsOKfaIJN4K', NULL, '2020-02-04', 'male', NULL, NULL, NULL, '543 Zulauf Station\nLake Loy, AZ 06426-7933', '1', '1', '9', 'active', '2025-08-03 01:22:04', NULL, '2025-08-03 01:22:04', '2025-08-03 01:22:04', NULL),
(15, 'Maegan', 'Harvey', 'mheidenreich@example.org', '$2y$12$38vfHO4cGf1mGkiozxIpzO6pu5KDC089/Y92FcPOnyHSg.2Qg4mVm', NULL, '2016-12-10', 'male', NULL, NULL, NULL, '595 Runolfsson Valleys Suite 636\nSouth Audreanneborough, MT 55549', '1', '6', '2', 'active', '2025-08-03 01:22:04', NULL, '2025-08-03 01:22:05', '2025-08-03 01:22:05', NULL),
(16, 'Anahi', 'Johnston', 'maggio.karley@example.net', '$2y$12$9n0O4TuBRN8.oLpE587NVus.FrVdNVnd2D3.6hIZkFFgBSzQAfE76', NULL, '1989-02-28', 'male', NULL, NULL, NULL, '8678 Stroman Throughway Apt. 194\nPurdyhaven, NC 35932-3302', '1', '1', '6', 'active', '2025-08-03 01:22:05', NULL, '2025-08-03 01:22:05', '2025-08-03 01:22:05', NULL),
(17, 'Donnell', 'Corwin', 'wolff.genevieve@example.com', '$2y$12$zhTQ/qszJ6MDo8Y3QkSlo.MzcE7S.QUQyXZYn6p0leUZq4L3PaIb2', NULL, '2021-04-03', 'female', NULL, NULL, NULL, '35360 Bogisich Cove\nGarlandtown, MS 01969', '1', '10', '4', 'active', '2025-08-03 01:22:05', NULL, '2025-08-03 01:22:05', '2025-08-03 01:22:05', NULL),
(18, 'Dianna', 'Kuhn', 'rjerde@example.net', '$2y$12$utWISuUjMDL29cbyFdaZmukJgjm.fHnxeh4Ky3W7Spra5iD0gA4AO', NULL, '1970-07-07', 'male', NULL, NULL, NULL, '2542 Hilton Ville Suite 273\nWest Pinkie, WV 49991', '1', '9', '2', 'active', '2025-08-03 01:22:05', NULL, '2025-08-03 01:22:06', '2025-08-03 23:52:19', '2025-08-03 23:52:19'),
(19, 'Sierra', 'Sanford', 'brennon.lesch@example.com', '$2y$12$XyvNBNTGoGmeBgLJ.r90B.aFi6K8Vt5exqa.SKNcCnZOs5HhDcsQm', NULL, '2013-07-04', 'female', NULL, NULL, NULL, '485 Calista Brooks\nBeattyfort, AR 31201-9073', '1', '6', '2', 'active', '2025-08-03 01:22:06', NULL, '2025-08-03 01:22:06', '2025-08-03 01:22:06', NULL),
(20, 'Ethyl', 'Russel', 'conor.ondricka@example.com', '$2y$12$Mw2WFTtb2eW1JPaCwSl5jObRVbd.R3rDSSPOY1txvYEKVpxbvh5Ji', NULL, '1989-05-21', 'male', NULL, NULL, NULL, '63847 Eldred Camp Apt. 668\nWest Vanberg, KS 88087', '1', '9', '3', 'active', '2025-08-03 01:22:06', NULL, '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(21, 'Jeanie', 'Turner', 'jaden05@example.net', '$2y$12$e0GHCKWL4ohR0XDw/ixPIermjpuma13acDMj6SyrkKoxaYwWMxJO6', NULL, '2002-05-24', 'male', NULL, NULL, NULL, '7971 Sauer Landing\nNew Jonathonshire, TN 32371-9365', '1', '3', '6', 'active', '2025-08-03 01:22:07', NULL, '2025-08-03 01:22:07', '2025-08-03 01:22:07', NULL),
(23, 'Noman', 'Driver', 'noman@gmail.com', '$2y$12$DVkxBb8nhpZJvGxWDFe/yu9kyPa3mHO/1mNQlKUmO8bk9unh2p54i', '0132456789', '1998-08-03', 'male', 1.70, 77.00, 'uploads/user/175423426966.jpg', 'address', NULL, NULL, NULL, 'active', NULL, NULL, '2025-08-03 09:17:50', '2025-08-03 23:52:07', NULL),
(24, 'Noman 1', 'Driver 1', 'noman1@gmail.com', '$2y$12$wl6zKewdKttIt3qT3LObfOq0k0hO50iFe1ay7./HVhxGE0L8NgvsC', '0132456786', '1996-07-28', 'male', 1.70, 77.00, 'uploads/user/175423649992.jpg', 'address 1', '1', '1', '1', 'active', NULL, NULL, '2025-08-03 09:54:59', '2025-08-03 09:54:59', NULL),
(25, 'aaa up', 'ssss', 'sss@gmail.com', '$2y$12$9TaIwsBwHnJPwVQu6Ducku9KLHbgWDV5z1vHyLEhe9z0m3mpgCot6', '0132456345', '1999-08-04', 'male', 1.60, 66.00, 'uploads/user/175427641721.jpg', 'address', '1', '1', '1', 'inactive', NULL, NULL, '2025-08-03 19:22:02', '2025-08-03 21:00:17', NULL),
(26, 'Par', 'ent', 'parent@gmail.com', '$2y$12$0eEXP1e6UJW7Kw/RfkkWpOKKCNutAkgOGFlNsfUIuHGU6phE/8C2a', '0132456111', '1999-08-04', 'male', 1.60, 66.00, 'uploads/user/175428987018.jpg', 'address', '1', '2', '2', 'inactive', NULL, NULL, '2025-08-04 10:44:30', '2025-08-04 10:44:30', NULL),
(27, 'Test', 'U', 'tu@gmail.com', '$2y$12$6u6kN8TcU0foR3J47OVtp.1Jjkog7UQeQb7a2.uV53GEsdEnBGVF2', '01234568778', '1999-11-01', 'female', 1.30, 55.00, 'uploads/user/175429099573.png', 'test address', '1', '1', '1', 'active', NULL, NULL, '2025-08-04 11:03:16', '2025-08-04 11:03:16', NULL),
(28, 'Noman w', 'Driver', 'noman@gmail.co', '$2y$12$6qJ7wetaXiWM21CrakR6fu3oY6238FK.k5ZivPEo213o155qtHfTq', '0132456789', '2023-08-05', 'male', 1.00, 33.00, 'uploads/user/175429131244.jpg', 'address', '1', '1', '1', 'active', NULL, NULL, '2025-08-04 11:08:32', '2025-08-04 16:10:50', NULL),
(30, 'Issue', 'M', 'issue@gmail.com', '$2y$12$xE8wUhHe2ba2paMBAsgrsOmbXPrjW6TpxmPM/c69fT7urqjBvayPG', '0132456744', '1999-08-05', 'female', 1.60, 66.00, 'uploads/user/175429230976.jpg', 'address', '1', '1', '1', 'active', NULL, NULL, '2025-08-04 11:25:09', '2025-08-04 11:25:09', NULL),
(31, 'Marc-André', 'ter Stegen', 'stegen@gmail.com', '$2y$12$4Zr4PhNV6c4Cf7ChUXqvjuIofwxMOpB4jL/1/Jk9cRjhOc/xSRY3u', '+1 (378) 556-7889', '2001-11-11', 'male', 135.00, 80.00, 'uploads/user/175430848819.jpeg', 'Barcelona', '1', '1', '1', 'inactive', NULL, NULL, '2025-08-04 15:40:19', '2025-08-04 15:54:48', NULL),
(32, 'Joan', 'García', 'test@gmail.com', '$2y$12$du2tIwM3GZJp8IWVCKdmnu0YKAxv6XBiHOQOhbv8B.kSl/MJctQAe', '+1 (378) 556-7889', '2000-11-11', 'male', 135.00, 80.00, 'uploads/user/175430928466.jpg', 'Australia', '1', NULL, NULL, 'inactive', NULL, NULL, '2025-08-04 16:04:17', '2025-08-04 16:10:01', NULL),
(33, 'Noman 112', 'Driver 22', 'noman22@gmail.co', '$2y$12$D3IXBLZ0hVDdLYVb1cKNquPGVnUwSeTtKzcXaaRaj8bS1tqo.lrtm', '0132456789', '1996-11-11', 'male', 2.39, 77.00, 'uploads/user/175460963250.png', 'address', '1', '2', '2', 'active', NULL, NULL, '2025-08-08 03:33:52', '2025-08-08 03:33:52', NULL),
(35, 'Joan', 'García', 'tamim10@gmail.com', '$2y$12$EFyppaSUeAOATNIjoY9QQ.qgK3U37.L69AdHXEJOEDbhT17ARXawi', '+1 (604) 171-8681', '2007-01-09', 'male', 1.70, 77.00, 'uploads/user/175467678792.jpg', 'road-12', '1', '1', '1', 'active', NULL, NULL, '2025-08-08 22:13:08', '2025-08-08 22:13:08', NULL),
(36, 'Marc-André', 'ter Stegen', 'ter@gmail.com', '$2y$12$.KPFxtl2Mh3vxpUssurvE.OZ90ZD2cpSSVnhRPyoI1rYP1ddhPphG', '+1 (604) 171-8681', '2006-02-01', 'male', 1.80, 75.00, 'uploads/user/175467774883.png', 'Road 5', '1', '1', '1', 'active', NULL, NULL, '2025-08-08 22:29:09', '2025-08-08 22:29:09', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cities_state_id_foreign` (`state_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `drivers_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kids`
--
ALTER TABLE `kids`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kids_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pickup_types`
--
ALTER TABLE `pickup_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`),
  ADD KEY `states_country_id_foreign` (`country_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kids`
--
ALTER TABLE `kids`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `pickup_types`
--
ALTER TABLE `pickup_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`);

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kids`
--
ALTER TABLE `kids`
  ADD CONSTRAINT `kids_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `states`
--
ALTER TABLE `states`
  ADD CONSTRAINT `states_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
