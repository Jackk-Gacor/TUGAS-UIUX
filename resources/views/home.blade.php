<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nasi Goreng Pak BI</title>
  @vite('resources/css/app.css')
</head>
<body class="bg-white font-sans">

  <!-- Navbar -->
  <header class="flex items-center px-10 py-6 absolute top-0 left-0 right-0 z-20 bg-transparent"> 
    {{-- Mengubah top-15 menjadi top-0 atau sesuaikan dengan desain Anda agar tidak terlalu rendah --}}
    
    <div class="mr-4 cursor-pointer">
      <svg class="w-7 h-7 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
    </div>
    
    <!-- Navigasi Menu dan Shop -->
    <nav class="flex items-center space-x-10 text-lg font-medium">
      {{-- Mengganti '#' dengan route Laravel untuk halaman menu --}}
      <a href="{{ route('menu') }}" class="relative hover:text-red-600 text-gray-800 pb-1 border-b-2 border-blue-500">Menu</a>
      <a href="#" class="hover:text-red-600 text-gray-800">Shop</a> {{-- Anda bisa ganti ini juga jika ada halaman shop terpisah --}}
    </nav>
  </header>

  <!-- Hero Section -->
  <section 
    class="relative w-screen h-screen flex items-center justify-start overflow-hidden bg-no-repeat bg-cover bg-center"
    style="background-image: url('/images/Home-1.png');"> 

    <!-- Konten Teks dan Tombol -->
    <div class="relative z-10 ml-20 pt-40 max-w-xs md:max-w-sm self-start"> 
      <h2 class="text-5xl md:text-6xl font-extrabold text-gray-900 leading-tight">Nasi Goreng Pak BI</h2>
      <p class="text-gray-700 mt-4 text-sm md:text-base">
        Nasi goreng murah, enak, dan bikin kenyang tentunya, cocok untuk mahasiswa yang porsi makannya besar dan minim budget.
      </p>
      <button class="mt-8 bg-black text-white px-10 py-4 rounded-full font-semibold hover:bg-gray-800 transition text-lg w-full">
        ORDER NOW
      </button>
    </div>
  </section>

</body>
</html>