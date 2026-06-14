-- Documentation table schema for Helping Hands NGO.php
-- Run this in phpMyAdmin or via your MySQL client to create the table

CREATE TABLE IF NOT EXISTS `documentation` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `category` VARCHAR(100) DEFAULT NULL,
  `event_id` INT DEFAULT NULL,
  `media_path` VARCHAR(255) NOT NULL,
  `media_type` ENUM('photo','video') DEFAULT 'photo',
  `uploader_type` ENUM('Admin','Volunteer') DEFAULT 'Admin',
  `upload_date` DATE DEFAULT CURRENT_DATE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Example inserts (update paths to files you uploaded in `uploads/documentation/`)
INSERT INTO `documentation` (`title`, `description`, `category`, `event_id`, `media_path`, `media_type`, `uploader_type`) VALUES
('Sample Album Cover', 'Cover image for sample album', 'Community Outreach', NULL, 'uploads/documentation/sample1.jpg', 'photo', 'Admin'),
('Sample Album Item 2', 'Second image in album', 'Community Outreach', NULL, 'uploads/documentation/sample2.jpg', 'photo', 'Admin'),
('Sample Video', 'A sample video item', 'Community Outreach', NULL, 'uploads/documentation/sample3.mp4', 'video', 'Admin');
