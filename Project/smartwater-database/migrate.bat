@echo off
setlocal

cd /d "%~dp0"
php "%~dp0..\smartwater-admin\artisan" migrate --path="%~dp0database\migrations" --realpath --force
exit /b %errorlevel%
