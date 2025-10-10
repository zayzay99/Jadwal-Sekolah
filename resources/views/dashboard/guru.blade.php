<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Beranda Guru - Klipaa Solusi Indonesia</title>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

  <!-- SweetAlert -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <link rel="icon" type="image/png" href="{{ asset('img/Klipaa Original.png') }}">
</head>
<body class="bg-[#f6f9fc] text-gray-800 min-h-screen relative font-[Inter] antialiased">

@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
    $tanggal = Carbon::now()->translatedFormat('l, d F Y');
    $user = Auth::guard('guru')->user();
    $mailToBody = "Nama Pengguna: {$user?->nama}\nNIP Pengguna: {$user?->nip}\nEmail Pengguna: {$user?->email}\n\nSebutkan masalah dan lampirkan foto (jika ada):";
@endphp

<!-- 🔹 Tombol Menu -->
<div class="fixed top-5 left-5 z-50">
  <button id="menuButton"
    class="bg-[#3b7ca5]/90 backdrop-blur-md text-white px-4 py-2 md:px-5 md:py-2.5 rounded-full shadow-lg hover:bg-[#336c90] transition-all duration-300 flex items-center gap-2 text-sm md:text-base">
    <i class="fa-solid fa-bars text-lg md:text-xl"></i>
    <span class="hidden sm:inline">Menu</span>
  </button>

  <div id="menuDropdown" class="hidden mt-3 w-56 bg-[#3b7ca5]/90 backdrop-blur-md rounded-2xl shadow-lg border border-white/20 text-white overflow-hidden">

    <a href="mailto:kesyapujiatmoko@gmail.com?subject=Laporan Masalah Pengguna (Guru)&body={{ rawurlencode($mailToBody) }}"
      class="block px-5 py-3 hover:bg-white/20 transition-all duration-200">
      <i class="fa-solid fa-headset mr-2"></i> Bantuan / CS
    </a>
    
    <button id="logoutBtn" class="w-full text-left px-5 py-3 hover:bg-red-600 transition-all duration-200">
      <i class="fa-solid fa-right-from-bracket mr-2"></i> Keluar
    </button>
    <form id="logoutForm" action="{{ route('logout') }}" method="GET" class="hidden">@csrf</form>
  </div>
</div>

<!-- 🌟 KONTEN UTAMA -->
<main class="p-6 md:p-8 space-y-8">

<<<<<<< HEAD
  <!-- 🏫 INSTANSI -->
  <section class="bg-white rounded-2xl shadow p-6 text-center max-w-3xl mx-auto border border-gray-100">
    <div class="flex justify-center mb-3">
      <img src="{{ asset('img/Klipaa Original.png') }}" alt="Logo Sekolah" class="w-14 h-14 object-contain">
=======
        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                Selamat datang di Meja guru <span class="user-name">{{ Auth::guard('guru')->user()->nama }}</span> 🎉🎉
            </header>

            <!-- Content -->
            <section class="content-section">
                <div class="content-box">
                    <!-- Greeting Card -->
                    <div class="greeting-card">
                        <p><strong>Bagaimana kabarnya hari ini?</strong></p>
                        <p>Tetap semangat mengajar anak-anak ya...</p>
                    </div>

                    <!-- Profile Card -->
                    <div class="profile-card">
                        <div class="profile-pic-container" style="cursor: pointer;"
                            onclick="document.getElementById('profile_picture_input').click();"
                            title="Klik untuk ganti foto">
                            <div class="profile-pic" style="padding: 0; border: none; background: transparent;">
                                <img src="{{ $guru->profile_picture ? asset('storage/' . $guru->profile_picture) : asset('storage/Default-Profile.png') }}"
                                    alt="Foto Profil"
                                    style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            </div>
                        </div>
                        <form id="profile-pic-form" action="{{ route('guru.profile.update') }}" method="POST"
                            enctype="multipart/form-data" style="display: none;">
                            @csrf
                            <input type="file" id="profile_picture_input" name="profile_picture" accept="image/*"
                                onchange="document.getElementById('profile-pic-form').submit();">
                        </form>
                        <div class="profile-info">
                            <p><strong>Nama guru</strong>: {{ Auth::guard('guru')->user()->nama }}</p>
                            <p><strong>NIP guru</strong>: {{ Auth::guard('guru')->user()->nip }}</p>
                            <p><strong>Pengampu Pelajaran</strong>: {{ Auth::guard('guru')->user()->pengampu }}</p>
                            <p><strong>E-Mail</strong>: {{ Auth::guard('guru')->user()->email }}</p>
                            <div class="mt-2">
                                <form action="{{ route('guru.switch-tahun-ajaran') }}" method="POST" id="tahunAjaranForm" class="flex items-center">
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
                        </div>
                    </div>

                    <!-- Jadwal Mengajar -->
                    @isset($jadwals)
                        <div class="jadwal-section">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="jadwal-title text-xl font-bold">Jadwal Mengajar</h2>
                                <div class="flex items-center space-x-2">
                                    <button id="openArsipModal" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                                        Lihat Arsip
                                    </button>
                                    <a href="{{ route('guru.jadwal.cetak') }}" target="_blank"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                                        Cetak Jadwal
                                    </a>
                                </div>
                            </div>
                            <div class="table-container">
                                @if ($jadwals && $jadwals->count() > 0)
                                    <table
                                        class="jadwal-table w-full rounded-lg overflow-hidden shadow-md bg-white border border-[#a1b9db]">
                                        <thead>
                                            <tr class="bg-blue-200 text-gray-700">
                                                <th class="py-3 px-4 text-center border border-[#a1b9db]">Hari</th>
                                                <th class="py-3 px-4 text-center border border-[#a1b9db]">Mata Pelajaran</th>
                                                <th class="py-3 px-4 text-center border border-[#a1b9db]">Kelas</th>
                                                <th class="py-3 px-4 text-center border border-[#a1b9db]">Jam</th>
                                                <th class="py-3 px-4 text-center border border-[#a1b9db]">Durasi (JP)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalJP = 0;
                                            @endphp
                                            @foreach ($jadwals->groupBy('hari') as $hari => $jadwalHarian)
                                                @foreach ($jadwalHarian as $index => $jadwal)
                                                    <tr class="hover:bg-blue-50 transition">
                                                        @if ($index === 0)
                                                            <td rowspan="{{ count($jadwalHarian) }}"
                                                                class="py-2 px-4 font-semibold align-top border border-[#a1b9db] text-center">
                                                                {{ $hari }}
                                                            </td>
                                                        @endif
                                                        @if($jadwal->kategori)
                                                            <td colspan="2" style="text-align: center; font-weight: bold;">{{ $jadwal->kategori->nama_kategori }}</td>
                                                        @else
                                                            <td class="py-2 px-4 border border-[#a1b9db]">
                                                                {{ $jadwal->mapel }}</td>
                                                                <td class="py-2 px-4 border border-[#a1b9db]">
                                                                  {{ $jadwal->kelas ? $jadwal->kelas->nama_kelas : '-' }}
                                                                </td>
                                                        @endif
                                                        <td class="py-2 px-4 border border-[#a1b9db]">
                                                            {{ $jadwal->jam }}
                                                        </td>
                                                        <td class="py-2 px-4 border border-[#a1b9db] text-center">
                                                            @php
                                                                $jamParts = explode(' - ', $jadwal->jam);
                                                                if (count($jamParts) == 2) {
                                                                    $jamMulai = \Carbon\Carbon::parse($jamParts[0]);
                                                                    $jamSelesai = \Carbon\Carbon::parse($jamParts[1]);
                                                                    $durasiMenit = $jamSelesai->diffInMinutes($jamMulai);
                                                                    $jp = floor($durasiMenit / 35); // Asumsi 1 JP = 35 menit
                                                                    $totalJP += $jp;
                                                                    echo $jp;
                                                                } else {
                                                                    echo '-';
                                                                }
                                                            @endphp
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                            <tr class="bg-gray-100 font-bold">
                                                <td colspan="4" class="py-2 px-4 text-right border border-[#a1b9db]">Total Jam Pelajaran (JP) per Minggu:</td>
                                                <td class="py-2 px-4 text-center border border-[#a1b9db]">{{ $totalJP }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @else
                                    <div class="empty-jadwal">
                                        <i class="fas fa-calendar-times fa-3x"></i>
                                        <p>Belum ada jadwal mengajar untuk hari ini.</p>
                                        <p class="small-text">Silakan hubungi administrator jika ada kendala</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endisset

                    <!-- Tambahkan ini untuk menampilkan alert -->
                    @if (session('success'))
                        <script>
                            Swal.fire({
                                position: "top-end",
                                icon: "success",
                                title: "{{ session('success') }}",
                                showConfirmButton: false,
                                timer: 1500
                            });
                        </script>
                    @endif
                </div>
            </section>
        </main>
>>>>>>> c993933c604b2457a3fed0356510e1a526ac4def
    </div>
    <h1 class="text-2xl font-bold text-[#3b3b7c]">Klipaa Students</h1>
    <p class="text-sm font-semibold text-gray-700 mt-1">
      TP. <span class="text-[#3b3b7c]">2025–2026</span>
      <a href="#" class="text-blue-500 hover:underline ml-1">Ganti</a>
    </p>
    <p class="text-sm text-gray-600 mt-1">
      {{ $tanggal }} | Semester 1 | TENGAH SEMESTER GANJIL
    </p>
  </section>
  
  <!-- 👩‍🏫 PROFIL -->
  <section class="bg-white rounded-2xl shadow p-6 max-w-3xl mx-auto border border-gray-100">
    <div class="flex flex-col items-center">
      <div class="relative mb-4">
        <div class="cursor-pointer group" onclick="document.getElementById('profile_picture_input').click();" title="Klik untuk ganti foto">
          <img src="{{ $guru->profile_picture ? asset('storage/' . $guru->profile_picture) : asset('storage/Default-Profile.png') }}"
               alt="Foto Profil"
               class="w-28 h-28 rounded-full object-cover border-4 border-[#3b7ca5]/20 shadow-md group-hover:scale-105 transition duration-300">
          <div class="absolute inset-0 rounded-full bg-black/20 opacity-0 group-hover:opacity-100 transition"></div>
        </div>
        <form id="profile-pic-form" action="{{ route('guru.profile.update') }}" method="POST" enctype="multipart/form-data" class="hidden">
          @csrf
          <input type="file" id="profile_picture_input" name="profile_picture" accept="image/*" onchange="document.getElementById('profile-pic-form').submit();">
        </form>
      </div>

      <h2 class="text-xl font-semibold text-[#3b7ca5] mb-1">
        Selamat Datang, {{ $user?->nama ?? 'Guru' }}
      </h2>
      <p class="text-gray-500 text-sm mb-5">Terima kasih sudah mengajar hari ini 👩‍🏫🌼</p>

      <div class="grid sm:grid-cols-2 gap-5 w-full">
        <div class="bg-[#d9eaf5] p-5 rounded-lg text-[#274c77] shadow-sm">
          <h3 class="font-semibold mb-2 flex items-center gap-2">
            <i class="fa-solid fa-user"></i> Profil
          </h3>
          <p><strong>Nama:</strong> {{ $user?->nama ?? '-' }}</p>
          <p><strong>NIP:</strong> {{ $user?->nip ?? '-' }}</p>
          <p><strong>Pengampu:</strong> {{ $user?->pengampu ?? '-' }}</p>
          <p><strong>Email:</strong> {{ $user?->email ?? '-' }}</p>
        </div>

        <div class="bg-[#f2f8fc] p-5 rounded-lg shadow-sm">
          <h3 class="font-semibold mb-2 flex items-center gap-2 text-[#3b7ca5]">
            <i class="fa-solid fa-chalkboard-user"></i> Status Guru
          </h3>
          <p class="text-gray-600">Aktif mengajar semester ini.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- 📅 JADWAL -->
  <section class="bg-white rounded-2xl shadow p-6 max-w-3xl mx-auto border border-gray-100">
    <h3 class="text-lg font-semibold text-[#3b7ca5] mb-4 flex items-center gap-2">
      <i class="fa-solid fa-calendar-days"></i> Jadwal Mengajar Hari Ini
    </h3>
    <p class="text-gray-700">Untuk melihat jadwal lengkap, silakan buka menu <strong>Jadwal</strong> di sidebar 📚</p>
  </section>
</main>

<!-- SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const menuButton = document.getElementById('menuButton');
const menuDropdown = document.getElementById('menuDropdown');
const logoutBtn = document.getElementById('logoutBtn');
const logoutForm = document.getElementById('logoutForm');

// Animasi dropdown
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

// ✅ Alert login sukses
window.onload = function() {
  @if (session('login_success'))
    Swal.fire({
      icon: 'success',
      title: 'Selamat Datang!',
      text: '{{ session('login_success') }}',
      timer: 2000,
      showConfirmButton: false,
      background: '#ffffff',
      color: '#274c77',
      toast: true,
      position: 'top-end',
    });
  @endif
};
</script>

<!-- 🌈 Animasi CSS -->
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
