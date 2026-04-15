-- MySQL schema voor Bezoekersregistratie
-- Bevat de geüpdatete structuur inclusief status en expected_arrival/departure tijden

DROP TABLE IF EXISTS `visits`;
DROP TABLE IF EXISTS `visitors`;
DROP TABLE IF EXISTS `users`;

-- 1. Users table (Medewerkers, Receptie, Admin)
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'receptionist', 'employee') NOT NULL DEFAULT 'employee',
    `department` VARCHAR(255) NULL,
    `phone` VARCHAR(255) NULL,
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Visitors table (De gasten zelf)
CREATE TABLE `visitors` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NULL UNIQUE,
    `company` VARCHAR(255) NULL,
    `phone` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Visits table (De koppeltabel voor het daadwerkelijke bezoek)
CREATE TABLE `visits` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `visitor_id` BIGINT UNSIGNED NOT NULL,
    `host_employee_id` BIGINT UNSIGNED NOT NULL,
    `registered_by_user_id` BIGINT UNSIGNED NOT NULL,
    
    `status` ENUM('expected', 'active', 'completed', 'cancelled') NOT NULL DEFAULT 'active',
    
    `expected_arrival_time` TIMESTAMP NULL DEFAULT NULL,
    `check_in_time` TIMESTAMP NULL DEFAULT NULL,
    `expected_departure_time` TIMESTAMP NULL DEFAULT NULL,
    `check_out_time` TIMESTAMP NULL DEFAULT NULL,
    
    `reason_of_visit` TEXT NULL,
    `badge_sent` TINYINT(1) NOT NULL DEFAULT 0,
    
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    
    -- Foreign Keys
    CONSTRAINT `visits_visitor_id_foreign` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE,
    CONSTRAINT `visits_host_id_foreign` FOREIGN KEY (`host_employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `visits_registered_by_foreign` FOREIGN KEY (`registered_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexes voor snelle queries
CREATE INDEX `visits_check_in_time_index` ON `visits` (`check_in_time`);
CREATE INDEX `visits_check_out_time_index` ON `visits` (`check_out_time`);
