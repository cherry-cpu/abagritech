# Login System Setup Guide

## Overview
A complete login system with database authentication has been added to protect admin pages.

## Files Created

1. **`login.html`** - Login page with modern UI
2. **`auth_login.php`** - Login authentication handler
3. **`auth_logout.php`** - Logout handler
4. **`check_auth.php`** - Authentication helper functions
5. **`create_admin.php`** - Script to create admin users

## Default Login Credentials

After running `database_setup.sql`, you can login with:

**Super Admin:**
- Username: `admin`
- Password: `admin123`
- Email: `admin@abagritech.com`

**Test User:**
- Username: `test`
- Password: `test123`
- Email: `test@abagritech.com`

**⚠️ IMPORTANT:** Change these default passwords immediately after setup!

## Setup Steps

### 1. Run Database Setup
```bash
mysql -u root -p < database_setup.sql
```

This creates the `admin_users` table and inserts default users.

### 2. Create Additional Admin Users

**Option A: Using create_admin.php (CLI)**
```bash
php create_admin.php
```

**Option B: Using SQL**
```sql
INSERT INTO admin_users (username, email, password_hash, full_name, role) 
VALUES ('newuser', 'newuser@example.com', '$2y$10$...', 'New User', 'admin');
```

To generate password hash:
```php
<?php
echo password_hash('your_password', PASSWORD_DEFAULT);
?>
```

### 3. Access Login Page
Navigate to: `http://localhost:8000/login.html`

### 4. Protected Pages
The following pages now require login:
- `view_applications.php` - View all exam applications

## Features

### Security Features
- ✅ Password hashing using PHP `password_hash()`
- ✅ Session-based authentication
- ✅ SQL injection protection (prepared statements)
- ✅ CSRF protection ready (can be added)
- ✅ Remember me functionality
- ✅ Last login tracking

### User Roles
- **admin** - Standard admin access
- **super_admin** - Full access (can access everything)

### Session Management
- Sessions expire after browser close (or 30 days with "Remember Me")
- Automatic logout on session expiry
- Secure session handling

## Usage

### Protecting a Page
Add at the top of any PHP file:
```php
<?php
require_once 'check_auth.php';
requireLogin(); // Require user to be logged in
?>
```

### Check User Role
```php
<?php
require_once 'check_auth.php';

if (hasRole('super_admin')) {
    // Super admin only code
}
?>
```

### Get Current User
```php
<?php
require_once 'check_auth.php';
$user = getCurrentUser();

echo $user['username'];
echo $user['full_name'];
echo $user['role'];
?>
```

### Logout
Simply link to: `auth_logout.php`
```html
<a href="auth_logout.php">Logout</a>
```

## Customization

### Change Session Timeout
Edit `check_auth.php` or add to `config.php`:
```php
ini_set('session.gc_maxlifetime', 3600); // 1 hour
```

### Change Remember Me Duration
Edit `auth_login.php`:
```php
setcookie('remember_token', $cookie_value, time() + (30 * 24 * 60 * 60), '/'); // 30 days
```

## Security Best Practices

1. **Change Default Passwords** - Immediately after setup
2. **Use Strong Passwords** - Minimum 8 characters, mixed case, numbers, symbols
3. **HTTPS in Production** - Always use HTTPS for login pages
4. **Regular Updates** - Keep PHP and MySQL updated
5. **Delete create_admin.php** - After creating users, delete this file
6. **Limit Login Attempts** - Consider adding rate limiting
7. **Two-Factor Authentication** - Consider adding 2FA for production

## Troubleshooting

### Login Not Working
1. Check database connection in `config.php`
2. Verify `admin_users` table exists
3. Check PHP error logs
4. Verify session is starting correctly

### Session Expires Too Quickly
- Check PHP `session.gc_maxlifetime` setting
- Verify server time is correct
- Check browser cookie settings

### Password Not Working
- Verify password hash in database matches
- Check if password was hashed correctly
- Try resetting password using `create_admin.php`

## Support

For login issues:
1. Check browser console for JavaScript errors
2. Check PHP error logs
3. Verify database connection
4. Test with default credentials first

