<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Beranda Admin - Klipaa Solusi Indonesia</title>

    <!-- Styles -->
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" sizes="60x60" href="{{ asset('img/Klipaa Original.png') }}">

    @stack('styles')
    <style>
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            opacity: 0; /* Start invisible */
            transition: opacity 0.3s ease-out; /* Fade in/out */
        }

        .modal.show {
            opacity: 1;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto; /* 10% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
            border-radius: 8px;
            position: relative;
            transform: translateY(-50px); /* Start slightly above */
            transition: opacity 0.3s ease-out, transform 0.3s ease-out; /* Slide and fade */
        }

        .modal.show .modal-content {
            transform: translateY(0); /* Slide to original position */
        }

        .modal-close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .modal-close-button:hover,
        .modal-close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div id="admin-backdrop" class="backdrop"></div>
    <!-- Navbar -->
    <nav class="navbar" style="overflow: visible; z-index: 1001;">
        <div class="nav-brand">
            <button id="admin-menu-toggle" class="menu-toggle-btn">
                <i class="fas fa-bars"></i>
            </button>
            <h2>Admin Dashboard</h2>
        </div>
        <div class="nav-user">
            <!-- User info moved to the right for better alignment -->
            <span>Welcome,  {{ Auth::guard('web')->user()->name }}</span>
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </nav>

    <div class="main-layout">
        <!-- Sidebar -->
        <aside id="admin-sidebar" class="sidebar">
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
                    <a href="{{ route('manage.kelas.index') }}" class="menu-item">
                        <i class="fas fa-building"></i><span>Manajemen Kelas</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('manage.tabelj.index') }}" class="menu-item">
                        <i class="fas fa-clock"></i><span>Manajemen Slot Waktu</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('manage.guru.index') }}" class="menu-item">
                        <i class="fas fa-chalkboard-teacher"></i><span>Manajemen Guru</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('jadwal-kategori.index') }}" class="menu-item">
                        <i class="fas fa-tags"></i><span>Manajemen Kategori Jadwal</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('jadwal.pilihKelas') }}" class="menu-item">
                        <i class="fas fa-calendar-check"></i><span>Manajemen Jadwal</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('manage.siswa.index') }}" class="menu-item">
                        <i class="fas fa-users"></i><span>Manajemen Siswa</span>
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
                <div style="display: flex; align-items: center; gap: 10px;">
                    @if($tahunAjarans->isNotEmpty())
                        <select name="tahun_ajaran" class="form-control" onchange="window.location.href = '{{ url('manage/tahun-ajaran') }}/' + this.value + '/switch-active';" style="height: 38px; min-width: 220px;" title="Ganti Tahun Ajaran Aktif">
                            @foreach($tahunAjarans as $tahun)
                                <option value="{{ $tahun->id }}" {{ $tahun->is_active ? 'selected' : '' }}>
                                    {{ $tahun->tahun_ajaran }} {{ $tahun->semester }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <span style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: .375rem .75rem; border-radius: .25rem;">
                            Tahun Ajaran Belum Diatur
                        </span>
                    @endif
                    <button type="button" class="btn btn-primary btn-tiny" id="openTahunAjaranModal" title="Kelola Tahun Ajaran">
                        <i class="fas fa-cog"></i> Kelola T.A
                    </button>
                </div>
            </div>
            <!--
<div class="profile-card">
 <div class="profile-info">
              <p><strong>Nama </strong>: {{ Auth::guard('web')->user()->name }}</p>
              <p><strong>NIP </strong>: {{ Auth::guard('web')->user()->nip }}</p>
              <p><strong>Pengampu Pelajaran</strong>: {{ Auth::guard('web')->user()->pelajaran?? '-' }}</p>
              <p><strong>E-Mail</strong>: {{ Auth::guard('web')->user()->email }}</p>
            </div>
</div>-->
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

    <script>
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
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var currentUrl = window.location.href;
            var sidebarLinks = document.querySelectorAll('.sidebar-menu a.menu-item');
            var bestMatch = null;

            // Hapus kelas aktif dari semua link terlebih dahulu
            sidebarLinks.forEach(function(link) {
                link.classList.remove('active');
            });

            // Logika baru yang lebih spesifik
            if (currentUrl.includes('/jadwal/kelas')) {
                // Case 1: URL untuk "Lihat Jadwal"
                bestMatch = document.querySelector('a.menu-item[href*="/jadwal/kelas"]');
            } else if (currentUrl.includes('/jadwal/')) {
                // Case 2: URL untuk "Manajemen Jadwal" (create, edit, etc.)
                bestMatch = document.querySelector('a.menu-item[href*="pilih-kelas"]');
            } else {
                // Case 3: Fallback untuk halaman lainnya
                sidebarLinks.forEach(function (link) {
                    if (link.href.includes('logout')) {
                        return;
                    }
                    if (currentUrl.startsWith(link.href)) {
                        if (!bestMatch || link.href.length > bestMatch.href.length) {
                            bestMatch = link;
                        }
                    }
                });
            }

            // Tambahkan kelas aktif ke link yang cocok
            if (bestMatch) {
                bestMatch.classList.add('active');
            }
        });
    </script>
    @stack('scripts')

    <!-- Tahun Ajaran Management Modal -->
    <div id="tahunAjaranModal" class="modal">
        <div class="modal-content">
            <span class="modal-close-button">&times;</span>
            <div class="content-header">
                <h1>Manajemen Tahun Ajaran</h1>
                <button type="button" class="btn btn-success" id="openCreateTahunAjaranModal">Tambah Tahun Ajaran</button>
            </div>
            <div class="content-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
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
                                    <button type="button" class="btn btn-info btn-sm edit-tahun-ajaran" data-id="{{ $tahunAjaran->id }}" data-tahun_ajaran="{{ $tahunAjaran->tahun_ajaran }}" data-semester="{{ $tahunAjaran->semester }}" data-is_active="{{ $tahunAjaran->is_active }}">Edit</button>
                                    <button type="button" class="btn btn-danger btn-sm delete-tahun-ajaran" data-id="{{ $tahunAjaran->id }}">Delete</button>
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
            <div class="content-header">
                <h1>Tambah Tahun Ajaran Baru</h1>
            </div>
            <div class="content-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                <li style="list-style-type: disc; margin-left: 20px;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('manage.tahun-ajaran.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="tahun_ajaran">Tahun Ajaran (format: YYYY/YYYY)</label>
                        <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" placeholder="Contoh: 2025/2026" required pattern="^\d{4}\/\d{4}$">
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
                        <p class="text-muted small" style="font-size: 0.85rem; margin-bottom: 10px;">Opsi berikut hanya berlaku untuk <strong>Tahun Ajaran Baru</strong> yang sedang dibuat. Data dari tahun ajaran yang Anda salin akan tetap utuh dan tidak akan berubah (tersimpan sebagai arsip).</p>
                        <div class="form-check">
                        <div class="form-check" style="margin-bottom: 10px;">
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
                
                    <div style="margin-top: 20px;">
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
            <div class="content-header">
                <h1>Edit Tahun Ajaran</h1>
            </div>
            <div class="content-body">
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
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" data-close-modal="editTahunAjaranModal">Batal</button>
                </form>
            </div>
        </div>
    </div>

    <script>
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
    
            // Open main modal
            document.getElementById('openTahunAjaranModal').addEventListener('click', () => openModal(modals.main));
    
            // Open create modal from main modal
            document.getElementById('openCreateTahunAjaranModal').addEventListener('click', () => {
                closeModal(modals.main);
                openModal(modals.create);
            });
    
            // Generic close buttons (X)
            document.querySelectorAll('.modal-close-button').forEach(btn => {
                btn.addEventListener('click', function() {
                    closeModal(this.closest('.modal'));
                });
            });
    
            // Generic cancel buttons
            document.querySelectorAll('[data-close-modal]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const modalToClose = document.getElementById(this.dataset.closeModal);
                    closeModal(modalToClose);
                });
            });
    
            // Edit button listeners
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
    
            // Delete button listeners
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
    
            // Close modal by clicking on the background
            window.addEventListener('click', function (event) {
                if (event.target.classList.contains('modal')) {
                    closeModal(event.target);
                }
            });
        });
    </script>
</body>
</html>