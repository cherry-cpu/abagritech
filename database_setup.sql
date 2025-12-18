-- Database setup for Aakasha Bindu Agritech Exam Applications

-- Create database
CREATE DATABASE IF NOT EXISTS dbmaster CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE dbmaster;

-- Create exam_applications table
CREATE TABLE IF NOT EXISTS exam_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id VARCHAR(50) UNIQUE NOT NULL,
    
    -- Personal Information
    full_name VARCHAR(255) NOT NULL,
    date_of_birth DATE NOT NULL,
    age INT NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    father_name VARCHAR(255),
    aadhar VARCHAR(20),
    caste VARCHAR(100),
    address TEXT,
    
    -- Educational Qualifications
    ssc_year INT,
    ssc_percentage VARCHAR(10),
    inter_year INT,
    inter_percentage VARCHAR(10),
    degree_year INT,
    degree_percentage VARCHAR(10),
    
    -- Application Details
    position ENUM('MAFO', 'DAFO', 'RAFO', 'ZAFO') NOT NULL,
    exam_center VARCHAR(255) NOT NULL,
    transaction_id VARCHAR(255) NOT NULL,
    
    -- File Uploads
    photo_path VARCHAR(500),
    signature_path VARCHAR(500),
    
    -- Status and Timestamps
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_application_id (application_id),
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create payments table (optional - for tracking payments)
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(255) UNIQUE NOT NULL,
    razorpay_payment_id VARCHAR(255),
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'INR',
    payment_status ENUM('pending', 'success', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (application_id) REFERENCES exam_applications(application_id) ON DELETE CASCADE,
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_payment_status (payment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create admin_users table (optional - for admin panel)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    role ENUM('admin', 'super_admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123 - change this!)
-- Password hash for 'admin123' generated using password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO admin_users (username, email, password_hash, full_name, role) 
VALUES ('admin', 'admin@abagritech.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'super_admin')
ON DUPLICATE KEY UPDATE username=username;

-- Insert test user (password: test123)
-- Password hash for 'test123'
INSERT INTO admin_users (username, email, password_hash, full_name, role) 
VALUES ('test', 'test@abagritech.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'Test User', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- Create contact_messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_subject (subject)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

