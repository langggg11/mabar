-- Add admin column to users table
ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0;

-- Make the first user an admin (change email as needed)
UPDATE users SET is_admin = 1 WHERE email = 'admin@fishinggear.id' LIMIT 1;

-- Or create a default admin user
INSERT INTO users (name, email, password, is_admin) VALUES 
('Admin', 'admin@fishinggear.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1)
ON DUPLICATE KEY UPDATE is_admin = 1;
