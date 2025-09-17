-- Database fixes for LMS payment system
-- Run this script to fix all database-related issues

-- 1. Create recording thumbnails table for admin-uploaded thumbnails
CREATE TABLE IF NOT EXISTS `recording_thumbnails` (
  `id` int NOT NULL AUTO_INCREMENT,
  `recording_id` int NOT NULL,
  `thumbnail_url` varchar(255) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `recording_id` (`recording_id`),
  KEY `recording_id_idx` (`recording_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 2. Ensure enrollments table has status column (if not already present)
ALTER TABLE `enrollments` 
ADD COLUMN IF NOT EXISTS `status` enum('Pending','Active','Inactive') NOT NULL DEFAULT 'Active';

-- 3. Ensure payments table has all required columns
ALTER TABLE `payments` 
ADD COLUMN IF NOT EXISTS `payment_date` date DEFAULT NULL;

-- 4. Update any existing enrollments without status to have 'Active' status
UPDATE `enrollments` SET `status` = 'Active' WHERE `status` IS NULL OR `status` = '';

-- 5. Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_enrollments_user_class` ON `enrollments` (`user_id`, `class_id`);
CREATE INDEX IF NOT EXISTS `idx_payments_user_class` ON `payments` (`user_id`, `class_id`);
CREATE INDEX IF NOT EXISTS `idx_payments_status` ON `payments` (`status`);
CREATE INDEX IF NOT EXISTS `idx_class_recordings_class` ON `class_recordings` (`class_id`);

-- 6. Ensure proper foreign key relationships (if using InnoDB)
-- Note: These are commented out since the current setup uses MyISAM
-- If you want to switch to InnoDB for better data integrity, uncomment these:

/*
ALTER TABLE `enrollments` ENGINE=InnoDB;
ALTER TABLE `payments` ENGINE=InnoDB;
ALTER TABLE `class_recordings` ENGINE=InnoDB;
ALTER TABLE `recording_thumbnails` ENGINE=InnoDB;

ALTER TABLE `enrollments` 
ADD CONSTRAINT `fk_enrollments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_enrollments_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

ALTER TABLE `payments` 
ADD CONSTRAINT `fk_payments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_payments_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

ALTER TABLE `class_recordings` 
ADD CONSTRAINT `fk_class_recordings_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_class_recordings_recording` FOREIGN KEY (`recording_id`) REFERENCES `recordings` (`id`) ON DELETE CASCADE;

ALTER TABLE `recording_thumbnails` 
ADD CONSTRAINT `fk_recording_thumbnails_recording` FOREIGN KEY (`recording_id`) REFERENCES `recordings` (`id`) ON DELETE CASCADE;
*/