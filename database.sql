-- =======================================================
-- ShareFood Database Schema & Seed Data
-- Database Name: food_sharing_platform
-- =======================================================

CREATE DATABASE IF NOT EXISTS `food_sharing_platform` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `food_sharing_platform`;

-- -------------------------------------------------------
-- Table Structure: users
-- -------------------------------------------------------
DROP TABLE IF EXISTS `food_claims`;
DROP TABLE IF EXISTS `food_listings`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `address` TEXT NOT NULL,
  `user_type` ENUM('donor', 'receiver') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------
-- Table Structure: food_listings
-- -------------------------------------------------------
CREATE TABLE `food_listings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `donor_id` INT NOT NULL,
  `food_type` VARCHAR(255) NOT NULL,
  `quantity` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `expiry_time` DATETIME NOT NULL,
  `pickup_location` TEXT NOT NULL,
  `status` ENUM('available', 'claimed', 'picked_up') DEFAULT 'available',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`donor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------
-- Table Structure: food_claims
-- -------------------------------------------------------
CREATE TABLE `food_claims` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `food_id` INT NOT NULL,
  `receiver_id` INT NOT NULL,
  `claimed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`food_id`) REFERENCES `food_listings`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------
-- Seed Data: Demo Users
-- Default password for all demo accounts: password123
-- -------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `user_type`) VALUES
(1, 'ABC Restaurant', 'restaurant@abc.com', '$2y$12$HCkWfTDLDdUF5eqirUCfOuSBSTvEsPZszGb3eoPimidTmCMZ9Xyry', '+1 555-0199', '123 Gourmet Street, Downtown', 'donor'),
(2, 'Hope Food Bank NGO', 'ngo@foodbank.com', '$2y$12$HCkWfTDLDdUF5eqirUCfOuSBSTvEsPZszGb3eoPimidTmCMZ9Xyry', '+1 555-0122', '456 Community Care Center, Westside', 'receiver');

-- -------------------------------------------------------
-- Seed Data: Sample Food Listings
-- -------------------------------------------------------
INSERT INTO `food_listings` (`id`, `donor_id`, `food_type`, `quantity`, `description`, `expiry_time`, `pickup_location`, `status`, `created_at`) VALUES
(1, 1, 'Fresh Cooked Rice & Curry', '15 Servings', 'Freshly prepared vegetarian meals packed in hygienic containers.', DATE_ADD(NOW(), INTERVAL 4 HOUR), '123 Gourmet Street, Rear Entrance', 'available', NOW()),
(2, 1, 'Bakery Assorted Bread & Pastries', '20 Boxes', 'Fresh baked baguettes, croissants, and sandwich loaves from today morning.', DATE_ADD(NOW(), INTERVAL 24 HOUR), '123 Gourmet Street, Front Desk', 'available', NOW()),
(3, 1, 'Fresh Fruit Baskets', '5 Large Baskets', 'Mixed fresh apples, oranges, and bananas from catering surplus.', DATE_ADD(NOW(), INTERVAL 48 HOUR), '123 Gourmet Street, Kitchen Door', 'claimed', NOW());

-- -------------------------------------------------------
-- Seed Data: Sample Claims
-- -------------------------------------------------------
INSERT INTO `food_claims` (`id`, `food_id`, `receiver_id`, `claimed_at`) VALUES
(1, 3, 2, NOW());
