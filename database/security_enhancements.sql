-- Security Enhancement Database Schema
-- Zephyr Festival Management System

-- Rate limiting table
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(45) NOT NULL,
    timestamp INT NOT NULL,
    INDEX idx_identifier_timestamp (identifier, timestamp),
    INDEX idx_timestamp (timestamp)
);

-- Login attempts tracking
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255),
    ip_address VARCHAR(45) NOT NULL,
    success TINYINT(1) DEFAULT 0,
    timestamp INT NOT NULL,
    INDEX idx_email_timestamp (email, timestamp),
    INDEX idx_ip_timestamp (ip_address, timestamp),
    INDEX idx_success_timestamp (success, timestamp)
);

-- Security logs
CREATE TABLE IF NOT EXISTS security_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    message TEXT NOT NULL,
    level ENUM('info', 'warning', 'error', 'critical') DEFAULT 'info',
    ip_address VARCHAR(45),
    user_agent TEXT,
    timestamp INT NOT NULL,
    INDEX idx_level_timestamp (level, timestamp),
    INDEX idx_user_timestamp (user_id, timestamp),
    INDEX idx_timestamp (timestamp)
);

-- Notification settings
CREATE TABLE IF NOT EXISTS notification_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_type ENUM('participant', 'admin') NOT NULL,
    email_notifications TINYINT(1) DEFAULT 1,
    push_notifications TINYINT(1) DEFAULT 1,
    event_reminders TINYINT(1) DEFAULT 1,
    registration_updates TINYINT(1) DEFAULT 1,
    general_announcements TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user (user_id, user_type)
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'danger') DEFAULT 'info',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    recipient_id INT NOT NULL,
    recipient_type ENUM('participant', 'admin') NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_recipient (recipient_id, recipient_type),
    INDEX idx_read_status (is_read, created_at),
    INDEX idx_created_at (created_at)
);

-- Achievements table
CREATE TABLE IF NOT EXISTS achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participant_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    badge_icon VARCHAR(100),
    earned_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    event_id INT,
    FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL,
    INDEX idx_participant (participant_id),
    INDEX idx_earned_date (earned_date)
);

-- Attendance table
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participant_id INT NOT NULL,
    event_id INT NOT NULL,
    marked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    marked_by INT,
    FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (marked_by) REFERENCES admin(id) ON DELETE SET NULL,
    UNIQUE KEY unique_attendance (participant_id, event_id),
    INDEX idx_event (event_id),
    INDEX idx_marked_at (marked_at)
);

-- Update existing tables with security enhancements

-- Add avatar column to participants table if not exists
ALTER TABLE participants 
ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) NULL AFTER bio,
ADD COLUMN IF NOT EXISTS bio TEXT NULL AFTER year,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Add registration fee to events table if not exists
ALTER TABLE events 
ADD COLUMN IF NOT EXISTS registration_fee DECIMAL(10,2) DEFAULT 0.00 AFTER max_participants,
ADD COLUMN IF NOT EXISTS venue VARCHAR(255) NULL AFTER event_date,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Add password reset fields to participants table
ALTER TABLE participants 
ADD COLUMN IF NOT EXISTS reset_token VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS reset_expires INT NULL,
ADD INDEX IF NOT EXISTS idx_reset_token (reset_token);

-- Add password reset fields to admin table
ALTER TABLE admin 
ADD COLUMN IF NOT EXISTS reset_token VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS reset_expires INT NULL,
ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS login_attempts INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS locked_until TIMESTAMP NULL,
ADD INDEX IF NOT EXISTS idx_reset_token (reset_token);

-- Add session tracking
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_type ENUM('participant', 'admin') NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    INDEX idx_session_id (session_id),
    INDEX idx_user (user_id, user_type),
    INDEX idx_expires (expires_at),
    INDEX idx_active (is_active, last_activity)
);

-- Add blocked IPs table
CREATE TABLE IF NOT EXISTS blocked_ips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    reason TEXT,
    blocked_by INT,
    blocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    is_active TINYINT(1) DEFAULT 1,
    UNIQUE KEY unique_ip (ip_address),
    FOREIGN KEY (blocked_by) REFERENCES admin(id) ON DELETE SET NULL,
    INDEX idx_ip_active (ip_address, is_active),
    INDEX idx_expires (expires_at)
);

-- Add file uploads tracking
CREATE TABLE IF NOT EXISTS file_uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_type ENUM('participant', 'admin') NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    stored_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    upload_purpose ENUM('avatar', 'document', 'event_image', 'other') DEFAULT 'other',
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    INDEX idx_user (user_id, user_type),
    INDEX idx_purpose (upload_purpose),
    INDEX idx_uploaded_at (uploaded_at)
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_participants_email ON participants(email);
CREATE INDEX IF NOT EXISTS idx_participants_phone ON participants(phone);
CREATE INDEX IF NOT EXISTS idx_participants_college ON participants(college);
CREATE INDEX IF NOT EXISTS idx_events_date ON events(event_date);
CREATE INDEX IF NOT EXISTS idx_events_status ON events(max_participants);
CREATE INDEX IF NOT EXISTS idx_participation_date ON participation(registration_date);

-- Insert default notification settings for existing users
INSERT IGNORE INTO notification_settings (user_id, user_type, email_notifications, push_notifications, event_reminders, registration_updates, general_announcements)
SELECT id, 'participant', 1, 1, 1, 1, 1 FROM participants;

INSERT IGNORE INTO notification_settings (user_id, user_type, email_notifications, push_notifications, event_reminders, registration_updates, general_announcements)
SELECT id, 'admin', 1, 1, 1, 1, 1 FROM admin;

-- Create system notification for all participants
INSERT INTO notifications (title, message, type, priority, recipient_id, recipient_type, created_at)
SELECT 
    'System Security Enhanced',
    'Welcome to the enhanced Zephyr Festival Management System! We have implemented comprehensive security measures to protect your data and improve your experience.',
    'info',
    'medium',
    id,
    'participant',
    NOW()
FROM participants;

-- Create system notification for all admins
INSERT INTO notifications (title, message, type, priority, recipient_id, recipient_type, created_at)
SELECT 
    'Security Dashboard Available',
    'The new security dashboard is now available. Monitor system security, view logs, and manage threats from the admin panel.',
    'success',
    'high',
    id,
    'admin',
    NOW()
FROM admin;

-- Create sample achievements
INSERT IGNORE INTO achievements (participant_id, title, description, badge_icon) 
VALUES 
(1, 'Early Bird', 'Registered for the first event', 'fas fa-trophy'),
(1, 'Social Butterfly', 'Registered for multiple events', 'fas fa-users'),
(1, 'Festival Pioneer', 'One of the first participants', 'fas fa-star');

-- Log security enhancement installation
INSERT INTO security_logs (user_id, message, level, ip_address, user_agent, timestamp)
VALUES (NULL, 'Security enhancement system installed successfully', 'info', '127.0.0.1', 'System Installation', UNIX_TIMESTAMP());

-- Clean up old data (optional - uncomment if needed)
-- DELETE FROM rate_limits WHERE timestamp < (UNIX_TIMESTAMP() - 604800); -- 1 week old
-- DELETE FROM login_attempts WHERE timestamp < (UNIX_TIMESTAMP() - 2592000); -- 1 month old
-- DELETE FROM security_logs WHERE timestamp < (UNIX_TIMESTAMP() - 2592000) AND level = 'info'; -- 1 month old info logs

COMMIT;