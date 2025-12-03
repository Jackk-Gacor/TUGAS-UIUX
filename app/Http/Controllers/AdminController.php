<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        // Query pembayaran yang sudah dibayar (dasar pembukuan keuangan)
        $paymentsQuery = Payment::where('status', 'paid');
        if ($from) {
            $paymentsQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $paymentsQuery->whereDate('created_at', '<=', $to);
        }

        $totalRevenue = (clone $paymentsQuery)->sum('amount');

        $today = now()->toDateString();
        $todayRevenue = (clone $paymentsQuery)
            ->whereDate('created_at', $today)
            ->sum('amount');

        $monthRevenue = (clone $paymentsQuery)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        // Data pesanan untuk tabel
        $ordersQuery = Order::with('payments')->latest();
        if ($from) {
            $ordersQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $ordersQuery->whereDate('created_at', '<=', $to);
        }

        $orders = $ordersQuery->paginate(10)->withQueryString();

        $stats = [
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
            'month_revenue' => $monthRevenue,
            'total_orders' => (clone $ordersQuery)->count(),
            'paid_orders' => (clone $ordersQuery)->where('status', 'completed')->count(),
            'pending_orders' => (clone $ordersQuery)->where('status', 'pending')->count(),
        ];

        return view('admin.dashboard', compact('orders', 'stats', 'from', 'to'));
    }

    public function edit(Order $order)
    {
        $order->load('payments');

        $payment = $order->payments()->latest()->first();

        return view('admin.edit-order', [
            'order' => $order,
            'payment' => $payment,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'payment_method' => ['required', 'in:COD,QRIS'],
            'status' => ['required', 'in:pending,completed,cancelled'],
        ]);

        $order->update([
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'payment_method' => $data['payment_method'],
            'status' => $data['status'],
        ]);

        // Sinkronkan payment terbaru dengan status order
        $payment = $order->payments()->latest()->first();
        if ($payment) {
            $paymentStatus = $data['status'] === 'completed' ? 'paid' : 'pending';
            $payment->update([
                'type' => $data['payment_method'],
                'status' => $paymentStatus,
            ]);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'Pesanan berhasil diperbarui.');
    }

    public function destroy(Order $order)
    {
        // Hapus relasi terkait secara eksplisit agar rapi
        $order->items()->delete();
        $order->payments()->delete();
        $order->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'Pesanan berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'order_ids' => ['required', 'array', 'min:1'],
            'order_ids.*' => ['required', 'integer'],
        ]);

        $orderIds = array_filter($request->input('order_ids', []), function ($id) {
            return is_numeric($id) && $id > 0;
        });

        if (empty($orderIds)) {
            return redirect()
                ->route('admin.dashboard')
                ->with('status', 'Tidak ada pesanan yang valid untuk dihapus.');
        }

        $deletedCount = 0;

        foreach ($orderIds as $orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->items()->delete();
                $order->payments()->delete();
                $order->delete();
                $deletedCount++;
            }
        }

        // Preserve filter tanggal jika ada
        $from = $request->query('from');
        $to = $request->query('to');
        $redirect = redirect()->route('admin.dashboard');
        if ($from) {
            $redirect->with('from', $from);
        }
        if ($to) {
            $redirect->with('to', $to);
        }

        return $redirect->with('status', "Berhasil menghapus {$deletedCount} pesanan.");
    }

    public function pembukuan()
    {
        // Get monthly sales data
        $monthlySales = \DB::table('payments')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(DISTINCT order_id) as order_count, SUM(amount) as total_amount')
            ->where('status', 'paid')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at) DESC, MONTH(created_at) DESC')
            ->get();

        // Get payment methods data
        $paymentMethods = \DB::table('payments')
            ->selectRaw('type as method, COUNT(*) as transaction_count, SUM(amount) as total_amount')
            ->where('status', 'paid')
            ->groupBy('type')
            ->orderByRaw('SUM(amount) DESC')
            ->get();

        // Calculate total for all payments
        $totalAllPayments = \DB::table('payments')
            ->where('status', 'paid')
            ->sum('amount');

        // Calculate percentages
        if ($totalAllPayments > 0) {
            $paymentMethods = $paymentMethods->map(function ($item) use ($totalAllPayments) {
                $item->percentage = round(($item->total_amount / $totalAllPayments) * 100, 1);
                return $item;
            });
        }

        return view('admin.pembukuan', compact('monthlySales', 'paymentMethods', 'totalAllPayments'));
    }
}


