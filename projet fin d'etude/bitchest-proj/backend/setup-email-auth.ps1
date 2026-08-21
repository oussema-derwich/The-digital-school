# Email Auth System - Setup Script for Windows

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "Email Auth System - Setup Script" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Check if we're in the backend directory
if (-not (Test-Path "artisan")) {
    Write-Host "Error: Please run this script from the backend directory" -ForegroundColor Red
    exit 1
}

Write-Host "Starting setup..." -ForegroundColor Yellow
Write-Host ""

# Step 1: Check PHP
Write-Host "1. Checking PHP..."
$phpCheck = php -v 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "   ✗ PHP not found" -ForegroundColor Red
    exit 1
}
Write-Host "   ✓ PHP found" -ForegroundColor Green

# Step 2: Check Composer
Write-Host "2. Checking Composer..."
$composerCheck = composer --version 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "   ✗ Composer not found" -ForegroundColor Red
    Write-Host "   Please install Composer from https://getcomposer.org" -ForegroundColor Yellow
    exit 1
}
Write-Host "   ✓ Composer found" -ForegroundColor Green

# Step 3: Install dependencies
Write-Host "3. Installing dependencies..."
composer install --no-progress --no-suggest > $null 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "   ✓ Dependencies installed" -ForegroundColor Green
} else {
    Write-Host "   ✗ Failed to install dependencies" -ForegroundColor Red
    exit 1
}

# Step 4: Setup .env
Write-Host "4. Setting up .env..."
if (-not (Test-Path ".env")) {
    Write-Host "   Creating .env from .env.example..."
    Copy-Item ".env.example" ".env"
}

$envContent = Get-Content ".env" -Raw
if (-not ($envContent -match "^APP_KEY=")) {
    Write-Host "   Generating APP_KEY..."
    php artisan key:generate --force > $null 2>&1
    Write-Host "   ✓ APP_KEY generated" -ForegroundColor Green
} else {
    Write-Host "   ✓ APP_KEY already set" -ForegroundColor Green
}

# Step 5: Ensure FRONTEND_URL is set
if (-not ($envContent -match "FRONTEND_URL=")) {
    Write-Host "   Adding FRONTEND_URL to .env..."
    Add-Content ".env" "FRONTEND_URL=http://localhost:5173"
    Write-Host "   ✓ FRONTEND_URL added" -ForegroundColor Green
} else {
    Write-Host "   ✓ FRONTEND_URL already set" -ForegroundColor Green
}

# Step 6: Run migrations
Write-Host "5. Running migrations..."
php artisan migrate --force > $null 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "   ✓ Migrations completed" -ForegroundColor Green
} else {
    Write-Host "   ⚠ Migration completed (check logs if errors)" -ForegroundColor Yellow
}

# Step 7: Clear cache
Write-Host "6. Clearing cache..."
php artisan cache:clear > $null 2>&1
php artisan config:clear > $null 2>&1
Write-Host "   ✓ Cache cleared" -ForegroundColor Green

# Step 8: Test email auth
Write-Host "7. Testing email auth system..."
php test_email_auth.php > $null 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "   ✓ Email auth system ready" -ForegroundColor Green
} else {
    Write-Host "   ⚠ Email auth test (check logs)" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Green
Write-Host "Setup completed!" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Green
Write-Host ""

Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Start Laravel: php artisan serve" -ForegroundColor White
Write-Host "2. In another terminal, start frontend: cd ..\frontend && npm run dev" -ForegroundColor White
Write-Host "3. Test registration: http://localhost:5173/register" -ForegroundColor White
Write-Host ""

Write-Host "Configuration:" -ForegroundColor Cyan
Write-Host "- Backend: http://localhost:8000" -ForegroundColor White
Write-Host "- Frontend: http://localhost:5173" -ForegroundColor White
Write-Host ""

Write-Host "Email configuration (.env):" -ForegroundColor Cyan
Write-Host "- For development: MAIL_MAILER=log" -ForegroundColor White
Write-Host "- For production: Configure SMTP" -ForegroundColor White
Write-Host ""

Write-Host "Documentation:" -ForegroundColor Cyan
Write-Host "- See EMAIL_AUTH_SYSTEM.md" -ForegroundColor White
Write-Host "- See IMPLEMENTATION_GUIDE.md" -ForegroundColor White
Write-Host ""
