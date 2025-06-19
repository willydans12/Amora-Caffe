// tailwind.config.js (atau bisa langsung di dalam <script> sebelum payments.js)
tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: '#3C2A21',
        secondary: '#E5D3B7'
      },
      borderRadius: {
        none: '0px',
        sm: '4px',
        DEFAULT: '8px',
        md: '12px',
        lg: '16px',
        xl: '20px',
        '2xl': '24px',
        '3xl': '32px',
        full: '9999px',
        button: '8px'
      }
    }
  }
};


// payments.js

    document.addEventListener('DOMContentLoaded', () => {
  // Ambil elemen radio & panel
  const paymentOptions  = document.querySelectorAll('.payment-option');
  const bankDiv         = document.getElementById('bankDetails');
  const ewalletDiv      = document.getElementById('ewalletDetails');
  const qrisDiv         = document.getElementById('qrisDetails');
  const creditDiv       = document.getElementById('creditDetails');
  const copyBtn         = document.getElementById('copyAccount');

  // Container di masing-masing panel untuk menaruh upload area
  const uploadCtnBank     = document.getElementById('uploadContainer');
  const uploadCtnEwallet  = document.getElementById('uploadContainerEwallet');
  const uploadCtnQris     = document.getElementById('uploadContainerQris');
  // (Tidak perlu container untuk credit karena tidak perlu upload)

  // 1) Fungsi untuk render area upload (satu file-input) ke panel tertentu
  function renderUploadArea(container) {
    container.innerHTML = `
      <label for="proofUpload" class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Pembayaran</label>
      <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center relative">
        <!-- Preview Setelah Upload -->
        <div id="filePreview" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-white bg-opacity-75 rounded-lg">
          <i class="ri-file-3-fill text-green-600 ri-3x mb-2"></i>
          <span id="fileName" class="text-gray-800 font-medium"></span>
          <p class="text-xs text-gray-500 mt-1">Klik untuk mengganti file</p>
        </div>
        <!-- Prompt Upload Awal -->
        <div id="uploadPrompt">
          <div class="w-12 h-12 mx-auto mb-2 flex items-center justify-center bg-gray-100 rounded-full text-gray-500">
            <i class="ri-upload-2-line ri-xl"></i>
          </div>
          <p class="text-sm text-gray-500 mb-1">Klik untuk upload atau tarik file ke sini</p>
          <p class="text-xs text-gray-400">Format: JPG, PNG, atau PDF (Maks. 2MB)</p>
        </div>
        <input 
          type="file"
          name="proofUpload"
          id="proofUpload"
          accept="image/*,.pdf"
          class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
        />
      </div>
    `;
  }

  // 2) Saat radio diklik, hide semua panel + bersihkan uploadContainer masing-masing,
  //    lalu tampilkan panel yg sesuai dan inject upload area di situ (kecuali credit).
  paymentOptions.forEach(option => {
    option.addEventListener('click', () => {
      const radioValue = option.querySelector('input[type="radio"]').value;

      // Reset semua panel: sembunyikan dan kosongkan area upload
      [bankDiv, ewalletDiv, qrisDiv, creditDiv].forEach(div => div.classList.add('hidden'));
      uploadCtnBank.innerHTML    = "";
      uploadCtnEwallet.innerHTML = "";
      uploadCtnQris.innerHTML    = "";

      // Tampilkan panel yg dipilih, dan render upload area jika perlu
      if (radioValue === 'bank') {
        bankDiv.classList.remove('hidden');
        renderUploadArea(uploadCtnBank);
      }
      else if (radioValue === 'ewallet') {
        ewalletDiv.classList.remove('hidden');
        renderUploadArea(uploadCtnEwallet);
      }
      else if (radioValue === 'qris') {
        qrisDiv.classList.remove('hidden');
        renderUploadArea(uploadCtnQris);
      }
      else if (radioValue === 'credit') {
        creditDiv.classList.remove('hidden');
        // Kredit tidak butuh upload di sini, jadi kita skip renderUploadArea()
      }

      // 2a) Set ulang listener uploadPreview karena kita baru men‐inject HTML
      attachUploadListeners();
    });
  });

  // 3) Inisialisasi tampilan sesuai nilai yang sudah di‐set di database (jika ada)
  const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
  if (selectedMethod) {
    const parentLabel = selectedMethod.closest('.payment-option');
    const outerDiv    = parentLabel.querySelector('.outer');
    const innerDiv    = parentLabel.querySelector('.inner');

    // Beri highlight border & circle
    parentLabel.classList.add('border-[#3C2A21]');
    outerDiv.classList.remove('border-gray-300');
    outerDiv.classList.add('border-[#3C2A21]');
    innerDiv.classList.remove('bg-transparent');
    innerDiv.classList.add('bg-[#3C2A21]');

    // Tampilkan panel dan area upload sesuai metode awal
    switch (selectedMethod.value) {
      case 'bank':
        bankDiv.classList.remove('hidden');
        renderUploadArea(uploadCtnBank);
        break;
      case 'ewallet':
        ewalletDiv.classList.remove('hidden');
        renderUploadArea(uploadCtnEwallet);
        break;
      case 'qris':
        qrisDiv.classList.remove('hidden');
        renderUploadArea(uploadCtnQris);
        break;
      case 'credit':
        creditDiv.classList.remove('hidden');
        break;
    }
    attachUploadListeners();
  }

  // 4) Fungsi attachListener ke setiap input#proofUpload yang muncul di panel
  function attachUploadListeners() {
    const proofInput = document.getElementById('proofUpload');
    const uploadPrompt = document.getElementById('uploadPrompt');
    const filePreview  = document.getElementById('filePreview');
    const fileNameEl   = document.getElementById('fileName');

    if (proofInput) {
      proofInput.addEventListener('change', e => {
        const f = e.target.files[0];
        if (f) {
          uploadPrompt.classList.add('hidden');
          filePreview.classList.remove('hidden');
          fileNameEl.textContent = f.name;
        } else {
          uploadPrompt.classList.remove('hidden');
          filePreview.classList.add('hidden');
          fileNameEl.textContent = '';
        }
      });
    }
  }

  // 5) Copy nomor rekening ke clipboard (hanya di panel “bank”)
  if (copyBtn) {
    copyBtn.addEventListener('click', () => {
      const accountNumber = '8723456789';
      navigator.clipboard.writeText(accountNumber)
        .then(() => alert("Nomor rekening disalin: " + accountNumber))
        .catch(() => alert("Gagal menyalin nomer rekening."));
    });
  }

  // 6) Countdown timer (statis 24 jam)
  let duration = 24 * 60 * 60; // 24 jam (dalam detik)
  const countdownEl = document.getElementById('countdown');
  if (countdownEl) {
    function updateTimer() {
      let hr = Math.floor(duration / 3600),
          mn = Math.floor((duration % 3600) / 60),
          sc = duration % 60;
      countdownEl.textContent =
        String(hr).padStart(2,'0') + ":" +
        String(mn).padStart(2,'0') + ":" +
        String(sc).padStart(2,'0');
      if (duration > 0) duration--;
    }
    setInterval(updateTimer, 1000);
    updateTimer();
  }
});
