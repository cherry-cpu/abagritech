@echo off
echo ========================================
echo Starting PHP Development Server
echo ========================================
echo.
echo Server will start at: http://localhost:8000
echo.
echo Press Ctrl+C to stop the server
echo.
cd /d "%~dp0"
php -S localhost:8000
pause

