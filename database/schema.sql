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

-- ==========================================
-- DUMMY DATA / FAKE DATA (SEED)
-- ==========================================

-- 1. Voeg Users toe (Wachtwoord overal: 'password')
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `department`, `created_at`, `updated_at`) VALUES
(1, 'Admin Gebruiker', 'admin@bedrijf.nl', '$2y$12$cO2mS8hYj1/K.Xz.0Mv7F.C.J.6K2QZ3Yc2G9gR.R5x.n.a9m3eY2', 'admin', 'IT Beheer', NOW(), NOW()),
(2, 'Sanne Receptie', 'receptie@bedrijf.nl', '$2y$12$cO2mS8hYj1/K.Xz.0Mv7F.C.J.6K2QZ3Yc2G9gR.R5x.n.a9m3eY2', 'receptionist', 'Facilitaire Zaken', NOW(), NOW()),
(3, 'Peter de Vries', 'peter@bedrijf.nl', '$2y$12$cO2mS8hYj1/K.Xz.0Mv7F.C.J.6K2QZ3Yc2G9gR.R5x.n.a9m3eY2', 'employee', 'Marketing', NOW(), NOW()),
(4, 'Lisa van den Berg', 'lisa@bedrijf.nl', '$2y$12$cO2mS8hYj1/K.Xz.0Mv7F.C.J.6K2QZ3Yc2G9gR.R5x.n.a9m3eY2', 'employee', 'HR', NOW(), NOW());

-- 2. Voeg Visitors (gasten) toe
INSERT INTO `visitors` (`id`, `name`, `email`, `company`, `phone`, `created_at`, `updated_at`) VALUES
(1, 'Jan Jansen', 'jan@consultancy.nl', 'Consultancy BV', '0612345678', NOW(), NOW()),
(2, 'Emma Willems', 'emma@designstudio.nl', 'Design Studio', '0687654321', NOW(), NOW()),
(3, 'Thomas Bakker', 'thomas@softwarehuis.nl', 'SoftwareHuis BV', '0645678912', NOW(), NOW());

-- 3. Voeg Visits (bezoeken) toe
INSERT INTO `visits` (`id`, `visitor_id`, `host_employee_id`, `registered_by_user_id`, `status`, `expected_arrival_time`, `check_in_time`, `expected_departure_time`, `check_out_time`, `reason_of_visit`, `created_at`, `updated_at`) VALUES
-- Een inkomend toekomstig bezoek (voor over 2 uur)
(1, 1, 3, 2, 'expected', DATE_ADD(NOW(), INTERVAL 2 HOUR), NULL, DATE_ADD(NOW(), INTERVAL 4 HOUR), NULL, 'Marketing strategie bespreken', NOW(), NOW()),
-- Een actief bezoek (nu in het pand, ingecheckt door receptionist)
(2, 2, 4, 2, 'active', NULL, DATE_SUB(NOW(), INTERVAL 1 HOUR), DATE_ADD(NOW(), INTERVAL 1 HOUR), NULL, 'Sollicitatiegesprek', NOW(), NOW()),
-- Een afgerond bezoek van gisteren
(3, 3, 3, 3, 'completed', NULL, DATE_SUB(NOW(), INTERVAL 25 HOUR), NULL, DATE_SUB(NOW(), INTERVAL 23 HOUR), 'Technische demo software', DATE_SUB(NOW(), INTERVAL 26 HOUR), DATE_SUB(NOW(), INTERVAL 22 HOUR));

