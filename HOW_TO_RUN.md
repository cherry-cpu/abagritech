# How to Run PHP Files

## Problem
If `dashboard.php` is showing as plain text or not displaying correctly, it means PHP is not being executed. PHP files **must** be run through a web server, not opened directly in a browser.

## Solution: Use a Web Server

### Option 1: PHP Built-in Server (Recommended)

1. Open Command Prompt or PowerShell
2. Navigate to your project directory:
   ```bash
   cd D:\Gettia\web
   ```

3. Start PHP server:
   ```bash
   php -S localhost:8000
   ```

4. Open browser and go to:
   ```
   http://localhost:8000/dashboard.php
   ```

**Note:** Keep the command prompt window open while using the server.

### Option 2: XAMPP/WAMP

1. **XAMPP:**
   - Copy project files to `C:\xampp\htdocs\web\`
   - Start Apache from XAMPP Control Panel
   - Access: `http://localhost/web/dashboard.php`

2. **WAMP:**
   - Copy project files to `C:\wamp64\www\web\`
   - Start WAMP services
   - Access: `http://localhost/web/dashboard.php`

### Option 3: Python Server (for HTML files only)

For HTML files (not PHP), you can use Python:
```bash
cd D:\Gettia\web
python -m http.server 8000
```

Then access: `http://localhost:8000/exam_application.html`

**Note:** This won't work for PHP files - use PHP server instead.

## Quick Test

1. Create a test file `test.php`:
   ```php
   <?php
   echo "PHP is working!";
   phpinfo();
   ?>
   ```

2. Run PHP server:
   ```bash
   php -S localhost:8000
   ```

3. Open: `http://localhost:8000/test.php`

If you see "PHP is working!" and PHP info, your setup is correct.

## Accessing Dashboard

1. Start PHP server:
   ```bash
   cd D:\Gettia\web
   php -S localhost:8000
   ```

2. Login first:
   ```
   http://localhost:8000/login.html
   ```
   - Username: `admin`
   - Password: `admin123`

3. After login, you'll be redirected to:
   ```
   http://localhost:8000/dashboard.php
   ```

## Troubleshooting

### "This site can't be reached"
- Make sure PHP server is running
- Check if port 8000 is available
- Try a different port: `php -S localhost:8080`

### "Page shows PHP code"
- You're opening file:// directly - use http://localhost instead
- Make sure PHP server is running

### "Database connection failed"
- Check `config.php` database credentials
- Make sure MySQL is running
- Verify database exists: `mysql -u root -p -e "SHOW DATABASES;"`

### "Login page not working"
- Make sure you're accessing via `http://localhost:8000/login.html`
- Check browser console for errors
- Verify PHP server is running

## File Types

- **.html files** - Can open directly OR use server
- **.php files** - MUST use PHP server
- **.js, .css** - Can open directly OR use server

## Production Deployment

For production, use:
- Apache with mod_php
- Nginx with PHP-FPM
- Or any PHP-compatible web server

Never use PHP built-in server in production - it's for development only!

