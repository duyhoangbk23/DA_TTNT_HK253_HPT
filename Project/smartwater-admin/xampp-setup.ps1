# XAMPP Setup Script for SmartWater Frontend (PowerShell)
# Run as Administrator for best results

$ScriptPath = Split-Path -Parent -Path $MyInvocation.MyCommand.Definition
Set-Location $ScriptPath

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  SmartWater Frontend - XAMPP Setup" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Check if Composer is installed
$ComposerPath = Get-Command composer -ErrorAction SilentlyContinue
if (-not $ComposerPath) {
    Write-Host "ERROR: Composer not found. Please install Composer first." -ForegroundColor Red
    Write-Host "Download from: https://getcomposer.org/download/" -ForegroundColor Yellow
    Read-Host "Press Enter to exit"
    exit 1
}

# Step 1: Install Composer dependencies
Write-Host "[1/4] Installing Composer dependencies..." -ForegroundColor Yellow
composer install
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Composer install failed." -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}

# Step 2: Check/Create .env
Write-Host ""
Write-Host "[2/4] Checking .env file..." -ForegroundColor Yellow
if (-not (Test-Path .env)) {
    Write-Host "Creating .env from .env.example..." -ForegroundColor Cyan
    Copy-Item .env.example .env
    Write-Host ".env created successfully." -ForegroundColor Green
} else {
    Write-Host ".env already exists." -ForegroundColor Green
}

# Step 3: Generate APP_KEY
Write-Host ""
Write-Host "[3/4] Generating APP_KEY..." -ForegroundColor Yellow
php artisan key:generate
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Failed to generate APP_KEY." -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}

# Step 4: Setup Database
Write-Host ""
Write-Host "[4/4] Setting up database..." -ForegroundColor Yellow
php artisan migrate --force 2>$null
if ($LASTEXITCODE -eq 0) {
    Write-Host "Database setup complete." -ForegroundColor Green
} else {
    Write-Host "Note: Database setup skipped (may not be necessary for demo)." -ForegroundColor Cyan
}

# Summary
Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Setup Complete!" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Cyan
Write-Host ""
Write-Host "Option 1 - Virtual Host (Recommended):" -ForegroundColor Yellow
Write-Host "  1. Copy this folder to: C:\xampp\htdocs\smartwater-frontend"
Write-Host "  2. Update Windows hosts file: C:\Windows\System32\drivers\etc\hosts"
Write-Host "  3. Add: 127.0.0.1   smartwater.local"
Write-Host "  4. Configure Apache VirtualHost (see XAMPP_SETUP.md)"
Write-Host "  5. Access: http://smartwater.local"
Write-Host ""
Write-Host "Option 2 - Direct Access:" -ForegroundColor Yellow
Write-Host "  1. Copy this folder to: C:\xampp\htdocs\frontend"
Write-Host "  2. Access: http://localhost/frontend"
Write-Host ""
Write-Host "Option 3 - Artisan Serve:" -ForegroundColor Yellow
Write-Host "  Run: php artisan serve"
Write-Host "  Access: http://127.0.0.1:8000"
Write-Host ""
Write-Host "For more details, see XAMPP_SETUP.md" -ForegroundColor Cyan
Write-Host ""
Read-Host "Press Enter to exit"
