<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite('resources/css/app.css')
</head>

<body class="bg-white font-sans">
  <header class="px-6 lg:px-10 py-5">
    <div class="flex items-center space-x-6">
      <a href="{{ route('home') }}" class="text-gray-800 hover:text-red-500">Home</a>
      <a href="{{ route('menu') }}" class="text-gray-800 hover:text-red-500">Menu</a>
      <span class="text-gray-500">Checkout</span>
    </div>
  </header>

  <main class="max-w-4xl mx-auto px-6 lg:px-10 py-8">
    <h1 class="text-2xl font-bold mb-6">Ringkasan Pesanan</h1>
    <div class="bg-gray-50 border rounded p-6">
      <div class="space-y-4">
        @foreach($order->items as $it)
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <img src="{{ $it->product->image ? asset($it->product->image) : asset('images/nasi-goreng.png') }}"
                alt="{{ $it->product->name }}" class="w-12 h-12 rounded object-cover">
              <div>
                <div class="font-semibold">{{ $it->product->name }}</div>
                <div class="text-gray-500 text-sm">Qty {{ $it->quantity }}</div>
              </div>
            </div>
            <div class="font-semibold">Rp {{ number_format($it->subtotal, 0, ',', '.') }}</div>
          </div>
        @endforeach
      </div>
      <div class="mt-6 flex items-center justify-between">
        <span class="font-semibold">Total</span>
        <span class="font-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
      </div>
    </div>

    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-gray-50 border rounded p-6">
        <div class="font-semibold mb-2">Data Pelanggan</div>
        <div class="text-sm text-gray-700">Nama: {{ $order->customer_name ?? '-' }}</div>
        <div class="text-sm text-gray-700">HP: {{ $order->customer_phone ?? '-' }}</div>
        <div class="text-sm text-gray-700">Catatan: {{ $order->note ?? '-' }}</div>
      </div>
      <div class="bg-gray-50 border rounded p-6">
        <div class="font-semibold mb-2">Pembayaran</div>
        <div class="text-sm text-gray-700">Metode: {{ $order->payment_method }}</div>
        <div class="text-sm text-gray-700 mb-4">Status: {{ optional($order->payments->first())->status ?? 'pending' }}
        </div>
        @if($order->payment_method === 'QRIS')
          <div class="flex items-center justify-center mb-4">
            <div class="w-40 h-40 bg-white border rounded grid grid-cols-5 gap-1 p-2">
              @for($i = 0; $i < 25; $i++)
                <div class="w-full h-6 {{ $i % 2 ? 'bg-black' : 'bg-gray-200' }}"></div>
              @endfor
            </div>
          </div>
          <div class="text-sm text-gray-600 mb-4 text-center">Scan kode QR untuk membayar</div>
        @elseif($order->payment_method === 'Transfer')
          <div class="text-sm text-gray-700 mb-4">Rekening: BCA 123456789 a/n UMKM Pak BI</div>
        @else
          <div class="text-sm text-gray-700 mb-4">Bayar di tempat (COD)</div>
        @endif
        <button id="payBtn" class="w-full bg-black text-white py-3 rounded transition-colors hover:bg-gray-800">Bayar
          Sekarang</button>
      </div>
    </div>
  </main>

  <script>
    const btn = document.getElementById('payBtn');
    btn && btn.addEventListener('click', async () => {
      const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      const res = await fetch('{{ route('checkout.pay', $order) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf } });
      const json = await res.json();
      if (json && json.ok) { location.reload(); }
    });
  </script>
</body>

</html>