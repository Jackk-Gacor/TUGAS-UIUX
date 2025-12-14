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

class OrderController extends Controller
{
    /**
     * POST /checkout
     * Create new order (JSON ONLY)
     */
    public function store(Request $request)
    {
        try {
            // =========================
            // VALIDATION
            // =========================
            $validated = $request->validate([
                'customer_name'   => 'required|string|max:255',
                'customer_phone'  => 'required|string|max:30',
                'payment_method'  => 'required|string',

                'items'           => 'required|array|min:1',
                'items.*.name'    => 'required|string|max:255',

                // price / qty flexible
                'items.*.priceNumber' => 'nullable|numeric|min:0',
                'items.*.price'       => 'nullable|numeric|min:0',
                'items.*.qty'         => 'nullable|integer|min:1',
                'items.*.quantity'    => 'nullable|integer|min:1',

                'note' => 'nullable|string|max:500',
            ]);

            // =========================
            // TRANSACTION
            // =========================
            $order = DB::transaction(function () use ($validated) {

                $paymentMethod = $this->normalizePaymentMethod($validated['payment_method']);
                $total = 0;

                $category = Category::firstOrCreate(
                    ['name' => 'Umum'],
                    ['description' => 'Kategori default']
                );

                $order = Order::create([
                    'user_id'        => null,
                    'customer_name'  => $validated['customer_name'],
                    'customer_phone' => $validated['customer_phone'],
                    'note'           => $validated['note'] ?? null,
                    'payment_method' => $paymentMethod,
                    'status'         => 'pending',
                    'total_price'    => 0,
                ]);

                foreach ($validated['items'] as $item) {

                    // 🔒 AMAN: apapun format FE
                    $price = (int) ($item['priceNumber'] ?? $item['price'] ?? 0);
                    $qty   = (int) ($item['qty'] ?? $item['quantity'] ?? 1);

                    $subtotal = $price * $qty;

                    $product = Product::firstOrCreate(
                        ['name' => $item['name']],
                        [
                            'category_id' => $category->id,
                            'price'       => $price,
                            'description' => null,
                            'image'       => null,
                            'is_available'=> true,
                        ]
                    );

                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $product->id,
                        'quantity'   => $qty,
                        'subtotal'   => $subtotal,
                    ]);

                    $total += $subtotal;
                }

                $order->update(['total_price' => $total]);

                Payment::create([
                    'order_id' => $order->id,
                    'type'     => $paymentMethod,
                    'amount'   => $total,
                    'status'   => 'pending',
                ]);

                return $order;
            });

            // =========================
            // RESPONSE
            // =========================
            return response()->json([
    'status'       => 'success',
    'message'      => 'Order berhasil dibuat',
    'order_id'     => $order->id,
    'redirect_url' => route('checkout.show', $order->id),
], 201);


        } catch (\Throwable $e) {
            Log::error('Checkout error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal membuat order',
            ], 500);
        }
    }

    /**
     * GET /checkout/order/{order}
     */
    public function show(Order $order)
    {
        $order->load(['items.product', 'payments']);
        return view('checkout', compact('order'));
    }

    /**
     * POST /checkout/{order}/pay
     */
    public function pay(Order $order)
    {
        try {
            $payment = $order->payments()->latest()->first();

            if ($payment && $payment->status === 'pending') {
                $payment->update([
                    'status' => 'paid',
                    'transaction_ref' => 'TXN-' . now()->timestamp,
                ]);
            }

            $order->update(['status' => 'completed']);

            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran berhasil',
            ]);

        } catch (\Throwable $e) {
            Log::error('Payment error', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses pembayaran',
            ], 500);
        }
    }

    /**
     * GET /checkout/success
     */
    public function successPage(Request $request)
    {
        $order = Order::with(['items.product', 'payments'])
            ->findOrFail($request->query('order'));

        return view('success', compact('order'));
    }

    /**
     * POST /checkout/{order}/upload-proof
     */
    public function uploadQrisProof(Request $request, Order $order)
    {
        $request->validate([
            'payment_proof' => 'required|image|max:5120',
        ]);

        try {
            $path = $request->file('payment_proof')
                ->store('qris_proofs', 'public');

            $payment = $order->payments()
                ->where('type', 'QRIS')
                ->latest()
                ->firstOrFail();

            $payment->update([
                'status' => 'paid',
                'qris_proof_path' => $path,
            ]);

            $order->update(['status' => 'completed']);

            return response()->json([
                'status'   => 'success',
                'file_url' => asset('storage/' . $path),
            ]);

        } catch (\Throwable $e) {
            Log::error('Upload QRIS error', ['error' => $e->getMessage()]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Upload bukti pembayaran gagal',
            ], 500);
        }
    }

    /**
     * Normalize payment method
     */
    private function normalizePaymentMethod(string $method): string
    {
        return match (strtolower($method)) {
            'cash', 'cod' => 'COD',
            'qris'        => 'QRIS',
            'transfer'    => 'Transfer',
            default       => strtoupper($method),
        };
    }
}
