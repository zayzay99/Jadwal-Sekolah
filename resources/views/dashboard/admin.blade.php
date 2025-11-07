<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Beranda Admin - Klipaa Solusi Indonesia</title>

    <!-- Styles -->
    <link rel="stylesheet" href="/css/newStyle.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" sizes="60x60" href="{{ asset('img/Klipaa Original.png') }}">

    @stack('styles')
    <style>
        /* User Profile Dropdown */
        .user-dropdown-container {
            position: relative;
        }
        .user-avatar {
            cursor: pointer;
        }
        .user-dropdown-menu {
            position: absolute;
            top: calc(100% + 15px);
            right: 0;
            background-color: var(--bg-secondary);
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            border: 1px solid var(--border-color);
            width: 240px;
            z-index: 1100;
            overflow: hidden;
            display: none;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .user-dropdown-menu.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
        .user-dropdown-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-primary);
        }
        .user-dropdown-header strong {
            display: block;
            color: var(--text-color);
            font-weight: 600;
        }
        .user-dropdown-header small {
            color: var(--text-light);
            font-size: 0.85rem;
        }
        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
        }
        .user-dropdown-item:hover {
            background-color: var(--bg-primary);
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div id="admin-backdrop" class="backdrop"></div>
    
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-brand">
            <button id="admin-menu-toggle" class="menu-toggle-btn">
                <i class="fas fa-bars"></i>
            </button>
            <h2>Admin Dashboard</h2>
        </div>
        <div class="nav-user" style="display: flex; align-items: center; gap: 20px;">
            <!-- Tahun Ajaran Selector -->
            <div class="tahun-ajaran-selector" id="tahunAjaranSelector">
                @if($tahunAjarans->isNotEmpty())
                    <span>Tahun Ajaran</span>
                    <select name="tahun_ajaran" onchange="window.location.href = '{{ url('manage/tahun-ajaran') }}/' + this.value + '/switch-active';">
                        @php
                            $activeId = session('tahun_ajaran_id');
                        @endphp
                        @foreach($tahunAjarans as $tahun)
                            <option value="{{ $tahun->id }}" {{ $tahun->id == $activeId ? 'selected' : '' }}>
                                {{ $tahun->tahun_ajaran }} {{ $tahun->semester }}
                            </option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down"></i>
                @else
                    <span>Tahun Ajaran Belum Diatur</span>
                @endif
            </div>
            <button type="button" class="btn-kelola-ta" id="openTahunAjaranModal">Kelola T.A</button>
            
            <!-- User Profile Dropdown -->
            <div class="user-dropdown-container">
                <div class="user-avatar" id="user-menu-toggle" title="Welcome, {{ Auth::guard('web')->user()->name }}">
                    <i class="fas fa-user"></i>
                </div>
                <div id="user-dropdown-menu" class="user-dropdown-menu">
                    <div class="user-dropdown-header">
                        <strong>{{ Auth::guard('web')->user()->name }}</strong>
                        <small>{{ Auth::guard('web')->user()->email }}</small>
                    </div>
                    <a href="{{ route('logout') }}" class="user-dropdown-item" onclick="showLogoutConfirmation(event)">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-layout">
        <!-- Sidebar -->
        <aside id="admin-sidebar" class="sidebar">
            <div class="sidebar-header">
            <img src="{{ asset('img/Klipaa Original.png') }}" alt="Logo Klipaa">
                  <div style="
                    margin-top: 10px;
                    font-size: 16px;
                    font-weight: bold;
                    color: #ffffffff;
                    padding: 8px 18px;
                    border-radius: 10px;
                    box-shadow: 0 2px 8px rgba(33,150,243,0.10);
                    letter-spacing: 1px;
                    display: inline-block;
                    ">
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
                </div>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i><span>Home</span>
                    </a> 
                </li>
                <li>
                    <a href="{{ route('manage.guru.index') }}" class="menu-item {{ request()->routeIs('manage.guru.*') ? 'active' : '' }}">
                        <i class="fas fa-chalkboard-teacher"></i><span>Manajemen Guru</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('manage.siswa.index') }}" class="menu-item {{ request()->routeIs('manage.siswa.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i><span>Manajemen Siswa</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('manage.kelas.index') }}" class="menu-item {{ request()->routeIs('manage.kelas.*') ? 'active' : '' }}">
                        <i class="fas fa-building"></i><span>Manajemen Kelas</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('jadwal-kategori.index') }}" class="menu-item {{ request()->routeIs('jadwal-kategori.*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i><span>Kategori Jadwal</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('manage.tabelj.index') }}" class="menu-item {{ request()->routeIs('manage.tabelj.*') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i><span>Manajemen Slot Waktu</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('jadwal.pilihKelas') }}" class="menu-item {{ request()->routeIs('jadwal.pilihKelas') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i><span>Manajemen Jadwal</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('kelas.kategori') }}" class="menu-item {{ request()->routeIs('kelas.kategori') ? 'active' : '' }}">
                        <i class="fa-solid fa-people-roof"></i><span>Lihat Kelas</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('jadwal.pilihKelasLihat') }}" class="menu-item {{ request()->routeIs('jadwal.pilihKelasLihat') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i><span>Lihat Jadwal</span>
                    </a>
                </li>
            </ul>

            <a href="{{ route('logout') }}" class="logout-btn" onclick="showLogoutConfirmation(event)">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </aside>

        <!-- Main Content -->
        <main class="content">
            @yield('content')
        </main>
    </div>

    <!-- Tahun Ajaran Management Modal -->
    <div id="tahunAjaranModal" class="modal">
        <div class="modal-content">
            <span class="modal-close-button">&times;</span>
            <div class="modal-header">
                <h1>Manajemen Tahun Ajaran</h1>
                <button type="button" class="btn btn-success" id="openCreateTahunAjaranModal">Tambah Tahun Ajaran</button>
            </div>
            <div class="modal-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tahunAjarans as $tahunAjaran)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $tahunAjaran->tahun_ajaran }}</td>
                                <td>{{ $tahunAjaran->semester }}</td>
                                <td>
                                    @if($tahunAjaran->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm edit-tahun-ajaran" 
                                        data-id="{{ $tahunAjaran->id }}" 
                                        data-tahun_ajaran="{{ $tahunAjaran->tahun_ajaran }}" 
                                        data-semester="{{ $tahunAjaran->semester }}" 
                                        data-is_active="{{ $tahunAjaran->is_active }}">Edit</button>
                                    <button type="button" class="btn btn-danger btn-sm delete-tahun-ajaran" 
                                        data-id="{{ $tahunAjaran->id }}">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Tahun Ajaran Modal -->
    <div id="createTahunAjaranModal" class="modal">
        <div class="modal-content">
            <span class="modal-close-button">&times;</span>
            <div class="modal-header">
                <h1>Tambah Tahun Ajaran Baru</h1>
            </div>
            <div class="modal-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('manage.tahun-ajaran.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="tahun_ajaran">Tahun Ajaran (format: YYYY/YYYY)</label>
                        <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" 
                            placeholder="Contoh: 2025/2026" required pattern="^\d{4}\/\d{4}$">
                    </div>
                    <div class="form-group">
                        <label for="semester">Semester</label>
                        <select class="form-control" id="semester" name="semester" required>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                    <div class="form-group form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1">
                        <label class="form-check-label" for="is_active">Langsung aktifkan tahun ajaran ini</label>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="source_tahun_ajaran_id">Salin Data dari Tahun Ajaran:</label>
                        <select class="form-control" id="source_tahun_ajaran_id" name="source_tahun_ajaran_id">
                            <option value="">-- Jangan Salin Data (Buat Kosong) --</option>
                            @foreach($tahunAjarans as $tahun)
                                <option value="{{ $tahun->id }}">{{ $tahun->tahun_ajaran }} - {{ $tahun->semester }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih tahun ajaran untuk menyalin data. Jika tidak dipilih, tahun ajaran baru akan kosong.</small>
                    </div>
                    <div class="form-group">
                        <label>Opsi untuk Tahun Ajaran Baru:</label>
                        <p class="text-muted small">Opsi berikut hanya berlaku untuk <strong>Tahun Ajaran Baru</strong> yang sedang dibuat. Data dari tahun ajaran yang Anda salin akan tetap utuh dan tidak akan berubah (tersimpan sebagai arsip).</p>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="skip_kelas_assignments" name="skip_kelas_assignments" value="1">
                            <label class="form-check-label" for="skip_kelas_assignments">
                                <strong>Kosongkan Penempatan Siswa di Kelas</strong><br>
                                <small class="text-muted">(Siswa di tahun ajaran BARU ini tidak akan dimasukkan ke kelas. Data di tahun ajaran LAMA tetap aman sebagai arsip).</small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="skip_jadwal" name="skip_jadwal" value="1">
                            <label class="form-check-label" for="skip_jadwal">
                                <strong>Kosongkan Jadwal Pelajaran</strong><br>
                                <small class="text-muted">(Jadwal pelajaran di tahun ajaran BARU akan kosong. Data jadwal di tahun ajaran LAMA tetap aman sebagai arsip).</small>
                            </label>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-close-modal="createTahunAjaranModal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Tahun Ajaran Modal -->
    <div id="editTahunAjaranModal" class="modal">
        <div class="modal-content">
            <span class="modal-close-button">&times;</span>
            <div class="modal-header">
                <h1>Edit Tahun Ajaran</h1>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="edit_tahun_ajaran">Tahun Ajaran</label>
                        <input type="text" name="tahun_ajaran" id="edit_tahun_ajaran" class="form-control" required pattern="^\d{4}\/\d{4}$">
                    </div>
                    <div class="form-group">
                        <label for="edit_semester">Semester</label>
                        <select name="semester" id="edit_semester" class="form-control" required>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" name="is_active" id="edit_is_active" class="form-check-input" value="1">
                        <label for="edit_is_active" class="form-check-label">Aktifkan Tahun Ajaran ini?</label>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <button type="button" class="btn btn-secondary" data-close-modal="editTahunAjaranModal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
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

        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.getElementById('admin-menu-toggle');
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('admin-backdrop');

            if (menuToggle && sidebar && backdrop) {
                menuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    backdrop.classList.toggle('show');
                });

                backdrop.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    backdrop.classList.remove('show');
                });
            }
        });

        // Active menu logic
        document.addEventListener('DOMContentLoaded', function () {
            var currentUrl = window.location.href;
            var sidebarLinks = document.querySelectorAll('.sidebar-menu a.menu-item');
            var bestMatch = null;

            sidebarLinks.forEach(function(link) {
                link.classList.remove('active');
            });

            if (currentUrl.includes('/jadwal/kelas')) {
                bestMatch = document.querySelector('a.menu-item[href*="/jadwal/kelas"]');
            } else if (currentUrl.includes('/jadwal/')) {
                bestMatch = document.querySelector('a.menu-item[href*="pilih-kelas"]');
            } else {
                sidebarLinks.forEach(function (link) {
                    if (link.href.includes('logout')) return;
                    if (currentUrl.startsWith(link.href)) {
                        if (!bestMatch || link.href.length > bestMatch.href.length) {
                            bestMatch = link;
                        }
                    }
                });
            }

            if (bestMatch) {
                bestMatch.classList.add('active');
            }
        });

        // Modal management
        document.addEventListener('DOMContentLoaded', function () {
            const modals = {
                main: document.getElementById('tahunAjaranModal'),
                create: document.getElementById('createTahunAjaranModal'),
                edit: document.getElementById('editTahunAjaranModal')
            };
    
            function openModal(modal) {
                if (modal) {
                    modal.style.display = 'block';
                    setTimeout(() => modal.classList.add('show'), 10);
                }
            }
    
            function closeModal(modal) {
                if (modal) {
                    modal.classList.remove('show');
                    setTimeout(() => modal.style.display = 'none', 300);
                }
            }
    
            document.getElementById('openTahunAjaranModal').addEventListener('click', () => openModal(modals.main));
            
            const createBtn = document.getElementById('openCreateTahunAjaranModal');
            if (createBtn) {
                createBtn.addEventListener('click', () => {
                    closeModal(modals.main);
                    openModal(modals.create);
                });
            }
    
            document.querySelectorAll('.modal-close-button').forEach(btn => {
                btn.addEventListener('click', function() {
                    closeModal(this.closest('.modal'));
                });
            });
    
            document.querySelectorAll('[data-close-modal]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const modalToClose = document.getElementById(this.dataset.closeModal);
                    closeModal(modalToClose);
                });
            });
    
            document.querySelectorAll('.edit-tahun-ajaran').forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const form = modals.edit.querySelector('form');
                    form.action = `/manage/tahun-ajaran/${id}`;
                    form.querySelector('#edit_tahun_ajaran').value = this.dataset.tahun_ajaran;
                    form.querySelector('#edit_semester').value = this.dataset.semester;
                    form.querySelector('#edit_is_active').checked = this.dataset.is_active == 1;
                    closeModal(modals.main);
                    openModal(modals.edit);
                });
            });
    
            document.querySelectorAll('.delete-tahun-ajaran').forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.dataset.id;
                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: "Data yang terhapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/manage/tahun-ajaran/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json().then(data => ({ status: response.status, body: data })))
                            .then(({ status, body }) => {
                                const message = body.message || 'Tidak ada pesan dari server.';
                                if (status >= 200 && status < 300) {
                                    Swal.fire({ title: 'Berhasil!', text: message, icon: 'success' })
                                    .then(() => window.location.reload());
                                } else {
                                    Swal.fire({ title: 'Gagal!', text: message, icon: 'error' });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Gagal!', 'Tidak dapat terhubung ke server atau terjadi kesalahan.', 'error');
                            });
                        }
                    });
                });
            });
    
            window.addEventListener('click', function (event) {
                if (event.target.classList.contains('modal')) {
                    closeModal(event.target);
                }
            });

        });
    </script>
    @stack('scripts')
</body>
</html>