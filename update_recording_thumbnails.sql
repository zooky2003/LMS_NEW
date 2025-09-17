-- Add recording thumbnails table for admin-uploaded thumbnails
CREATE TABLE IF NOT EXISTS `recording_thumbnails` (
  `id` int NOT NULL AUTO_INCREMENT,
  `recording_id` int NOT NULL,
  `thumbnail_url` varchar(255) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `recording_id` (`recording_id`),
  KEY `recording_id_idx` (`recording_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;