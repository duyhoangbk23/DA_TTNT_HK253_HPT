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

set "DB_PATH=%~dp0..\smartwater-database\database\database.sqlite"
if not exist "%DB_PATH%" (
    echo Error: Shared database not found at "%DB_PATH%".
    pause
    exit /b 1
)

echo [2/3] Using shared database:
echo %DB_PATH%

set "APP_DEBUG=true"
set "DB_PATH=%DB_PATH%"

echo [3/3] Starting Device Monitor on http://127.0.0.1:8001
echo Open /config to set HiveMQ broker and /telemetry to connect live.
echo.
call php -S 127.0.0.1:8001 -t public public/index.php

