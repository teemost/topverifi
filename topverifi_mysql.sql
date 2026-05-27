-- ============================================================
-- TopVerifi — Full MySQL Schema + Seed Data
-- Import via: phpMyAdmin > Import > select this file
-- Compatible with: MySQL 5.7+ / MariaDB 10.3+
-- Generated: 2026-05-27
-- ============================================================
-- After import, log in to admin panel at /adminlogin
--   Email:    admin@blues.com
--   Password: admin123
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ============================================================
-- Laravel migration tracker
-- ============================================================
CREATE TABLE IF NOT EXISTS `migrations` (
  `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch`     INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2026_05_23_170156_create_admins_users_table', 1),
('2026_05_23_170157_create_listings_table', 1),
('2026_05_23_170157_create_profiles_table', 1),
('2026_05_23_170158_create_purchases_table', 1),
('2026_05_23_170159_create_wallets_table', 1),
('2026_05_23_170159_create_wallet_transactions_table', 1),
('2026_05_23_170200_create_support_tickets_table', 1),
('2026_05_23_170201_create_notifications_table', 1),
('2026_05_23_170202_create_admin_audit_log_table', 1),
('2026_05_23_211537_add_status_to_users_table', 1),
('2026_05_23_211538_create_settings_table', 1),
('2026_05_23_211758_add_extra_fields_to_listings_table', 1),
('2026_05_23_215407_add_role_to_admins_users_table', 1),
('2026_05_23_215408_create_password_reset_tokens_table', 1),
('2026_05_24_000001_create_wishlists_table', 1),
('2026_05_24_000002_create_listing_categories_table', 1),
('2026_05_24_000003_add_extra_fields_to_listing_categories_table', 1),
('2026_05_24_070448_create_announcements_table', 1),
('2026_05_25_000001_update_listing_categories_add_twitter_telegram', 1),
('2026_05_25_000002_create_virtual_number_orders_table', 1),
('2026_05_25_000003_add_login_details_to_listings_table', 1),
('2026_05_25_100001_add_email_notifications_to_users_table', 1),
('2026_05_25_100002_add_referred_by_to_users_table', 1),
('2026_05_25_200001_create_listing_reviews_table', 1),
('2026_05_26_000001_add_referral_tracking_to_users', 1),
('2026_05_26_000002_add_login_tracking_to_users', 1),
('2026_05_26_100001_create_bank_transfer_payments_table', 1),
('2026_05_27_000001_add_provider_to_virtual_number_orders', 1),
('2026_05_27_100001_add_image_to_listing_categories', 1),
('2026_05_27_200001_add_user_confirmed_at_to_bank_transfer_payments', 1),
('2026_05_27_300001_create_boosting_orders_table', 1);

-- ============================================================
-- USERS
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`                 VARCHAR(255) NOT NULL,
  `email`                VARCHAR(255) NOT NULL,
  `status`               VARCHAR(255) NOT NULL DEFAULT 'active',
  `email_notifications`  TINYINT(1) NOT NULL DEFAULT 1,
  `referred_by`          BIGINT UNSIGNED NULL DEFAULT NULL,
  `referral_deposited`   TINYINT(1) NOT NULL DEFAULT 0,
  `referral_purchased`   TINYINT(1) NOT NULL DEFAULT 0,
  `referral_bonus_paid`  TINYINT(1) NOT NULL DEFAULT 0,
  `last_login_at`        TIMESTAMP NULL DEFAULT NULL,
  `last_login_ip`        VARCHAR(45) NULL DEFAULT NULL,
  `email_verified_at`    TIMESTAMP NULL DEFAULT NULL,
  `password`             VARCHAR(255) NOT NULL,
  `remember_token`       VARCHAR(100) NULL DEFAULT NULL,
  `created_at`           TIMESTAMP NULL DEFAULT NULL,
  `updated_at`           TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PASSWORD RESET TOKENS
-- ============================================================
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email`      VARCHAR(255) NOT NULL,
  `token`      VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SESSIONS
-- ============================================================
CREATE TABLE IF NOT EXISTS `sessions` (
  `id`            VARCHAR(255) NOT NULL,
  `user_id`       BIGINT UNSIGNED NULL DEFAULT NULL,
  `ip_address`    VARCHAR(45) NULL DEFAULT NULL,
  `user_agent`    TEXT NULL DEFAULT NULL,
  `payload`       LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CACHE
-- ============================================================
CREATE TABLE IF NOT EXISTS `cache` (
  `key`        VARCHAR(255) NOT NULL,
  `value`      MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key`        VARCHAR(255) NOT NULL,
  `owner`      VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- JOBS / QUEUES
-- ============================================================
CREATE TABLE IF NOT EXISTS `jobs` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue`        VARCHAR(255) NOT NULL,
  `payload`      LONGTEXT NOT NULL,
  `attempts`     TINYINT UNSIGNED NOT NULL,
  `reserved_at`  INT UNSIGNED NULL DEFAULT NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at`   INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id`             VARCHAR(255) NOT NULL,
  `name`           VARCHAR(255) NOT NULL,
  `total_jobs`     INT NOT NULL,
  `pending_jobs`   INT NOT NULL,
  `failed_jobs`    INT NOT NULL,
  `failed_job_ids` LONGTEXT NOT NULL,
  `options`        MEDIUMTEXT NULL DEFAULT NULL,
  `cancelled_at`   INT NULL DEFAULT NULL,
  `created_at`     INT NOT NULL,
  `finished_at`    INT NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid`       VARCHAR(255) NOT NULL,
  `connection` TEXT NOT NULL,
  `queue`      TEXT NOT NULL,
  `payload`    LONGTEXT NOT NULL,
  `exception`  LONGTEXT NOT NULL,
  `failed_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ADMIN USERS
-- ============================================================
CREATE TABLE IF NOT EXISTS `admins_users` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`        VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `display_name` VARCHAR(255) NULL DEFAULT NULL,
  `role`         VARCHAR(255) NOT NULL DEFAULT 'admin',
  `avatar_url`   VARCHAR(255) NULL DEFAULT NULL,
  `is_active`    TINYINT(1) NOT NULL DEFAULT 1,
  `last_login`   TIMESTAMP NULL DEFAULT NULL,
  `created_at`   TIMESTAMP NULL DEFAULT NULL,
  `updated_at`   TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin account: admin@blues.com / admin123
INSERT INTO `admins_users` (`email`, `password_hash`, `display_name`, `role`, `is_active`, `created_at`, `updated_at`)
VALUES ('admin@blues.com', '$2y$12$JuNQP18xCqk6zK5hkHTXm.1yy9ulDQ/VVLAVFcz.Gr6iiBjf5.fOu', 'Super Admin', 'admin', 1, NOW(), NOW());

-- ============================================================
-- SETTINGS
-- ============================================================
CREATE TABLE IF NOT EXISTS `settings` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key`        VARCHAR(255) NOT NULL,
  `value`      TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ANNOUNCEMENTS
-- ============================================================
CREATE TABLE IF NOT EXISTS `announcements` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`            VARCHAR(255) NOT NULL,
  `message`          TEXT NOT NULL,
  `type`             VARCHAR(255) NOT NULL DEFAULT 'info',
  `sent_by`          BIGINT UNSIGNED NULL DEFAULT NULL,
  `email_sent`       TINYINT(1) NOT NULL DEFAULT 0,
  `recipients_count` INT NOT NULL DEFAULT 0,
  `created_at`       TIMESTAMP NULL DEFAULT NULL,
  `updated_at`       TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- LISTING CATEGORIES (kept for DB completeness, not shown in UI)
-- ============================================================
CREATE TABLE IF NOT EXISTS `listing_categories` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(255) NOT NULL,
  `slug`        VARCHAR(255) NULL DEFAULT NULL,
  `description` VARCHAR(255) NULL DEFAULT NULL,
  `icon`        VARCHAR(255) NULL DEFAULT NULL,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `image_path`  VARCHAR(255) NULL DEFAULT NULL,
  `created_at`  TIMESTAMP NULL DEFAULT NULL,
  `updated_at`  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `listing_categories_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `listing_categories` (`name`, `created_at`, `updated_at`) VALUES
('Facebook', NOW(), NOW()),
('Instagram', NOW(), NOW()),
('TikTok', NOW(), NOW()),
('Virtual Numbers', NOW(), NOW()),
('Twitter', NOW(), NOW()),
('Telegram', NOW(), NOW());

-- ============================================================
-- LISTINGS (legacy, kept for DB completeness)
-- ============================================================
CREATE TABLE IF NOT EXISTS `listings` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`        VARCHAR(255) NOT NULL,
  `description`  TEXT NULL DEFAULT NULL,
  `login_details` TEXT NULL DEFAULT NULL,
  `category`     VARCHAR(255) NULL DEFAULT NULL,
  `price`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock`        INT NOT NULL DEFAULT 0,
  `is_active`    TINYINT(1) NOT NULL DEFAULT 1,
  `featured`     TINYINT(1) NOT NULL DEFAULT 0,
  `image_url`    VARCHAR(255) NULL DEFAULT NULL,
  `image_path`   VARCHAR(255) NULL DEFAULT NULL,
  `created_at`   TIMESTAMP NULL DEFAULT NULL,
  `updated_at`   TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PROFILES
-- ============================================================
CREATE TABLE IF NOT EXISTS `profiles` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`      BIGINT UNSIGNED NOT NULL,
  `username`     VARCHAR(255) NULL DEFAULT NULL,
  `display_name` VARCHAR(255) NULL DEFAULT NULL,
  `avatar_url`   VARCHAR(255) NULL DEFAULT NULL,
  `status`       ENUM('active','suspended','banned') NOT NULL DEFAULT 'active',
  `referral_code` VARCHAR(255) NULL DEFAULT NULL,
  `created_at`   TIMESTAMP NULL DEFAULT NULL,
  `updated_at`   TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `profiles_user_id_unique` (`user_id`),
  UNIQUE KEY `profiles_username_unique` (`username`),
  UNIQUE KEY `profiles_referral_code_unique` (`referral_code`),
  CONSTRAINT `profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- WALLETS
-- ============================================================
CREATE TABLE IF NOT EXISTS `wallets` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `balance`    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wallets_user_id_unique` (`user_id`),
  CONSTRAINT `wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- WALLET TRANSACTIONS
-- Note: type uses VARCHAR (not ENUM) to support all code-level values:
--   deposit, withdrawal, purchase, refund, referral_bonus,
--   admin_credit, admin_debit, debit, credit
-- ============================================================
CREATE TABLE IF NOT EXISTS `wallet_transactions` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     BIGINT UNSIGNED NOT NULL,
  `amount`      DECIMAL(10,2) NOT NULL,
  `type`        VARCHAR(50) NOT NULL,
  `reference`   VARCHAR(255) NULL DEFAULT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `created_at`  TIMESTAMP NULL DEFAULT NULL,
  `updated_at`  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wallet_transactions_user_id_index` (`user_id`),
  CONSTRAINT `wallet_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- NOTIFICATIONS
-- ============================================================
CREATE TABLE IF NOT EXISTS `notifications` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `title`      VARCHAR(255) NOT NULL,
  `message`    TEXT NOT NULL,
  `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
  `type`       VARCHAR(255) NOT NULL DEFAULT 'info',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_index` (`user_id`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SUPPORT TICKETS
-- ============================================================
CREATE TABLE IF NOT EXISTS `support_tickets` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     BIGINT UNSIGNED NOT NULL,
  `subject`     VARCHAR(255) NOT NULL,
  `message`     TEXT NOT NULL,
  `admin_reply` TEXT NULL DEFAULT NULL,
  `status`      ENUM('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `priority`    ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
  `created_at`  TIMESTAMP NULL DEFAULT NULL,
  `updated_at`  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_tickets_user_id_index` (`user_id`),
  CONSTRAINT `support_tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ADMIN AUDIT LOG
-- ============================================================
CREATE TABLE IF NOT EXISTS `admin_audit_log` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id`    BIGINT UNSIGNED NULL DEFAULT NULL,
  `action`      VARCHAR(255) NOT NULL,
  `target_type` VARCHAR(255) NULL DEFAULT NULL,
  `target_id`   BIGINT UNSIGNED NULL DEFAULT NULL,
  `details`     JSON NULL DEFAULT NULL,
  `ip_address`  VARCHAR(255) NULL DEFAULT NULL,
  `created_at`  TIMESTAMP NULL DEFAULT NULL,
  `updated_at`  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_audit_log_admin_id_index` (`admin_id`),
  CONSTRAINT `admin_audit_log_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PURCHASES (legacy, kept for DB completeness)
-- ============================================================
CREATE TABLE IF NOT EXISTS `purchases` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`       BIGINT UNSIGNED NOT NULL,
  `listing_id`    BIGINT UNSIGNED NOT NULL,
  `amount`        DECIMAL(10,2) NOT NULL,
  `status`        ENUM('pending','completed','refunded','disputed') NOT NULL DEFAULT 'pending',
  `delivery_data` TEXT NULL DEFAULT NULL,
  `created_at`    TIMESTAMP NULL DEFAULT NULL,
  `updated_at`    TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchases_user_id_index` (`user_id`),
  KEY `purchases_listing_id_index` (`listing_id`),
  CONSTRAINT `purchases_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchases_listing_id_foreign` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- WISHLISTS (legacy, kept for DB completeness)
-- ============================================================
CREATE TABLE IF NOT EXISTS `wishlists` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `listing_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wishlists_user_id_listing_id_unique` (`user_id`, `listing_id`),
  CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlists_listing_id_foreign` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- LISTING REVIEWS (legacy, kept for DB completeness)
-- ============================================================
CREATE TABLE IF NOT EXISTS `listing_reviews` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     BIGINT UNSIGNED NOT NULL,
  `listing_id`  BIGINT UNSIGNED NOT NULL,
  `purchase_id` BIGINT UNSIGNED NOT NULL,
  `rating`      TINYINT NOT NULL,
  `comment`     TEXT NULL DEFAULT NULL,
  `created_at`  TIMESTAMP NULL DEFAULT NULL,
  `updated_at`  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `listing_reviews_user_id_purchase_id_unique` (`user_id`, `purchase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BANK TRANSFER PAYMENTS
-- ============================================================
CREATE TABLE IF NOT EXISTS `bank_transfer_payments` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`           BIGINT UNSIGNED NOT NULL,
  `type`              VARCHAR(20) NOT NULL DEFAULT 'wallet_topup',
  `listing_id`        BIGINT UNSIGNED NULL DEFAULT NULL,
  `purchase_id`       BIGINT UNSIGNED NULL DEFAULT NULL,
  `amount`            DECIMAL(10,2) NOT NULL,
  `reference`         VARCHAR(100) NOT NULL,
  `status`            VARCHAR(20) NOT NULL DEFAULT 'pending',
  `admin_note`        TEXT NULL DEFAULT NULL,
  `confirmed_at`      TIMESTAMP NULL DEFAULT NULL,
  `user_confirmed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at`        TIMESTAMP NULL DEFAULT NULL,
  `updated_at`        TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bank_transfer_payments_reference_unique` (`reference`),
  KEY `bank_transfer_payments_user_id_index` (`user_id`),
  CONSTRAINT `bank_transfer_payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bank_transfer_payments_listing_id_foreign` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bank_transfer_payments_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- VIRTUAL NUMBER ORDERS
-- ============================================================
CREATE TABLE IF NOT EXISTS `virtual_number_orders` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`           BIGINT UNSIGNED NOT NULL,
  `provider`          VARCHAR(255) NOT NULL DEFAULT 'logsplug',
  `external_order_id` VARCHAR(255) NULL DEFAULT NULL,
  `service`           VARCHAR(255) NOT NULL,
  `country`           VARCHAR(255) NOT NULL DEFAULT 'ng',
  `phone_number`      VARCHAR(255) NULL DEFAULT NULL,
  `sms_code`          VARCHAR(255) NULL DEFAULT NULL,
  `cost`              DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status`            ENUM('pending','active','completed','cancelled','failed') NOT NULL DEFAULT 'pending',
  `raw_response`      TEXT NULL DEFAULT NULL,
  `created_at`        TIMESTAMP NULL DEFAULT NULL,
  `updated_at`        TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `virtual_number_orders_external_order_id_index` (`external_order_id`),
  KEY `virtual_number_orders_user_id_index` (`user_id`),
  CONSTRAINT `virtual_number_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BOOSTING ORDERS (SMM)
-- ============================================================
CREATE TABLE IF NOT EXISTS `boosting_orders` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`      BIGINT UNSIGNED NOT NULL,
  `jap_order_id` BIGINT NULL DEFAULT NULL,
  `service_id`   INT NOT NULL,
  `service_name` VARCHAR(255) NOT NULL,
  `category`     VARCHAR(255) NULL DEFAULT NULL,
  `link`         VARCHAR(255) NOT NULL,
  `quantity`     INT NOT NULL,
  `charge`       DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `start_count`  INT NULL DEFAULT NULL,
  `remains`      INT NULL DEFAULT NULL,
  `status`       VARCHAR(255) NOT NULL DEFAULT 'pending',
  `created_at`   TIMESTAMP NULL DEFAULT NULL,
  `updated_at`   TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `boosting_orders_user_id_index` (`user_id`),
  CONSTRAINT `boosting_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- DONE
-- After import:
-- 1. Upload your Laravel .env file with DB credentials + APP_KEY
-- 2. Run: php artisan key:generate  (if APP_KEY not set)
-- 3. Set JAP API key at /adminlogin → Settings → SMM Boosting
-- 4. Set Paystack keys at /adminlogin → Settings → Payment
-- ============================================================
