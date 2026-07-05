@echo off
REM XAMPP Setup Script for SmartWater Frontend
REM This script automates the initial setup for XAMPP deployment

setlocal enabledelayedexpansion
cd /d "%~dp0"

echo.
echo ==========================================
echo  SmartWater Frontend - XAMPP Setup
echo ==========================================
echo.

REM Check if Composer is installed
where composer >nul 2>nul
if errorlevel 1 (
    echo ERROR: Composer not found. Please install Composer first.
    echo Download from: https://getcomposer.org/download/
    pause
    exit /b 1
)

echo [1/4] Installing Composer dependencies...
call composer install
if errorlevel 1 (
    echo ERROR: Composer install failed.
    pause
    exit /b 1
)

echo.
echo [2/4] Checking .env file...
if not exist .env (
    echo Creating .env from .env.example...
    copy .env.example .env
    echo .env created successfully.
) else (
    echo .env already exists.
)

echo.
echo [3/4] Generating APP_KEY...
call php artisan key:generate
if errorlevel 1 (
    echo ERROR: Failed to generate APP_KEY.
    pause
    exit /b 1
)

echo.
echo [4/4] Setting up database...
call php artisan migrate --force 2>nul
if errorlevel 1 (
    echo Note: Database setup skipped (may not be necessary for demo).
)

echo.
echo ==========================================
echo  Setup Complete!
echo ==========================================
echo.
echo Next Steps:
echo.
echo Option 1 - Virtual Host (Recommended):
echo   1. Copy this folder to: C:\xampp\htdocs\smartwater-frontend
echo   2. Update Windows hosts file: C:\Windows\System32\drivers\etc\hosts
echo   3. Add: 127.0.0.1   smartwater.local
echo   4. Configure Apache VirtualHost (see XAMPP_SETUP.md)
echo   5. Access: http://smartwater.local
echo.
echo Option 2 - Direct Access:
echo   1. Copy this folder to: C:\xampp\htdocs\frontend
echo   2. Access: http://localhost/frontend
echo.
echo Option 3 - Artisan Serve:
echo   Run: php artisan serve
echo   Access: http://127.0.0.1:8000
echo.
echo For more details, see XAMPP_SETUP.md
echo.
pause
