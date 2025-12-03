<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Pesanan - Admin</title>
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
          <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900 transition">
              <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-900">Edit Pesanan</h1>
          </div>
        </div>
      </header>

      <main class="flex-1 px-8 py-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 max-w-2xl">
          <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Nama Pelanggan</label>
                <input type="text" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}"
                  class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                @error('customer_name')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">No. HP</label>
                <input type="text" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}"
                  class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                @error('customer_phone')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Metode Pembayaran</label>
                <select name="payment_method"
                  class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                  <option value="COD" @selected(old('payment_method', $order->payment_method) === 'COD')>Cash / COD</option>
                  <option value="QRIS" @selected(old('payment_method', $order->payment_method) === 'QRIS')>QRIS</option>
                </select>
                @error('payment_method')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Status Pesanan</label>
                <select name="status"
                  class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                  <option value="pending" @selected(old('status', $order->status) === 'pending')>Pending</option>
                  <option value="completed" @selected(old('status', $order->status) === 'completed')>Completed</option>
                  <option value="cancelled" @selected(old('status', $order->status) === 'cancelled')>Cancelled</option>
                </select>
                @error('status')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
              <p class="text-sm text-gray-700">
                <span class="font-semibold">Total:</span> 
                <span class="text-lg font-bold text-gray-900">Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}</span>
              </p>
              @if ($payment)
                <p class="text-sm text-gray-700 mt-2">
                  <span class="font-semibold">Status Pembayaran:</span>
                  <span class="font-bold {{ $payment->status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">
                    {{ strtoupper($payment->status) }}
                  </span>
                </p>
              @endif
            </div>

            <div class="flex items-center gap-4 pt-4">
              <button type="submit"
                class="bg-gray-900 text-white text-sm font-semibold px-6 py-3 rounded-lg hover:bg-black transition flex items-center gap-2">
                <i class="fas fa-check w-4"></i>
                Simpan Perubahan
              </button>
              <a href="{{ route('admin.dashboard') }}"
                class="text-sm font-semibold text-gray-600 px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                Batal
              </a>
            </div>
          </form>
        </div>
      </main>
    </div>
  </div>
</body>

</html>


