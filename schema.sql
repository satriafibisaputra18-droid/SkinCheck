-- SkinCheck Database Schema Definition
-- Defines the database structure and relational tables.

CREATE DATABASE IF NOT EXISTS `skincheck` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `skincheck`;

-- 1. TABLE users
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    `status` VARCHAR(50) NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. TABLE skin_scans
CREATE TABLE IF NOT EXISTS `skin_scans` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'completed',
    `image_quality_score` DECIMAL(5,2) NOT NULL DEFAULT 100.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. TABLE analysis_results
CREATE TABLE IF NOT EXISTS `analysis_results` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `skin_scan_id` INT NOT NULL,
    `acne_score` INT NOT NULL,
    `redness_score` INT NOT NULL,
    `texture_score` INT NOT NULL,
    `hyperpigmentation_score` INT NOT NULL,
    `overall_score` INT NOT NULL,
    `ai_response` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`skin_scan_id`) REFERENCES `skin_scans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. TABLE sunscreen_results
CREATE TABLE IF NOT EXISTS `sunscreen_results` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `skin_scan_id` INT NOT NULL,
    `coverage_score` INT NOT NULL,
    `coverage_status` VARCHAR(50) NOT NULL,
    `heatmap_path` VARCHAR(255) NOT NULL,
    `low_coverage_areas` TEXT,
    `analysis_method` VARCHAR(100) NOT NULL,
    `hardware_type` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`skin_scan_id`) REFERENCES `skin_scans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. TABLE products
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `brand` VARCHAR(100) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `description` TEXT NOT NULL,
    `ingredients` TEXT NOT NULL,
    `skin_concern` VARCHAR(255) NOT NULL,
    `usage_guidance` TEXT NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'active',
    `price` INT NOT NULL DEFAULT 0,
    `skin_type` VARCHAR(100) NOT NULL DEFAULT 'All Skin Types',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 6. TABLE activity_logs
CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `activity` VARCHAR(255) NOT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 7. TABLE settings (Dynamic Centralized API Keys & System Configurations)
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT NULL,
    `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general',
    `setting_label` VARCHAR(255) NOT NULL,
    `setting_type` VARCHAR(50) NOT NULL DEFAULT 'text',
    `is_secret` TINYINT(1) NOT NULL DEFAULT 0,
    `description` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

