-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for shiftech_kas_digital_laravel_adminlte
CREATE DATABASE IF NOT EXISTS `shiftech_kas_digital_laravel_adminlte` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `shiftech_kas_digital_laravel_adminlte`;

-- Dumping structure for table shiftech_kas_digital_laravel_adminlte.cash_accounts
CREATE TABLE IF NOT EXISTS `cash_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('bank','cash') COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `bank` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `bank_account_number` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `bank_account_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `balance` decimal(18,2) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_accounts_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table shiftech_kas_digital_laravel_adminlte.cash_accounts: ~0 rows (approximately)

-- Dumping structure for table shiftech_kas_digital_laravel_adminlte.cash_transactions
CREATE TABLE IF NOT EXISTS `cash_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `amount` decimal(18,0) NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_transactions_account_id_foreign` (`account_id`),
  KEY `cash_transactions_category_id_foreign` (`category_id`),
  CONSTRAINT `cash_transactions_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `cash_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_transactions_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `cash_transaction_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table shiftech_kas_digital_laravel_adminlte.cash_transactions: ~0 rows (approximately)

-- Dumping structure for table shiftech_kas_digital_laravel_adminlte.cash_transaction_categories
CREATE TABLE IF NOT EXISTS `cash_transaction_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('income','expense') COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_transaction_categories_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table shiftech_kas_digital_laravel_adminlte.cash_transaction_categories: ~0 rows (approximately)

-- Dumping structure for table shiftech_kas_digital_laravel_adminlte.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table shiftech_kas_digital_laravel_adminlte.migrations: ~1 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000002_create_users_table', 1),
	(2, '0001_01_01_000003_create_settings_table', 1),
	(3, '2024_06_06_232506_create_cash_accounts_table', 1),
	(4, '2024_06_06_232517_create_cash_transaction_categories_table', 1),
	(5, '2024_06_06_232546_create_cash_transactions_table', 1);

-- Dumping structure for table shiftech_kas_digital_laravel_adminlte.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `key` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `value` text COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table shiftech_kas_digital_laravel_adminlte.settings: ~0 rows (approximately)

-- Dumping structure for table shiftech_kas_digital_laravel_adminlte.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table shiftech_kas_digital_laravel_adminlte.users: ~1 rows (approximately)
INSERT INTO `users` (`id`, `username`, `fullname`, `password`, `is_active`, `is_admin`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'Administrator', '$2y$12$cSKDdtbBiH7AChDW/PRlZOiaI9wnYlAMgVO6fS7ZQ9xqgXLlLBKue', 1, 1, NULL, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
