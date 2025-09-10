<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Admin - Klipaa Solusi Indonesia</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" sizes="60x60" href="{{ asset('img/Klipaa Original.png') }}">

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-brand">
            <h2>Admin Dashboard</h2>
        </div>
        <div class="nav-user">
            <span>Welcome, Admin</span>
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </nav>

    <div class="main-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>Menu</h3>
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="menu-item">
                        <i class="fas fa-home"></i><span>Home Admin</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('manage.siswa.index') }}" class="menu-item">
                        <i class="fas fa-users"></i><span>Manajemen Siswa</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('manage.guru.index') }}" class="menu-item">
                        <i class="fas fa-chalkboard-teacher"></i><span>Manajemen Guru</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('manage.kelas.index') }}" class="menu-item">
                        <i class="fas fa-building"></i><span>Manajemen Kelas</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('jadwal.pilihKelas') }}" class="menu-item">
                        <i class="fas fa-calendar-check"></i><span>Manajemen Jadwal</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('jadwal-kategori.index') }}" class="menu-item">
                        <i class="fas fa-tags"></i><span>Manajemen Kategori Jadwal</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('kelas.kategori') }}" class="menu-item">
                        <i class="fa-solid fa-people-roof"></i><span>Lihat Kelas</span>
                    </a>
                </li>


                <li>
                    <a href="{{ route('jadwal.pilihKelasLihat') }}" class="menu-item">
                        <i class="fas fa-calendar-alt"></i><span>Lihat Jadwal</span>
                    </a>
                </li>


                <li>
                    <a href="{{ route('logout') }}" class="menu-item" onclick="showLogoutConfirmation(event)">
                        <i class="fas fa-sign-out-alt"></i><span>Logout</span>
                    </a>
                </li>
            </ul>
        </aside>
        {{-- End Sidebar --}}

        <!-- Main Content -->
        <main class="content">
            @yield('content')

            <div class="content-header">
                <h1>Dashboard Overview</h1>
                <p>Kelola data guru, siswa, kelas, dan jadwal dengan mudah</p>
            </div>

            <!-- Stats Section -->
            @if (isset($guruCount))
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                        <div class="stat-value">{{ $guruCount }}</div>
                        <div class="stat-label">Guru</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <div class="stat-value">{{ $siswaCount }}</div>
                        <div class="stat-label">Siswa</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-building"></i></div>
                        <div class="stat-value">{{ $kelasCount }}</div>
                        <div class="stat-label">Kelas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                        <div class="stat-value">{{ $jadwalCount }}</div>
                        <div class="stat-label">Jadwal</div>
                    </div>
                </div>
            @endif

            <!-- Info Section -->
            <div class="info-section">
                <div class="info-box">
                    <h3>Informasi Penting</h3>
                    <p>Data sekolah selalu up-to-date. Selamat bekerja dan semoga harimu menyenangkan!</p>
                    <blockquote>"Pendidikan adalah senjata paling ampuh untuk mengubah dunia."</blockquote>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showLogoutConfirmation(event) {
            event.preventDefault();
            let link = event.currentTarget.href;

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
                    window.location.href = link;
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Notifikasi login sukses
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

            // Notifikasi aksi sukses
            @if (session('success'))
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
                Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
            @endif

            // Error login
            @if ($errors->has('login'))
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: '{{ $errors->first('login') }}',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            @endif
        });
    </script>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var currentUrl = window.location.href;
            var sidebarLinks = document.querySelectorAll('.sidebar-menu a.menu-item');
            var bestMatch = null;

            sidebarLinks.forEach(function (link) {
                // Skip the logout link
                if (link.href.includes('logout')) {
                    return;
                }

                if (currentUrl.startsWith(link.href)) {
                    if (!bestMatch || link.href.length > bestMatch.href.length) {
                        bestMatch = link;
                    }
                }
            });

            if (bestMatch) {
                // First, remove active class from all links to be safe
                sidebarLinks.forEach(function(link) {
                    link.classList.remove('active');
                });
                
                bestMatch.classList.add('active');
            }
        });
    </script>
</body>
</html>
