# CORS Error Fix Guide

## Problem
When opening `exam_application.html` directly in a browser (file:// protocol), you may encounter CORS errors when trying to submit the form to `submit_application.php`.

## Solution

### Option 1: Use a Local Web Server (Recommended)

**Using PHP Built-in Server:**
```bash
# Navigate to your project directory
cd D:\Gettia\web

# Start PHP server
php -S localhost:8000

# Then access: http://localhost:8000/exam_application.html
```

**Using XAMPP/WAMP:**
1. Copy your project files to `htdocs` (XAMPP) or `www` (WAMP)
2. Access via: `http://localhost/your-project/exam_application.html`

**Using Python Server:**
```bash
# Python 3
python -m http.server 8000

# Python 2
python -m SimpleHTTPServer 8000

# Then access: http://localhost:8000/exam_application.html
```

### Option 2: CORS Headers Already Added

The `submit_application.php` file now includes CORS headers:
```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
```

However, browsers still block file:// to http:// requests for security reasons.

### Option 3: Browser Settings (Not Recommended)

You can disable CORS in Chrome for testing only:
```bash
# Windows
chrome.exe --user-data-dir="C:/Chrome dev session" --disable-web-security

# Mac
open -na Google\ Chrome --args --user-data-dir=/tmp/chrome_dev --disable-web-security
```

**Warning:** Only use this for development. Never disable security in production.

## Best Practice

Always use a local web server when developing web applications. This ensures:
- No CORS issues
- Proper file handling
- Real-world testing environment
- Better debugging capabilities

## Testing

After starting a local server:
1. Open `http://localhost:8000/exam_application.html`
2. Fill out the form
3. Complete payment
4. Submit the form
5. Check database for saved data

## Troubleshooting

If you still see CORS errors:
1. Check browser console for specific error messages
2. Verify PHP server is running
3. Check that `submit_application.php` is accessible
4. Ensure database connection is configured correctly
5. Check PHP error logs for backend issues

