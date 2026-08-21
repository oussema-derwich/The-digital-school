#!/bin/bash

echo "=========================================="
echo "Email Auth System - Setup Script"
echo "=========================================="
echo ""

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if we're in the backend directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}Error: Please run this script from the backend directory${NC}"
    exit 1
fi

echo -e "${YELLOW}Starting setup...${NC}\n"

# Step 1: Check PHP
echo "1. Checking PHP..."
if ! command -v php &> /dev/null; then
    echo -e "${RED}   ✗ PHP not found${NC}"
    exit 1
fi
echo -e "${GREEN}   ✓ PHP found${NC}"

# Step 2: Check Composer
echo "2. Checking Composer..."
if ! command -v composer &> /dev/null; then
    echo -e "${RED}   ✗ Composer not found${NC}"
    exit 1
fi
echo -e "${GREEN}   ✓ Composer found${NC}"

# Step 3: Install dependencies
echo "3. Installing dependencies..."
if composer install --no-progress --no-suggest > /dev/null 2>&1; then
    echo -e "${GREEN}   ✓ Dependencies installed${NC}"
else
    echo -e "${RED}   ✗ Failed to install dependencies${NC}"
    exit 1
fi

# Step 4: Generate APP_KEY if not exists
echo "4. Setting up .env..."
if [ ! -f ".env" ]; then
    echo "   Creating .env from .env.example..."
    cp .env.example .env
fi

if ! grep -q "APP_KEY=" .env || grep -q "^APP_KEY=$" .env; then
    echo "   Generating APP_KEY..."
    php artisan key:generate --force > /dev/null 2>&1
    echo -e "${GREEN}   ✓ APP_KEY generated${NC}"
else
    echo -e "${GREEN}   ✓ APP_KEY already set${NC}"
fi

# Step 5: Ensure FRONTEND_URL is set
if ! grep -q "FRONTEND_URL=" .env; then
    echo "   Adding FRONTEND_URL to .env..."
    echo "FRONTEND_URL=http://localhost:5173" >> .env
    echo -e "${GREEN}   ✓ FRONTEND_URL added${NC}"
else
    echo -e "${GREEN}   ✓ FRONTEND_URL already set${NC}"
fi

# Step 6: Run migrations
echo "5. Running migrations..."
if php artisan migrate --force > /dev/null 2>&1; then
    echo -e "${GREEN}   ✓ Migrations completed${NC}"
else
    echo -e "${YELLOW}   ⚠ Migration warning (check logs)${NC}"
fi

# Step 7: Clear cache
echo "6. Clearing cache..."
php artisan cache:clear > /dev/null 2>&1
php artisan config:clear > /dev/null 2>&1
echo -e "${GREEN}   ✓ Cache cleared${NC}"

# Step 8: Test email auth
echo "7. Testing email auth system..."
if php test_email_auth.php > /dev/null 2>&1; then
    echo -e "${GREEN}   ✓ Email auth system ready${NC}"
else
    echo -e "${YELLOW}   ⚠ Email auth test warning (check logs)${NC}"
fi

echo ""
echo "=========================================="
echo -e "${GREEN}Setup completed!${NC}"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Start Laravel: php artisan serve"
echo "2. In another terminal, start frontend: cd ../frontend && npm run dev"
echo "3. Test registration: http://localhost:5173/register"
echo ""
echo "Configuration:"
echo "- Backend: http://localhost:8000"
echo "- Frontend: http://localhost:5173"
echo ""
echo "Email configuration (.env):"
echo "- For development: MAIL_MAILER=log"
echo "- For production: Configure SMTP"
echo ""
echo "Documentation:"
echo "- See EMAIL_AUTH_SYSTEM.md"
echo "- See IMPLEMENTATION_GUIDE.md"
echo ""
