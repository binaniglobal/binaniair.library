-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 24, 2024 at 07:56 AM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issuing_books`
--

DROP TABLE IF EXISTS `issuing_books`;
CREATE TABLE IF NOT EXISTS `issuing_books` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ibd` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manual_id` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date-time` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `issuing_books_ibd_unique` (`ibd`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `manuals`
--

DROP TABLE IF EXISTS `manuals`;
CREATE TABLE IF NOT EXISTS `manuals` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `mid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_of_items` bigint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '0',
  `type` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `manuals_mid_unique` (`mid`),
  UNIQUE KEY `manuals_name_unique` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manuals`
--

INSERT INTO `manuals` (`id`, `mid`, `name`, `no_of_items`, `status`, `type`, `created_at`, `updated_at`) VALUES
(1, 'e95f08d8-cfbb-45cd-852d-c4fc2b60330a', 'Company Manuals', 0, 0, 0, '2024-05-24 07:34:24', '2024-05-24 07:34:24');

-- --------------------------------------------------------

--
-- Table structure for table `manuals_items`
--

DROP TABLE IF EXISTS `manuals_items`;
CREATE TABLE IF NOT EXISTS `manuals_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `miid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manual_uid` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `manuals_items_miid_unique` (`miid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manuals_items`
--

INSERT INTO `manuals_items` (`id`, `miid`, `manual_uid`, `name`, `link`, `file_type`, `file_size`, `created_at`, `updated_at`) VALUES
(2, '9312778c-82b1-4030-b53f-6b388a1d9810', 'e95f08d8-cfbb-45cd-852d-c4fc2b60330a', 'Abdulaeez.pdf', 'public/uploads/Abdulaeez.pdf', 'application/pdf', '225726', '2024-05-24 07:36:15', '2024-05-24 07:36:15'),
(3, 'c5f1946b-9981-4e22-85b8-88f5e9c8578a', 'e95f08d8-cfbb-45cd-852d-c4fc2b60330a', 'Hello', 'Hello', 'Folder', '0MB', '2024-05-24 07:37:36', '2024-05-24 07:37:36');

-- --------------------------------------------------------

--
-- Table structure for table `manual_item_contents`
--

DROP TABLE IF EXISTS `manual_item_contents`;
CREATE TABLE IF NOT EXISTS `manual_item_contents` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `micd` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manual_uid` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manual_iid` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `manual_item_contents_micd_unique` (`micd`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manual_item_contents`
--

INSERT INTO `manual_item_contents` (`id`, `micd`, `manual_uid`, `manual_iid`, `name`, `link`, `file_type`, `file_size`, `created_at`, `updated_at`) VALUES
(1, 'd83bd842-a072-4b2b-8fff-e3e3b0ed45ec', 'c5f1946b-9981-4e22-85b8-88f5e9c8578a', 'e95f08d8-cfbb-45cd-852d-c4fc2b60330a', 'airline library.jpeg', 'public/storage/uploads/WCKIrj0SfYbO8DkAuLTQC5oIXwRaQRisxiyrHSUH.jpg', 'image/jpeg', '615305', NULL, NULL),
(5, '0d7d7dd1-3d3e-4d9b-a94d-fa10580943f8', 'c5f1946b-9981-4e22-85b8-88f5e9c8578a', 'e95f08d8-cfbb-45cd-852d-c4fc2b60330a', 'airline library.jpeg', 'public/storage/uploads/contents/airline library.jpeg', 'image/jpeg', '615305', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_05_20_235142_create_permission_tables', 1),
(5, '2024_05_21_050621_create_manuals_table', 1),
(6, '2024_05_21_051449_create_manuals_items_table', 1),
(7, '2024_05_21_052304_create_issuing_books_table', 1),
(8, '2024_05_22_233541_create_manual_item_contents_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'can edit', 'web', '2024-05-24 07:34:23', '2024-05-24 07:34:23'),
(2, 'can delete', 'web', '2024-05-24 07:34:23', '2024-05-24 07:34:23'),
(3, 'issue books', 'web', '2024-05-24 07:34:23', '2024-05-24 07:34:23'),
(4, 'view books', 'web', '2024-05-24 07:34:23', '2024-05-24 07:34:23'),
(5, 'update books', 'web', '2024-05-24 07:34:23', '2024-05-24 07:34:23'),
(6, 'add manuals', 'web', '2024-05-24 07:34:23', '2024-05-24 07:34:23'),
(7, 'edit manuals', 'web', '2024-05-24 07:34:23', '2024-05-24 07:34:23'),
(8, 'destroy manuals', 'web', '2024-05-24 07:34:23', '2024-05-24 07:34:23');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super-admin', 'web', '2024-05-24 07:34:23', '2024-05-24 07:34:23'),
(2, 'admin', 'web', '2024-05-24 07:34:23', '2024-05-24 07:34:23'),
(3, 'librarian', 'web', '2024-05-24 07:34:23', '2024-05-24 07:34:23'),
(4, 'user', 'web', '2024-05-24 07:34:23', '2024-05-24 07:34:23');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('8KKkZ5T7jnaAvwL5VQ1Xa2iVvQw4WUu9MAKx5BZd', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiZHRIenRoNHJQbkdXRTdsWkRwd0pTSnhoT3c3d3d0ZmhTQ0plSVFzVyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjc0OiJodHRwOi8vMTI3LjAuMC44L21hbnVhbC9pdGVtcy9jb250ZW50L2M1ZjE5NDZiLTk5ODEtNGUyMi04NWI4LTg4ZjVlOWM4NTc4YSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7czo0OiJhdXRoIjthOjE6e3M6MjE6InBhc3N3b3JkX2NvbmZpcm1lZF9hdCI7aToxNzE2NTM2MTM5O319', 1716537333);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surname` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_uid_unique` (`uid`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uid`, `name`, `surname`, `email`, `phone`, `status`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'c7a118aa-8096-479c-a300-b8b3c80933f0', 'SuperAdmin', 'BinaniAir', 'super-admin@binaniair.com', '09000023456', 0, NULL, '$2y$12$j9hM7rMsmMd7GMTFcBta.u9DoRPHnTeyI6g/bwsO6AxU0kF9NjRhu', '5PiONJNY9i', '2024-05-24 07:34:24', '2024-05-24 07:34:24'),
(2, '8c4ec279-28fb-42ba-ad2b-3bc84d2ac3ba', 'Admin', 'BinaniAir', 'admin@binaniair.com', '09200023456', 0, NULL, '$2y$12$j9hM7rMsmMd7GMTFcBta.u9DoRPHnTeyI6g/bwsO6AxU0kF9NjRhu', 'UciyZx172N', '2024-05-24 07:34:24', '2024-05-24 07:34:24'),
(3, '55a19813-6718-46b0-8ef0-d09da7a94b71', 'Librarian', 'BinaniAir', 'librarian@binaniair.com', '09130023456', 0, NULL, '$2y$12$j9hM7rMsmMd7GMTFcBta.u9DoRPHnTeyI6g/bwsO6AxU0kF9NjRhu', 'c7VLOcvVWQ', '2024-05-24 07:34:24', '2024-05-24 07:34:24'),
(4, '39dea29b-cad3-482e-9204-aaba3c4dafe3', 'User', 'BinaniAir', 'user@binaniair.com', '09100023456', 0, NULL, '$2y$12$j9hM7rMsmMd7GMTFcBta.u9DoRPHnTeyI6g/bwsO6AxU0kF9NjRhu', 'zEviASjiGA', '2024-05-24 07:34:24', '2024-05-24 07:34:24');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
