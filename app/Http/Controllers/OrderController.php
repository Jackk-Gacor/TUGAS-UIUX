<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Create a new order with items and payment record
     * POST /checkout
     * 
     * Expected JSON request:
     * {
     *   "customer_name": "John",
     *   "customer_phone": "081234567890",
     *   "payment_method": "cash|qris|COD|QRIS|Transfer",
     *   "items": [{"name": "Nasi Goreng", "priceNumber": 12000, "qty": 2}, ...],
     *   "note": "optional instructions"
     * }
     */
    public function store(Request $request)
    {
        // Force JSON response header for this endpoint
        if (!$request->wantsJson()) {
            $request->headers->set('Accept', 'application/json');
        }

        try {
            // Validate request input (422 on validation failure)
            $validated = $request->validate(
                [
                    'customer_name' => 'required|string|max:255',
                    'customer_phone' => 'required|string|min:3|max:20',

                   'payment_method' => 'required|string',

                    'items' => 'required|array|min:1',
                    'items.*.name' => 'required|string|max:255',
                    'items.*.priceNumber' => 'required|numeric|min:0',
                    'items.*.qty' => 'required|integer|min:1',
                    'note' => 'nullable|string|max:500',
                ],
                [
                    'customer_name.required' => 'Nama pelanggan wajib diisi',
                    'customer_phone.regex' => 'Nomor telepon tidak valid (format: 08xx xxx xxxx)',
                    'payment_method.in' => 'Metode pembayaran tidak didukung',
                    'items.required' => 'Minimal ada 1 item pesanan',
                    'items.*.name.required' => 'Nama item wajib diisi',
                    'items.*.priceNumber.required' => 'Harga item wajib diisi',
                    'items.*.qty.min' => 'Jumlah item minimal 1',
                ]
            );

            // Use database transaction for atomic order creation
            $order = DB::transaction(function () use ($validated) {
                // Normalize payment method (cash -> COD, qris -> QRIS)
                $paymentMethod = $this->normalizePaymentMethod($validated['payment_method']);
                
                $total = 0;
                $items = [];

                // Get or create default category for products
                $category = Category::firstOrCreate(
                    ['name' => 'Umum'],
                    ['description' => 'Kategori umum untuk produk yang di-create dinamis']
                );

                // Process items: create products if they don't exist
                foreach ($validated['items'] as $cartItem) {
                    $price = (int)$cartItem['priceNumber'];
                    $quantity = (int)$cartItem['qty'];
                    $subtotal = $price * $quantity;
                    
                    // Create product if it doesn't exist (by name)
                    $product = Product::firstOrCreate(
                        ['name' => $cartItem['name']],
                        [
                            'category_id' => $category->id,
                            'price' => $price,
                            'description' => null,
                            'image' => null,
                            'is_available' => true,
                        ]
                    );
                    
                    $items[] = [
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                    ];
                    
                    $total += $subtotal;
                }

                // Create order with pending status
                $order = Order::create([
                    'user_id' => null,
                    'customer_name' => $validated['customer_name'],
                    'customer_phone' => $validated['customer_phone'],
                    'note' => $validated['note'] ?? null,
                    'payment_method' => $paymentMethod,
                    'status' => 'pending',
                    'total_price' => $total,
                ]);

                // Create order items
                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }

                // Create payment record with pending status
                Payment::create([
                    'order_id' => $order->id,
                    'type' => $paymentMethod,
                    'amount' => $total,
                    'status' => 'pending',
                    'transaction_ref' => null,
                ]);

                return $order;
            });

            // Return success response with order ID and redirect URL
            return redirect()->route('checkout.show', $order->id);


        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors (422 on validation failure)
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database errors
            Log::error('Database error during checkout', [
                'exception' => $e->getMessage(),
                'customer' => $request->only(['customer_name', 'customer_phone']),
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Database error',
                'detail' => config('app.debug') ? $e->getMessage() : 'Gagal menyimpan pesanan ke database',
            ], 500);

        } catch (\Throwable $e) {
            // Handle all other exceptions (500 Internal Server Error)
            Log::error('Checkout transaction failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'customer' => $request->only(['customer_name', 'customer_phone']),
                'items_count' => count($validated['items'] ?? []),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error',
                'detail' => config('app.debug') ? $e->getMessage() : 'Gagal membuat pesanan, silakan coba lagi',
            ], 500);
        }
    }

    /**
     * Display checkout page with order details
     * GET /checkout/{order}
     */
    public function show(Order $order)
    {
        $order->load(['items.product', 'payments']);
        return view('checkout', compact('order'));
    }

    /**
     * Confirm payment and mark as paid
     * POST /checkout/{order}/pay
     */
    public function pay(Order $order)
    {
        try {
            $order->load('payments');
            
            // Update latest payment to paid status
            $payment = $order->payments()->latest()->first();
            if ($payment && $payment->status === 'pending') {
                $payment->update([
                    'status' => 'paid',
                    'transaction_ref' => 'TXN-' . now()->format('YmdHis') . '-' . $order->id,
                ]);
            }

            // Update order to completed
            $order->update(['status' => 'completed']);

            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran berhasil dikonfirmasi',
                'order_id' => $order->id,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Payment confirmation failed', [
                'order_id' => $order->id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengkonfirmasi pembayaran',
                'detail' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Display success page after checkout
     * GET /checkout/success?order={order_id}
     */
    public function successPage(Request $request)
    {
        $orderId = $request->query('order');
        if (!$orderId) {
            return redirect()->route('menu')->with('error', 'Order ID tidak ditemukan');
        }

        $order = Order::with(['items.product', 'payments'])->findOrFail($orderId);
        
        return view('success', compact('order'));
    }

    /**
     * Upload QRIS payment proof file
     * POST /checkout/{order}/upload-proof
     * 
     * Expects multipart form-data with 'payment_proof' file field
     */
    public function uploadQrisProof(Request $request, Order $order)
    {
        // Validate file upload (422 on validation failure)
        $validated = $request->validate(
            [
                'payment_proof' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            ],
            [
                'payment_proof.required' => 'File bukti pembayaran wajib diupload',
                'payment_proof.mimes' => 'File harus berformat JPG, JPEG, atau PNG',
                'payment_proof.max' => 'Ukuran file maksimal 5MB',
            ]
        );

        try {
            // Store file in public disk under qris_proofs directory
            $file = $request->file('payment_proof');
            $filename = 'qris_proofs/' . $order->id . '_' . now()->timestamp . '.' . $file->getClientOriginalExtension();
            $path = Storage::disk('public')->putFileAs('qris_proofs', $file, basename($filename));

            // Update payment record with proof file path
            $payment = $order->payments()->where('type', 'QRIS')->latest()->first();
            if (!$payment) {
                throw new \Exception('Payment record not found for QRIS order');
            }

            $payment->update([
                'qris_proof_path' => $path,
                'status' => 'paid',
                'transaction_ref' => 'QRIS-' . now()->format('YmdHis') . '-' . $order->id,
            ]);

            // Update order status to completed
            $order->update(['status' => 'completed']);

            Log::info('QRIS proof uploaded successfully', [
                'order_id' => $order->id,
                'file_path' => $path,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Bukti pembayaran berhasil diunggah',
                'file_url' => asset('storage/' . $path),
            ], 200);

        } catch (\Throwable $e) {
            Log::error('QRIS proof upload failed', [
                'order_id' => $order->id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengunggah bukti pembayaran',
                'detail' => config('app.debug') ? $e->getMessage() : 'Silakan coba lagi',
            ], 500);
        }
    }

    /**
     * Normalize payment method to standard values
     * Maps: cash -> COD, qris -> QRIS, etc.
     */
    private function normalizePaymentMethod(string $method): string
    {
        $map = [
            'cash' => 'COD',
            'cod' => 'COD',
            'qris' => 'QRIS',
            'transfer' => 'Transfer',
        ];

        return $map[strtolower($method)] ?? ucfirst($method);
    }
}

