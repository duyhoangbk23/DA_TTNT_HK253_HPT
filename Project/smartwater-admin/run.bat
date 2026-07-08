@echo off
setlocal enabledelayedexpansion

cd /d "%~dp0"

echo ======================================
echo SmartWater Admin - Setup and Run
echo ======================================
echo.

:: Check PHP
where php >nul 2>nul
if errorlevel 1 (
    echo Error: PHP was not found. Please install PHP and add it to PATH.
    pause
    exit /b 1
)

:: Check Composer
where composer >nul 2>nul
if errorlevel 1 (
    echo Error: Composer was not found. Please install Composer and add it to PATH.
    pause
    exit /b 1
)

:: Create .env if not exists
if not exist .env (
    echo [1/4] Creating .env file from .env.example...
    copy .env.example .env >nul
) else (
    echo [1/4] .env file already exists.
)

:: Install composer dependencies if vendor is missing
if not exist vendor (
    echo [2/4] Installing Composer dependencies (this may take a few minutes)...
    call composer install
    if errorlevel 1 (
        echo Error: Composer install failed.
        pause
        exit /b 1
    )
) else (
    echo [2/4] Composer dependencies already installed (vendor folder exists).
)

:: Generate APP_KEY if empty or not set
echo [3/4] Checking APP_KEY...
findstr /C:"APP_KEY=base64:" .env >nul
if errorlevel 1 (
    echo Generating APP_KEY...
    call php artisan key:generate
) else (
    echo APP_KEY already exists.
)

:: Run database migrations
echo [4/4] Running database migrations...
call php artisan migrate --force
if errorlevel 1 (
    echo.
    echo [CANH BAO] Chay migration that bai.
    echo Mac dinh ung dung su dung MySQL (xem thiet lap trong file .env).
    echo - Neu ban dung MySQL: Vui long mo MySQL (XAMPP/Docker) va tao DB 'smartwater_admin'.
    echo - Neu ban muon dung SQLite (Tam thoi/Thay the): Hay doi 'DB_CONNECTION=mysql' thanh 'DB_CONNECTION=sqlite' trong file .env.
    echo.
    pause
    exit /b 1
)

echo.
echo ======================================
echo SmartWater Admin is ready!
echo ======================================
echo.
echo Access the website at: http://127.0.0.1:8000
echo Login page: http://127.0.0.1:8000/login
echo Dashboard: http://127.0.0.1:8000/dashboard
echo.
echo Press Ctrl+C in this window to stop the server.
echo ======================================
echo.

call php artisan serve --host=127.0.0.1 --port=8000
