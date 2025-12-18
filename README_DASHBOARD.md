# Dashboard Access Guide

## Why Dashboard is PHP, not HTML

The dashboard (`dashboard.php`) is a **PHP file** because it needs to:
- Connect to MySQL database
- Fetch real-time application data
- Display dynamic statistics
- Show charts with live data
- Require user authentication

**HTML files are static** - they can't connect to databases or show dynamic content.

## How to Access Dashboard

### Step 1: Start PHP Server

**Option A: Use Batch File (Easiest)**
- Double-click `START_SERVER.bat`
- Keep the window open

**Option B: Manual Command**
```cmd
cd D:\Gettia\web
php -S localhost:8000
```

### Step 2: Login First

1. Open browser: `http://localhost:8000/login.html`
2. Login with:
   - Username: `admin`
   - Password: `admin123`

### Step 3: Access Dashboard

After login, you'll be automatically redirected to:
- `http://localhost:8000/dashboard.php`

Or access directly (if logged in):
- `http://localhost:8000/dashboard.php`

## Files Available

- ✅ `dashboard.php` - Main dashboard (PHP, requires server)
- ✅ `dashboard.html` - Redirect page (HTML, redirects to PHP version)
- ✅ `login.html` - Login page (HTML)

## Quick Access URLs

When server is running on `localhost:8000`:

| Page | URL | Type |
|------|-----|------|
| Login | `http://localhost:8000/login.html` | HTML |
| Dashboard | `http://localhost:8000/dashboard.php` | PHP |
| Applications | `http://localhost:8000/view_applications.php` | PHP |
| Exam Form | `http://localhost:8000/exam_application.html` | HTML |

## Troubleshooting

### "dashboard.php shows as code"
- You're opening it directly (file://)
- **Solution:** Use `http://localhost:8000/dashboard.php`

### "Page not found"
- PHP server is not running
- **Solution:** Start server with `START_SERVER.bat` or `php -S localhost:8000`

### "Please login"
- You need to login first
- **Solution:** Go to `http://localhost:8000/login.html`

### "Database connection failed"
- Database not configured
- **Solution:** Update `config.php` with correct database credentials

## Dashboard Features

The dashboard shows:
- 📊 Total applications count
- ⏳ Pending applications
- ✅ Approved applications
- ❌ Rejected applications
- 📈 Charts (by position, by status)
- 📋 Recent applications table
- 🏢 Top exam centers

All data is fetched live from the MySQL database!

