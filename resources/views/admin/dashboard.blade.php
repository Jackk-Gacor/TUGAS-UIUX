<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Pembukuan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex flex-col">
        <!-- Top bar -->
        <header class="bg-white shadow-sm">
            <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-900">Dashboard Admin</h1>
                <nav class="flex items-center space-x-4 text-sm">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-red-500">Lihat Halaman Utama</a>
                    <span class="px-3 py-1 rounded-full text-xs bg-gray-900 text-white">Pembukuan</span>
                </nav>
            </div>
        </header>

        <main class="flex-1 max-w-6xl mx-auto px-6 py-8">
            @if (session('status'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Filter tanggal -->
            <section class="mb-6">
                <form method="GET" action="{{ route('admin.dashboard') }}"
                    class="bg-white rounded-lg shadow-sm p-4 flex flex-wrap items-end gap-4">
                    <div>
                        <label for="from" class="block text-xs font-semibold text-gray-600 mb-1">Dari tanggal</label>
                        <input type="date" id="from" name="from" value="{{ $from }}"
                            class="border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-900">
                    </div>
                    <div>
                        <label for="to" class="block text-xs font-semibold text-gray-600 mb-1">Sampai tanggal</label>
                        <input type="date" id="to" name="to" value="{{ $to }}"
                            class="border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-900">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="bg-gray-900 text-white text-sm px-4 py-2 rounded-md hover:bg-black transition">Terapkan</button>
                        <a href="{{ route('admin.dashboard') }}"
                            class="text-xs text-gray-500 hover:text-red-500">Reset</a>
                    </div>
                </form>
            </section>

            <!-- Ringkasan keuangan -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500 mb-1">Pemasukan Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900">
                        Rp {{ number_format($stats['today_revenue'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500 mb-1">Pemasukan Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900">
                        Rp {{ number_format($stats['month_revenue'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500 mb-1">Total Pemasukan (filter)</p>
                    <p class="text-2xl font-bold text-green-600">
                        Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs text-gray-500 mb-1">Total Pesanan</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $stats['total_orders'] ?? 0 }} pesanan
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Selesai: <span class="font-semibold text-green-600">{{ $stats['paid_orders'] ?? 0 }}</span> |
                        Pending: <span class="font-semibold text-yellow-600">{{ $stats['pending_orders'] ?? 0 }}</span>
                    </p>
                </div>
            </section>

            <!-- Tabel pesanan -->
            <section class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-800">Daftar Pesanan</h2>
                    <p class="text-xs text-gray-500">Data diurutkan dari yang terbaru</p>
                </div>

                <form id="bulkDeleteForm" action="{{ route('admin.orders.bulk-destroy') }}" method="POST">
                    @csrf
                    <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                        <div class="flex items-center gap-3">
                            <button type="button" id="selectAllBtn"
                                class="text-xs text-gray-600 hover:text-red-500 px-2 py-1 rounded border border-gray-300 hover:bg-gray-50">
                                Pilih Semua
                            </button>
                            <button type="button" id="deselectAllBtn"
                                class="text-xs text-gray-600 hover:text-gray-800 px-2 py-1 rounded border border-gray-300 hover:bg-gray-50">
                                Batal Pilih
                            </button>
                            <span id="selectedCount" class="text-xs text-gray-500">0 dipilih</span>
                        </div>
                        <button type="submit" id="bulkDeleteBtn" disabled
                            class="text-xs text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                            Hapus yang Dipilih
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 w-12">
                                        <input type="checkbox" id="selectAllCheckbox"
                                            class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Tanggal</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Nama</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">No. HP</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Metode</th>
                                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Total</th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Status</th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr class="border-t border-gray-100 hover:bg-gray-50">
                                        <td class="px-4 py-2 align-top text-center">
                                            <input type="checkbox" name="order_ids[]" value="{{ $order->id }}"
                                                class="order-checkbox rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        </td>
                                        <td class="px-4 py-2 align-top">
                                            <div class="text-xs text-gray-900">
                                                {{ $order->created_at?->format('d M Y') ?? '-' }}
                                            </div>
                                            <div class="text-[11px] text-gray-500">
                                                {{ $order->created_at?->format('H:i') ?? '' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 align-top">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $order->customer_name ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 align-top text-xs text-gray-700">
                                            {{ $order->customer_phone ?? '-' }}
                                        </td>
                                        <td class="px-4 py-2 align-top text-xs text-gray-700">
                                            {{ $order->payment_method ?? '-' }}
                                        </td>
                                        <td class="px-4 py-2 align-top text-right text-sm font-semibold text-gray-900">
                                            Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-2 align-top text-center">
                                            @php
                                                $badgeColor = $order->status === 'completed'
                                                    ? 'bg-green-100 text-green-700'
                                                    : ($order->status === 'pending'
                                                        ? 'bg-yellow-100 text-yellow-700'
                                                        : 'bg-gray-100 text-gray-700');
                                            @endphp
                                            <span
                                                class="inline-flex items-center justify-center px-2 py-1 rounded-full text-[11px] font-semibold {{ $badgeColor }}">
                                                {{ ucfirst($order->status ?? 'unknown') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 align-top text-center text-xs">
                                            <div class="inline-flex items-center gap-2">
                                                <a href="{{ route('admin.orders.edit', $order) }}"
                                                    class="px-2 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus pesanan ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-2 py-1 rounded border border-red-200 text-red-600 hover:bg-red-50">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">
                                            Belum ada pesanan untuk rentang tanggal ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($orders->hasPages())
                        <div
                            class="px-4 py-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-600">
                            <div>
                                Menampilkan
                                <span class="font-semibold">{{ $orders->firstItem() }}</span>
                                -
                                <span class="font-semibold">{{ $orders->lastItem() }}</span>
                                dari
                                <span class="font-semibold">{{ $orders->total() }}</span>
                                pesanan
                            </div>
                            <div class="flex items-center space-x-2">
                                @if ($orders->onFirstPage())
                                    <span
                                        class="px-2 py-1 rounded border border-gray-200 text-gray-400 cursor-not-allowed">Prev</span>
                                @else
                                    <a href="{{ $orders->previousPageUrl() }}"
                                        class="px-2 py-1 rounded border border-gray-200 hover:bg-gray-50">Prev</a>
                                @endif

                                @if ($orders->hasMorePages())
                                    <a href="{{ $orders->nextPageUrl() }}"
                                        class="px-2 py-1 rounded border border-gray-200 hover:bg-gray-50">Next</a>
                                @else
                                    <span
                                        class="px-2 py-1 rounded border border-gray-200 text-gray-400 cursor-not-allowed">Next</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </form>
            </section>
        </main>
    </div>

    <script>
        // Handle checkbox selection untuk bulk delete
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const selectAllBtn = document.getElementById('selectAllBtn');
        const deselectAllBtn = document.getElementById('deselectAllBtn');
        const orderCheckboxes = document.querySelectorAll('.order-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const selectedCountSpan = document.getElementById('selectedCount');
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');

        function updateSelectedCount() {
            const checked = document.querySelectorAll('.order-checkbox:checked').length;
            selectedCountSpan.textContent = `${checked} dipilih`;
            bulkDeleteBtn.disabled = checked === 0;
            selectAllCheckbox.checked = checked > 0 && checked === orderCheckboxes.length;
        }

        // Select all checkbox di header
        selectAllCheckbox.addEventListener('change', function () {
            orderCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateSelectedCount();
        });

        // Tombol "Pilih Semua"
        selectAllBtn.addEventListener('click', function () {
            orderCheckboxes.forEach(cb => {
                cb.checked = true;
            });
            selectAllCheckbox.checked = true;
            updateSelectedCount();
        });

        // Tombol "Batal Pilih"
        deselectAllBtn.addEventListener('click', function () {
            orderCheckboxes.forEach(cb => {
                cb.checked = false;
            });
            selectAllCheckbox.checked = false;
            updateSelectedCount();
        });

        // Update count saat checkbox individual diubah
        orderCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectedCount);
        });

        // Handle form submit dengan validasi
        bulkDeleteForm.addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.order-checkbox:checked').length;
            if (checked === 0) {
                e.preventDefault();
                alert('Pilih minimal 1 pesanan untuk dihapus.');
                return false;
            }
            if (!confirm(`Yakin ingin menghapus ${checked} pesanan yang dipilih?`)) {
                e.preventDefault();
                return false;
            }
        });

        // Inisialisasi
        updateSelectedCount();
    </script>
</body>

</html>