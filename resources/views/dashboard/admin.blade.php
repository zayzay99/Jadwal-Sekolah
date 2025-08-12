<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                <li><a href="{{ route('admin.dashboard') }}" class="menu-item active">
                        <i class="fas fa-home"></i>
                        <span>Home Admin</span>
                    </a></li>
                <li><a href="{{ route('jadwal.pilihKelasLihat') }}" class="menu-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Jadwal</span>
                    </a></li>
                <li><a href="{{ route('jadwal.pilihKelas') }}" class="menu-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>Manajemen Jadwal</span>
                    </a></li>
                <li><a href="{{ route('manage.guru.index') }}" class="menu-item">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Manajemen Guru</span>
                    </a></li>
                <li><a href="{{ route('manage.siswa.index') }}" class="menu-item">
                        <i class="fas fa-users"></i>
                        <span>Manajemen Siswa</span>
                    </a></li>
                <li><a href="{{ route('manage.kelas.index') }}" class="menu-item">
                        <i class="fas fa-building"></i>
                        <span>Manajemen Kelas</span>
                    </a></li>
                <li><a href="{{ route('logout') }}" class="menu-item">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a></li>
            </ul>
        </aside>

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
                        <div class="stat-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="stat-value">{{ $guruCount }}</div>
                        <div class="stat-label">Guru</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-value">{{ $siswaCount }}</div>
                        <div class="stat-label">Siswa</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-value">{{ $kelasCount }}</div>
                        <div class="stat-label">Kelas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
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
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
