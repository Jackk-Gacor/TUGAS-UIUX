<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Pembukuan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex">
        
        <!-- Left Sidebar -->
        <aside class="w-64 bg-gray-900 text-white fixed h-screen overflow-y-auto flex flex-col shadow-lg">
            <div class="p-6 border-b border-gray-800">
                <div class="text-2xl font-bold">PAK BIE</div>
            </div>

            <nav class="flex-1 px-3 py-6 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-white text-gray-900 font-medium transition">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.pembukuan') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 transition">
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

                <!-- Date Filter Bar -->
                <div class="mb-6 bg-white rounded-2xl shadow-sm p-4 flex flex-wrap items-end gap-4">
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-end gap-4 w-full">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Dari Tanggal</label>
                            <input type="date" id="from" name="from" value="{{ $from }}"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Sampai Tanggal</label>
                            <input type="date" id="to" name="to" value="{{ $to }}"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="submit"
                                class="bg-gray-900 text-white text-sm font-medium px-6 py-2 rounded-lg hover:bg-black transition">
                                Terapkan
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Reset</a>
                        </div>
                    </form>
                </div>

                <!-- Analytics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-md transition border border-gray-100">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Pemasukan Hari Ini</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">
                                    Rp {{ number_format($stats['today_revenue'] ?? 0) }}
                                </p>
                            </div>
                            <div class="bg-gray-100 p-3 rounded-lg">
                                <i class="fas fa-arrow-trend-up text-gray-700"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-md transition border border-gray-100">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Pemasukan Bulan Ini</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">
                                    Rp {{ number_format($stats['month_revenue'] ?? 0) }}
                                </p>
                            </div>
                            <div class="bg-gray-100 p-3 rounded-lg">
                                <i class="fas fa-calendar text-gray-700"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-md transition border border-gray-100">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Total Pemasukan (Filter)</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">
                                    Rp {{ number_format($stats['total_revenue'] ?? 0) }}
                                </p>
                            </div>
                            <div class="bg-gray-100 p-3 rounded-lg">
                                <i class="fas fa-chart-pie text-gray-700"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-md transition border border-gray-100">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Total Pesanan</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">
                                    {{ $stats['total_orders'] ?? 0 }}
                                </p>
                                <p class="text-xs text-gray-500 mt-2">
                                    <span class="font-semibold text-gray-700">{{ $stats['paid_orders'] }}</span> Selesai • 
                                    <span class="font-semibold text-gray-700">{{ $stats['pending_orders'] }}</span> Pending
                                </p>
                            </div>
                            <div class="bg-gray-100 p-3 rounded-lg">
                                <i class="fas fa-shopping-cart text-gray-700"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Table Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900">Daftar Pesanan</h2>
                        <p class="text-xs text-gray-500">Urut terbaru</p>
                    </div>

                    <!-- Bulk Action Bar -->
                    <div class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <button type="button" id="selectAllBtn"
                                class="text-xs font-medium text-gray-600 hover:text-gray-900 transition">Pilih Semua</button>

                            <button type="button" id="deselectAllBtn"
                                class="text-xs font-medium text-gray-600 hover:text-gray-900 transition">Batal Pilih</button>

                            <span id="selectedCount" class="text-xs text-gray-500">0 dipilih</span>
                        </div>

                        <button type="button" disabled id="bulkDeleteBtn"
                            class="text-xs bg-red-600 text-white font-medium px-4 py-2 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-red-700 transition">
                            Hapus Dipilih
                        </button>
                    </div>

                    <!-- Bulk Delete Form (hidden) -->
                    <form id="bulkDeleteForm" action="{{ route('admin.orders.bulk-destroy') }}" method="POST" style="display:none;">
                        @csrf
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="w-12 px-6 py-3"><input type="checkbox" id="selectAllCheckbox" class="rounded"></th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">No. HP</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Metode</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Total</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">
                                @forelse ($orders as $order)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-center">
                                            <input type="checkbox" form="bulkDeleteForm" name="order_ids[]" value="{{ $order->id }}"
                                                class="order-checkbox rounded">
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $order->created_at?->format('d M Y') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $order->created_at?->format('H:i') }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $order->customer_name }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $order->customer_phone }}</td>

                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $order->payment_method }}</td>

                                        <td class="px-6 py-4 text-right font-semibold text-gray-900">
                                            Rp {{ number_format($order->total_price) }}
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $color = $order->status === 'completed'
                                                    ? 'bg-green-100 text-green-800'
                                                    : ($order->status === 'pending'
                                                        ? 'bg-yellow-100 text-yellow-800'
                                                        : 'bg-gray-100 text-gray-800');
                                            @endphp

                                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $color }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('admin.orders.edit', $order) }}"
                                                    class="px-3 py-1 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                                    Edit
                                                </a>

                                                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
                                                    style="display:inline;"
                                                    onsubmit="return confirm('Yakin ingin menghapus pesanan ini?');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="px-3 py-1 text-sm text-red-600 border border-red-300 rounded-lg hover:bg-red-50 transition">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-8 text-center">
                                            <p class="text-sm text-gray-500">Tidak ada data untuk rentang tanggal ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($orders->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-xs text-gray-600 flex items-center justify-between">
                            <div>
                                Menampilkan <b>{{ $orders->firstItem() }}</b> -
                                <b>{{ $orders->lastItem() }}</b>
                                dari <b>{{ $orders->total() }}</b>
                            </div>

                            <div class="flex gap-2">
                                {!! $orders->onEachSide(0)->links('pagination::simple-tailwind') !!}
                            </div>
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <!-- Script tetap sama (no logic changes) -->
    <script>
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const selectAllBtn = document.getElementById('selectAllBtn');
        const deselectAllBtn = document.getElementById('deselectAllBtn');
        const orderCheckboxes = document.querySelectorAll('.order-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const selectedCountSpan = document.getElementById('selectedCount');
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');

        function updateSelectedCount() {
            const c = document.querySelectorAll('.order-checkbox:checked').length;
            selectedCountSpan.textContent = `${c} dipilih`;
            bulkDeleteBtn.disabled = c === 0;
            selectAllCheckbox.checked = c > 0 && c === orderCheckboxes.length;
        }

        selectAllCheckbox.addEventListener('change', () => {
            orderCheckboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
            updateSelectedCount();
        });

        selectAllBtn.addEventListener('click', () => {
            orderCheckboxes.forEach(cb => cb.checked = true);
            selectAllCheckbox.checked = true;
            updateSelectedCount();
        });

        deselectAllBtn.addEventListener('click', () => {
            orderCheckboxes.forEach(cb => cb.checked = false);
            selectAllCheckbox.checked = false;
            updateSelectedCount();
        });

        orderCheckboxes.forEach(cb => cb.addEventListener('change', updateSelectedCount));

        bulkDeleteBtn.addEventListener('click', (e) => {
            const c = document.querySelectorAll('.order-checkbox:checked').length;
            if (c === 0) {
                alert('Pilih minimal 1 pesanan untuk dihapus.');
            } else if (confirm(`Yakin menghapus ${c} pesanan?`)) {
                bulkDeleteForm.submit();
            }
        });

        updateSelectedCount();
    </script>

</body>
</html>