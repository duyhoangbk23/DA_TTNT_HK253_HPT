@echo off
setlocal

cd /d "%~dp0"

set "COMPOSER_CMD="
if exist "%ProgramData%\ComposerSetup\bin\composer.bat" (
    set "COMPOSER_CMD=%ProgramData%\ComposerSetup\bin\composer.bat"
) else (
    for /f "delims=" %%I in ('where composer.bat 2^>nul') do set "COMPOSER_CMD=%%I"
)

echo ================================
echo Starting SmartWater Admin...
echo ================================
echo.

echo Checking PHP and Composer...
where php >nul 2>nul
if errorlevel 1 (
    echo PHP was not found. Please install PHP and add it to PATH.
    pause
    exit /b 1
)

if not defined COMPOSER_CMD (
    echo Composer was not found. Please install Composer and add it to PATH.
    pause
    exit /b 1
)

echo.
echo Installing PHP dependencies...
%COMPOSER_CMD% install
if errorlevel 1 (
    echo Composer install failed.
    pause
    exit /b 1
)

echo.
if not exist .env (
    echo Creating .env file...
    copy .env.example .env >nul
)

echo.
echo Generating application key...
php artisan key:generate --force
if errorlevel 1 (
    echo Failed to generate app key.
    pause
    exit /b 1
)

echo.
echo Starting Laravel development server...
start "SmartWater Admin" cmd /k php artisan serve --host 127.0.0.1 --port 8000

echo.
echo Website is starting at http://127.0.0.1:8000/login

echo Press any key to exit this window...
pause >nul
