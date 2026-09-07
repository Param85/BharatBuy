-- BharatBuy Admin Panel Migration
-- Run this file in phpMyAdmin or via CLI to enable the admin panel.

USE smart_shopping_assistant;

-- 1. Add admin role column to users table
ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0 AFTER alerts_enabled;

-- 2. Add profile_image column if not exists (referenced in header.php)
-- ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL AFTER alerts_enabled;

-- 3. Create admin activity log table for audit trail
CREATE TABLE IF NOT EXISTS admin_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    target_type VARCHAR(50) NULL,
    target_id INT NULL,
    details TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 4. Promote your first admin account (update the email to match yours)
-- UPDATE users SET is_admin = 1 WHERE email = 'your_email@example.com';
