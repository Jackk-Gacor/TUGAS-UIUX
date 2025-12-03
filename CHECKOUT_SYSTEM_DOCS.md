# Checkout System Implementation Documentation

## Overview
This document describes the production-ready checkout system with comprehensive error handling, logging, and proper JSON responses.

## Changes Implemented

### 1. Database Migration
**File**: `database/migrations/2025_12_04_000000_add_qris_proof_path_to_payments.php`
- Added `qris_proof_path` column (nullable string) to `payments` table
- Stores the file path of uploaded QRIS payment proofs
- Allows tracking proof submissions for QRIS payments

### 2. Model Updates

#### Payment Model
**File**: `app/Models/Payment.php`
- Added `qris_proof_path` to `$fillable` array
- Enables mass assignment for the new proof path field

#### Product Model  
**File**: `app/Models/Product.php`
- Added Category import statement for Eloquent relationships

#### Category Model
**File**: `app/Models/Category.php`
- Fixed missing PHP opening tag
- Proper formatting and indentation

### 3. OrderController (Complete Rewrite)
**File**: `app/Http/Controllers/OrderController.php`

#### store() - POST /checkout
**Purpose**: Create order with items and payment record

**Request Format**:
```json
{
  "customer_name": "John",
  "customer_phone": "081234567890",
  "payment_method": "cash|qris|COD|QRIS|Transfer",
  "items": [
    {"name": "Nasi Goreng", "priceNumber": 12000, "qty": 2}
  ],
  "note": "optional instructions"
}
```

**Validation**:
- Phone: 10-15 digits with optional +/- characters
- Payment method: Normalized to standard values (cash→COD, qris→QRIS)
- Items: Array with min 1 item, each with name, price, quantity
- Returns 422 with validation errors on failure

**Processing**:
1. Validates all input fields
2. Uses DB::transaction() for atomic operations
3. Gets/creates default product category
4. Creates products on-the-fly if they don't exist
5. Creates order with pending status
6. Creates order items with calculated subtotals
7. Creates payment record with pending status
8. Logs all exceptions with full context

**Success Response** (200):
```json
{
  "status": "success",
  "ok": true,
  "order_id": 123,
  "payment_method": "cod",
  "redirect": "/checkout/success?order=123"
}
```

**Error Responses**:
- 422: Validation failure with errors array
- 409: Product not found (if using product IDs)
- 500: Database or other exceptions with debug info

#### pay() - POST /checkout/{order}/pay
**Purpose**: Confirm payment and mark order as completed

**Processing**:
1. Loads payment relationships
2. Updates latest payment to 'paid' status
3. Adds transaction reference (TXN-TIMESTAMP-ORDER_ID)
4. Updates order status to 'completed'
5. Returns success/error JSON response

**Success Response** (200):
```json
{
  "status": "success",
  "message": "Pembayaran berhasil dikonfirmasi",
  "order_id": 123
}
```

#### successPage() - GET /checkout/success
**Purpose**: Display success page after checkout

**Features**:
- Reads order ID from query parameter
- Loads order with items, products, and payments
- Displays payment-method-specific UI:
  - **COD**: Confirmation popup with customer details
  - **QRIS**: QR code + file upload form with validation
  - **Transfer**: Bank details and instructions
- Provides both skip and proceed buttons

#### uploadQrisProof() - POST /checkout/{order}/upload-proof
**Purpose**: Handle QRIS payment proof file uploads

**Validation**:
- File required
- Type: JPG, JPEG, PNG only (images)
- Max size: 5MB (5120 KB)
- Returns 422 with validation errors on failure

**Processing**:
1. Validates file upload
2. Stores file to `storage/app/public/qris_proofs/`
3. Filename format: `qris_proofs/{order_id}_{timestamp}.{ext}`
4. Updates payment record:
   - Sets `qris_proof_path`
   - Updates status to 'paid'
   - Adds transaction reference (QRIS-TIMESTAMP-ORDER_ID)
5. Updates order status to 'completed'
6. Logs success with file path

**Success Response** (200):
```json
{
  "status": "success",
  "message": "Bukti pembayaran berhasil diunggah",
  "file_url": "/storage/qris_proofs/123_1701686400.jpg"
}
```

**Error Response** (500):
```json
{
  "status": "error",
  "message": "Gagal mengunggah bukti pembayaran",
  "detail": "Error message (if APP_DEBUG=true)"
}
```

### 4. Routes Configuration
**File**: `routes/web.php`

```php
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout');
Route::get('/checkout/{order}', [OrderController::class, 'show'])->name('checkout.show');
Route::post('/checkout/{order}/pay', [OrderController::class, 'pay'])->name('checkout.pay');
Route::get('/checkout/success', [OrderController::class, 'successPage'])->name('checkout.success');
Route::post('/checkout/{order}/upload-proof', [OrderController::class, 'uploadQrisProof'])->name('checkout.upload-proof');
```

### 5. Frontend Updates

#### checkout.blade.php
- Updated "Bayar Sekarang" button handler
- Better error handling and loading state
- Proper JSON response status checking
- Shows error messages on failure

#### success.blade.php
- Updated QRIS upload route to use `checkout.upload-proof`
- Checks both `response.ok` and `json.status === 'success'`
- Improved error message display
- File upload with drag-drop, validation, and preview

#### home.blade.php & menu.blade.php
- Updated checkout error handling
- Checks for both `json.ok` and `json.status === 'success'`
- Better error message extraction from response
- Validates HTTP status before parsing JSON

## Error Handling & Logging

### Log Locations
- All logs: `storage/logs/laravel.log`
- Enable detailed debugging: Set `APP_DEBUG=true` in `.env`

### Logged Events
1. **Database errors**: Connection issues, constraint violations
2. **Validation failures**: Detailed validation errors
3. **Checkout failures**: Full exception trace, customer info, item count
4. **Payment confirmation errors**: Order ID, exception details
5. **QRIS upload errors**: File details, storage errors

### Debug Information
In development mode (APP_DEBUG=true):
- Full exception messages returned in JSON responses
- Stack traces logged for investigation
- Validation errors include field names and rules

In production (APP_DEBUG=false):
- Generic error messages to users
- Full details only in logs
- No sensitive information exposed

## Response Status Codes

### 200 OK
- Successful order creation
- Successful payment confirmation
- Successful file upload
- Successful page load

### 422 Unprocessable Entity
- Validation errors
- Missing required fields
- Invalid field formats
- File validation errors

### 409 Conflict
- Product not found
- Stock unavailable
- Order conflict

### 500 Internal Server Error
- Database connection failures
- Transaction failures
- File storage errors
- Unexpected exceptions

## Frontend Integration Guide

### Checkout Flow (home.blade.php / menu.blade.php)
```javascript
1. User fills name, phone, payment method
2. POST /checkout with cart items
3. Response: { status: "success", order_id: 123 }
4. Redirect to: /checkout/{order_id}
5. Display checkout details page
```

### Payment Confirmation (checkout.blade.php)
```javascript
1. User clicks "Bayar Sekarang" button
2. POST /checkout/{order}/pay
3. Response: { status: "success" }
4. Redirect to: /checkout/success?order={order_id}
5. Show success page
```

### Success Page (success.blade.php)
```javascript
IF payment_method === 'COD':
  - Show confirmation popup
  - Display customer details
  - Button: "Lanjutkan ke Toko" → /menu

IF payment_method === 'QRIS':
  - Show QR code placeholder
  - File upload form (drag-drop)
  - Validate file type and size
  - POST /checkout/{order}/upload-proof
  - On success: Show popup and redirect
  - Option: Skip upload for now

IF payment_method === 'Transfer':
  - Show bank account details
  - Display transfer amount
  - Button: "Mengerti, Lanjutkan ke Toko" → /menu
```

## Storage Setup

### Ensure Storage Link Exists
```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`, enabling:
- Direct access to uploaded files via HTTP
- Serving QRIS proofs to admin dashboard
- No direct file system exposure

### Directory Structure
```
storage/
├── app/
│   ├── public/
│   │   └── qris_proofs/
│   │       ├── 123_1701686400.jpg
│   │       ├── 124_1701686500.png
│   │       └── ...
│   └── private/
└── logs/
    └── laravel.log
```

## Testing Checklist

### Unit Tests to Run
```bash
php artisan test
```

- [ ] Checkout validation passes with correct data
- [ ] Checkout validation fails with invalid phone
- [ ] Checkout creates order and payment records
- [ ] Payment confirmation updates statuses
- [ ] QRIS upload validates file size
- [ ] QRIS upload validates file type
- [ ] QRIS upload stores file correctly
- [ ] QRIS upload updates payment record

### Manual Testing Steps
1. Fill checkout form with valid data → Order created
2. Attempt checkout with invalid phone → 422 error shown
3. Click "Bayar Sekarang" on checkout → Success page
4. For QRIS: Drag-drop file > 5MB → Error shown
5. For QRIS: Upload valid JPG → File stored, payment marked paid
6. For COD: See confirmation popup → Redirect to menu
7. Check `storage/logs/laravel.log` for proper logging

## Migration Notes

### If Migration Fails
1. Check if payments table exists: `php artisan tinker` → `DB::table('payments')->getColumns()`
2. If column already exists, run: `php artisan migrate:refresh --seeder`
3. Or manually add column via database client

### Rolling Back
```bash
php artisan migrate:rollback
```

## Performance Considerations

- DB::transaction() ensures atomicity (all-or-nothing)
- Lazy loading prevents N+1 query problems
- File uploads to public disk for fast access
- Validation happens before database operations
- Exception handling prevents server crashes

## Security Considerations

- File uploads: Validated type and size before storage
- File path: Stored as relative path, not exposed directly
- CSRF: All POST requests validated via middleware
- Validation: Server-side validation (never trust client)
- Logging: Sensitive data (passwords) excluded from logs
- Error messages: No system paths or SQL exposed to users

## Future Enhancements

1. **Email notifications**: Send confirmation emails to customers
2. **Admin notification**: Alert admin of new QRIS uploads
3. **Payment retry**: Allow users to retry failed payments
4. **Proof verification**: Admin dashboard to view and verify proofs
5. **Auto-mark paid**: Mark COD paid after time period
6. **Webhook integration**: Connect to payment gateway for real-time updates
7. **Invoice generation**: Create PDF invoices for orders
8. **Transaction export**: Export payment data for accounting

## Support & Debugging

### Common Issues

**Q: Orders not creating**
A: Check `storage/logs/laravel.log` for validation errors or database connection issues

**Q: File upload fails silently**
A: Ensure `php artisan storage:link` was run and `storage/app/public` has write permissions

**Q: Payment marked paid but admin doesn't see it**
A: Check payment status in database: `SELECT * FROM payments WHERE status='paid'`

**Q: Old response format not working**
A: Controller now returns both `ok` and `status` fields for compatibility

### Debug Commands
```bash
# Check database connection
php artisan tinker
DB::connection()->getPdo()

# Check file permissions
php artisan storage:link

# View logs
tail -f storage/logs/laravel.log

# Test route
php artisan route:list | grep checkout
```
