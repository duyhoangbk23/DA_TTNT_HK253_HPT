# SmartWater Admin - Setup and Run Script (PowerShell)
# This script will install dependencies, migrate database, and start the development server

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "SmartWater Admin - Setup and Run" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Check if composer.json exists
if (-not (Test-Path "composer.json")) {
    Write-Host "Error: composer.json not found. Please run this script from smartwater-admin directory." -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}

# Step 1: Install dependencies
Write-Host "[1/4] Installing Composer dependencies..." -ForegroundColor Yellow
Write-Host ""
& composer install
if ($LASTEXITCODE -ne 0) {
    Write-Host "Error: Composer install failed" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}
Write-Host ""

# Step 2: Generate APP_KEY if not exists
Write-Host "[2/4] Checking APP_KEY..." -ForegroundColor Yellow
$envContent = Get-Content ".env" -Raw
if ($envContent -match "APP_KEY=base64:") {
    Write-Host "APP_KEY already exists" -ForegroundColor Green
} else {
    Write-Host "Generating APP_KEY..." -ForegroundColor Yellow
    & php artisan key:generate
}
Write-Host ""

# Step 3: Run migrations
Write-Host "[3/4] Running database migrations..." -ForegroundColor Yellow
Write-Host ""
& php artisan migrate --force
if ($LASTEXITCODE -ne 0) {
    Write-Host "Error: Migration failed" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}
Write-Host ""

# Step 4: Start development server
Write-Host "[4/4] Starting development server..." -ForegroundColor Yellow
Write-Host ""
Write-Host "======================================" -ForegroundColor Green
Write-Host "SmartWater Admin is running!" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Green
Write-Host ""
Write-Host "Access the website at: http://127.0.0.1:8000" -ForegroundColor Cyan
Write-Host "Login page: http://127.0.0.1:8000/login" -ForegroundColor Cyan
Write-Host "Dashboard: http://127.0.0.1:8000/dashboard" -ForegroundColor Cyan
Write-Host ""
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow
Write-Host "======================================" -ForegroundColor Green
Write-Host ""

& php artisan serve

Read-Host "Press Enter to exit"
