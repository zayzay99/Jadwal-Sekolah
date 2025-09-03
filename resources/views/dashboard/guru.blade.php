<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Beranda Guru - Klipaa Solusi Indonesia</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="{{ asset('css/guru.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
   <link rel="icon" type="image/png" href="{{ asset('img/Tut Wuri Handayani.png') }}">
</head>


<body class="bg-[#bdd7ee] font-sans text-gray-800">
  <div class="app-container">
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" onclick="toggleMobileMenu()" aria-label="Toggle Menu">
      <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" onclick="closeMobileMenu()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <!-- Logo -->
      <div class="logo-container">
  <div class="logo">
    <img src="{{ asset('img/Tut Wuri Handayani.png') }}" alt="Tut Wuri Handayani" class="logo-image" />
  </div>
</div>

      <!-- Menu -->
      <!-- Menu -->
<nav class="menu">
  <div class="menu-box">
    <a href="{{ route('guru.dashboard') }}" class="menu-item {{ Request::routeIs('guru.dashboard') ? 'active' : '' }}">Dashboard</a>
    
    <div class="cs-btn">
      <img src="/img/CS.svg" alt="Customer Service" />
    </div>
    
    <button class="logout-btn" data-url="{{ route('logout') }}" onclick="showLogoutConfirmation(event)">
      Keluar
    </button>
  </div>
</nav>
    </aside>

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
            <div class="profile-pic-container" style="cursor: pointer;" onclick="document.getElementById('profile_picture_input').click();" title="Klik untuk ganti foto">
                <div class="profile-pic" style="padding: 0; border: none; background: transparent;">
<img src="{{ $guru->profile_picture ? asset('storage/' . $guru->profile_picture) : asset('storage/Default-Profile.png') }}" alt="Foto Profil" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                </div>
            </div>
            <form id="profile-pic-form" action="{{ route('guru.profile.update') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                @csrf
                <input type="file" id="profile_picture_input" name="profile_picture" accept="image/*" onchange="document.getElementById('profile-pic-form').submit();">
            </form>
            <div class="profile-info">
              <p><strong>Nama guru</strong>: {{ Auth::guard('guru')->user()->nama }}</p>
              <p><strong>NIP guru</strong>: {{ Auth::guard('guru')->user()->nip }}</p>
              <p><strong>Pengampu Pelajaran</strong>: {{ Auth::guard('guru')->user()->pengampu }}</p>
              <p><strong>E-Mail</strong>: {{ Auth::guard('guru')->user()->email }}</p>
            </div>
          </div>

          <!-- Jadwal Mengajar -->
          @isset($jadwals)
          <div class="jadwal-section">
            <div class="flex justify-between items-center mb-4">
              <h2 class="jadwal-title text-xl font-bold">Jadwal Mengajar</h2>
              <a href="{{ route('guru.jadwal.cetak') }}" target="_blank" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                Cetak Jadwal
              </a>
            </div>
            <div class="table-container">
              @if($jadwals && $jadwals->count() > 0)
                <table class="jadwal-table w-full rounded-lg overflow-hidden shadow-md bg-white border border-[#a1b9db]">
  <thead>
    <tr class="bg-blue-200 text-gray-700">
      <th class="py-3 px-4 text-left border border-[#a1b9db]">Hari</th>
      <th class="py-3 px-4 text-left border border-[#a1b9db]">Mata Pelajaran</th>
      <th class="py-3 px-4 text-left border border-[#a1b9db]">Kelas</th>
      <th class="py-3 px-4 text-left border border-[#a1b9db]">Jam</th>
    </tr>
  </thead>
  <tbody>
    @foreach($jadwals->groupBy('hari') as $hari => $jadwalHarian)
      @foreach($jadwalHarian as $index => $jadwal)
      <tr class="hover:bg-blue-50 transition">
        @if($index === 0)
          <td rowspan="{{ count($jadwalHarian) }}" class="py-2 px-4 font-semibold align-top border border-[#a1b9db] text-center">
            {{ $hari }}
          </td>
        @endif
        <td class="py-2 px-4 border border-[#a1b9db]">{{ $jadwal->mapel }}</td>
        <td class="py-2 px-4 border border-[#a1b9db]">{{ $jadwal->kelas ? $jadwal->kelas->nama_kelas : '-' }}</td>
        <td class="py-2 px-4 border border-[#a1b9db]">{{ $jadwal->jam }}</td>
      </tr>
      @endforeach
    @endforeach
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
          @if(session('success'))
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
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
  <script>
    function toggleMobileMenu() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.querySelector('.sidebar-overlay');
      
      sidebar.classList.toggle('open');
      overlay.classList.toggle('active');
    }

    function closeMobileMenu() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.querySelector('.sidebar-overlay');
      
      sidebar.classList.remove('open');
      overlay.classList.remove('active');
    }

    // Close mobile menu when clicking on menu items
    document.querySelectorAll('.menu-item, .logout-btn').forEach(item => {
      item.addEventListener('click', closeMobileMenu);
    });

    // Close mobile menu on window resize if desktop
    window.addEventListener('resize', function() {
      if (window.innerWidth > 768) {
        closeMobileMenu();
      }
    });

    function showLogoutConfirmation(event) {
        event.preventDefault();
        let url = event.currentTarget.getAttribute('data-url');
        Swal.fire({
            title: 'Yakin akan keluar?',
            text: "Anda akan keluar dari sesi ini.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, keluar!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

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
  </script>
</body>
</html>