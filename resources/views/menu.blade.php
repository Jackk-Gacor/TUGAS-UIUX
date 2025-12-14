<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Foodbar - Menu</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    /* Pointer cursor untuk elemen interaktif */
    button,
    a,
    .slider-dot,
    .small-menu-card {
      cursor: pointer;
    }

    /* Transisi halus untuk panel menu & keranjang */
    #mobileMenu {
      transition: opacity 420ms cubic-bezier(0.16, 1, 0.3, 1);
      will-change: opacity;
    }

    #mobileMenu .transform {
      transition: transform 420ms cubic-bezier(0.16, 1, 0.3, 1);
      will-change: transform;
    }

    #cartModal {
      transition: opacity 420ms cubic-bezier(0.16, 1, 0.3, 1);
      will-change: opacity;
    }

    #cartModal .transform {
      transition: transform 420ms cubic-bezier(0.16, 1, 0.3, 1);
      will-change: transform;
    }

    #mobileOverlay,
    #cartOverlay {
      transition: opacity 420ms cubic-bezier(0.16, 1, 0.3, 1);
    }

    #pageFade {
      transition: opacity 600ms cubic-bezier(0.2, 0.8, 0.2, 1);
      backdrop-filter: blur(8px);
      background: radial-gradient(ellipse at center, rgba(255, 255, 255, .15) 0%, rgba(0, 0, 0, .6) 100%);
      will-change: opacity;
    }

    #pageRoot {
      transition: transform 600ms cubic-bezier(0.2, 0.8, 0.2, 1), filter 600ms cubic-bezier(0.2, 0.8, 0.2, 1), opacity 600ms cubic-bezier(0.2, 0.8, 0.2, 1);
    }

    #pageRoot.is-leave-left {
      transform: translateX(-14px) scale(.985);
      filter: blur(4px) saturate(.9);
      opacity: .88;
    }

    #pageRoot.is-leave-right {
      transform: translateX(14px) scale(.985);
      filter: blur(4px) saturate(.9);
      opacity: .88;
    }

    #pageFade {
      transition: opacity 420ms cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Transisi untuk efek fade pada gambar utama */
    .fade-image {
      transition: opacity 250ms ease-in-out;
      opacity: 1;
    }

    .fade-image.is-fading {
      opacity: 0;
    }

    /* Navbar Active Link Styling */
    .nav-link.active {
      color: #EF4444;
      border-bottom: 2px solid #EF4444;
      padding-bottom: 4px;
    }

    .nav-link {
      transition: color .2s ease, border-bottom .2s ease;
    }

    /* Main Menu Card Styling */
    .main-menu-card {
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    @media (min-width:1024px) {
      .main-menu-card {
        flex-direction: row;
        height: 480px;
      }
    }

    .main-menu-image-wrapper {
      background-color: #f7f7f7;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      padding: 1rem;
      width: 100%;
      max-height: 300px;
    }

    @media (min-width:1024px) {
      .main-menu-image-wrapper {
        width: 50%;
        max-height: none;
      }
    }

    .main-menu-image-wrapper img {
      width: 100%;
      max-width: 90%;
      max-height: 90%;
      object-fit: contain;
      border-radius: 8px;
    }

    .main-menu-details {
      flex-grow: 1;
      padding: 2.5rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .main-menu-title {
      font-size: 3.5rem;
      font-weight: 800;
      color: #333;
      margin-bottom: 1rem;
      line-height: 1.1;
    }

    .main-menu-description {
      font-size: .95rem;
      color: #555;
      margin-bottom: 2rem;
      line-height: 1.6;
    }

    .main-menu-price {
      font-size: 2rem;
      font-weight: 700;
      color: #EF4444;
      margin-right: 1.5rem;
    }

    .order-now-button {
      background: #333;
      color: #fff;
      padding: .9rem 2.2rem;
      border-radius: 6px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .03em;
      cursor: pointer;
      transition: background-color .2s ease;
      font-size: .9rem;
    }

    .order-now-button:hover {
      background: #555;
    }

    /* Small Menu Card Styling */
    .small-menu-card {
      background: #2D2D2D;
      border-radius: 8px;
      overflow: hidden;
      /* Penting untuk pseudo-elemen jika panah terlalu jauh */
      color: white;
      padding: .75rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      /* Penting untuk pseudo-elemen */
      height: 190px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, .15);
      cursor: pointer;
      transition: transform .2s ease, box-shadow .2s ease;
      /* default border transparan, akan diganti pseudo-elemen */
      border: 2px solid transparent;
    }

    /* Highlight untuk kartu kecil yang aktif */
    .small-menu-card.active {
      outline: none;
      /* Hapus atau komentari jika ada border solid default di sini */
      transform: translateY(-5px);
      /* Efek naik sedikit */
      box-shadow: 0 8px 15px rgba(0, 0, 0, .25);
      /* Shadow lebih tebal */
      z-index: 10;
      /* Pastikan kartu aktif ada di atas */
      /* Tambahan: pastikan parent grid/flex tidak memiliki overflow:hidden yang akan memotong panah */
    }

    /* Pseudo-elemen untuk border putus-putus */
    .small-menu-card.active::before {
      content: '';
      position: absolute;
      /* Sesuaikan agar border pas di tepi kartu */
      top: -2px;
      left: -2px;
      right: -2px;
      bottom: -2px;
      border: 2px dashed #EF4444;
      /* Border putus-putus merah */
      border-radius: 8px;
      /* Ikuti radius kartu */
      pointer-events: none;
      /* Agar tidak menghalangi klik */
      z-index: 1;
      /* Di atas kartu tapi di bawah panah */
    }

    /* Pseudo-elemen untuk panah */
    .small-menu-card.active::after {
      content: '';
      position: absolute;
      right: -25px;
      /* Posisi panah, sesuaikan jika perlu */
      top: 50%;
      /* Tengah vertikal */
      transform: translateY(-50%);
      width: 0;
      height: 0;
      border-top: 10px solid transparent;
      /* Bagian atas segitiga */
      border-bottom: 10px solid transparent;
      /* Bagian bawah segitiga */
      border-left: 15px solid #EF4444;
      /* Bentuk panah ke kiri, warna merah */
      z-index: 11;
      /* Pastikan panah di atas semua elemen lain */
    }

    /* Untuk fokus keyboard, agar tetap ada feedback visual */
    .small-menu-card:focus-visible {
      outline: 2px solid #EF4444;
      outline-offset: 2px;
    }

    .small-menu-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, .25);
    }

    .small-menu-card img {
      width: 100%;
      height: 100px;
      object-fit: cover;
      border-radius: 6px;
      margin-bottom: .75rem;
    }

    .small-card-price-overlay {
      position: absolute;
      top: 1.25rem;
      right: 1.25rem;
      background: rgba(0, 0, 0, .6);
      color: white;
      padding: 3px 8px;
      border-radius: 4px;
      font-size: .8rem;
      font-weight: 600;
      z-index: 12;
      /* Di atas panah jika perlu */
    }

    .small-card-details {
      width: 100%;
      text-align: center;
      padding: 0 .5rem;
    }

    .small-card-details h4 {
      font-size: 1rem;
      font-weight: 600;
      color: white;
      margin-bottom: .25rem;
      line-height: 1.3;
    }

    .small-card-details p {
      font-size: .75rem;
      color: #aaa;
      line-height: 1.3;
    }

    /* Slider Dots Styling */
    .slider-dot {
      width: 10px;
      height: 10px;
      background: #D1D5DB;
      border-radius: 50%;
      cursor: pointer;
      transition: background-color .2s ease;
    }

    .slider-dot.active {
      background: #EF4444;
    }

    /* Responsive Tweaks */
    @media (max-width:640px) {
      .main-menu-title {
        font-size: 2rem;
      }

      .main-menu-details {
        padding: 1.25rem;
      }

      .main-menu-card {
        border-radius: 6px;
      }

      /* Sesuaikan posisi panah untuk layar kecil jika grid berubah */
      .small-menu-card.active::after {
        right: -15px;
        /* Mungkin lebih dekat atau disembunyikan */
      }
    }

    /* Favorites Section */
    .favorites-wrap {
      margin-top: 3rem;
      padding: 2rem;
      background: linear-gradient(180deg, #1f1f1f 0%, #2b2b2b 100%);
      border-radius: 16px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, .25);
      color: white;
    }

    .favorites-title {
      font-size: 1.75rem;
      font-weight: 800;
      color: #fff;
    }

    .favorites-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1rem;
    }

    .order-now-red {
      background: #EF4444;
      color: #fff;
      padding: .6rem 1.2rem;
      border-radius: 9999px;
      font-weight: 700;
    }

    .fav-cards {
      display: flex;
      gap: 1rem;
      overflow-x: auto;
      padding: .75rem 0;
    }

    .fav-card {
      background: #2D2D2D;
      border-radius: 10px;
      padding: .75rem;
      width: 160px;
      flex: 0 0 auto;
      position: relative;
      cursor: pointer;
      transition: transform .2s ease, box-shadow .2s ease;
    }

    .fav-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, .25);
    }

    .fav-card img {
      width: 100%;
      height: 90px;
      border-radius: 8px;
      object-fit: cover;
    }

    .fav-price {
      position: absolute;
      top: .75rem;
      right: .75rem;
      background: rgba(0, 0, 0, .6);
      color: #fff;
      font-size: .75rem;
      padding: 2px 6px;
      border-radius: 6px;
    }

    .fav-name {
      margin-top: .5rem;
      font-size: .95rem;
      font-weight: 700;
    }

    .fav-variant {
      font-size: .7rem;
      color: #bbb;
    }

    .fav-stars {
      margin-top: .35rem;
      color: #F59E0B;
      font-size: .8rem;
      letter-spacing: 1px;
    }
  </style>
</head>

<body class="bg-gray-100 font-sans">
  <div id="pageRoot">

    <nav class="bg-white p-4 flex justify-between items-center px-6 lg:px-10 border-b border-gray-200 shadow-sm">
      <div class="flex items-center space-x-6">
        <a href="{{ route('home') }}" class="nav-link text-gray-800 hover:text-red-500">Home</a>
        <a href="{{ route('menu') }}" class="nav-link active" aria-current="page">Menu</a>
      </div>
      <div>
        <button id="cartBtnMenu" type="button" class="text-gray-700" aria-label="Cart">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
            </path>
          </svg>
        </button>
      </div>
    </nav>

    <div id="pageFade" class="fixed inset-0 bg-black/50 opacity-0 pointer-events-none z-40 hidden"></div>

    <div id="mobileMenu"
      class="fixed inset-0 z-30 opacity-0 pointer-events-none transition-opacity duration-200 hidden">
      <div id="mobileOverlay" class="absolute inset-0 bg-black/50"></div>
      <div
        class="absolute left-0 top-0 h-full w-72 bg-white shadow-lg p-6 space-y-6 transform -translate-x-full transition-transform duration-200">
        <div class="flex items-center justify-between">
          <span class="text-lg font-bold">Menu</span>
          <button id="closeMobile" class="text-gray-700" aria-label="Close">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <a href="{{ route('home') }}" class="block text-gray-800 font-semibold">Home</a>
        <a href="{{ route('menu') }}" class="block text-gray-800 font-semibold">Menu</a>
      </div>
    </div>

  </div>

  <div id="cartModal" class="fixed inset-0 z-30 opacity-0 pointer-events-none transition-opacity duration-200 hidden">
    <div id="cartOverlay" class="absolute inset-0 bg-black/50"></div>
    <div
      class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-lg p-6 transform translate-x-full transition-transform duration-200">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold">Keranjang</h3>
        <button id="closeCart" class="text-gray-700" aria-label="Close">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <div id="cartItems" class="space-y-4 text-sm text-gray-700">Keranjang kosong</div>
      <div class="mt-6 flex items-center justify-between">
        <span class="font-semibold">Total</span>
        <span id="cartTotal" class="font-bold">Rp 0</span>
      </div>
      <div class="mt-6 space-y-3">
        <input id="custName" type="text" class="w-full border rounded px-3 py-2" placeholder="Nama">
        <div id="errName" class="text-red-600 text-xs"></div>
        <input id="custPhone" type="text" class="w-full border rounded px-3 py-2" placeholder="Nomor HP">
        <div id="errPhone" class="text-red-600 text-xs"></div>
        <select id="payMethod" class="w-full border rounded px-3 py-2">
          <option value="">Pilih metode pembayaran</option>
          <option value="Cash">Cash</option>
          <option value="QRIS">QRIS</option>
        </select>
        <div id="errMethod" class="text-red-600 text-xs"></div>
        <div id="checkoutError" class="text-red-600 text-sm"></div>
        <button id="doCheckout" class="w-full bg-black text-white py-3 rounded">Checkout</button>
      </div>
    </div>
  </div>

  <div class="container mx-auto px-6 py-12 lg:px-10">
    {{-- HERO / MAIN MENU --}}
    <div class="main-menu-card">
      <div class="main-menu-image-wrapper">
        @php
          $initial = $menus[0] ?? null;
          $initialImage = $initial['image'] ?? 'images/nasi-goreng.png';
          $initialName = $initial['name'] ?? '';
          $initialDesc = $initial['description'] ?? '';
          $initialPrice = $initial['price'] ?? '';
        @endphp

        <img id="mainImage" src="{{ asset($initialImage) }}" alt="{{ $initialName }}" class="fade-image">
      </div>

      <div class="main-menu-details">
        <h2 id="mainTitle" class="main-menu-title">{{ $initialName }}</h2>
        <p id="mainDesc" class="main-menu-description">{!! nl2br(e($initialDesc)) !!}</p>

        <div class="flex items-center justify-start mt-auto">
          <span id="mainPrice" class="main-menu-price">{{ $initialPrice }}</span>
          <button id="orderNowBtn" type="button" class="order-now-button">ORDER NOW</button>
        </div>
      </div>
    </div>

    {{-- SMALL CARDS --}}
    <div class="mt-12">
      {{-- Grid container untuk kartu-kartu kecil --}}
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 relative">
        @foreach($menus as $index => $menu)
          <button type="button" class="small-menu-card" data-menu-name="{{ $menu['name'] ?? '' }}"
            data-menu-price="{{ $menu['price'] ?? '' }}" data-menu-desc="{{ $menu['description'] ?? '' }}"
            data-menu-image="{{ asset($menu['image'] ?? 'images/nasi-goreng.png') }}"
            aria-label="Tampilkan {{ $menu['name'] ?? 'menu' }}">
            <img src="{{ asset($menu['image'] ?? 'images/nasi-goreng.png') }}" alt="{{ $menu['name'] ?? '' }}">
            <span class="small-card-price-overlay">{{ $menu['price'] ?? '' }}</span>
            <div class="small-card-details">
              <h4 class="font-semibold">{{ $menu['name'] ?? '' }}</h4>
              <p>{{ $menu['variant'] ?? '' }}</p>
            </div>
          </button>
        @endforeach
      </div>
    </div>

    {{-- Slider dots (statis, bisa dikembangkan nanti) --}}
    <div class="flex justify-center mt-10 space-x-3">
      @for($i = 0; $i < max(1, count($menus)); $i++)
        <span class="slider-dot {{ $i === 0 ? 'active' : '' }}"></span>
      @endfor
    </div>
  </div>

  <div class="container mx-auto px-6 lg:px-10">
    <section class="favorites-wrap">
      <div class="favorites-head">
        <h3 class="favorites-title">Rekomendasi Menu Favorit</h3>
        <button id="orderFavoritesBtn" type="button" class="order-now-red">ORDER NOW</button>
      </div>
      <div class="fav-cards" id="favCards">
        @foreach(array_slice($menus, 0, 5) as $m)
          <button type="button" class="fav-card" data-menu-name="{{ $m['name'] ?? '' }}"
            data-menu-price="{{ $m['price'] ?? '' }}" data-menu-desc="{{ $m['description'] ?? '' }}"
            data-menu-image="{{ asset($m['image'] ?? 'images/nasi-goreng.png') }}">
            <img src="{{ asset($m['image'] ?? 'images/nasi-goreng.png') }}" alt="{{ $m['name'] ?? '' }}">
            <span class="fav-price">{{ $m['price'] ?? '' }}</span>
            <div class="fav-name">{{ $m['name'] ?? '' }}</div>
            <div class="fav-variant">{{ $m['variant'] ?? '' }}</div>
            <div class="fav-stars">★★★★★</div>
          </button>
        @endforeach
      </div>
    </section>
  </div>

  <script>
    function parsePrice(label) {
      const n = label.replace(/[^0-9]/g, '');
      return n ? parseInt(n, 10) : 0;
    }
    function formatPrice(n) {
      return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    document.addEventListener('DOMContentLoaded', function () {
      const mainImage = document.getElementById('mainImage');
      const mainTitle = document.getElementById('mainTitle');
      const mainDesc = document.getElementById('mainDesc');
      const mainPrice = document.getElementById('mainPrice');
      const orderNowBtn = document.getElementById('orderNowBtn');
      const cartBtn = document.getElementById('cartBtnMenu') || document.querySelector('[aria-label="Cart"]');
      const cartModal = document.getElementById('cartModal');
      const itemsEl = document.getElementById('cartItems');
      const totalEl = document.getElementById('cartTotal');
      const burgerBtn = document.querySelector('button[aria-label="Menu Toggle"]');
      const mobileMenu = document.getElementById('mobileMenu');
      const closeMobile = document.getElementById('closeMobile');
      const mobileOverlay = document.getElementById('mobileOverlay');
      const cartOverlay = document.getElementById('cartOverlay');
      const closeCartBtn = document.getElementById('closeCart');

      const cards = document.querySelectorAll('.small-menu-card');
      const sliderDots = document.querySelectorAll('.slider-dot');
      const doCheckout = document.getElementById('doCheckout');
      const custName = document.getElementById('custName');
      const custPhone = document.getElementById('custPhone');
      const payMethod = document.getElementById('payMethod');
      const errName = document.getElementById('errName');
      const errPhone = document.getElementById('errPhone');
      const errMethod = document.getElementById('errMethod');
      const PAGE_DURATION = 520;
      const pageFade = document.getElementById('pageFade');
      const pageRoot = document.getElementById('pageRoot');
      const homeUrl = '{{ route('home') }}';
      const menuUrl = '{{ route('menu') }}';
      const favCards = document.querySelectorAll('.fav-card');
      const orderFavoritesBtn = document.getElementById('orderFavoritesBtn');

      function updateHero({ name, desc, price, image }) {
        // Fade out efek pada gambar utama
        mainImage.classList.add('is-fading');
        setTimeout(() => {
          mainImage.src = image;
          mainImage.alt = name;
          mainTitle.textContent = name;
          mainDesc.textContent = desc;
          mainPrice.textContent = price;
          // Fade in efek
          mainImage.classList.remove('is-fading');
        }, 180); // Durasi ini harus cocok dengan transisi CSS 'fade-image'
      }

      // overlay disembunyikan secara default melalui class di markup

      cards.forEach((card, index) => {
        card.addEventListener('click', () => {
          const payload = {
            name: card.dataset.menuName || '',
            desc: card.dataset.menuDesc || '',
            price: card.dataset.menuPrice || '',
            image: card.dataset.menuImage || ''
          };
          updateHero(payload);
          highlightCard(card, index);
        });

        // Membuat kartu fokusable dan mendukung aktivasi keyboard
        card.setAttribute('tabindex', '0');
        card.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            card.click();
          }
        });
      });

      favCards.forEach((card) => {
        card.addEventListener('click', () => {
          const payload = {
            name: card.dataset.menuName || '',
            desc: card.dataset.menuDesc || '',
            price: card.dataset.menuPrice || '',
            image: card.dataset.menuImage || ''
          };
          updateHero(payload);
        });
      });

      function highlightCard(activeCard, activeIndex) {
        cards.forEach(c => c.classList.remove('active')); // Hapus highlight dari semua kartu
        activeCard.classList.add('active'); // Tambahkan highlight ke kartu yang aktif

        // Update slider dots
        sliderDots.forEach(dot => dot.classList.remove('active'));
        if (sliderDots[activeIndex]) {
          sliderDots[activeIndex].classList.add('active');
        }
      }

      // Inisialisasi: set kartu pertama sebagai aktif/highlighted saat halaman dimuat
      if (cards.length > 0) {
        // Isi konten hero dengan data dari kartu pertama
        const initialCard = cards[0];
        const initialPayload = {
          name: initialCard.dataset.menuName || '',
          desc: initialCard.dataset.menuDesc || '',
          price: initialCard.dataset.menuPrice || '',
          image: initialCard.dataset.menuImage || ''
        };
        mainImage.src = initialPayload.image;
        mainImage.alt = initialPayload.name;
        mainTitle.textContent = initialPayload.name;
        mainDesc.textContent = initialPayload.desc;
        mainPrice.textContent = initialPayload.price;

        highlightCard(initialCard, 0); // Highlight kartu pertama
      }

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
      const DURATION = 420;
      function openMenuPanel() {
        mobileMenu.classList.remove('hidden');
        requestAnimationFrame(() => {
          mobileMenu.classList.remove('pointer-events-none', 'opacity-0');
          const panel = mobileMenu.querySelector('.transform');
          panel && panel.classList.remove('-translate-x-full');
        });
      }
      function closeMenuPanel() {
        mobileMenu.classList.add('opacity-0', 'pointer-events-none');
        const panel = mobileMenu.querySelector('.transform');
        panel && panel.classList.add('-translate-x-full');
        setTimeout(() => mobileMenu.classList.add('hidden'), DURATION);
      }
      function openCartPanel() {
        cartModal.classList.remove('hidden');
        requestAnimationFrame(() => {
          cartModal.classList.remove('pointer-events-none', 'opacity-0');
          const panel = cartModal.querySelector('.transform');
          panel && panel.classList.remove('translate-x-full');
        });
      }
      function closeCartPanel() {
        cartModal.classList.add('opacity-0', 'pointer-events-none');
        const panel = cartModal.querySelector('.transform');
        panel && panel.classList.add('translate-x-full');
        setTimeout(() => cartModal.classList.add('hidden'), DURATION);
      }
      burgerBtn && burgerBtn.addEventListener('click', openMenuPanel);
      closeMobile && closeMobile.addEventListener('click', closeMenuPanel);
      mobileOverlay && mobileOverlay.addEventListener('click', closeMenuPanel);
      cartBtn && cartBtn.addEventListener('click', () => { renderCart(); openCartPanel(); });
      closeCartBtn && closeCartBtn.addEventListener('click', closeCartPanel);
      cartOverlay && cartOverlay.addEventListener('click', closeCartPanel);
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeMenuPanel(); closeCartPanel(); } });
      orderNowBtn && orderNowBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const item = {
          name: mainTitle.textContent || '',
          priceLabel: mainPrice.textContent || 'Rp 0',
          priceNumber: parsePrice(mainPrice.textContent || '0'),
          image: mainImage.src || '',
          qty: 1,
          spice: 'Normal'
        };
        const cart = loadCart();
        const idx = cart.findIndex(i => i.name === item.name);
        if (idx >= 0) { cart[idx].qty = (cart[idx].qty || 1) + 1; }
        else { cart.push(item); }
        saveCart(cart);
        renderCart();
        openCartPanel();
      });

      orderFavoritesBtn && orderFavoritesBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const item = {
          name: mainTitle.textContent || '',
          priceLabel: mainPrice.textContent || 'Rp 0',
          priceNumber: parsePrice(mainPrice.textContent || '0'),
          image: mainImage.src || '',
          qty: 1,
          spice: 'Normal'
        };
        const cart = loadCart();
        const idx = cart.findIndex(i => i.name === item.name);
        if (idx >= 0) { cart[idx].qty = (cart[idx].qty || 1) + 1; } else { cart.push(item); }
        saveCart(cart);
        renderCart();
        openCartPanel();
      });

      doCheckout && doCheckout.addEventListener('click', async () => {
        const cart = loadCart();
        if (cart.length === 0) return;
        // simple form validation
        errName.textContent = '';
        errPhone.textContent = '';
        errMethod.textContent = '';
        let hasError = false;
        if (!custName.value || !custName.value.trim()) { errName.textContent = 'Masukkan username terlebih dahulu'; hasError = true; }
        if (!custPhone.value || !custPhone.value.trim()) { errPhone.textContent = 'Masukkan nomor HP terlebih dahulu'; hasError = true; }
        if (!payMethod.value) { errMethod.textContent = 'Pilih metode pembayaran terlebih dahulu'; hasError = true; }
        if (hasError) return;
        const payload = {
          customer_name: custName.value || null,
          customer_phone: custPhone.value || null,
          payment_method: payMethod.value || 'Cash',
          items: cart.map(c => ({ name: (c.name || '') + ' - ' + (c.spice || 'Normal'), priceNumber: c.priceNumber || parsePrice(c.priceLabel || '0'), qty: c.qty || 1 }))
        };
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        try {
          const res = await fetch('{{ route('checkout') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(payload) });
          if (!res.ok) {
            const json = await res.json();
            throw new Error(json.message || ('HTTP ' + res.status));
          }
          const json = await res.json();
          if (json && (json.ok || json.status === 'success')) {
           window.location.href = '{{ url('/checkout/order') }}/' + json.order_id;
          } else {
            throw new Error(json.message || 'Unknown error');
          }
        } catch (err) {
          const errBox = document.getElementById('checkoutError');
          if (errBox) { errBox.textContent = 'Gagal membuat order (' + (err.message || 'error') + ')'; }
        }
      });

      const dots = document.querySelectorAll('.slider-dot');
      dots.forEach((dot, i) => { dot.addEventListener('click', () => { const target = cards[i]; target && target.click(); }); });

      function navigateSmooth(url) {
        if (!pageFade) { window.location.href = url; return; }
        if (pageRoot) {
          const leaveClass = (url === homeUrl) ? 'is-leave-right' : 'is-leave-left';
          pageRoot.classList.remove('is-leave-left', 'is-leave-right');
          pageRoot.classList.add(leaveClass);
        }
        pageFade.classList.remove('hidden', 'pointer-events-none');
        pageFade.classList.remove('opacity-0');
        pageFade.classList.add('opacity-100');
        setTimeout(() => { window.location.href = url; }, PAGE_DURATION);
      }

      Array.from(document.querySelectorAll('a')).forEach(a => {
        if ([homeUrl, menuUrl].includes(a.href)) {
          a.addEventListener('click', (e) => { e.preventDefault(); navigateSmooth(a.href); });
        }
      });
    });
  </script>

</body>

</html>