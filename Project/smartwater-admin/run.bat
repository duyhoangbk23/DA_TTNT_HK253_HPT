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
if exist .env goto env_exists

echo [1/4] Creating .env file from .env.example...
copy /Y .env.example .env >nul
if errorlevel 1 (
    echo Error: Failed to create .env file.
    pause
    exit /b 1
)
goto env_done

:env_exists
echo [1/4] .env file already exists.

:env_done

:: Install composer dependencies if vendor is missing
if exist vendor goto vendor_exists

echo [2/4] Installing Composer dependencies (this may take a few minutes)...
call composer install
if errorlevel 1 (
    echo Error: Composer install failed.
    pause
    exit /b 1
)
goto vendor_done

:vendor_exists
echo [2/4] Composer dependencies already installed (vendor folder exists).

:vendor_done

:: Generate APP_KEY if empty or not set
echo [3/4] Checking APP_KEY...
findstr /C:"APP_KEY=base64:" .env >nul
if errorlevel 1 (
    echo Generating APP_KEY...
    call php artisan key:generate
)
if not errorlevel 1 echo APP_KEY already exists.

:: Run database migrations
echo [4/4] Running database migrations...
call php artisan migrate --force
if errorlevel 1 goto migrate_failed

:migrate_ok
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

:migrate_failed
echo.
echo [CANH BAO] Chay migration that bai.
echo Vui long kiem tra ket noi MySQL, dam bao MySQL dang hoat dong va database 'smartwater-database' da duoc tao.
echo.
pause
exit /b 1
