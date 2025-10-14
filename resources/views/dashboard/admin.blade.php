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
        <div class="nav-user">
            <span>Welcome, {{ Auth::guard('web')->user()->name }}</span>
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </nav>

    <div class="main-layout">
        <!-- Sidebar with Fixed Active State Logic -->
        <aside id="admin-sidebar" class="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('img/Klipaa Original.png') }}" alt="Logo Klipaa">
            </div>

            @php
                use Illuminate\Support\Str;
                $currentRoute = request()->route()->getName();
                $currentPath = request()->path();
            @endphp
            
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="{{ $currentRoute === 'admin.dashboard' ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('manage.kelas.index') }}" 
                       class="{{ (Str::startsWith($currentRoute, 'manage.kelas.') || Str::contains($currentPath, 'manage/kelas')) && !Str::contains($currentPath, 'tambah-kelas-baru') ? 'active' : '' }}">
                        <i class="fas fa-building"></i>
                        <span>Manajemen Kelas</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('manage.tabelj.index') }}" 
                       class="{{ Str::startsWith($currentRoute, 'manage.tabelj.') || Str::contains($currentPath, 'manage/tabelj') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i>
                        <span>Manajemen Slot Waktu</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('jadwal-kategori.index') }}" 
                       class="{{ Str::startsWith($currentRoute, 'jadwal-kategori.') || Str::contains($currentPath, 'jadwal-kategori') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i>
                        <span>Kategori Jadwal</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('manage.guru.index') }}" 
                       class="{{ Str::startsWith($currentRoute, 'manage.guru.') || Str::contains($currentPath, 'manage/guru') ? 'active' : '' }}">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Manajemen Guru</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('jadwal.pilihKelas') }}" 
                       class="{{ (in_array($currentRoute, ['jadwal.pilihKelas', 'jadwal.pilihSubKelas', 'jadwal.create', 'jadwal.edit', 'jadwal.store', 'jadwal.update']) || (Str::contains($currentPath, 'jadwal') && !Str::contains($currentPath, 'jadwal-kategori') && !Str::startsWith($currentPath, 'jadwal/kelas'))) ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Manajemen Jadwal</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('manage.siswa.index') }}" 
                       class="{{ Str::startsWith($currentRoute, 'manage.siswa.') || Str::contains($currentPath, 'manage/siswa') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Manajemen Siswa</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('kelas.kategori') }}" 
                       class="{{ in_array($currentRoute, ['kelas.kategori', 'kelas.show', 'kelas.detail']) || (Str::startsWith($currentPath, 'kelas/') && !Str::contains($currentPath, 'manage/kelas')) ? 'active' : '' }}">
                        <i class="fa-solid fa-people-roof"></i>
                        <span>Lihat Kelas</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('jadwal.pilihKelasLihat') }}" 
                       class="{{ Str::startsWith($currentPath, 'jadwal/kelas') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Lihat Jadwal</span>
                    </a>
                </li>
            </ul>

            <a href="{{ route('logout') }}" class="logout-btn" onclick="showLogoutConfirmation(event)">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <!-- Header: Tahun Ajaran + Kelola T.A -->
            @if ($currentRoute === 'admin.dashboard')
            <div class="content-header">
                <h1>Dashboard Overview</h1>
                <div style="display: flex; align-items: center; gap: 10px;">
                    @if($tahunAjarans->isNotEmpty())
                        <div class="tahun-ajaran-selector">
                            <select name="tahun_ajaran" onchange="window.location.href = '{{ url('manage/tahun-ajaran') }}/' + this.value + '/switch-active';">
                                @foreach($tahunAjarans as $tahun)
                                    <option value="{{ $tahun->id }}" {{ $tahun->is_active ? 'selected' : '' }}>
                                        {{ $tahun->tahun_ajaran }} {{ $tahun->semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <span style="color: #721c24; background-color: #f8d7da; padding: .375rem .75rem; border-radius: .25rem;">
                            Tahun Ajaran Belum Diatur
                        </span>
                    @endif
                    <button type="button" class="btn-kelola-ta" id="openTahunAjaranModal">Kelola T.A</button>
                </div>
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
            @endif

            <!-- CONTENT DARI CHILD VIEW AKAN MUNCUL DI SINI -->
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
// ==================== SIDEBAR TOGGLE - MOBILE & TABLET ====================
document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.getElementById('admin-menu-toggle');
    const sidebar = document.getElementById('admin-sidebar');
    const backdrop = document.getElementById('admin-backdrop');
    const body = document.body;

    function openSidebar() {
        if (sidebar && backdrop) {
            sidebar.classList.add('show');
            backdrop.classList.add('show');
            body.classList.add('sidebar-open');
        }
    }

    function closeSidebar() {
        if (sidebar && backdrop) {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
            body.classList.remove('sidebar-open');
        }
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            if (sidebar && sidebar.classList.contains('show')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', function() {
            closeSidebar();
        });
    }

    const menuItems = document.querySelectorAll('.sidebar-menu a');
    if (menuItems.length > 0) {
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                if (window.innerWidth <= 1024) {
                    setTimeout(closeSidebar, 150);
                }
            });
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('show')) {
            closeSidebar();
        }
    });
});

// ==================== MODAL MANAGEMENT ====================
document.addEventListener('DOMContentLoaded', function () {
    const modals = {
        main: document.getElementById('tahunAjaranModal'),
        create: document.getElementById('createTahunAjaranModal'),
        edit: document.getElementById('editTahunAjaranModal')
    };

    function openModal(modal) {
        if (modal) {
            modal.style.display = 'block';
            modal.offsetHeight;
            setTimeout(() => modal.classList.add('show'), 10);
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modal) {
        if (modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }, 300);
        }
    }

    const openTahunAjaranBtn = document.getElementById('openTahunAjaranModal');
    if (openTahunAjaranBtn) {
        openTahunAjaranBtn.addEventListener('click', () => openModal(modals.main));
    }

    const createBtn = document.getElementById('openCreateTahunAjaranModal');
    if (createBtn) {
        createBtn.addEventListener('click', () => {
            closeModal(modals.main);
            setTimeout(() => openModal(modals.create), 350);
        });
    }

    document.querySelectorAll('.modal-close-button').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            closeModal(modal);
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach(btn => {
        btn.addEventListener('click', function() {
            const modalId = this.dataset.closeModal;
            const modalToClose = document.getElementById(modalId);
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
            setTimeout(() => openModal(modals.edit), 350);
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
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

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
                            Swal.fire({
                                title: 'Berhasil!',
                                text: message,
                                icon: 'success',
                                confirmButtonColor: '#11998e'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: message,
                                icon: 'error',
                                confirmButtonColor: '#11998e'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Tidak dapat terhubung ke server atau terjadi kesalahan.',
                            icon: 'error',
                            confirmButtonColor: '#11998e'
                        });
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

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            Object.values(modals).forEach(modal => {
                if (modal && modal.classList.contains('show')) {
                    closeModal(modal);
                }
            });
        }
    });
});

// ==================== LOGOUT CONFIRMATION ====================
function showLogoutConfirmation(event) {
    event.preventDefault();
    const link = event.currentTarget.href;
    
    Swal.fire({
        title: 'Yakin akan keluar?',
        text: "Anda akan keluar dari sesi ini.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#11998e',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, keluar!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = link;
        }
    });
}

// ==================== FORM VALIDATION ====================
document.addEventListener('DOMContentLoaded', function() {
    const tahunAjaranInputs = document.querySelectorAll('input[name="tahun_ajaran"]');
    
    tahunAjaranInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const pattern = /^\d{4}\/\d{4}$/;
            if (this.value && !pattern.test(this.value)) {
                this.setCustomValidity('Format harus YYYY/YYYY (contoh: 2025/2026)');
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
        });

        input.addEventListener('input', function() {
            this.setCustomValidity('');
        });
    });
});
    </script>
    @stack('scripts')
</body>
</html>