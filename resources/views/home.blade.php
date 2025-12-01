<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nasi Goreng Pak BI</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite('resources/css/app.css')
</head>

<body class="bg-white font-sans">

  <!-- Navbar -->
  <header class="absolute top-0 left-0 right-0 z-20">
    <nav class="flex items-center justify-between px-6 lg:px-10 py-5">
      <div class="flex items-center space-x-6">
        <a href="{{ route('home') }}" class="nav-link text-gray-800 hover:text-red-500 aria-[current=page]:text-red-500"
          aria-current="page">Home</a>
        <a href="{{ route('menu') }}" class="nav-link text-gray-800 hover:text-red-500">Menu</a>
      </div>

      <div class="hidden lg:block">
        <button id="cartBtnHome" class="text-white" aria-label="Cart">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
            </path>
          </svg>
        </button>
      </div>
    </nav>
  </header>

  <div id="mobileMenuHome"
    class="fixed inset-0 z-30 opacity-0 pointer-events-none transition-opacity duration-200 hidden">
    <div id="mobileOverlayHome" class="absolute inset-0 bg-black/50"></div>
    <div
      class="absolute left-0 top-0 h-full w-72 bg-white shadow-lg p-6 space-y-6 transform -translate-x-full transition-transform duration-200">
      <div class="flex items-center justify-between">
        <span class="text-lg font-bold">Menu</span>
        <button id="closeMobileHome" class="text-gray-700" aria-label="Close">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <a href="{{ route('home') }}" class="block text-gray-800 font-semibold">Home</a>
      <a href="{{ route('menu') }}" class="block text-gray-800 font-semibold">Menu</a>
    </div>
  </div>

  <div id="cartModalHome"
    class="fixed inset-0 z-30 opacity-0 pointer-events-none transition-opacity duration-200 hidden">
    <div id="cartOverlayHome" class="absolute inset-0 bg-black/50"></div>
    <div
      class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-lg p-6 transform translate-x-full transition-transform duration-200">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold">Keranjang</h3>
        <button id="closeCartHome" class="text-gray-700" aria-label="Close">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <div id="cartItemsHome" class="space-y-4 text-sm text-gray-700">Keranjang kosong</div>
      <div class="mt-6 flex items-center justify-between">
        <span class="font-semibold">Total</span>
        <span id="cartTotalHome" class="font-bold">Rp 0</span>
      </div>
      <div class="mt-6 space-y-3">
        <input id="custNameHome" type="text" class="w-full border rounded px-3 py-2" placeholder="Nama">
        <div id="errNameHome" class="text-red-600 text-xs"></div>
        <input id="custPhoneHome" type="text" class="w-full border rounded px-3 py-2" placeholder="Nomor HP">
        <div id="errPhoneHome" class="text-red-600 text-xs"></div>
        <select id="payMethodHome" class="w-full border rounded px-3 py-2">
          <option value="">Pilih metode pembayaran</option>
          <option value="Cash">Cash</option>
          <option value="QRIS">QRIS</option>
        </select>
        <div id="errMethodHome" class="text-red-600 text-xs"></div>
        <div id="checkoutErrorHome" class="text-red-600 text-sm"></div>
        <button id="doCheckoutHome" class="w-full bg-black text-white py-3 rounded">Checkout</button>
      </div>
    </div>
  </div>

  <!-- Hero Section -->
  <section
    class="relative w-screen h-screen flex items-center justify-start overflow-hidden bg-no-repeat bg-cover bg-center"
    style="background-image: url('/images/Home-1.png');">

    <!-- Konten Teks dan Tombol -->
    <div class="relative z-10 ml-20 pt-40 max-w-xs md:max-w-sm self-start">
      <h2 class="text-5xl md:text-6xl font-extrabold text-gray-900 leading-tight">Nasi Goreng Pak BI</h2>
      <p class="text-gray-700 mt-4 text-sm md:text-base">
        Nasi goreng murah, enak, dan bikin kenyang tentunya, cocok untuk mahasiswa yang porsi makannya besar dan minim
        budget.
      </p>
      <a href="{{ route('menu') }}"
        class="mt-8 block text-center bg-black text-white px-10 py-4 rounded-full font-semibold hover:bg-gray-800 transition text-lg w-full">ORDER
        NOW</a>
    </div>
  </section>
  <script>
    function parsePrice(label) {
      const n = label.replace(/[^0-9]/g, '');
      return n ? parseInt(n, 10) : 0;
    }
    function formatPrice(n) {
      return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    const mobileMenu = document.getElementById('mobileMenuHome');
    const cartModal = document.getElementById('cartModalHome');
    const cartBtn = document.getElementById('cartBtnHome');
    const closeMobile = document.getElementById('closeMobileHome');
    const closeCart = document.getElementById('closeCartHome');
    const mobileOverlay = document.getElementById('mobileOverlayHome');
    const cartOverlay = document.getElementById('cartOverlayHome');
    const itemsEl = document.getElementById('cartItemsHome');
    const totalEl = document.getElementById('cartTotalHome');
    const doCheckoutHome = document.getElementById('doCheckoutHome');
    const custNameHome = document.getElementById('custNameHome');
    const custPhoneHome = document.getElementById('custPhoneHome');
    const payMethodHome = document.getElementById('payMethodHome');
    const errNameHome = document.getElementById('errNameHome');
    const errPhoneHome = document.getElementById('errPhoneHome');
    const errMethodHome = document.getElementById('errMethodHome');
    const checkoutErrorHome = document.getElementById('checkoutErrorHome');
    function loadCart() {
      try { return JSON.parse(localStorage.getItem('cart') || '[]'); } catch (e) { return []; }
    }
    function saveCart(c) { localStorage.setItem('cart', JSON.stringify(c)); }
    function renderCart() {
      const cart = loadCart();
      if (cart.length === 0) { itemsEl.textContent = 'Keranjang kosong'; totalEl.textContent = 'Rp 0'; return; }
      itemsEl.innerHTML = '';
      let total = 0;
      cart.forEach((it, idx) => {
        if (!it.spice) { it.spice = 'Normal'; }
        total += (it.priceNumber || 0) * (it.qty || 1);
        const row = document.createElement('div');
        row.className = 'flex items-center justify-between';
        row.innerHTML = `
          <div class="flex items-center space-x-3">
            <img src="${it.image || ''}" alt="${it.name || ''}" class="w-12 h-12 rounded object-cover">
            <div>
              <div class="font-semibold">${it.name || ''}</div>
              <div class="text-gray-500 text-xs">${it.priceLabel || ''}</div>
            </div>
          </div>
          <div class="flex items-center space-x-2">
            <button data-dec="${idx}" class="px-2 py-1 border rounded">-</button>
            <span>${it.qty || 1}</span>
            <button data-inc="${idx}" class="px-2 py-1 border rounded">+</button>
            <select data-spice="${idx}" class="px-2 py-1 border rounded text-sm">
              <option ${it.spice === 'Tidak Pedas' ? 'selected' : ''}>Tidak Pedas</option>
              <option ${it.spice === 'Normal' ? 'selected' : ''}>Normal</option>
              <option ${it.spice === 'Sedang' ? 'selected' : ''}>Sedang</option>
              <option ${it.spice === 'Pedas' ? 'selected' : ''}>Pedas</option>
            </select>
            <button data-del="${idx}" class="ml-3 text-red-600">Hapus</button>
          </div>`;
        itemsEl.appendChild(row);
      });
      totalEl.textContent = formatPrice(total);
      itemsEl.querySelectorAll('[data-inc]').forEach(btn => {
        btn.addEventListener('click', () => { const idx = parseInt(btn.getAttribute('data-inc'), 10); const c = loadCart(); c[idx].qty = (c[idx].qty || 1) + 1; saveCart(c); renderCart(); });
      });
      itemsEl.querySelectorAll('[data-dec]').forEach(btn => {
        btn.addEventListener('click', () => { const idx = parseInt(btn.getAttribute('data-dec'), 10); const c = loadCart(); c[idx].qty = Math.max(1, (c[idx].qty || 1) - 1); saveCart(c); renderCart(); });
      });
      itemsEl.querySelectorAll('[data-del]').forEach(btn => {
        btn.addEventListener('click', () => { const idx = parseInt(btn.getAttribute('data-del'), 10); const c = loadCart(); c.splice(idx, 1); saveCart(c); renderCart(); });
      });
      itemsEl.querySelectorAll('[data-spice]').forEach(sel => {
        sel.addEventListener('change', () => { const idx = parseInt(sel.getAttribute('data-spice'), 10); const c = loadCart(); c[idx].spice = sel.value; saveCart(c); });
      });
    }
    const DURATION = 300;
    function closeMenuHome() {
      mobileMenu.classList.add('opacity-0', 'pointer-events-none');
      const panel = mobileMenu.querySelector('.transform');
      panel && panel.classList.add('-translate-x-full');
      setTimeout(() => mobileMenu.classList.add('hidden'), DURATION);
    }
    function openCartHome() {
      cartModal.classList.remove('hidden');
      requestAnimationFrame(() => {
        cartModal.classList.remove('pointer-events-none', 'opacity-0');
        const panel = cartModal.querySelector('.transform');
        panel && panel.classList.remove('translate-x-full');
      });
    }
    function closeCartHome() {
      cartModal.classList.add('opacity-0', 'pointer-events-none');
      const panel = cartModal.querySelector('.transform');
      panel && panel.classList.add('translate-x-full');
      setTimeout(() => cartModal.classList.add('hidden'), DURATION);
    }
    closeMobile && closeMobile.addEventListener('click', closeMenuHome);
    mobileOverlay && mobileOverlay.addEventListener('click', closeMenuHome);
    cartBtn && cartBtn.addEventListener('click', () => { renderCart(); openCartHome(); });
    closeCart && closeCart.addEventListener('click', closeCartHome);
    cartOverlay && cartOverlay.addEventListener('click', closeCartHome);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeMenuHome(); closeCartHome(); } });
    doCheckoutHome && doCheckoutHome.addEventListener('click', async () => {
      const cart = loadCart();
      if (cart.length === 0) return;
      errNameHome.textContent = '';
      errPhoneHome.textContent = '';
      errMethodHome.textContent = '';
      checkoutErrorHome.textContent = '';
      let hasError = false;
      if (!custNameHome.value || !custNameHome.value.trim()) { errNameHome.textContent = 'Masukkan username terlebih dahulu'; hasError = true; }
      if (!custPhoneHome.value || !custPhoneHome.value.trim()) { errPhoneHome.textContent = 'Masukkan nomor HP terlebih dahulu'; hasError = true; }
      if (!payMethodHome.value) { errMethodHome.textContent = 'Pilih metode pembayaran terlebih dahulu'; hasError = true; }
      if (hasError) return;
      const payload = {
        customer_name: custNameHome.value || null,
        customer_phone: custPhoneHome.value || null,
        payment_method: payMethodHome.value || 'COD',
        items: cart.map(c => ({ name: (c.name || '') + ' - ' + (c.spice || 'Normal'), priceNumber: c.priceNumber || parsePrice(c.priceLabel || '0'), qty: c.qty || 1 }))
      };
      const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      try {
        const res = await fetch('{{ route('checkout') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(payload) });
        if (!res.ok) { throw new Error('HTTP ' + res.status); }
        const json = await res.json();
        if (json && json.ok) { window.location.href = '{{ url('/checkout') }}/' + json.order_id; }
      } catch (err) { checkoutErrorHome.textContent = 'Gagal membuat order (' + (err.message || 'error') + ')'; }
    });

  </script>

</body>

</html>
<style>
  button,
  a {
    cursor: pointer;
  }

  #mobileMenuHome {
    transition: opacity 300ms cubic-bezier(0.22, 1, 0.36, 1);
    will-change: opacity;
  }

  #mobileMenuHome .transform {
    transition: transform 300ms cubic-bezier(0.22, 1, 0.36, 1);
    will-change: transform;
  }

  #cartModalHome {
    transition: opacity 300ms cubic-bezier(0.22, 1, 0.36, 1);
    will-change: opacity;
  }

  #cartModalHome .transform {
    transition: transform 300ms cubic-bezier(0.22, 1, 0.36, 1);
    will-change: transform;
  }

  /* halaman home: tidak ada animasi khusus untuk perpindahan halaman */
</style>