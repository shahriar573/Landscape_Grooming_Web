#!/usr/bin/env pwsh
# PowerShell script for Windows users
# Laravel Landscape Grooming - Quick Setup

Write-Host "🌿 Laravel Landscape Grooming - Quick Setup" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Green
Write-Host ""

# Change to project directory
$basePath = Split-Path -Parent $PSScriptRoot
Set-Location $basePath

# Function to run commands with error handling
function Invoke-Step {
    param(
        [string]$Command,
        [string]$Description,
        [switch]$ContinueOnError
    )
    
    Write-Host "📋 $Description" -ForegroundColor Cyan
    Write-Host "   → $Command" -ForegroundColor Gray
    
    try {
        Invoke-Expression $Command
        Write-Host "   ✅ Success" -ForegroundColor Green
        return $true
    }
    catch {
        Write-Host "   ❌ Failed: $($_.Exception.Message)" -ForegroundColor Red
        if (-not $ContinueOnError) {
            throw
        }
        return $false
    }
}

try {
    # Step 1: Check requirements
    Write-Host "🔍 Checking requirements..." -ForegroundColor Yellow
    Invoke-Step "php --version" "Checking PHP"
    Invoke-Step "composer --version" "Checking Composer"

    # Step 2: Install dependencies
    if (-not (Test-Path "vendor\autoload.php")) {
        Invoke-Step "composer install" "Installing PHP dependencies"
    }

    # Step 3: Setup environment
    if (-not (Test-Path ".env")) {
        if (Test-Path ".env.example") {
            Copy-Item ".env.example" ".env"
            Write-Host "✅ Created .env file" -ForegroundColor Green
        }
        Invoke-Step "php artisan key:generate" "Generating application key" -ContinueOnError
    }

    # Step 4: Setup SQLite database
    $dbPath = "database\database.sqlite"
    if (-not (Test-Path $dbPath)) {
        New-Item -ItemType File -Path $dbPath -Force | Out-Null
        Write-Host "✅ Created SQLite database file" -ForegroundColor Green
    }

    # Step 5: Generate Laravel files from CSV
    Write-Host ""
    Write-Host "🏗️ Generating Laravel files from CSV catalog..." -ForegroundColor Yellow
    if (Test-Path "scripts\generate_from_csv.php") {
        Invoke-Step "php scripts\generate_from_csv.php" "Generating files from CSV" -ContinueOnError
    }

    # Step 6: Run migrations
    Write-Host ""
    Write-Host "📊 Setting up database..." -ForegroundColor Yellow
    Invoke-Step "php artisan migrate --force" "Running database migrations" -ContinueOnError

    # Step 7: Clear caches
    Invoke-Step "php artisan config:clear" "Clearing config cache" -ContinueOnError
    Invoke-Step "php artisan route:clear" "Clearing route cache" -ContinueOnError
    Invoke-Step "php artisan view:clear" "Clearing view cache" -ContinueOnError

    # Step 8: Create storage link
    Invoke-Step "php artisan storage:link" "Creating storage symlink" -ContinueOnError

    # Step 9: Seed database
    Invoke-Step "php artisan db:seed" "Creating sample data" -ContinueOnError

    # Success message
    Write-Host ""
    Write-Host "🎉 Setup Complete!" -ForegroundColor Green
    Write-Host "================" -ForegroundColor Green
    Write-Host ""

    Write-Host "🌐 Your application is ready at:" -ForegroundColor Cyan
    Write-Host "   → http://127.0.0.1:8000" -ForegroundColor White
    Write-Host "   → http://landscape_grooming.test:8000 (if hosts file configured)" -ForegroundColor White
    Write-Host ""

    Write-Host "👤 Login credentials:" -ForegroundColor Cyan
    Write-Host "   Admin:    admin@landscape.test    / password" -ForegroundColor White
    Write-Host "   Staff:    staff@landscape.test    / password" -ForegroundColor White
    Write-Host "   Customer: customer@landscape.test / password" -ForegroundColor White
    Write-Host ""

    Write-Host "🚀 To start the development server:" -ForegroundColor Cyan
    Write-Host "   php artisan serve --host=127.0.0.1 --port=8000" -ForegroundColor White
    Write-Host ""

    # Ask if user wants to start the server
    $response = Read-Host "🔥 Start the development server now? [Y/n]"
    if ([string]::IsNullOrEmpty($response) -or $response.ToLower() -eq 'y') {
        Write-Host ""
        Write-Host "🌟 Starting Laravel development server..." -ForegroundColor Green
        Write-Host "   Press Ctrl+C to stop" -ForegroundColor Yellow
        Write-Host ""
        php artisan serve --host=127.0.0.1 --port=8000
    } else {
        Write-Host ""
        Write-Host "✅ Setup complete! Run 'php artisan serve' when ready." -ForegroundColor Green
    }

} catch {
    Write-Host ""
    Write-Host "❌ Setup failed: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Please check the error messages above and try again." -ForegroundColor Red
    exit 1
}