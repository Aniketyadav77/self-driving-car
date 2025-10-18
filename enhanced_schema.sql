-- Enhanced Zephyr Database Schema
-- Additional tables for improved functionality

-- Enhanced participants table
CREATE TABLE IF NOT EXISTS `participants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `participant_id` varchar(20) NOT NULL UNIQUE,
  `fname` varchar(50) NOT NULL,
  `mname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `phone` varchar(15) DEFAULT NULL,
  `college` varchar(100) DEFAULT NULL,
  `course` varchar(50) DEFAULT NULL,
  `year` varchar(10) DEFAULT NULL,
  `status` enum('active','inactive','blocked') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_participant_id` (`participant_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Enhanced admin table with proper security
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','moderator') DEFAULT 'admin',
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Login logs for security monitoring
CREATE TABLE IF NOT EXISTS `login_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_type` enum('admin','participant') NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `status` enum('success','failed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_type_id` (`user_type`, `user_id`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Enhanced events table
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` enum('dance','music','drama','literary','sports','tech') NOT NULL,
  `description` text,
  `rules` text,
  `max_participants` int(11) DEFAULT NULL,
  `registration_fee` decimal(10,2) DEFAULT 0.00,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `registration_deadline` datetime NOT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `status` enum('upcoming','ongoing','completed','cancelled') DEFAULT 'upcoming',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_status` (`status`),
  KEY `idx_dates` (`start_date`, `end_date`),
  FOREIGN KEY (`created_by`) REFERENCES `admin` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Event registrations
CREATE TABLE IF NOT EXISTS `event_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `participant_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `team_name` varchar(100) DEFAULT NULL,
  `additional_info` text,
  `payment_status` enum('pending','paid','refunded') DEFAULT 'pending',
  `registration_status` enum('registered','confirmed','cancelled') DEFAULT 'registered',
  `registered_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_participant_event` (`participant_id`, `event_id`),
  KEY `idx_event_id` (`event_id`),
  KEY `idx_payment_status` (`payment_status`),
  FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- System settings
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL UNIQUE,
  `setting_value` text,
  `description` text,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Audit trail for important actions
CREATE TABLE IF NOT EXISTS `audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_type` enum('admin','participant') NOT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_type_id` (`user_type`, `user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_table_record` (`table_name`, `record_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin (password: 'admin123' - change in production!)
INSERT INTO `admin` (`name`, `email`, `password`, `role`) VALUES 
('System Administrator', 'admin@zephyr.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin')
ON DUPLICATE KEY UPDATE email = email;

-- Insert default settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES 
('site_name', 'Zephyr Festival Management', 'Name of the festival'),
('registration_open', '1', 'Whether registration is open (1) or closed (0)'),
('max_registrations_per_day', '100', 'Maximum registrations allowed per day'),
('contact_email', 'contact@zephyr.com', 'Contact email for inquiries'),
('festival_date', '2024-07-15', 'Main festival date')
ON DUPLICATE KEY UPDATE setting_key = setting_key;

-- Create indexes for better performance
CREATE INDEX idx_participants_created_at ON participants(created_at);
CREATE INDEX idx_event_registrations_registered_at ON event_registrations(registered_at);
CREATE INDEX idx_login_logs_created_at ON login_logs(created_at);

-- Create view for participant statistics
CREATE OR REPLACE VIEW participant_stats AS
SELECT 
    COUNT(*) as total_participants,
    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_participants,
    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_registrations,
    COUNT(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as week_registrations
FROM participants;

-- Create view for event statistics
CREATE OR REPLACE VIEW event_stats AS
SELECT 
    e.id,
    e.name,
    e.category,
    COUNT(er.id) as registered_count,
    e.max_participants,
    (COUNT(er.id) / NULLIF(e.max_participants, 0)) * 100 as fill_percentage
FROM events e
LEFT JOIN event_registrations er ON e.id = er.event_id AND er.registration_status = 'confirmed'
GROUP BY e.id;

-- Create stored procedure for participant registration
DELIMITER //
CREATE PROCEDURE RegisterParticipant(
    IN p_fname VARCHAR(50),
    IN p_mname VARCHAR(50),
    IN p_lname VARCHAR(50),
    IN p_email VARCHAR(100),
    IN p_phone VARCHAR(15),
    IN p_college VARCHAR(100),
    IN p_course VARCHAR(50),
    IN p_year VARCHAR(10),
    OUT p_participant_id VARCHAR(20),
    OUT p_result VARCHAR(100)
)
BEGIN
    DECLARE duplicate_count INT DEFAULT 0;
    DECLARE new_id INT;
    
    -- Check for duplicate email
    SELECT COUNT(*) INTO duplicate_count FROM participants WHERE email = p_email;
    
    IF duplicate_count > 0 THEN
        SET p_result = 'ERROR: Email already registered';
        SET p_participant_id = NULL;
    ELSE
        -- Insert new participant
        INSERT INTO participants (fname, mname, lname, email, phone, college, course, year)
        VALUES (p_fname, p_mname, p_lname, p_email, p_phone, p_college, p_course, p_year);
        
        SET new_id = LAST_INSERT_ID();
        SET p_participant_id = CONCAT('ZEP', LPAD(new_id, 6, '0'));
        
        -- Update with generated participant_id
        UPDATE participants SET participant_id = p_participant_id WHERE id = new_id;
        
        SET p_result = 'SUCCESS: Participant registered successfully';
    END IF;
END //
DELIMITER ;