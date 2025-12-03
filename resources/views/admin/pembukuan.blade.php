<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembukuan - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex">
        
        <!-- Left Sidebar -->
        <aside class="w-64 bg-gray-900 text-white fixed h-screen overflow-y-auto flex flex-col shadow-lg">
            <div class="p-6 border-b border-gray-800">
                <div class="text-2xl font-bold">LOGO</div>
            </div>

            <nav class="flex-1 px-3 py-6 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 transition">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.pembukuan') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-white text-gray-900 font-medium transition">
                    <i class="fas fa-book w-5"></i>
                    <span>Pembukuan</span>
                </a>
            </nav>

            <div class="p-3 border-t border-gray-800">
                <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 w-full px-4 py-3 rounded-lg bg-white text-gray-900 font-semibold hover:bg-gray-100 transition">
                    <i class="fas fa-sign-out-alt w-4"></i>
                    <span>LOGOUT</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="ml-64 w-full flex flex-col">
            
            <!-- Top Bar -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-10">
                <div class="px-8 py-4 flex items-center justify-start">
                    <div class="flex-1 max-w-xs">
                        <div class="relative">
                            <input type="search" placeholder="Search..." class="w-full bg-gray-100 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-8 py-8 overflow-auto">

                @if (session('status'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-xl">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Page Title -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Pembukuan</h1>
                    <p class="text-gray-600 mt-2">Laporan penjualan dan metode pembayaran</p>
                </div>

                <!-- Monthly Sales Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900">Penjualan Per Bulan</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Bulan</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Tahun</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide">Jumlah Pesanan</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Total Penjualan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($monthlySales as $sale)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::createFromDate($sale->year, $sale->month, 1)->format('F') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $sale->year }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm text-gray-700">
                                            {{ $sale->order_count }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                            Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                            Tidak ada data penjualan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payment Methods Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900">Bukti Pembayaran / Metode Pembayaran</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Metode Pembayaran</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide">Jumlah Transaksi</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Total Nilai</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Persentase</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($paymentMethods as $payment)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            <div class="flex items-center gap-2">
                                                @if ($payment->method === 'QRIS')
                                                    <i class="fas fa-qrcode text-lg text-gray-600"></i>
                                                @else
                                                    <i class="fas fa-money-bill-1 text-lg text-gray-600"></i>
                                                @endif
                                                {{ $payment->method }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm text-gray-700">
                                            {{ $payment->transaction_count }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                            Rp {{ number_format($payment->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                            <div class="flex items-center justify-end gap-2">
                                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-gray-900 h-2 rounded-full" style="width: {{ $payment->percentage }}%"></div>
                                                </div>
                                                {{ $payment->percentage }}%
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                            Tidak ada data pembayaran
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($totalAllPayments > 0)
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-700">Total Seluruh Pembayaran</p>
                                <p class="text-lg font-bold text-gray-900">Rp {{ number_format($totalAllPayments, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

            </main>
        </div>
    </div>
</body>

</html>
