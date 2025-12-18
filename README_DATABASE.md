# Database Setup Guide

## Overview
This guide will help you set up the MySQL database for storing exam application data.

## Prerequisites
- MySQL Server installed (MySQL 5.7+ or MariaDB 10.2+)
- PHP 7.4+ with PDO MySQL extension
- Web server (Apache/Nginx)

## Setup Steps

### 1. Create Database
Run the SQL script to create the database and tables:

```bash
mysql -u root -p < database_setup.sql
```

Or manually:
```sql
mysql -u root -p
CREATE DATABASE abagritech_db;
USE abagritech_db;
SOURCE database_setup.sql;
```

### 2. Configure Database Connection
Edit `config.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'abagritech_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 3. Set Upload Directory Permissions
Create and set permissions for the uploads directory:

```bash
mkdir uploads
chmod 755 uploads
```

Or on Windows, ensure the directory has write permissions.

### 4. Test Connection
Create a test file `test_db.php`:

```php
<?php
require_once 'config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    echo "Database connection successful!";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
```

## Database Structure

### exam_applications Table
Stores all application data including:
- Personal information
- Educational qualifications
- Application details
- File paths for photos and signatures
- Payment transaction IDs

### payments Table (Optional)
Tracks payment transactions separately for better record keeping.

### admin_users Table (Optional)
For future admin panel implementation.

## Security Notes

1. **Database Credentials**: Never commit `config.php` with real credentials to version control
2. **File Uploads**: The uploads directory should be outside the web root or protected
3. **SQL Injection**: All queries use prepared statements (PDO) to prevent SQL injection
4. **File Validation**: File uploads are validated for type and size
5. **HTTPS**: Use HTTPS in production to protect data transmission

## Backup Recommendations

Regularly backup your database:

```bash
mysqldump -u root -p abagritech_db > backup_$(date +%Y%m%d).sql
```

## Troubleshooting

### Connection Errors
- Check MySQL service is running
- Verify credentials in config.php
- Ensure database exists

### Permission Errors
- Check uploads directory permissions
- Ensure PHP can write to uploads directory

### File Upload Issues
- Check PHP upload_max_filesize in php.ini
- Verify uploads directory exists and is writable

## Support
For database issues, check:
- MySQL error logs
- PHP error logs
- Browser console for JavaScript errors

