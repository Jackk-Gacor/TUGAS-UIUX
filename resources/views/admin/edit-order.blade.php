<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Pesanan - Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">
  <div class="min-h-screen flex flex-col">
    <header class="bg-white shadow-sm">
      <div class="max-w-3xl mx-auto px-6 py-4 flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-900">Edit Pesanan</h1>
        <a href="{{ route('admin.dashboard') }}"
          class="text-sm text-gray-600 hover:text-red-500 underline-offset-2">Kembali ke Dashboard</a>
      </div>
    </header>

    <main class="flex-1 max-w-3xl mx-auto px-6 py-8">
      <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="space-y-5">
          @csrf
          @method('PUT')

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Pelanggan</label>
              <input type="text" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}"
                class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-900">
              @error('customer_name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">No. HP</label>
              <input type="text" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}"
                class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-900">
              @error('customer_phone')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Metode Pembayaran</label>
              <select name="payment_method"
                class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-900">
                <option value="COD" @selected(old('payment_method', $order->payment_method) === 'COD')>Cash / COD
                </option>
                <option value="QRIS" @selected(old('payment_method', $order->payment_method) === 'QRIS')>QRIS</option>
              </select>
              @error('payment_method')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Status Pesanan</label>
              <select name="status"
                class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-900">
                <option value="pending" @selected(old('status', $order->status) === 'pending')>Pending</option>
                <option value="completed" @selected(old('status', $order->status) === 'completed')>Completed</option>
                <option value="cancelled" @selected(old('status', $order->status) === 'cancelled')>Cancelled</option>
              </select>
              @error('status')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <div>
            <p class="text-xs text-gray-500">
              Total: <span class="font-semibold text-gray-900">Rp
                {{ number_format($order->total_price ?? 0, 0, ',', '.') }}</span>
              @if ($payment)
                • Status pembayaran saat ini:
                <span
                  class="font-semibold {{ $payment->status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">
                  {{ strtoupper($payment->status) }}
                </span>
              @endif
            </p>
          </div>

          <div class="pt-3 flex items-center justify-between">
            <button type="submit"
              class="bg-gray-900 text-white text-sm px-4 py-2 rounded-md hover:bg-black transition">Simpan
              Perubahan</button>
          </div>
        </form>
      </div>
    </main>
  </div>
</body>

</html>


