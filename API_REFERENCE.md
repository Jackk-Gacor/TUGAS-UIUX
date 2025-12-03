# API Endpoints Reference

## Base URL
```
http://127.0.0.1:8000
```

## Endpoints

### 1. Create Order (Checkout)
**Endpoint**: `POST /checkout`

**Authentication**: CSRF Token (X-CSRF-TOKEN header)

**Request Headers**:
```
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}
```

**Request Body**:
```json
{
  "customer_name": "string (required, max 255)",
  "customer_phone": "string (required, regex: 10-15 digits with optional +/-)",
  "payment_method": "string (required, in: cash|qris|COD|QRIS|Transfer)",
  "items": [
    {
      "name": "string (required, max 255)",
      "priceNumber": "number (required, min 0)",
      "qty": "integer (required, min 1)"
    }
  ],
  "note": "string (optional, max 500)"
}
```

**Success Response** (HTTP 200):
```json
{
  "status": "success",
  "ok": true,
  "order_id": 123,
  "payment_method": "cod",
  "redirect": "/checkout/success?order=123"
}
```

**Validation Error Response** (HTTP 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "customer_phone": ["Nomor telepon tidak valid (format: 08xx xxx xxxx)"],
    "items": ["Minimal ada 1 item pesanan"]
  }
}
```

**Server Error Response** (HTTP 500):
```json
{
  "status": "error",
  "message": "Internal server error",
  "detail": "Detailed error message (if APP_DEBUG=true)"
}
```

---

### 2. Display Checkout Page
**Endpoint**: `GET /checkout/{order_id}`

**Response**: HTML page (Blade template)

**Example**:
```
GET /checkout/123
```

**Returns**: Checkout page with order summary, payment options, and confirmation button

---

### 3. Confirm Payment
**Endpoint**: `POST /checkout/{order_id}/pay`

**Authentication**: CSRF Token

**Request Headers**:
```
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}
```

**Request Body**: Empty (no parameters needed)

**Success Response** (HTTP 200):
```json
{
  "status": "success",
  "message": "Pembayaran berhasil dikonfirmasi",
  "order_id": 123
}
```

**Error Response** (HTTP 500):
```json
{
  "status": "error",
  "message": "Gagal mengkonfirmasi pembayaran",
  "detail": "Exception message (if APP_DEBUG=true)"
}
```

**Side Effects**:
- Payment status updated to 'paid'
- Order status updated to 'completed'
- Transaction reference generated

---

### 4. Display Success Page
**Endpoint**: `GET /checkout/success?order={order_id}`

**Query Parameters**:
- `order` (required): Order ID returned from POST /checkout

**Response**: HTML page (Blade template)

**Example**:
```
GET /checkout/success?order=123
```

**Page Features**:
- Order summary with items and total
- Payment method-specific content:
  - **COD**: Confirmation popup with customer details
  - **QRIS**: QR code display + file upload form
  - **Transfer**: Bank account details
- "Proceed to Shop" button redirecting to `/menu`

---

### 5. Upload QRIS Payment Proof
**Endpoint**: `POST /checkout/{order_id}/upload-proof`

**Authentication**: CSRF Token

**Request Headers**:
```
Content-Type: multipart/form-data
X-CSRF-TOKEN: {csrf_token}
```

**Form Data**:
```
payment_proof: (file, required, mimes: jpg|jpeg|png, max: 5120 KB)
```

**Success Response** (HTTP 200):
```json
{
  "status": "success",
  "message": "Bukti pembayaran berhasil diunggah",
  "file_url": "/storage/qris_proofs/123_1701686400.jpg"
}
```

**Validation Error Response** (HTTP 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "payment_proof": [
      "File harus berformat JPG, JPEG, atau PNG"
    ]
  }
}
```

**Server Error Response** (HTTP 500):
```json
{
  "status": "error",
  "message": "Gagal mengunggah bukti pembayaran",
  "detail": "Storage error details (if APP_DEBUG=true)"
}
```

**Side Effects**:
- File stored in `storage/app/public/qris_proofs/{order_id}_{timestamp}.{ext}`
- Payment status updated to 'paid'
- Payment record includes file path
- Order status updated to 'completed'
- Success logged with file path

---

## Request/Response Examples

### Example 1: Complete Checkout Flow (COD)

**Step 1**: Create order
```bash
curl -X POST http://127.0.0.1:8000/checkout \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: abc123def456" \
  -d '{
    "customer_name": "Budi Santoso",
    "customer_phone": "081234567890",
    "payment_method": "cash",
    "items": [
      {"name": "Nasi Goreng Spesial", "priceNumber": 15000, "qty": 2},
      {"name": "Teh Manis", "priceNumber": 5000, "qty": 2}
    ]
  }'
```

Response:
```json
{
  "status": "success",
  "ok": true,
  "order_id": 42,
  "payment_method": "cod",
  "redirect": "/checkout/success?order=42"
}
```

**Step 2**: Confirm payment
```bash
curl -X POST http://127.0.0.1:8000/checkout/42/pay \
  -H "X-CSRF-TOKEN: abc123def456"
```

Response:
```json
{
  "status": "success",
  "message": "Pembayaran berhasil dikonfirmasi",
  "order_id": 42
}
```

**Step 3**: Display success page
```bash
GET /checkout/success?order=42
```

Returns HTML success page for COD payment method

---

### Example 2: QRIS Checkout with File Upload

**Step 1**: Create QRIS order (same as above but payment_method="qris")

**Step 2**: Confirm payment (returns success)

**Step 3**: Upload QRIS proof
```bash
curl -X POST http://127.0.0.1:8000/checkout/43/upload-proof \
  -H "X-CSRF-TOKEN: abc123def456" \
  -F "payment_proof=@/path/to/qris-proof.jpg"
```

Response:
```json
{
  "status": "success",
  "message": "Bukti pembayaran berhasil diunggah",
  "file_url": "/storage/qris_proofs/43_1701686400.jpg"
}
```

---

### Example 3: Validation Errors

**Invalid Phone Number**:
```bash
curl -X POST http://127.0.0.1:8000/checkout \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "John",
    "customer_phone": "abc",
    "payment_method": "cash",
    "items": [{"name": "Food", "priceNumber": 10000, "qty": 1}]
  }'
```

Response (HTTP 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "customer_phone": ["Nomor telepon tidak valid (format: 08xx xxx xxxx)"]
  }
}
```

**Empty Items Array**:
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

Response (HTTP 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "items": ["Minimal ada 1 item pesanan"]
  }
}
```

**File Too Large (QRIS Upload)**:
```bash
curl -X POST http://127.0.0.1:8000/checkout/43/upload-proof \
  -H "X-CSRF-TOKEN: abc123" \
  -F "payment_proof=@/path/to/large-file.jpg"  # 6MB
```

Response (HTTP 422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "payment_proof": ["Ukuran file maksimal 5MB"]
  }
}
```

---

## Status Codes Reference

| Code | Meaning | Usage |
|------|---------|-------|
| 200 | OK | Successful request |
| 422 | Unprocessable Entity | Validation failed |
| 409 | Conflict | Resource conflict (e.g., product not found) |
| 500 | Internal Server Error | Server exception |

---

## Payment Method Values

### Accepted Values (Frontend Input)
- `cash` → Normalized to `COD`
- `qris` → Normalized to `QRIS`
- `COD` → Stored as `COD`
- `QRIS` → Stored as `QRIS`
- `Transfer` → Stored as `Transfer`

### Response Value (Payment Method Field)
Always lowercase in response:
- `cod` (for COD)
- `qris` (for QRIS)
- `transfer` (for Transfer)

---

## Order Status Values

### Initial State
- `pending` - Order created, awaiting payment

### After Payment Confirmation
- `completed` - Payment received, order ready to process

### Payment Record Status
- `pending` - Initial state
- `paid` - After pay() or uploadQrisProof() call
- `failed` - Manual marking (not automated)

---

## File Upload Storage

### Upload Directory
```
storage/app/public/qris_proofs/
```

### File Naming Format
```
{order_id}_{unix_timestamp}.{extension}
```

**Example**:
```
43_1701686400.jpg
44_1701686500.png
45_1701686600.jpeg
```

### File Access URL
```
/storage/qris_proofs/{filename}
```

### Full URL Example
```
http://127.0.0.1:8000/storage/qris_proofs/43_1701686400.jpg
```

---

## Logging Events

### Successful Checkout
Logged with: Order ID, item count, total amount, customer name

### Failed Checkout
Logged with: Exception type, message, trace, customer info, item count

### Successful Payment Confirmation
Logged with: Order ID, transaction reference, timestamp

### Failed Payment Confirmation
Logged with: Order ID, exception message, trace

### Successful QRIS Upload
Logged with: Order ID, file path, timestamp

### Failed QRIS Upload
Logged with: Order ID, file size, exception message, trace

**Log File Location**: `storage/logs/laravel.log`

---

## CSRF Token

All POST requests require CSRF token in header:
```
X-CSRF-TOKEN: {value_from_meta_tag}
```

**Meta Tag**:
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

**JavaScript**:
```javascript
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
```

---

## Rate Limiting (Not Implemented Yet)

Currently no rate limiting. Future implementation should consider:
- Max 10 checkout attempts per IP per minute
- Max 5 file uploads per order
- Max 100 requests per IP per hour

---

## Backward Compatibility

For legacy clients expecting `{ "ok": true }` response:
- New response includes both `ok` and `status` fields
- Clients checking `json.ok` will continue working
- New clients can check `json.status === 'success'`

**Legacy Check**:
```javascript
if (json && json.ok) { /* Success */ }
```

**New Check**:
```javascript
if (json && json.status === 'success') { /* Success */ }
```

Both will work simultaneously.
