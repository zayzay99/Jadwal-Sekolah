<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Beranda Siswa - Klipaa Solusi Indonesia</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="icon" type="image/png" href="{{ asset('img/Klipaa Original.png') }}">
</head>
<body class="bg-[#f6f9fc] text-gray-800 min-h-screen relative font-[Inter] antialiased">

@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
    $tanggal = Carbon::now()->translatedFormat('l, d F Y');
    $user = Auth::guard('siswa')->user();
    $mailToBody = "Nama Pengguna: {$user?->nama}\nNIP Pengguna: {$user?->nip}\nEmail Pengguna: {$user?->email}\n\nSebutkan masalah dan lampirkan foto (jika ada):";
@endphp

<!-- Tombol Menu -->
<div class="fixed top-5 left-5 z-50">
  <button id="menuButton"
    class="bg-[#3b7ca5]/90 backdrop-blur-md text-white px-4 py-2 md:px-5 md:py-2.5 rounded-full shadow-lg hover:bg-[#336c90] transition-all duration-300 flex items-center gap-2 text-sm md:text-base">
    <i class="fa-solid fa-bars text-lg md:text-xl"></i>
    <span class="hidden sm:inline">Menu</span>
  </button>
  <div id="menuDropdown" class="hidden mt-3 w-56 bg-[#3b7ca5]/90 backdrop-blur-md rounded-2xl shadow-lg border border-white/20 text-white overflow-hidden">
    <a href="mailto:kesyapujiatmoko@gmail.com?subject=Laporan Masalah Pengguna (siswa)&body={{ rawurlencode($mailToBody) }}"
      class="block px-5 py-3 hover:bg-white/20 transition-all duration-200">
      <i class="fa-solid fa-headset mr-2"></i> Bantuan / CS
    </a>
    <button id="logoutBtn" class="w-full text-left px-5 py-3 hover:bg-red-600 transition-all duration-200">
      <i class="fa-solid fa-right-from-bracket mr-2"></i> Keluar
    </button>
    <form id="logoutForm" action="{{ route('logout') }}" method="GET" class="hidden">@csrf</form>
  </div>
</div>

<main class="p-6 md:p-8 space-y-8">

  <!-- Header Instansi -->
  <section class="bg-white rounded-2xl shadow p-6 text-center max-w-3xl mx-auto border border-gray-100">
    <div class="flex justify-center mb-3">
      <img src="{{ asset('img/Klipaa Original.png') }}" alt="Logo Sekolah" class="w-14 h-14 object-contain">
    </div>
    <h1 class="text-2xl font-bold text-[#3b3b7c]">Klipaa Students</h1>
    <p class="text-sm text-gray-600 mt-1">{{ $tanggal }}</p>
    <div class="mt-2">
      <form action="{{ route('siswa.switch-tahun-ajaran') }}" method="POST" id="tahunAjaranForm" class="flex items-center justify-center">
        @csrf
        <label for="tahun_ajaran_id" class="mr-2 text-sm font-medium">Tahun Ajaran:</label>
        <select name="tahun_ajaran_id" id="tahun_ajaran_id" onchange="document.getElementById('tahunAjaranForm').submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5">
          @foreach($allTahunAjarans as $tahun)
            <option value="{{ $tahun->id }}" {{ $selectedTahunAjaranId == $tahun->id ? 'selected' : '' }}>
              {{ $tahun->tahun_ajaran }} {{ $tahun->semester }}
            </option>
          @endforeach
        </select>
      </form>
      @if($isViewingActiveYear)
        <span class="text-xs text-green-600 font-semibold">(Tahun Ajaran Aktif)</span>
      @else
        <span class="text-xs text-yellow-600 font-semibold">(Melihat Arsip)</span>
      @endif
    </div>
  </section>

  <!-- Profil Siswa -->
  <section class="bg-white rounded-2xl shadow p-6 max-w-3xl mx-auto border border-gray-100">
    <div class="profile-card flex flex-col items-center">
      <div class="profile-pic-container" style="cursor: pointer;" onclick="document.getElementById('profile_picture_input').click();" title="Klik untuk ganti foto">
        <div class="profile-pic" style="padding: 0; border: none; background: transparent;">
          <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('img/Default-Profile.png') }}"
            alt="Foto Profil"
            style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
        </div>
      </div>
      <form id="profile-pic-form" action="{{ route('siswa.profile.update') }}" method="POST" enctype="multipart/form-data" style="display: none;">
        @csrf
        <input type="file" id="profile_picture_input" name="profile_picture" accept="image/*" onchange="document.getElementById('profile-pic-form').submit();">
      </form>
      <h2 class="text-xl font-semibold text-[#3b7ca5] mb-1 mt-4">
        Selamat Datang, {{ $user?->nama ?? 'Siswa' }}
      </h2>
      <p class="text-gray-500 text-sm mb-5">Terima kasih sudah semangat belajar hari ini 👩‍🏫🌼</p>
      <div class="grid sm:grid-cols-2 gap-5 w-full">
        <div class="bg-[#d9eaf5] p-5 rounded-lg text-[#274c77] shadow-sm">
          <h3 class="font-semibold mb-2 flex items-center gap-2">
            <i class="fa-solid fa-user"></i> Profil
          </h3>
          <p><strong>Nama:</strong> {{ $user?->nama ?? '-' }}</p>
          <p><strong>NIS:</strong> {{ $user?->nis ?? '-' }}</p>
          <p><strong>Kelas:</strong> {{ $kelasSiswa?->nama_kelas ?? 'Tidak terdaftar di kelas' }}</p>
          <p><strong>Email:</strong> {{ $user?->email ?? '-' }}</p>
        </div>
        <div class="bg-[#f2f8fc] p-5 rounded-lg shadow-sm">
          <h3 class="font-semibold mb-2 flex items-center gap-2 text-[#3b7ca5]">
            <i class="fa-solid fa-chalkboard-user"></i> Status Siswa
          </h3>
          <p class="text-gray-600">Aktif belajar semester ini.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Jadwal Pelajaran -->
  <section id="jadwal-section" class="bg-white rounded-2xl shadow p-6 max-w-3xl mx-auto border border-gray-100">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-lg font-semibold text-[#3b7ca5] flex items-center gap-2">
        <i class="fa-solid fa-calendar-days"></i> Jadwal Pelajaran Untuk Kelas {{ $kelasSiswa?->nama_kelas ?? '-' }}
      </h3>
      <div class="flex items-center space-x-2">
        <button id="arsipBtn" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-300">
          Lihat Arsip
        </button>
        <button id="cetakJadwalBtn"
          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
          Cetak Jadwal
        </button>
      </div>
    </div>
    <div class="table-container overflow-x-auto">
      @if(isset($jadwals) && count($jadwals) > 0)
        <table class="jadwal-table w-full rounded-lg overflow-hidden shadow-md bg-white border border-[#a1b9db]">
          <thead>
            <tr class="bg-blue-200 text-gray-700">
              <th class="py-3 px-4 text-center border border-[#a1b9db]">Hari</th>
              <th class="py-3 px-4 text-center border border-[#a1b9db]">Jam</th>
              <th class="py-3 px-4 text-center border border-[#a1b9db]">Mata Pelajaran</th>
              <th class="py-3 px-4 text-center border border-[#a1b9db]">Guru</th>
            </tr>
          </thead>
          <tbody>
            @php $totalJP = 0; @endphp
            @foreach($jadwals as $hari => $jadwalHarian)
              @foreach($jadwalHarian as $index => $jadwal)
                <tr class="hover:bg-blue-50 transition">
                  @if($index === 0)
                    <td rowspan="{{ count($jadwalHarian) }}" class="py-2 px-4 font-semibold align-top border border-[#a1b9db] text-center">
                      {{ $hari }}
                    </td>
                  @endif
                  <td class="py-2 px-4 border border-[#a1b9db]">{{ $jadwal->jam }}</td>
                  @if($jadwal->kategori)
                    <td colspan="2" class="py-2 px-4 border border-[#a1b9db] text-center font-bold">{{ $jadwal->kategori->nama_kategori }}</td>
                  @else
                    <td class="py-2 px-4 border border-[#a1b9db]">{{ $jadwal->mapel }}</td>
                    <td class="py-2 px-4 border border-[#a1b9db]">{{ $jadwal->guru ? $jadwal->guru->nama : '-' }}</td>
                  @endif
                  
                  </td>
                </tr>
              @endforeach
            @endforeach
            
          </tbody>
        </table>
      @else
        <div class="empty-jadwal text-center py-8">
          <i class="fas fa-calendar-times fa-3x mb-2 text-gray-400"></i>
          <p class="text-gray-600">Belum ada jadwal untuk kelas ini.</p>
          <p class="text-sm text-gray-500">Silakan hubungi administrator jika ada kendala</p>
        </div>
      @endif
    </div>
  </section>

</main>

<!-- Modal Arsip Jadwal -->
<div id="arsipModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" style="display: none;">
  <div class="bg-white rounded-lg shadow-xl w-11/12 md:w-3/4 lg:w-1/2 max-h-[80vh] flex flex-col">
    <div class="flex justify-between items-center p-4 border-b">
      <h3 class="text-xl font-bold">Arsip Jadwal</h3>
      <button id="closeArsipModal" class="text-2xl text-gray-600 hover:text-black">&times;</button>
    </div>
    <div class="p-4 overflow-y-auto">
      <div class="mb-4">
        <label for="arsip_tahun_ajaran" class="block text-sm font-medium text-gray-700">Pilih Tahun Ajaran:</label>
        <div class="flex">
          <select name="arsip_tahun_ajaran_id" id="arsip_tahun_ajaran" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            @foreach($allTahunAjarans as $tahun)
              <option value="{{ $tahun->id }}">{{ $tahun->tahun_ajaran }} {{ $tahun->semester }}</option>
            @endforeach
          </select>
          <button id="fetchJadwalBtn" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Go</button>
        </div>
      </div>
      <div id="arsip_jadwal_content" class="mt-5"></div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Menu Dropdown
const menuButton = document.getElementById('menuButton');
const menuDropdown = document.getElementById('menuDropdown');
const logoutBtn = document.getElementById('logoutBtn');
const logoutForm = document.getElementById('logoutForm');

function openDropdown() {
  menuDropdown.classList.remove('hidden', 'animate-dropdown-close');
  menuDropdown.classList.add('flex', 'flex-col', 'animate-dropdown-open');
}

function closeDropdown() {
  menuDropdown.classList.remove('animate-dropdown-open');
  menuDropdown.classList.add('animate-dropdown-close');
  setTimeout(() => menuDropdown.classList.add('hidden'), 280);
}

menuButton.addEventListener('click', (e) => {
  e.stopPropagation();
  menuDropdown.classList.contains('hidden') ? openDropdown() : closeDropdown();
});

document.addEventListener('click', (e) => {
  if (!menuButton.contains(e.target) && !menuDropdown.contains(e.target)) {
    if (!menuDropdown.classList.contains('hidden')) closeDropdown();
  }
});

// Logout
logoutBtn.addEventListener('click', (e) => {
  e.preventDefault();
  Swal.fire({
    title: 'Yakin mau keluar?',
    text: 'Anda akan keluar dari sesi ini.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, keluar!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) logoutForm.submit();
  });
});

// Modal Arsip
const arsipBtn = document.getElementById('arsipBtn');
const arsipModal = document.getElementById('arsipModal');
const closeArsipModal = document.getElementById('closeArsipModal');

arsipBtn?.addEventListener('click', () => {
  arsipModal.style.display = 'flex';
});

closeArsipModal?.addEventListener('click', () => {
  arsipModal.style.display = 'none';
});

arsipModal?.addEventListener('click', (e) => {
  if (e.target === arsipModal) {
    arsipModal.style.display = 'none';
  }
});

// Success Messages
document.addEventListener('DOMContentLoaded', function() {
  @if(session('login_success'))
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: '{{ session('login_success') }}',
      showConfirmButton: false,
      timer: 3500,
      timerProgressBar: true
    });
  @endif

  @if(session('success'))
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: '{{ session('success') }}',
      showConfirmButton: false,
      timer: 3500,
      timerProgressBar: true
    });
  @endif

  @if($errors->any())
    Swal.fire({
      icon: 'error',
      title: 'Gagal Mengunggah',
      html: '<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
      confirmButtonColor: '#d33',
      confirmButtonText: 'Tutup'
    });
  @endif
});

// Print Jadwal
document.getElementById('cetakJadwalBtn')?.addEventListener('click', function () {
    const jadwalSection = document.getElementById('jadwal-section').cloneNode(true);
    
    const buttonsContainer = jadwalSection.querySelector('.flex.justify-between.items-center .flex.items-center.space-x-2');
    if (buttonsContainer) {
        buttonsContainer.remove();
    }

    const printWindow = window.open('', '', 'height=800,width=800');
    printWindow.document.write('<html><head><title>Cetak Jadwal</title>');
    printWindow.document.write('<script src="https://cdn.tailwindcss.com"><\/script>');
    printWindow.document.write('<style>body { padding: 2rem; } .table-container { overflow: visible !important; } table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #ddd; padding: 8px; } th { background-color: #f2f2f2; } h3 { font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; } </style>');
    printWindow.document.write('</head><body class="font-[Inter]">');
    printWindow.document.write(jadwalSection.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    setTimeout(() => {
        try {
            printWindow.print();
            printWindow.close();
        } catch (e) {
            console.error('Could not print:', e);
            printWindow.close();
        }
    }, 500);
});
</script>

<style>
@keyframes dropdownOpen {
  0% {opacity: 0; transform: translateY(-15px) scale(0.95);}
  60% {opacity: 1; transform: translateY(3px) scale(1.03);}
  100% {opacity: 1; transform: translateY(0) scale(1);}
}
@keyframes dropdownClose {
  0% {opacity: 1; transform: translateY(0) scale(1);}
  40% {opacity: 0.7; transform: translateY(-5px) scale(0.98);}
  100% {opacity: 0; transform: translateY(-10px) scale(0.9);}
}
.animate-dropdown-open { animation: dropdownOpen 0.35s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
.animate-dropdown-close { animation: dropdownClose 0.3s ease-in forwards; }
</style>
</body>
</html>