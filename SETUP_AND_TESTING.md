# Quick Setup & Testing Guide

## Prerequisites
- PHP 8.0+ with Laravel 10
- MySQL database
- XAMPP or similar PHP environment

## Setup Steps

### 1. Run Migrations
```bash
cd c:\xampp\htdocs\TUGAS-UIUX
php artisan migrate --force
```

The new migration adds `qris_proof_path` column to payments table.

### 2. Create Storage Link (if not exists)
```bash
php artisan storage:link
```

This allows served files from `/storage/` URL path.

### 3. Enable Debug Mode (Optional - for Development)
Edit `.env`:
```
APP_DEBUG=true
```

This shows full error details in JSON responses and logs.

### 4. Start Application
```bash
php artisan serve
```

Application runs on: `http://127.0.0.1:8000`

## Testing Checkout Flow

### Test 1: Valid Checkout (Should Succeed - 200)
```bash
curl -X POST http://127.0.0.1:8000/checkout \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: $(your_csrf_token)" \
  -d '{
    "customer_name": "John Doe",
    "customer_phone": "081234567890",
    "payment_method": "cash",
    "items": [
      {"name": "Nasi Goreng", "priceNumber": 12000, "qty": 2}
    ]
  }'
```

Expected Response:
```json
{
  "status": "success",
  "ok": true,
  "order_id": 1,
  "payment_method": "cod",
  "redirect": "/checkout/success?order=1"
}
```

### Test 2: Invalid Phone (Should Fail - 422)
```bash
curl -X POST http://127.0.0.1:8000/checkout \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "John",
    "customer_phone": "abc",
    "payment_method": "cash",
    "items": [{"name": "Nasi", "priceNumber": 10000, "qty": 1}]
  }'
```

Expected Response (422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "customer_phone": ["Nomor telepon tidak valid (format: 08xx xxx xxxx)"]
  }
}
```

### Test 3: Missing Items (Should Fail - 422)
```bash
curl -X POST http://127.0.0.1:8000/checkout \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "John",
    "customer_phone": "081234567890",
    "payment_method": "cash",
    "items": []
  }'
```

Expected Response (422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "items": ["Minimal ada 1 item pesanan"]
  }
}
```

## Testing Payment Confirmation

### Test Payment Confirmation (Should Succeed - 200)
After creating an order (assume order_id=1):

```bash
curl -X POST http://127.0.0.1:8000/checkout/1/pay \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: $(your_csrf_token)"
```

Expected Response:
```json
{
  "status": "success",
  "message": "Pembayaran berhasil dikonfirmasi",
  "order_id": 1
}
```

## Testing QRIS Upload

### Prepare a Test Image
Create a test image or use an existing one (must be JPG/JPEG/PNG, max 5MB)

### Test Valid Upload (Should Succeed - 200)
```bash
curl -X POST http://127.0.0.1:8000/checkout/1/upload-proof \
  -H "X-CSRF-TOKEN: $(your_csrf_token)" \
  -F "payment_proof=@/path/to/test-image.jpg"
```

Expected Response:
```json
{
  "status": "success",
  "message": "Bukti pembayaran berhasil diunggah",
  "file_url": "/storage/qris_proofs/1_1701686400.jpg"
}
```

### Test File Too Large (Should Fail - 422)
Upload a file > 5MB:

Expected Response:
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "payment_proof": ["Ukuran file maksimal 5MB"]
  }
}
```

## Browser Testing

### Test from UI (home.blade.php)
1. Go to: `http://127.0.0.1:8000/`
2. Add item to cart
3. Enter name: "Test User"
4. Enter phone: "081234567890"
5. Select payment method: "COD"
6. Click "Checkout"
7. Should redirect to checkout page
8. Click "Bayar Sekarang"
9. Should show success page with confirmation

### Test from UI (menu.blade.php)
1. Go to: `http://127.0.0.1:8000/menu`
2. Select a menu item and add to cart
3. Click cart icon
4. Enter customer info
5. Select payment method
6. Click "Checkout"
7. Follow same flow as above

## Checking Logs

### View Latest Errors
```bash
tail -f storage/logs/laravel.log
```

### Search for Specific Error
```bash
grep "Checkout transaction failed" storage/logs/laravel.log
```

### View All Payment Errors
```bash
grep "Payment confirmation failed" storage/logs/laravel.log
```

## Database Verification

### Check Orders Created
```bash
php artisan tinker
>>> DB::table('orders')->get()
```

### Check Payment Records
```bash
>>> DB::table('payments')->where('type', 'QRIS')->get()
```

### Check QRIS Proofs
```bash
>>> DB::table('payments')->where('qris_proof_path', '!=', null)->get()
```

### Verify Storage Link
```bash
ls -la public/storage
```

Should show symlink to `storage/app/public`

## Common Issues & Solutions

### Issue: "Field 'id' doesn't have a default value"
**Solution**: Migration table structure issue, usually resolves on next run

### Issue: File upload says "required" even with file
**Solution**: Ensure form has `enctype="multipart/form-data"`

### Issue: Can't access uploaded files via /storage/ URL
**Solution**: Run `php artisan storage:link` to create symlink

### Issue: Validation errors not returning 422
**Solution**: Ensure `Accept: application/json` header is sent

### Issue: Order created but status remains "pending"
**Solution**: Run payment confirmation endpoint to update status

## Performance Notes

- Checkout creates ~3-4 database records per order
- Average processing time: 100-200ms
- QRIS upload: File I/O overhead, typically 500-1000ms
- Logging is async, minimal performance impact

## Production Checklist

- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Set up proper error notification (Sentry/Bugsnag)
- [ ] Configure email notifications for orders
- [ ] Test with real payment gateway (when available)
- [ ] Set up admin dashboard to view QRIS proofs
- [ ] Configure backup storage (if using cloud)
- [ ] Monitor log file sizes
- [ ] Set up log rotation
- [ ] Test with high volume checkout requests
- [ ] Verify all validation messages in Indonesian language

## Support

For detailed documentation, see: `CHECKOUT_SYSTEM_DOCS.md`

For database structure, check migrations in: `database/migrations/`

For model relationships, see: `app/Models/`
