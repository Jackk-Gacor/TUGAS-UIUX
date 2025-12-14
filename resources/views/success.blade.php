<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout Success</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite('resources/css/app.css')
</head>

<body class="bg-gray-50 font-sans">
  <header class="px-6 lg:px-10 py-5 bg-white border-b">
    <div class="flex items-center space-x-6">
      <a href="{{ route('home') }}" class="text-gray-800 hover:text-red-500">Home</a>
      <a href="{{ route('menu') }}" class="text-gray-800 hover:text-red-500">Menu</a>
      <span class="text-gray-500">Success</span>
    </div>
  </header>

  <main class="max-w-2xl mx-auto px-6 lg:px-10 py-12">
    <!-- Order Summary -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">Pesanan Anda</h2>
          <p class="text-sm text-gray-600 mt-1">Order ID: <span class="font-mono text-gray-900">#{{ $order->id }}</span></p>
        </div>
        <div class="text-right">
          <p class="text-sm text-gray-600">Total</p>
          <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
        </div>
      </div>

      <div class="space-y-3 border-t pt-4">
        @foreach($order->items as $item)
          <div class="flex items-center justify-between text-sm">
            <div>
              <p class="font-medium text-gray-900">{{ $item->product->name }}</p>
              <p class="text-gray-600">Qty: {{ $item->quantity }}</p>
            </div>
            <p class="font-medium text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Payment Method Specific Content -->
    @if($order->payment_method === 'COD')
      <!-- COD Success Modal -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <div class="mb-6">
          <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">Pesanan Dikonfirmasi!</h3>
          <p class="text-gray-600 mb-4">Silakan datang ke toko untuk menyelesaikan pembayaran dan pengambilan pesanan Anda.</p>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-left">
          <p class="text-sm font-semibold text-blue-900 mb-2">Informasi Pengambilan:</p>
          <div class="text-sm text-blue-800 space-y-2">
            <p><strong>Nama:</strong> {{ $order->customer_name }}</p>
            <p><strong>Nomor HP:</strong> {{ $order->customer_phone }}</p>
            <p><strong>Catatan:</strong> {{ $order->note ?? 'Tidak ada catatan khusus' }}</p>
          </div>
        </div>

        <button id="proceedBtn" class="w-full bg-black text-white py-3 rounded-lg font-semibold hover:bg-gray-800 transition">
          Lanjutkan ke Toko
        </button>
      </div>

    @elseif($order->payment_method === 'QRIS')
      <!-- QRIS Payment Section -->
      <div class="space-y-6">
        <!-- QRIS Code Display -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Scan Kode QR untuk Membayar</h3>
          
          <div class="flex flex-col items-center mb-6">
            <div class="w-48 h-48 bg-white border-2 border-gray-300 rounded-lg flex items-center justify-center mb-4">
              <div class="w-full h-full grid grid-cols-8 gap-px p-2">
                @for($i = 0; $i < 64; $i++)
                  <div class="w-full h-full {{ $i % 3 ? 'bg-black' : 'bg-white' }} rounded-sm"></div>
                @endfor
              </div>
            </div>
            <p class="text-sm text-gray-600 text-center mb-4">
              Gunakan aplikasi e-wallet atau banking Anda untuk scan kode QR di atas
            </p>
            <div class="w-full bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-left">
              <p class="text-xs text-yellow-800 font-semibold mb-1">⚠️ Penting:</p>
              <p class="text-xs text-yellow-700">
                Pastikan Anda mengirimkan bukti pembayaran setelah melakukan transfer untuk konfirmasi pesanan Anda.
              </p>
            </div>
          </div>
        </div>

        <!-- Upload Payment Proof -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Unggah Bukti Pembayaran</h3>
          
          <form id="qrisUploadForm" class="space-y-4">
            @csrf
            
            <div id="uploadArea" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center bg-gray-50 cursor-pointer hover:bg-gray-100 transition">
              <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
              </svg>
              <p class="text-gray-700 font-medium mb-1">Klik untuk memilih file</p>
              <p class="text-sm text-gray-600">atau drag dan drop di sini</p>
              <p class="text-xs text-gray-500 mt-2">PNG, JPG, atau PDF (Maks 5MB)</p>
              <input type="file" id="qrisFile" name="payment_proof" accept="image/png,image/jpeg,application/pdf" class="hidden">
            </div>

            <!-- File Preview -->
            <div id="filePreview" class="hidden">
              <div class="bg-gray-100 rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <div>
                    <p class="text-sm font-medium text-gray-900" id="fileName">File selected</p>
                    <p class="text-xs text-gray-600" id="fileSize">0 MB</p>
                  </div>
                </div>
                <button type="button" id="clearFileBtn" class="text-gray-500 hover:text-gray-700">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>

            <div id="uploadError" class="hidden bg-red-50 border border-red-200 rounded-lg p-4">
              <p class="text-sm text-red-800" id="uploadErrorText"></p>
            </div>

            <div class="flex gap-3">
              <button type="submit" id="uploadBtn" class="flex-1 bg-black text-white py-3 rounded-lg font-semibold hover:bg-gray-800 transition disabled:opacity-50 disabled:cursor-not-allowed">
                Unggah Bukti Pembayaran
              </button>
              <button type="button" id="skipBtn" class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-50 transition">
                Lewati untuk Sekarang
              </button>
            </div>
          </form>
        </div>
      </div>

    @else
      <!-- Transfer Payment -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Instruksi Transfer Bank</h3>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
          <p class="text-sm font-semibold text-blue-900 mb-3">Transfer ke rekening berikut:</p>
          <div class="bg-white rounded p-4 space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Bank:</span>
              <span class="font-mono font-semibold text-gray-900">BCA</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">No. Rekening:</span>
              <span class="font-mono font-semibold text-gray-900">123456789</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Atas Nama:</span>
              <span class="font-mono font-semibold text-gray-900">UMKM Pak BI</span>
            </div>
            <div class="border-t pt-2 flex justify-between">
              <span class="text-gray-600">Jumlah:</span>
              <span class="font-mono font-semibold text-red-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
          </div>
        </div>

        <p class="text-sm text-gray-600 mb-6">
          Silakan transfer sesuai jumlah di atas. Pesanan Anda akan diproses setelah konfirmasi pembayaran diterima.
        </p>

        <button id="proceedBtn" class="w-full bg-black text-white py-3 rounded-lg font-semibold hover:bg-gray-800 transition">
          Mengerti, Lanjutkan ke Toko
        </button>
      </div>
    @endif
  </main>

  <script>
    // Proceed to shop button
    const proceedBtn = document.getElementById('proceedBtn');
    if (proceedBtn) {
      proceedBtn.addEventListener('click', () => {
        // Show success popup then redirect
        showSuccessPopup(() => {
          window.location.href = '{{ route("menu") }}';
        });
      });
    }

    // QRIS File Upload Handler
    const uploadArea = document.getElementById('uploadArea');
    const qrisFile = document.getElementById('qrisFile');
    const filePreview = document.getElementById('filePreview');
    const qrisUploadForm = document.getElementById('qrisUploadForm');
    const uploadBtn = document.getElementById('uploadBtn');
    const skipBtn = document.getElementById('skipBtn');
    const clearFileBtn = document.getElementById('clearFileBtn');
    const uploadError = document.getElementById('uploadError');
    const uploadErrorText = document.getElementById('uploadErrorText');

    if (uploadArea) {
      // Click to upload
      uploadArea.addEventListener('click', () => qrisFile.click());

      // Drag and drop
      uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('border-blue-500', 'bg-blue-50');
      });

      uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('border-blue-500', 'bg-blue-50');
      });

      uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('border-blue-500', 'bg-blue-50');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
          qrisFile.files = files;
          updateFilePreview();
        }
      });

      // File input change
      qrisFile.addEventListener('change', updateFilePreview);

      function updateFilePreview() {
        const file = qrisFile.files[0];
        if (file) {
          const maxSize = 5 * 1024 * 1024; // 5MB
          if (file.size > maxSize) {
            uploadError.classList.remove('hidden');
            uploadErrorText.textContent = 'File terlalu besar. Maksimal 5MB.';
            qrisFile.value = '';
            return;
          }

          uploadError.classList.add('hidden');
          uploadArea.classList.add('hidden');
          filePreview.classList.remove('hidden');
          document.getElementById('fileName').textContent = file.name;
          document.getElementById('fileSize').textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
        }
      }

      clearFileBtn.addEventListener('click', () => {
        qrisFile.value = '';
        uploadArea.classList.remove('hidden');
        filePreview.classList.add('hidden');
        uploadError.classList.add('hidden');
      });

      // Form submission
      qrisUploadForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData();
        formData.append('payment_proof', qrisFile.files[0]);
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('_token', csrf);

        uploadBtn.disabled = true;
        uploadBtn.textContent = 'Mengunggah...';

        try {
          const response = await fetch('{{ route("checkout.upload-proof", $order->id) }}', {
            method: 'POST',
            body: formData,
          });

          const json = await response.json();

          if (response.ok && json.status === 'success') {
            uploadError.classList.add('hidden');
            showSuccessPopup(() => {
              window.location.href = '{{ route("menu") }}';
            });
          } else {
            uploadError.classList.remove('hidden');
            uploadErrorText.textContent = json.message || 'Gagal mengunggah file.';
            uploadBtn.disabled = false;
            uploadBtn.textContent = 'Unggah Bukti Pembayaran';
          }
        } catch (err) {
          uploadError.classList.remove('hidden');
          uploadErrorText.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
          uploadBtn.disabled = false;
          uploadBtn.textContent = 'Unggah Bukti Pembayaran';
        }
      });

      skipBtn.addEventListener('click', () => {
        showSuccessPopup(() => {
          window.location.href = '{{ route("menu") }}';
        });
      });
    }

    // Success Popup Modal
    function showSuccessPopup(callback) {
      const popup = document.createElement('div');
      popup.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4';
      popup.innerHTML = `
        <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6 text-center animate-bounce">
          <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h2 class="text-xl font-bold text-gray-900 mb-2">Terima Kasih!</h2>
          <p class="text-gray-600 mb-6">Silakan datang ke toko untuk melanjutkan.</p>
          <button class="w-full bg-black text-white py-2 rounded-lg font-semibold hover:bg-gray-800 transition">
            Lanjutkan ke Toko
          </button>
        </div>
      `;

      document.body.appendChild(popup);
      popup.querySelector('button').addEventListener('click', () => {
        popup.remove();
        callback();
      });
    }
  </script>

  <style>
    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }
    .animate-bounce {
      animation: bounce 1s infinite;
    }
  </style>
</body>

</html>
