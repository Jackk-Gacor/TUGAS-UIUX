<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            // dukung metode pembayaran dari home (COD, QRIS, Transfer) dan menu (Cash, QRIS)
            'payment_method' => ['required', 'in:Cash,COD,QRIS,Transfer'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string'],
            'items.*.priceNumber' => ['required', 'numeric', 'min:0'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($data) {
            $total = 0;
            $catId = DB::table('categories')->value('id');
            if (!$catId) {
                $catId = DB::table('categories')->insertGetId([
                    'name' => 'Umum',
                    'description' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            // Normalisasi label metode pembayaran agar konsisten di database
            $rawMethod = $data['payment_method'];
            $normalizedMethod = $rawMethod === 'Cash' ? 'COD' : $rawMethod;

            $order = Order::create([
                'user_id' => null,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'note' => $data['note'] ?? null,
                'payment_method' => $normalizedMethod,
                'status' => 'pending',
                'total_price' => 0,
            ]);

            foreach ($data['items'] as $item) {
                $price = (int) ($item['priceNumber'] ?? 0);
                $qty = (int) ($item['qty'] ?? 1);
                $subtotal = $price * $qty;
                $total += $subtotal;

                $product = Product::firstOrCreate(
                    ['name' => $item['name']],
                    [
                        'category_id' => $catId,
                        'description' => null,
                        'price' => $price,
                        'image' => null,
                        'is_available' => true,
                    ]
                );

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total_price' => $total]);

            // Saat checkout pertama kali, semua pembayaran disimpan sebagai pending.
            // Dashboard keuangan hanya akan menghitung data setelah pembayaran dikonfirmasi (status = paid).
            $status = 'pending';
            $payType = $normalizedMethod;
            Payment::create([
                'order_id' => $order->id,
                'type' => $payType,
                'amount' => $total,
                'status' => $status,
                'transaction_ref' => null,
            ]);

            return response()->json(['ok' => true, 'order_id' => $order->id]);
        });
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'payments']);
        return view('checkout', compact('order'));
    }

    public function pay(Order $order)
    {
        $order->load('payments');
        $payment = $order->payments()->latest()->first();
        if ($payment) {
            $payment->update(['status' => 'paid', 'transaction_ref' => 'SIM-' . now()->format('YmdHis')]);
        }
        $order->update(['status' => 'completed']);
        return response()->json(['ok' => true]);
    }
}
