<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Foodbar - Menu</title>
  @vite('resources/css/app.css') {{-- Pastikan ini di sini --}}

  <style>
    /* Transisi untuk efek fade pada gambar utama */
    .fade-image {
      transition: opacity 250ms ease-in-out;
      opacity: 1;
    }
    .fade-image.hidden {
      opacity: 0;
    }

    /* Navbar Active Link Styling */
    .nav-link.active {
        color:#EF4444;
        border-bottom:2px solid #EF4444;
        padding-bottom:4px;
    }
    .nav-link { transition: color .2s ease, border-bottom .2s ease; }

    /* Main Menu Card Styling */
    .main-menu-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        display:flex;
        flex-direction:column;
        overflow:hidden;
    }
    @media (min-width:1024px){
        .main-menu-card {
            flex-direction:row;
            height:480px;
        }
    }
    .main-menu-image-wrapper {
        background-color:#f7f7f7;
        flex-shrink:0;
        display:flex;
        align-items:center;
        justify-content:center;
        position:relative;
        padding:1rem;
        width:100%;
        max-height:300px;
    }
    @media (min-width:1024px){
        .main-menu-image-wrapper {
            width:50%;
            max-height:none;
        }
    }
    .main-menu-image-wrapper img {
        width:100%;
        max-width:90%;
        max-height:90%;
        object-fit:contain;
        border-radius:8px;
    }
    .main-menu-details {
        flex-grow:1;
        padding:2.5rem;
        display:flex;
        flex-direction:column;
        justify-content:center;
    }
    .main-menu-title {
        font-size:3.5rem;
        font-weight:800;
        color:#333;
        margin-bottom:1rem;
        line-height:1.1;
    }
    .main-menu-description {
        font-size:.95rem;
        color:#555;
        margin-bottom:2rem;
        line-height:1.6;
    }
    .main-menu-price {
        font-size:2rem;
        font-weight:700;
        color:#EF4444;
        margin-right:1.5rem;
    }

    .order-now-button {
        background:#333;
        color:#fff;
        padding:.9rem 2.2rem;
        border-radius:6px;
        font-weight:600;
        text-transform:uppercase;
        letter-spacing:.03em;
        cursor:pointer;
        transition:background-color .2s ease;
        font-size:.9rem;
    }
    .order-now-button:hover{ background:#555; }

    /* Small Menu Card Styling */
    .small-menu-card {
      background:#2D2D2D;
      border-radius:8px;
      overflow:hidden; /* Penting untuk pseudo-elemen jika panah terlalu jauh */
      color:white;
      padding:.75rem;
      display:flex;
      flex-direction:column;
      align-items:center;
      position:relative; /* Penting untuk pseudo-elemen */
      height:190px;
      box-shadow:0 4px 8px rgba(0,0,0,.15);
      cursor:pointer;
      transition:transform .2s ease, box-shadow .2s ease;
      /* default border transparan, akan diganti pseudo-elemen */
      border: 2px solid transparent;
    }

    /* Highlight untuk kartu kecil yang aktif */
    .small-menu-card.active {
        outline:none;
        /* Hapus atau komentari jika ada border solid default di sini */
        transform:translateY(-5px); /* Efek naik sedikit */
        box-shadow:0 8px 15px rgba(0,0,0,.25); /* Shadow lebih tebal */
        z-index: 10; /* Pastikan kartu aktif ada di atas */
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
      border: 2px dashed #EF4444; /* Border putus-putus merah */
      border-radius: 8px; /* Ikuti radius kartu */
      pointer-events: none; /* Agar tidak menghalangi klik */
      z-index: 1; /* Di atas kartu tapi di bawah panah */
    }

    /* Pseudo-elemen untuk panah */
    .small-menu-card.active::after {
      content: '';
      position: absolute;
      right: -25px; /* Posisi panah, sesuaikan jika perlu */
      top: 50%; /* Tengah vertikal */
      transform: translateY(-50%);
      width: 0;
      height: 0;
      border-top: 10px solid transparent; /* Bagian atas segitiga */
      border-bottom: 10px solid transparent; /* Bagian bawah segitiga */
      border-left: 15px solid #EF4444; /* Bentuk panah ke kiri, warna merah */
      z-index: 11; /* Pastikan panah di atas semua elemen lain */
    }

    /* Untuk fokus keyboard, agar tetap ada feedback visual */
    .small-menu-card:focus-visible {
        outline:2px solid #EF4444;
        outline-offset: 2px;
    }
    .small-menu-card:hover { transform:translateY(-5px); box-shadow:0 8px 15px rgba(0,0,0,.25); }

    .small-menu-card img {
        width:100%;
        height:100px;
        object-fit:cover;
        border-radius:6px;
        margin-bottom:.75rem;
    }
    .small-card-price-overlay {
        position:absolute;
        top:1.25rem;
        right:1.25rem;
        background:rgba(0,0,0,.6);
        color:white;
        padding:3px 8px;
        border-radius:4px;
        font-size:.8rem;
        font-weight:600;
        z-index:12; /* Di atas panah jika perlu */
    }
    .small-card-details {
        width:100%;
        text-align:center;
        padding:0 .5rem;
    }
    .small-card-details h4 {
        font-size:1rem;
        font-weight:600;
        color:white;
        margin-bottom:.25rem;
        line-height:1.3;
    }
    .small-card-details p {
        font-size:.75rem;
        color:#aaa;
        line-height:1.3;
    }

    /* Slider Dots Styling */
    .slider-dot {
        width:10px;
        height:10px;
        background:#D1D5DB;
        border-radius:50%;
        cursor:pointer;
        transition:background-color .2s ease;
    }
    .slider-dot.active { background:#EF4444; }

    /* Responsive Tweaks */
    @media (max-width:640px) {
        .main-menu-title { font-size:2rem; }
        .main-menu-details { padding:1.25rem; }
        .main-menu-card { border-radius:6px; }
        /* Sesuaikan posisi panah untuk layar kecil jika grid berubah */
        .small-menu-card.active::after {
            right: -15px; /* Mungkin lebih dekat atau disembunyikan */
        }
    }
  </style>
</head>
<body class="bg-gray-100 font-sans">

  <nav class="bg-white p-4 flex justify-between items-center px-6 lg:px-10 border-b border-gray-200 shadow-sm">
      <div class="flex items-center">
          <button class="text-gray-600 mr-4 lg:hidden" aria-label="Menu Toggle">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
          </button>
          <h1 class="text-2xl font-bold text-gray-800">foodbar</h1>
      </div>
      <div class="hidden lg:flex items-center space-x-10 text-lg font-medium absolute left-1/2 -translate-x-1/2">
          <a href="{{ route('menu') }}" class="nav-link active">Shop</a>
          <a href="#" class="nav-link text-gray-600 hover:text-red-500">Menu</a>
      </div>
      <div>
          <a href="#" class="text-gray-600" aria-label="Cart">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
          </a>
      </div>
  </nav>

  <div class="container mx-auto px-6 py-12 lg:px-10">
    {{-- HERO / MAIN MENU --}}
    <div class="main-menu-card">
      <div class="main-menu-image-wrapper">
        {{-- Ikon panah ke atas (bisa disesuaikan jika tidak relevan) --}}
        <div class="absolute top-4 left-4 bg-purple-600 text-white rounded-full p-2">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M17.707 9.293A1 1 0 0118 10v.002a1 1 0 01-1.707.707L13 7.414V17a1 1 0 11-2 0V7.414l-3.293 3.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0l5 5a1 1 0 010 1.414z" clip-rule="evenodd"></path></svg>
        </div>

        @php
          // safe default: gunakan index 0 jika ada, jika tidak gunakan nilai kosong/gambar placeholder
          $initial = $menus[0] ?? null;
          $initialImage = $initial['image'] ?? 'images/placeholder.png';
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
          <button class="order-now-button">ORDER NOW</button>
        </div>
      </div>
    </div>

    {{-- SMALL CARDS --}}
    <div class="mt-12">
      {{-- Grid container untuk kartu-kartu kecil --}}
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 relative">
        @foreach($menus as $index => $menu)
          <button
            type="button"
            class="small-menu-card"
            data-menu-name="{{ $menu['name'] ?? '' }}"
            data-menu-price="{{ $menu['price'] ?? '' }}"
            data-menu-desc="{{ $menu['description'] ?? '' }}"
            data-menu-image="{{ asset($menu['image'] ?? 'images/placeholder.png') }}"
            aria-label="Tampilkan {{ $menu['name'] ?? 'menu' }}"
          >
            <img src="{{ asset($menu['image'] ?? 'images/placeholder.png') }}" alt="{{ $menu['name'] ?? '' }}">
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
      @for($i=0; $i < max(1, count($menus)); $i++)
        <span class="slider-dot {{ $i === 0 ? 'active' : '' }}"></span>
      @endfor
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const mainImage = document.getElementById('mainImage');
      const mainTitle = document.getElementById('mainTitle');
      const mainDesc = document.getElementById('mainDesc');
      const mainPrice = document.getElementById('mainPrice');

      const cards = document.querySelectorAll('.small-menu-card');
      const sliderDots = document.querySelectorAll('.slider-dot');

      function updateHero({name, desc, price, image}) {
        // Fade out efek pada gambar utama
        mainImage.classList.add('hidden');
        setTimeout(() => {
          mainImage.src = image;
          mainImage.alt = name;
          mainTitle.textContent = name;
          mainDesc.textContent = desc;
          mainPrice.textContent = price;
          // Fade in efek
          mainImage.classList.remove('hidden');
        }, 180); // Durasi ini harus cocok dengan transisi CSS 'fade-image'
      }

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
    });
  </script>

</body>
</html>