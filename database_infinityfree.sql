-- SkinCheck Database Setup for InfinityFree & cPanel
-- Safe for import directly via phpMyAdmin without CREATE DATABASE restrictions

SET FOREIGN_KEY_CHECKS = 0;

-- Table structure for users
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `telegram_chat_id` varchar(100) DEFAULT NULL,
  `telegram_username` varchar(100) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for skin_scans
DROP TABLE IF EXISTS `skin_scans`;
CREATE TABLE `skin_scans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'completed',
  `image_quality_score` decimal(5,2) NOT NULL DEFAULT 100.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `skin_scans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for analysis_results
DROP TABLE IF EXISTS `analysis_results`;
CREATE TABLE `analysis_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `skin_scan_id` int(11) NOT NULL,
  `acne_score` int(11) NOT NULL,
  `redness_score` int(11) NOT NULL,
  `texture_score` int(11) NOT NULL,
  `hyperpigmentation_score` int(11) NOT NULL,
  `overall_score` int(11) NOT NULL,
  `ai_response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `skin_scan_id` (`skin_scan_id`),
  CONSTRAINT `analysis_results_ibfk_1` FOREIGN KEY (`skin_scan_id`) REFERENCES `skin_scans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for sunscreen_results
DROP TABLE IF EXISTS `sunscreen_results`;
CREATE TABLE `sunscreen_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `skin_scan_id` int(11) NOT NULL,
  `coverage_score` int(11) NOT NULL,
  `coverage_status` varchar(50) NOT NULL,
  `heatmap_path` varchar(255) NOT NULL,
  `low_coverage_areas` text DEFAULT NULL,
  `analysis_method` varchar(100) NOT NULL,
  `hardware_type` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `skin_scan_id` (`skin_scan_id`),
  CONSTRAINT `sunscreen_results_ibfk_1` FOREIGN KEY (`skin_scan_id`) REFERENCES `skin_scans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for products
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `ingredients` text NOT NULL,
  `skin_concern` varchar(255) NOT NULL,
  `usage_guidance` text NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `price` int(11) NOT NULL DEFAULT 0,
  `skin_type` varchar(100) NOT NULL DEFAULT 'All Skin Types',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for activity_logs
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `activity` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for ai_token_usage
DROP TABLE IF EXISTS `ai_token_usage`;
CREATE TABLE `ai_token_usage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_role` varchar(50) DEFAULT 'customer',
  `feature` varchar(100) DEFAULT 'chat',
  `model` varchar(100) NOT NULL,
  `prompt_tokens` int(11) NOT NULL DEFAULT 0,
  `completion_tokens` int(11) NOT NULL DEFAULT 0,
  `total_tokens` int(11) NOT NULL DEFAULT 0,
  `duration_ms` int(11) NOT NULL DEFAULT 0,
  `status` varchar(50) NOT NULL DEFAULT 'success',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_at` (`created_at`),
  KEY `model` (`model`),
  KEY `feature` (`feature`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for settings
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) NOT NULL DEFAULT 'general',
  `setting_label` varchar(255) NOT NULL,
  `setting_type` varchar(50) NOT NULL DEFAULT 'text',
  `is_secret` tinyint(1) NOT NULL DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data for users
INSERT INTO `users` (`id`, `name`, `profile_image`, `email`, `telegram_chat_id`, `telegram_username`, `reset_token`, `reset_expires_at`, `password_hash`, `role`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Internal Administrator', NULL, 'admin@skincheck.com', NULL, NULL, NULL, NULL, '$2y$10$fXS0xSaROl3fs3xE7ORnx.yOEiB.NQ9zJmJ8VFRLUr/SvTPYz7XqK', 'admin', 'active', '2026-08-28 21:52:25', '2026-10-09 13:28:14');
INSERT INTO `users` (`id`, `name`, `profile_image`, `email`, `telegram_chat_id`, `telegram_username`, `reset_token`, `reset_expires_at`, `password_hash`, `role`, `status`, `created_at`, `updated_at`) VALUES ('2', 'Jane Doe', NULL, 'customer@example.com', NULL, NULL, NULL, NULL, '$2y$10$MlMAF9eBeFu0IkTXrBwqSec0pPTJy/xSWH6HfXky255XZUvV4pcXK', 'customer', 'active', '2026-08-28 21:52:25', '2026-08-28 21:52:25');

-- Seed data for settings
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_label`, `setting_type`, `is_secret`, `description`, `created_at`, `updated_at`) VALUES ('1', 'telegram_bot_token', '', 'telegram', 'Telegram Bot Token', 'password', '1', 'Token API Bot Telegram dari @BotFather', '2026-10-23 14:05:17', '2026-10-23 14:05:17');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_label`, `setting_type`, `is_secret`, `description`, `created_at`, `updated_at`) VALUES ('2', 'telegram_bot_username', 'Kode_OTP_SkinCheckBot', 'telegram', 'Telegram Bot Username', 'text', '0', 'Username bot Telegram (tanpa @) untuk 1-klik verifikasi link', '2026-10-23 14:05:17', '2026-10-23 14:05:17');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_label`, `setting_type`, `is_secret`, `description`, `created_at`, `updated_at`) VALUES ('3', 'resend_api_key', '', 'email', 'Resend Email API Key', 'password', '1', 'API Key Resend untuk pengiriman email transaksional', '2026-10-23 14:05:17', '2026-10-23 14:05:17');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_label`, `setting_type`, `is_secret`, `description`, `created_at`, `updated_at`) VALUES ('4', 'resend_from_email', 'SkinCheck <onboarding@resend.dev>', 'email', 'Email Pengirim (Sender)', 'text', '0', 'Alamat pengirim email terdaftar di Resend', '2026-10-23 14:05:17', '2026-10-23 14:05:17');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_label`, `setting_type`, `is_secret`, `description`, `created_at`, `updated_at`) VALUES ('5', 'gemini_api_key', '', 'ai', 'Google Gemini API Key', 'password', '1', 'API Key Google Gemini AI Studio untuk konsultasi & analisis', '2026-10-23 14:05:17', '2026-10-23 14:05:17');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_label`, `setting_type`, `is_secret`, `description`, `created_at`, `updated_at`) VALUES ('6', 'gemini_primary_model', 'gemini-flash-lite-latest', 'ai', 'Gemini Primary Model', 'text', '0', 'Model utama AI Gemini', '2026-10-23 14:05:17', '2026-10-23 14:14:26');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_label`, `setting_type`, `is_secret`, `description`, `created_at`, `updated_at`) VALUES ('7', 'gemini_fallback_models', 'gemini-flash-lite-latest,gemini-3.5-flash,gemini-3.8-flash', 'ai', 'Gemini Fallback Models', 'text', '0', 'Model cadangan dipisahkan koma saat kuota penuh', '2026-10-23 14:05:17', '2026-10-23 14:05:17');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_label`, `setting_type`, `is_secret`, `description`, `created_at`, `updated_at`) VALUES ('8', 'gemini_temperature', '0.7', 'ai', 'AI Temperature', 'number', '0', 'Tingkat kreativitas respon AI (0.0 - 1.0)', '2026-10-23 14:05:17', '2026-10-23 14:05:17');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_label`, `setting_type`, `is_secret`, `description`, `created_at`, `updated_at`) VALUES ('9', 'site_name', 'SkinCheck', 'general', 'Nama Aplikasi / Brand', 'text', '0', 'Nama sistem yang tampil di header dan judul', '2026-10-23 14:05:17', '2026-10-23 14:05:17');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_label`, `setting_type`, `is_secret`, `description`, `created_at`, `updated_at`) VALUES ('10', 'otp_expiry_minutes', '5', 'general', 'Masa Berlaku OTP (Menit)', 'number', '0', 'Durasi waktu sebelum kode OTP kedaluwarsa', '2026-10-23 14:05:17', '2026-10-23 14:05:17');
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `setting_label`, `setting_type`, `is_secret`, `description`, `created_at`, `updated_at`) VALUES ('11', 'otp_resend_cooldown', '60', 'general', 'Cooldown Kirim Ulang OTP (Detik)', 'number', '0', 'Jeda waktu sebelum user bisa klik kirim ulang', '2026-10-23 14:05:17', '2026-10-23 14:05:17');

-- Seed data for products
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('1', 'Somethinc', 'Low pH Gentle Jelly Cleanser', 'Cleanser', 'Pembersih wajah bertekstur jelly dengan pH seimbang yang membersihkan debu dan minyak berlebih tanpa membuat kulit kering atau merusak skin barrier.', 'Japanese Mugwort, Tea Tree Oil, Centella Asiatica, Peppermint', 'Acne', 'Pagi & Malam: Basahi wajah, busakan produk di telapak tangan, pijat lembut dengan gerakan melingkar, lalu bilas air bersih.', 'active', '99000', 'Acne-Prone, Sensitive, All Skin Types', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('2', 'COSRX', 'AHA/BHA Clarifying Treatment Toner', 'Toner', 'Toner eksfoliasi harian yang lembut membantu mencegah timbulnya komedo, membersihkan pori-pori tersumbat, dan menyeimbangkan kadar minyak wajah.', 'Salix Alba (Willow) Bark Water, Pyrus Malus (Apple) Fruit Water, Glycolic Acid (AHA), Betaine Salicylate (BHA)', 'Acne', 'Malam Hari: Semprotkan pada kapas secukupnya lalu usapkan lembut ke seluruh wajah setelah membersihkan muka.', 'active', '175000', 'Oily, Acne-Prone, Normal', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('3', 'SKINTIFIC', '2% Salicylic Acid Anti Acne Serum', 'Serum', 'Serum dengan teknologi ACSD Salicylic Acid time-released yang efektif menenangkan jerawat meradang, memudarkan kemerahan jerawat, dan meratakan tekstur kulit.', '2% Salicylic Acid, Pionin, Centella Asiatica, Niacinamide, Ceramide', 'Acne', 'Pagi & Malam: Teteskan 2-3 tetes pada wajah bersih, tepuk-tepuk hingga meresap sebelum menggunakan moisturizer.', 'active', '139000', 'Acne-Prone, Oily', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('4', 'SKINTIFIC', 'Salicylic Acid Blemish Clearing Spot Treatment', 'Spot Treatment', 'Obat totol jerawat aktif transparan yang cepat meredakan inflamasi jerawat dan mempercepat penyembuhan tanpa meninggalkan bekas gelap.', 'Salicylic Acid, Pionin, Centella Asiatica, Niacinamide', 'Acne', 'Pagi & Malam: Totolkan langsung pada jerawat yang aktif atau meradang sebagai langkah terakhir skincare.', 'active', '89000', 'Acne-Prone', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('5', 'Somethinc', 'Calm Down! Skinpair R-Cover Cream', 'Moisturizer', 'Pelembap ringan penenang jerawat dan kemerahan yang melindungi pertahanan skin barrier tanpa menyumbat pori-pori.', 'Madagascar Centella Asiatica, Encapsulated Ceramide, Panthenol, Murumuru Butter', 'Acne', 'Pagi & Malam: Aplikasikan merata ke seluruh wajah dan leher setelah pemakaian serum.', 'active', '129000', 'Acne-Prone, Sensitive, Combination', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('6', 'Anua', 'Heartleaf 77% Soothing Toner', 'Toner', 'Toner penenang nomor satu dengan kandungan 77% ekstrak Daun Heartleaf yang sangat ampuh meredakan iritasi, kemerahan, dan inflamasi kulit reaktif.', '77% Houttuynia Cordata (Heartleaf) Extract, Panthenol, Centella Asiatica, Chamomile', 'Redness', 'Pagi & Malam: Tuangkan ke telapak tangan atau kapas, tepuk lembut ke kulit wajah setelah cuci muka.', 'active', '210000', 'Sensitive, Irritated, All Skin Types', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('7', 'SKIN1004', 'Madagascar Centella Ampoule', 'Serum', 'Ampoule murni 100% ekstrak Centella Asiatica dari Madagaskar yang memberikan efek dingin instan, meredakan kemerahan, dan memperbaiki skin barrier rapuh.', '100% Centella Asiatica Extract', 'Redness', 'Pagi & Malam: Teteskan 3-4 tetes dan ratakan ke seluruh wajah, terutama di area yang memerah atau iritasi.', 'active', '195000', 'Sensitive, Redness-Prone, Normal', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('8', 'SKINTIFIC', '5X Ceramide Barrier Repair Moisture Gel', 'Moisturizer', 'Pelembap gel populer yang menggabungkan 5 jenis ceramide untuk memperbaiki skin barrier yang rusak, mengunci kelembapan, dan meredakan rasa perih kemerahan.', '5X Ceramide (NP, NS, AS, EOP, AP), Hyaluronic Acid, Centella Asiatica, Marine Collagen', 'Redness', 'Pagi & Malam: Usapkan secukupnya ke wajah dan leher secara merata untuk hidrasi intensif.', 'active', '139000', 'Sensitive, Dry, Combination, Damaged Barrier', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('9', 'La Roche-Posay', 'Cicaplast Baume B5+ Ultra-Repairing Soothing Balm', 'Moisturizer', 'Balsem multifungsi penolong kulit iritasi, kemerahan parah, luka bekas jerawat, dan kulit yang terkelupas akibat over-exfoliating.', 'Madecassoside, 5% Panthenol (Vitamin B5), Tribioma Prebiotic Complex, Shea Butter, Zinc Gluconate', 'Redness', 'Malam Hari atau saat iritasi: Oleskan tebal di area kemerahan atau kulit yang teriritasi setelah pelembap biasa.', 'active', '250000', 'Dry, Sensitive, Severely Irritated', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('10', 'SKIN1004', 'Centella Ampoule Foam Cleanser', 'Cleanser', 'Pembersih wajah busa lembut ber-pH 5 yang diperkaya konsentrat Centella Asiatica untuk membersihkan kotoran tanpa memicu kemerahan kulit sensitif.', 'Centella Asiatica Extract, Sodium Cocoyl Isethionate, Glycerin, Cacao Extract', 'Redness', 'Pagi & Malam: Buat busa melimpah dengan sedikit air di tangan, pijat di wajah perlahan, lalu bilas.', 'active', '179000', 'Sensitive, Dry, All Skin Types', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('11', 'The Ordinary', 'Niacinamide 10% + Zinc 1%', 'Serum', 'Serum konsentrasi tinggi Niacinamide dan mineral Zinc yang terbukti klinis menyamarkan noda hitam bekas jerawat (PIH/PIE) serta mengontrol sebum pori-pori.', '10% Niacinamide (Vitamin B3), 1% Zinc PCA, Tamarindus Indica Seed Gum', 'Hyperpigmentation', 'Pagi & Malam: Oleskan beberapa tetes ke seluruh wajah sebelum krim pelembap.', 'active', '165000', 'Oily, Combination, Hyperpigmentation-Prone', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('12', 'Somethinc', 'Revive Potion 3% Arbutin + Bakuchiol', 'Serum', 'Serum pencerah ganda yang mengombinasikan Alpha Arbutin generasi baru dan Bakuchiol untuk memudarkan flek hitam, meratakan warna kulit, dan mencerahkan kulit kusam.', '3% Alpha Arbutin, Bakuchiol, SabiWhite, Rice Bran Water', 'Hyperpigmentation', 'Pagi & Malam: Teteskan pada kulit yang sudah dibersihkan, ratakan sebelum moisturizer.', 'active', '119000', 'Dull Skin, Dark Spots, All Skin Types', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('13', 'Avoskin', 'Your Skin Bae Vitamin C 3% + Niacinamide 2%', 'Serum', 'Serum antioksidan tinggi yang menghambat produksi melanin penyebab bintik hitam, menangkal polusi radikal bebas, dan membuat kulit tampak glowing alami.', '3% Ethyl Ascorbic Acid (Vitamin C), 2% Niacinamide, Mandarin Orange Extract', 'Hyperpigmentation', 'Pagi Hari: Gunakan beberapa tetes sebelum pelembap, dan selalu akhiri dengan tabir surya (sunscreen).', 'active', '149000', 'All Skin Types, Dull Skin', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('14', 'SKINTIFIC', 'SymWhite 377 Dark Spot Moisture Gel', 'Moisturizer', 'Pelembap pencerah dengan molekul SymWhite 377 yang bekerja 100x lebih efektif dari kojic acid dalam menarget bintik hitam membandel dan bekas jerawat.', 'SymWhite 377 (Phenylethyl Resorcinol), Niacinamide, Tranexamic Acid, Ceramide', 'Hyperpigmentation', 'Pagi & Malam: Ratakan pada seluruh wajah secara rutin untuk hasil warna kulit yang merata.', 'active', '149000', 'Hyperpigmentation, Dull, Normal, Combination', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('15', 'Hada Labo', 'Shirojyun Premium Ultimate Whitening Lotion', 'Toner', 'Hydrating toner pencerah asal Jepang dengan kandungan Tranexamic Acid murni dan Vitamin C untuk menyamarkan melasma serta melembapkan lapisan epidermis terdalam.', 'Tranexamic Acid, Vitamin C Phosphate, Vitamin E, Nano Hyaluronic Acid', 'Hyperpigmentation', 'Pagi & Malam: Tuang ke telapak tangan lalu tepuk-tepuk lembut ke wajah hingga meresap sempurna.', 'active', '98000', 'All Skin Types, Dry, Pigmented', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('16', 'Dermalogica', 'Daily Microfoliant Exfoliator', 'Exfoliator', 'Bubuk eksfoliasi beras legendaris yang aktif saat dicampur air, mengangkat sel kulit mati seketika, dan memperhalus tekstur kulit kasar tanpa abrasi mikro.', 'Rice Bran, Salicylic Acid, Papain Enzyme, Colloidal Oatmeal, Ginkgo Biloba', 'Texture', 'Malam Hari (2-3x seminggu): Tuang 1/2 sendok teh ke tangan basah, gosok jadi busa lembut, pijat ke wajah 1 menit lalu bilas.', 'active', '950000', 'Uneven Texture, Rough Skin, All Types', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('17', 'Avoskin', 'Miraculous Refining Serum', 'Exfoliator', 'Serum chemical peeling rumahan dengan perpaduan AHA BHA PHA dan Niacinamide yang meremajakan sel kulit, mengecilkan penampakan pori, dan menghaluskan bruntusan.', '10% AHA (Glycolic Acid), 3% BHA (Salicylic Acid), 2% Niacinamide, Ceramide, Aloe Vera', 'Texture', 'Malam Hari (2-3 kali seminggu): Aplikasikan tipis pada wajah bersih, tunggu 10 menit lalu lanjutkan dengan moisturizer pelembap.', 'active', '189000', 'Rough Texture, Oily, Normal (Bukan untuk kulit hiper-sensitif)', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('18', 'Somethinc', 'Level 1% Encapsulated Retinol', 'Serum', 'Serum retinol dengan enkapsulasi teknologi tinggi yang menembus ke dalam pori-pori untuk merangsang produksi kolagen, menyamarkan kerutan halus, dan menghaluskan tekstur kulit.', '1% Encapsulated Retinol, Ceramide-3, Allantoin, Hyaluronic Acid', 'Texture', 'Malam Hari: Mulai 1-2x seminggu, gunakan seukuran kacang polong setelah moisturizer (sandwich method). Wajib pakai sunscreen keesokan harinya.', 'active', '155000', 'Aging, Uneven Texture, Large Pores', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('19', 'Paula\'s Choice', 'Skin Perfecting 2% BHA Liquid Exfoliant', 'Exfoliator', 'Cairan eksfoliasi global terkemuka yang menembus lapisan sebum pori-pori terdalam untuk melarutkan sel kulit mati dan menghaluskan permukaan kulit bertekstur.', '2% Salicylic Acid (BHA), Camellia Oleifera (Green Tea) Leaf Extract, Methylpropanediol', 'Texture', 'Malam Hari: Usapkan lembut memakai kapas pada wajah bersih, jangan dibilas, lanjutkan ke langkah serum dan pelembap.', 'active', '480000', 'Large Pores, Rough Texture, Oily, Combination', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('20', 'CeraVe', 'Hydrating Facial Cleanser', 'Cleanser', 'Pembersih wajah berformula lotion tanpa busa yang membersihkan secara efektif tanpa mengikis minyak alami dan kelembapan esensial kulit kering.', '3 Essential Ceramides (1, 3, 6-II), Hyaluronic Acid, Glycerin, Phytosphingosine', 'Dryness', 'Pagi & Malam: Basahi wajah, usapkan lotion pembersih dengan lembut, bilas hingga bersih.', 'active', '215000', 'Dry, Very Dry, Sensitive, Normal', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('21', 'Hada Labo', 'Gokujyun Premium Ultimate Hydrating Lotion', 'Toner', 'Lotion hidrasi intensif berkonsistensi kaya dengan 7 jenis asam hialuronat yang mengunci air hingga ke lapisan kulit terdalam untuk mengatasi dehidrasi parah.', '7 Types of Hyaluronic Acid, Hydroxyethyl Urea, Allantoin', 'Dryness', 'Pagi & Malam: Tepuk-tepuk pada kulit wajah yang lembap tepat setelah mencuci muka.', 'active', '110000', 'Dry, Extremely Dehydrated', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('22', 'Isntree', 'Hyaluronic Acid Aqua Gel Cream', 'Moisturizer', 'Krim pelembap bertekstur gel sejuk yang kaya akan 5 molekul Hyaluronic Acid untuk hidrasi mendalam tanpa rasa lengket atau greasy.', '5 Types of Hyaluronic Acid, Centella Asiatica, Betaine, Sodium PCA', 'Dryness', 'Pagi & Malam: Oleskan merata pada wajah setelah memakai toner atau serum hidrasi.', 'active', '240000', 'Dehydrated, Combination, Oily-Dehydrated', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('23', 'CeraVe', 'Moisturizing Cream (Ceramides + MVE)', 'Moisturizer', 'Krim pelembap tebal dengan teknologi MVE Delivery yang melepaskan kelembapan dan 3 ceramide esensial secara bertahap selama 24 jam penuh.', 'Ceramides 1, 3, 6-II, Hyaluronic Acid, Petrolatum, Dimethicone', 'Dryness', 'Pagi & Malam: Aplikasikan pada kulit kering atau bersisik pada wajah dan leher sesuai kebutuhan.', 'active', '269000', 'Dry, Very Dry, Compromised Skin', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('24', 'EltaMD', 'UV Clear Broad-Spectrum SPF 46', 'Sunscreen', 'Sunscreen mineral hibrida bebas minyak berformulasi medis dengan Niacinamide dan Zinc Oxide murni yang menenangkan kulit sensitif dan rawan jerawat.', '9.0% Transparent Zinc Oxide, 7.5% Octinoxate, 5% Niacinamide, Hyaluronic Acid, Vitamin E', 'Sun Protection', 'Pagi Hari: Aplikasikan sebanyak 2 jari ke seluruh wajah dan leher 15 menit sebelum terkena sinar matahari.', 'active', '399000', 'Acne-Prone, Sensitive, Hyperpigmentation', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('25', 'La Roche-Posay', 'Anthelios UVMune 400 Invisible Fluid SPF 50+', 'Sunscreen', 'Sunscreen teknologi filter Mexoryl 400 mutakhir yang memberikan proteksi superior terhadap sinar UVA ultra-panjang. Tahan air, keringat, dan tidak perih di mata.', 'Mexoryl 400, Netlock Technology, Thermal Spring Water, Glycerin', 'Sun Protection', 'Pagi Hari: Kocok botol sebelum dipakai, aplikasikan 2 jari merata ke wajah dan leher. Reapply tiap 2 jam bila berkeringat/berenang.', 'active', '360000', 'All Skin Types, Sensitive, Outdoor', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('26', 'CeraVe', 'Hydrating Mineral Sunscreen SPF 30', 'Sunscreen', 'Tabir surya 100% mineral fisik dengan Titanium Dioxide dan Zinc Oxide yang membentuk lapisan perisai pelindung tanpa memicu iritasi di kulit sensitif.', '100% Mineral Zinc Oxide & Titanium Dioxide, Ceramides 1, 3, 6-II, Niacinamide', 'Sun Protection', 'Pagi Hari: Oleskan sedikit demi sedikit ke seluruh permukaan wajah hingga merata.', 'active', '289000', 'Sensitive, Dry, Reactive Skin', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('27', 'Azarine', 'Hydrasoothe Sunscreen Gel SPF 45 PA++++', 'Sunscreen', 'Sunscreen gel berbahan dasar air yang ringan, dingin, tanpa white cast, dan bebas alkohol serta silikon. Sangat nyaman untuk iklim tropis Indonesia.', 'Propolis, Aloe Vera, Green Tea, Royal Jelly, Pomegranate Extract', 'Sun Protection', 'Pagi Hari: Oleskan merata 2 ruas jari pada wajah dan leher setelah pelembap.', 'active', '68000', 'Oily, Combination, Acne-Prone', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('28', 'Skin Aqua', 'UV Moisture Gel SPF 30 PA++', 'Sunscreen', 'Pelembap tabir surya harian berbahan dasar air yang menyegarkan dengan Hyaluronic Acid dan Kolagen untuk menjaga kelembutan kulit sehari-hari.', 'Improved Hyaluronic Acid (AcHA), Collagen, Vitamin B5 & E', 'Sun Protection', 'Pagi Hari: Ratakan ke seluruh wajah dan leher sebelum beraktivitas di luar ruangan.', 'active', '65000', 'Normal to Oily, Daily Indoor', '2026-10-16 14:29:59', '2026-10-16 14:29:59');
INSERT INTO `products` (`id`, `brand`, `name`, `category`, `description`, `ingredients`, `skin_concern`, `usage_guidance`, `status`, `price`, `skin_type`, `created_at`, `updated_at`) VALUES ('29', 'La Roche-Posay', 'Anthelios Melt-In Milk Sunscreen SPF 60', 'Sunscreen', 'Tabir surya bertekstur susu velvet yang melembapkan secara mendalam dengan proteksi spektrum luas Cell-Ox Shield antioksidan.', 'Avobenzone, Homosalate, Octisalate, Octocrylene, Senna Alata Extract', 'Sun Protection', 'Pagi Hari: Usapkan merata ke seluruh wajah dan bagian tubuh yang terpapar matahari.', 'active', '450000', 'Dry, Normal', '2026-10-16 14:29:59', '2026-10-16 14:29:59');

SET FOREIGN_KEY_CHECKS = 1;
