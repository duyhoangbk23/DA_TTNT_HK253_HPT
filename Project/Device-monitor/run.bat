@echo off
setlocal enabledelayedexpansion

cd /d "%~dp0"

echo ======================================
echo Device Monitor - Setup and Run
echo ======================================
echo.

where php >nul 2>nul
if errorlevel 1 (
    echo Error: PHP was not found. Please install PHP and add it to PATH.
    pause
    exit /b 1
)

where composer >nul 2>nul
if errorlevel 1 (
    echo Error: Composer was not found. Please install Composer and add it to PATH.
    pause
    exit /b 1
)

if exist vendor goto vendor_exists

echo [1/3] Installing Composer dependencies...
call composer install
if errorlevel 1 (
    echo Error: Composer install failed.
    pause
    exit /b 1
)
goto vendor_done

:vendor_exists
echo [1/3] Composer dependencies already installed.

:vendor_done

set "DB_CONNECTION=mysql"
set "DB_HOST=127.0.0.1"
set "DB_PORT=3306"
set "DB_DATABASE=smartwater_database"
set "DB_USERNAME=root"
set "DB_PASSWORD="

echo [2/3] Using MySQL database:
echo Host: %DB_HOST%
echo Port: %DB_PORT%
echo Database: %DB_DATABASE%

set "APP_DEBUG=true"

echo [3/3] Starting Device Monitor on http://127.0.0.1:8001
echo Open /config to set HiveMQ broker and /telemetry to connect live.
echo.
call php -S 127.0.0.1:8001 -t public public/index.php

