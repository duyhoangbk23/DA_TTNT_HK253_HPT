@echo off
REM SmartWater Admin - Setup and Run Script
REM This script will install dependencies, migrate database, and start the development server

echo ======================================
echo SmartWater Admin - Setup and Run
echo ======================================
echo.

REM Check if composer.json exists
if not exist "composer.json" (
    echo Error: composer.json not found. Please run this script from smartwater-admin directory.
    pause
    exit /b 1
)

REM Step 1: Install dependencies
echo [1/4] Installing Composer dependencies...
echo.
call composer install
if %errorlevel% neq 0 (
    echo Error: Composer install failed
    pause
    exit /b 1
)
echo.

REM Step 2: Generate APP_KEY if not exists
echo [2/4] Checking APP_KEY...
for /f "tokens=*" %%i in ('findstr /c:"APP_KEY=" .env') do set APP_KEY=%%i
if "%APP_KEY%"=="" (
    echo Generating APP_KEY...
    call php artisan key:generate
) else (
    echo APP_KEY already exists
)
echo.

REM Step 3: Run migrations
echo [3/4] Running database migrations...
echo.
call php artisan migrate --force
if %errorlevel% neq 0 (
    echo Error: Migration failed
    pause
    exit /b 1
)
echo.

REM Step 4: Start development server
echo [4/4] Starting development server...
echo.
echo ======================================
echo SmartWater Admin is running!
echo ======================================
echo.
echo Access the website at: http://127.0.0.1:8000
echo Login page: http://127.0.0.1:8000/login
echo Dashboard: http://127.0.0.1:8000/dashboard
echo.
echo Press Ctrl+C to stop the server
echo ======================================
echo.

call php artisan serve

pause
