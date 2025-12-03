# Checkout Flow Diagrams & Architecture

## Complete Checkout Flow

```
┌─────────────────────────────────────────────────────────────────────┐
│                         CUSTOMER JOURNEY                              │
└─────────────────────────────────────────────────────────────────────┘

1. HOME / MENU PAGE
   ├─ Select items
   ├─ Add to cart
   └─ Click "Checkout"
        │
        ▼
2. CHECKOUT FORM
   ├─ Enter: Name, Phone, Payment Method
   ├─ Review cart & total
   └─ Click "Checkout" button
        │
        ▼ POST /checkout
        │
3. [SERVER: VALIDATION]
   ├─ Validate phone format
   ├─ Validate payment method
   ├─ Validate items array
   └─ If invalid → HTTP 422 + Error message
        │
        ▼ (if valid)
4. [SERVER: CREATE ORDER]
   ├─ START TRANSACTION
   ├─ Create Order (status: pending)
   ├─ Create OrderItems (one per item)
   ├─ Create Products (if don't exist)
   ├─ Create Payment (status: pending, type: COD/QRIS)
   ├─ COMMIT TRANSACTION
   └─ Return: { status: "success", order_id: 123 }
        │
        ▼
5. DISPLAY CHECKOUT PAGE
   ├─ Show order summary
   ├─ Show payment method details
   └─ Show "Bayar Sekarang" button
        │
        ▼
6. USER CLICKS "BAYAR SEKARANG"
        │
        ▼ POST /checkout/{order}/pay
        │
7. [SERVER: CONFIRM PAYMENT]
   ├─ Load Payment record
   ├─ Update status to "paid"
   ├─ Update Order status to "completed"
   ├─ Add transaction reference (TXN-TIMESTAMP-ID)
   └─ Return: { status: "success" }
        │
        ▼
8. REDIRECT TO SUCCESS PAGE
   GET /checkout/success?order=123
        │
        ▼
9. SHOW PAYMENT-SPECIFIC PAGE

   ┌──────────────────┐
   │ IF COD PAYMENT   │
   └──────────────────┘
   ├─ Show confirmation popup
   ├─ Display customer info
   └─ "Proceed to Shop" button → /menu

   ┌──────────────────┐
   │ IF QRIS PAYMENT  │
   └──────────────────┘
   ├─ Show QR code
   ├─ Show file upload form
   ├─ User drags/clicks to upload image
   └─ File upload:
      POST /checkout/{order}/upload-proof
         │
         ▼ [Validate file: JPG/PNG, max 5MB]
         │
         ├─ If invalid → HTTP 422 + Error
         │
         └─ If valid:
            ├─ Store file to storage/app/public/qris_proofs/
            ├─ Update Payment record with file path
            ├─ Mark Payment status as "paid"
            └─ Return: { status: "success", file_url: "/storage/..." }
         │
         ▼
      Show success popup
      └─ "Proceed to Shop" → /menu

   ┌──────────────────────┐
   │ IF TRANSFER PAYMENT  │
   └──────────────────────┘
   ├─ Show bank details
   ├─ Show transfer amount
   └─ "Proceed to Shop" button → /menu
        │
        ▼
10. REDIRECT TO MENU
    └─ Shopping session complete
```

---

## Request/Response Flow Diagram

```
┌────────────┐                                          ┌──────────────┐
│  FRONTEND  │                                          │  BACKEND     │
│ (Browser)  │                                          │ (Laravel)    │
└────────────┘                                          └──────────────┘
      │                                                        │
      │─── POST /checkout ──────────────────────────────────→ │
      │  {                                                     │
      │    "customer_name": "John",                           │
      │    "customer_phone": "081234567890",                  │
      │    "payment_method": "cash",                          │
      │    "items": [                                         │
      │      {"name": "Nasi", "priceNumber": 10000, "qty": 1} │
      │    ]                                                  │
      │  }                                                    │
      │                                                ┌──────┴────────┐
      │                                                │ VALIDATION    │
      │                                                │ Phone format? │
      │                                                │ Items array?  │
      │                                                │ Price >= 0?   │
      │                                                └──────┬────────┘
      │                                                       │
      │                                         ┌─────────────┴──────────┐
      │                                         │                        │
      │                                    INVALID              VALID    │
      │                                         │                        │
      │      ← HTTP 422 + Errors ─────────────┤                        │
      │  {                                      │     ┌────────────────┐│
      │    "message": "invalid",                │     │ DB::TRANSACTION││
      │    "errors": {...}                      │     │ ├─ Create      ││
      │  }                                      │     │ │  Order       ││
      │                                         │     │ ├─ Create      ││
      │                                         │     │ │  OrderItems  ││
      │                                         │     │ ├─ Create      ││
      │                                         │     │ │  Products    ││
      │                                         │     │ ├─ Create      ││
      │                                         │     │ │  Payment     ││
      │                                         │     │ └─ COMMIT      ││
      │                                         │     └────────────────┘│
      │                                         │                      │
      │      ← HTTP 200 ────────────────────────┤
      │  {                                       │
      │    "status": "success",                 │
      │    "order_id": 123,                     │
      │    "redirect": "/checkout/..."          │
      │  }                                       │
      │                                          │
      │─── GET /checkout/{order} ──────────────→ │
      │                                    ┌─────┴────────┐
      │                                    │ Load Order   │
      │                                    │ with items   │
      │                                    └─────┬────────┘
      │      ← HTTP 200 (HTML) ─────────────────┤
      │  [Checkout page with summary]           │
      │                                          │
      │─── POST /checkout/{order}/pay ─────────→ │
      │  (with CSRF token)                 ┌────┴─────────────────┐
      │                                    │ Update Payment       │
      │                                    │ Update Order status  │
      │                                    │ Add TXN reference    │
      │                                    └────┬─────────────────┘
      │      ← HTTP 200 ────────────────────────┤
      │  {                                       │
      │    "status": "success",                 │
      │    "message": "Payment confirmed",      │
      │    "order_id": 123                      │
      │  }                                       │
      │                                          │
      │─── GET /checkout/success?order=123 ────→ │
      │                                    ┌─────┴────────┐
      │                                    │ Load Order   │
      │                                    │ Check method │
      │                                    └─────┬────────┘
      │      ← HTTP 200 (HTML) ─────────────────┤
      │  [Success page with payment flow]       │
      │                                          │
      │─── (if QRIS) ──────────────────────────→ │
      │  POST /checkout/{order}/upload-proof    │
      │  multipart: {payment_proof: file}  ┌────┴──────────────┐
      │                                    │ Validate file:    │
      │                                    │ - Type (JPG/PNG)  │
      │                                    │ - Size (max 5MB)  │
      │                                    │ - Store file      │
      │                                    │ - Update Payment  │
      │                                    └────┬──────────────┘
      │      ← HTTP 200 ────────────────────────┤
      │  {                                       │
      │    "status": "success",                 │
      │    "file_url": "/storage/..."           │
      │  }                                       │
      │                                          │
      └──────────────────────────────────────────┘
```

---

## Database Transaction Flow (Atomic)

```
POST /checkout
    │
    ▼
┌─────────────────────────────────────────┐
│     DB::transaction() STARTS             │
│                                          │
│  1. INSERT INTO orders (...)             │
│     └─ ID: 123                           │
│                                          │
│  2. INSERT INTO order_items (...)        │
│     ├─ order_id: 123                     │
│     ├─ product_id: 1                     │
│     └─ quantity: 2                       │
│                                          │
│  3. INSERT INTO products (...)           │
│     ├─ name: "Nasi Goreng"               │
│     ├─ price: 12000                      │
│     └─ category_id: 1                    │
│                                          │
│  4. INSERT INTO payments (...)           │
│     ├─ order_id: 123                     │
│     ├─ type: 'COD'                       │
│     ├─ amount: 24000                     │
│     └─ status: 'pending'                 │
│                                          │
│  5. COMMIT TRANSACTION                   │
│     └─ All changes saved atomically      │
└─────────────────────────────────────────┘
    │
    ├─ If ANY step fails → ROLLBACK (undo all)
    │
    ▼
Return: { status: "success", order_id: 123 }


ERROR CASE:
─────────────

If error occurs (e.g., database connection):
    │
    ▼
┌─────────────────────────────────────────┐
│     DB::transaction() ROLLED BACK        │
│                                          │
│  ✗ All changes UNDONE                   │
│  ✗ No partial order created             │
│  ✗ Database stays consistent             │
│                                          │
│  Return: { status: "error", detail: ...  }
└─────────────────────────────────────────┘
```

---

## Payment Confirmation Flow

```
User clicks "Bayar Sekarang"
    │
    ▼
POST /checkout/{order}/pay
    │
    ▼
┌─────────────────────────────────────┐
│  Load Payment record from DB        │
│  Current state: status = 'pending'  │
└─────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────┐
│  UPDATE Payment:                    │
│  - status: 'paid'                   │
│  - transaction_ref: 'TXN-...-123'   │
└─────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────┐
│  UPDATE Order:                      │
│  - status: 'completed'              │
└─────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────┐
│  Payment marked as PAID             │
│  Order marked as COMPLETED          │
│  Ready for admin to process         │
└─────────────────────────────────────┘
```

---

## QRIS File Upload Flow

```
User selects/drops file on QRIS page
    │
    ▼
Frontend validates:
├─ File type (JPG/PNG only)
├─ File size (< 5MB)
└─ Show preview

    │ (if valid)
    ▼
POST /checkout/{order}/upload-proof
    │ multipart form data
    ▼
┌────────────────────────────────────┐
│  Backend Validation                │
│  ├─ File exists?                   │
│  ├─ Type in [jpg, jpeg, png]?      │
│  ├─ Size <= 5120 KB?               │
│  │                                 │
│  └─ Result: VALID or INVALID       │
└────────────────────────────────────┘
    │
    ├─ If INVALID → HTTP 422 + Error
    │
    └─ If VALID:
        │
        ▼
    ┌────────────────────────────────────┐
    │  Store File                        │
    │  Location:                         │
    │  storage/app/public/qris_proofs/   │
    │  {order_id}_{timestamp}.{ext}      │
    │  Example: 43_1701686400.jpg        │
    └────────────────────────────────────┘
        │
        ▼
    ┌────────────────────────────────────┐
    │  Update Payment Record             │
    │  ├─ qris_proof_path: 'qris_proo...'│
    │  ├─ status: 'paid'                 │
    │  └─ transaction_ref: 'QRIS-...'    │
    └────────────────────────────────────┘
        │
        ▼
    ┌────────────────────────────────────┐
    │  Update Order                      │
    │  └─ status: 'completed'            │
    └────────────────────────────────────┘
        │
        ▼
    Return HTTP 200:
    {
      "status": "success",
      "file_url": "/storage/qris_proofs/43_..."
    }
```

---

## Error Handling Flow

```
User/Request
    │
    ▼
Validation Layer
    │
    ├─ Invalid? → HTTP 422 + Field Errors
    │             (sent to user immediately)
    │
    └─ Valid? → Continue

    ▼
DB Transaction Layer
    │
    ├─ Connection error?
    │  └─ Log error + HTTP 500 + Generic message
    │
    ├─ Constraint violation?
    │  └─ Log error + HTTP 500 + Generic message
    │
    └─ Success? → Return order

    ▼
Success Response
    │
    └─ HTTP 200 + JSON data


ERROR RESPONSE CHAIN:
────────────────────

Exception occurs
    │
    ▼
Try-Catch Block
    │
    ▼
Log Exception:
├─ Exception class
├─ Message
├─ Stack trace
├─ Context (customer, items, etc.)
└─ Timestamp

    ▼
Prepare Response:
├─ HTTP Status Code (500)
├─ Status field: "error"
├─ Message field: Generic (or detailed if DEBUG)
└─ Detail field: (optional, only if DEBUG=true)

    ▼
Return JSON Response
└─ Client shows error message
   Admin checks logs for details
```

---

## Status Code Decision Tree

```
Request received
    │
    ▼
Is request valid JSON/format?
├─ NO  → HTTP 400 Bad Request
│
└─ YES → Continue

    ▼
Do field values pass validation?
├─ NO  → HTTP 422 Unprocessable Entity
│       (with errors object)
│
└─ YES → Continue

    ▼
Can database operation complete?
├─ NO (Product not found) → HTTP 409 Conflict
│
├─ NO (Other error) → HTTP 500 Internal Server Error
│                    (with logging)
│
└─ YES → Continue

    ▼
HTTP 200 OK
└─ Return success data
```

---

## Admin Dashboard Integration (Future)

```
┌─────────────────────────────────────┐
│      ADMIN DASHBOARD                 │
│  /admin/pembukuan                    │
└─────────────────────────────────────┘
        │
        ▼
    Display:
    ├─ Orders table
    │  ├─ Status column
    │  │  ├─ pending (gray)
    │  │  └─ completed (green)
    │  │
    │  └─ Action column
    │     ├─ View order details
    │     ├─ View payment
    │     └─ If QRIS: Download proof
    │
    ├─ Payment Summary
    │  ├─ COD count
    │  ├─ QRIS count
    │  ├─ QRIS proofs count
    │  └─ Transfer count
    │
    └─ Financial Report
       ├─ Monthly revenue
       ├─ Payment method breakdown
       └─ Proof verification status

Future Features:
├─ View QRIS proof images
├─ Verify payment proof
├─ Mark proof as confirmed
└─ Generate invoice PDF
```

---

## Database Schema (Simplified)

```
┌──────────────────────┐
│      ORDERS          │
├──────────────────────┤
│ id (PK)              │
│ customer_name        │
│ customer_phone       │
│ payment_method       │
│ total_price          │
│ status (pending/completed)
│ created_at           │
└──────────────────────┘
        │
        ├─── 1:N ──→ ┌──────────────────────┐
        │            │   ORDER_ITEMS        │
        │            ├──────────────────────┤
        │            │ id (PK)              │
        │            │ order_id (FK)        │
        │            │ product_id (FK)      │
        │            │ quantity             │
        │            │ subtotal             │
        │            └──────────────────────┘
        │                    │
        │                    ├─── N:1 ──→ ┌──────────────────────┐
        │                    │            │    PRODUCTS          │
        │                    │            ├──────────────────────┤
        │                    │            │ id (PK)              │
        │                    │            │ category_id (FK)     │
        │                    │            │ name                 │
        │                    │            │ price                │
        │                    │            │ is_available         │
        │                    │            └──────────────────────┘
        │                    │
        │                    └─── N:1 ──→ ┌──────────────────────┐
        │                                 │   CATEGORIES         │
        │                                 ├──────────────────────┤
        │                                 │ id (PK)              │
        │                                 │ name                 │
        │                                 │ description          │
        │                                 └──────────────────────┘
        │
        └─── 1:N ──→ ┌──────────────────────────────┐
                     │     PAYMENTS                  │
                     ├──────────────────────────────┤
                     │ id (PK)                      │
                     │ order_id (FK)                │
                     │ type (COD/QRIS/Transfer)     │
                     │ amount                       │
                     │ status (pending/paid/failed) │
                     │ transaction_ref              │
                     │ qris_proof_path (NEW!)       │
                     │ created_at                   │
                     └──────────────────────────────┘
```

---

## Storage Directory Structure

```
storage/
├── app/
│   ├── private/              (private files)
│   └── public/
│       └── qris_proofs/      (QRIS proofs accessible via web)
│           ├── 43_1701686400.jpg
│           ├── 43_1701686500.jpg
│           ├── 44_1701686600.png
│           └── ...
│
├── logs/
│   └── laravel.log           (all application logs)
│       ├── Checkout errors
│       ├── Payment errors
│       ├── File upload errors
│       └── System errors
│
└── framework/
    ├── cache/                (framework cache)
    ├── sessions/             (session storage)
    └── views/                (compiled views)
```

---

## Key Takeaways

✅ **Atomic Transactions**: All-or-nothing order creation
✅ **Clear Responses**: Structured JSON with consistent format
✅ **Proper Logging**: Full context logged for debugging
✅ **Validation First**: Input validated before database operations
✅ **Error Codes**: Different HTTP status for different error types
✅ **Payment Methods**: Each handled with specific UI/logic
✅ **File Security**: Type/size validated before storage
✅ **Backward Compatible**: Old and new response formats work

The system is production-ready, stable, and follows Laravel best practices!
