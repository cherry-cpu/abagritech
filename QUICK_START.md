# Quick Start Guide - Running Dashboard

## ⚠️ Important: PHP Files Need a Web Server

**You CANNOT open `dashboard.php` directly in a browser!** PHP files must be executed by a web server.

## 🚀 Quick Start (Choose One Method)

### Method 1: Use START_SERVER.bat (Easiest)

1. **Double-click** `START_SERVER.bat` in your project folder
2. A command window will open showing "Server started"
3. Open your browser and go to: `http://localhost:8000/login.html`
4. Login with:
   - Username: `admin`
   - Password: `admin123`
5. You'll be redirected to the dashboard!

**Keep the command window open** while using the site.

### Method 2: Manual PHP Server

1. Open **Command Prompt** (not PowerShell)
2. Type:
   ```cmd
   cd D:\Gettia\web
   php -S localhost:8000
   ```
3. Open browser: `http://localhost:8000/login.html`

### Method 3: XAMPP

1. Install XAMPP from https://www.apachefriends.org/
2. Copy your project to `C:\xampp\htdocs\web\`
3. Start Apache from XAMPP Control Panel
4. Open: `http://localhost/web/login.html`

## 🔍 Troubleshooting

### "php is not recognized"
- PHP is not installed or not in PATH
- Install PHP from https://www.php.net/downloads.php
- Or use XAMPP (includes PHP)

### "This site can't be reached"
- Make sure the server is running
- Check if port 8000 is available
- Try: `php -S localhost:8080` (different port)

### "Dashboard shows PHP code"
- You opened `file:///D:/Gettia/web/dashboard.php` directly
- **MUST use** `http://localhost:8000/dashboard.php` instead

### "Database connection failed"
- Check `config.php` - update database credentials
- Make sure MySQL is running
- Run: `mysql -u root -p < database_setup.sql`

## ✅ Testing

1. Test PHP is working:
   - Start server
   - Open: `http://localhost:8000/test_php.php`
   - Should show "PHP is working!"

2. Test Dashboard:
   - Login: `http://localhost:8000/login.html`
   - After login, dashboard should load automatically

## 📝 File Access URLs

When server is running on `localhost:8000`:

- Login: `http://localhost:8000/login.html`
- Dashboard: `http://localhost:8000/dashboard.php` (requires login)
- Applications: `http://localhost:8000/view_applications.php` (requires login)
- Exam Form: `http://localhost:8000/exam_application.html`
- Home: `http://localhost:8000/index.html`

## 🎯 Remember

- ✅ **DO:** Use `http://localhost:8000/...`
- ❌ **DON'T:** Open files directly with `file:///...`

