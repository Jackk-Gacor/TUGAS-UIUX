#!/bin/bash
# Checkout System Verification Script
# This script verifies the checkout system is properly configured

echo "===== Checkout System Verification ====="
echo ""

# Check 1: APP_URL Configuration
echo "[1] Checking APP_URL configuration..."
APP_URL=$(grep "APP_URL=" .env | cut -d'=' -f2)
if [[ "$APP_URL" == "http://localhost/TUGAS-UIUX/public" ]]; then
    echo "    ✅ APP_URL is correctly set to: $APP_URL"
else
    echo "    ❌ APP_URL is incorrect: $APP_URL"
    echo "       Expected: http://localhost/TUGAS-UIUX/public"
fi
echo ""

# Check 2: Routes Configuration
echo "[2] Checking checkout routes..."
php artisan route:list | grep -i checkout > /dev/null
if [ $? -eq 0 ]; then
    echo "    ✅ Checkout routes are registered"
    php artisan route:list | grep -i checkout
else
    echo "    ❌ Checkout routes not found"
fi
echo ""

# Check 3: Database Connection
echo "[3] Checking database connection..."
php artisan tinker --execute="dd('Database connected');" 2>/dev/null | grep -q "Database connected"
if [ $? -eq 0 ]; then
    echo "    ✅ Database connection verified"
else
    echo "    ❌ Database connection failed"
fi
echo ""

# Check 4: Models exist
echo "[4] Checking models..."
if [ -f "app/Models/Order.php" ] && [ -f "app/Models/Payment.php" ] && [ -f "app/Models/OrderItem.php" ]; then
    echo "    ✅ All required models exist"
else
    echo "    ❌ Some models are missing"
fi
echo ""

# Check 5: Controller exists
echo "[5] Checking controller..."
if [ -f "app/Http/Controllers/OrderController.php" ]; then
    echo "    ✅ OrderController exists"
else
    echo "    ❌ OrderController not found"
fi
echo ""

# Check 6: Views exist
echo "[6] Checking views..."
if [ -f "resources/views/checkout.blade.php" ] && [ -f "resources/views/success.blade.php" ]; then
    echo "    ✅ Checkout views exist"
else
    echo "    ❌ Some checkout views are missing"
fi
echo ""

echo "===== Verification Complete ====="
echo ""
echo "To test the checkout system:"
echo "1. Open http://localhost/TUGAS-UIUX/public/ in your browser"
echo "2. Add items to cart"
echo "3. Click 'Lanjutkan Pesanan' (Continue Order)"
echo "4. Fill in customer details"
echo "5. Select payment method"
echo "6. Click 'Buat Pesanan' (Create Order)"
echo ""
