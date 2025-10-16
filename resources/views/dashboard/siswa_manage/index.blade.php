@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <div>
        <h2 style="font-size: 2rem; font-weight: 700; margin: 0 0 8px 0; color: var(--text-color);">
            Manajemen Siswa
        </h2>
        <p style="color: var(--text-light); margin: 0; font-size: 0.95rem;">
            <i class="fas fa-users"></i> Kelola data siswa, import, export, dan pengaturan kelas
        </p>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-container" style="margin-bottom: 25px;">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="stat-value">{{ $totalSiswa }}</div>
        <div class="stat-label">Total Siswa</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-value">{{ $siswaSudahDikelas }}</div>
        <div class="stat-label">Sudah Dikelas (Thn. Aktif)</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-user-clock"></i>
        </div>
        <div class="stat-value">{{ $siswaBelumDikelas }}</div>
        <div class="stat-label">Belum Dikelas (Thn. Aktif)</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-filter"></i>
        </div>
        <div class="stat-value">{{ $siswas->count() }}</div>
        <div class="stat-label">Ditampilkan</div>
    </div>
</div>

<!-- Main Table Card -->
<div class="welcome-card" style="flex-direction: column; align-items: stretch; padding: 0; overflow: hidden;">
    <!-- Table Header -->
    <div style="padding: 25px 30px; border-bottom: 1px solid var(--border-color); background: linear-gradient(to right, rgba(17, 153, 142, 0.05), transparent);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="font-size: 2rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    <i class="fas fa-table"></i>
                </div>
                <h3 style="margin: 0; font-size: 1.3rem; font-weight: 600; color: var(--text-color);">
                    Daftar Siswa
                </h3>
            </div>
            
            <!-- Action Buttons -->
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('manage.siswa.import.form') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-file-import"></i> Import
                </a>
                
                <a href="{{ route('manage.siswa.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus-circle"></i> Tambah Siswa
                </a>
            </div>
        </div>

        <!-- Search Form -->
        <div style="margin-top: 20px;">
            <form action="{{ route('manage.siswa.index') }}" method="GET" style="display: flex; gap: 10px;">
                <div style="flex: 1; position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, NIS, kelas..." value="{{ $search ?? '' }}" style="padding-left: 45px;">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
        </div>
    </div>

    <!-- Table Content -->
    <div style="overflow-x: auto;">
        <table class="table" style="margin: 0;">
            <thead>
                <tr>
                    <th style="text-align: center; width: 80px;">
                        <i class="fas fa-image"></i> Foto
                    </th>
                    <th>
                        <i class="fas fa-user"></i> Nama
                    </th>
                    <th>
                        <i class="fas fa-id-card"></i> NIS
                    </th>
                    <th>
                        <i class="fas fa-door-open"></i> Kelas
                    </th>
                    <th>
                        <i class="fas fa-envelope"></i> Email
                    </th>
                    <th style="text-align: center; width: 120px;">
                        <i class="fas fa-cog"></i> Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                @if(count($siswas) > 0)
                    @foreach($siswas as $siswa)
                    <tr>
                        <td data-label="Foto" style="text-align: center;">
                            <div style="display: inline-flex; align-items: center; justify-content: center;">
                                <img src="{{ $siswa->profile_picture ? asset('storage/' . $siswa->profile_picture) : asset('img/Default-Profile.png') }}" 
                                     alt="Foto {{ $siswa->nama }}" 
                                     style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-color); box-shadow: 0 2px 8px rgba(17, 153, 142, 0.2);">
                            </div>
                        </td>
                        <td data-label="Nama">
                            <div style="font-weight: 600; color: var(--text-color);">{{ $siswa->nama }}</div>
                        </td>
                        <td data-label="NIS">
                            <span style="font-family: 'Courier New', monospace; font-weight: 600; color: var(--primary-color);">{{ $siswa->nis }}</span>
                        </td>
                        <td data-label="Kelas">
                            @if($siswa->kelas->first())
                                <span class="badge bg-success" style="font-size: 0.85rem;">
                                    <i class="fas fa-check-circle"></i> {{ $siswa->kelas->first()->nama_kelas }}
                                </span>
                            @else
                                <span class="badge bg-secondary" style="font-size: 0.85rem;">
                                    <i class="fas fa-minus-circle"></i> Belum Ada
                                </span>
                            @endif
                        </td>
                        <td data-label="Email">
                            <div style="color: var(--text-light); font-size: 0.9rem;">
                                <i class="fas fa-envelope" style="color: var(--text-muted); margin-right: 5px;"></i>{{ $siswa->email }}
                            </div>
                        </td>
                        <td data-label="Aksi" style="text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                <a href="{{ route('manage.siswa.edit', $siswa->id) }}" class="btn btn-info btn-sm" title="Edit" style="padding: 8px 12px;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('manage.siswa.destroy', $siswa->id) }}" method="POST" class="delete-form" style="display: inline-block; margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus" style="padding: 8px 12px;">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 50px 20px;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                                <div style="font-size: 3.5rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; opacity: 0.5;">
                                    <i class="fas fa-user-slash"></i>
                                </div>
                                <div>
                                    <div style="font-size: 1.1rem; font-weight: 600; color: var(--text-color); margin-bottom: 5px;">
                                        Tidak ada data siswa
                                    </div>
                                    <div style="font-size: 0.9rem; color: var(--text-muted);">
                                        @if(isset($search) && $search)
                                            Tidak ditemukan siswa dengan kata kunci "{{ $search }}"
                                        @else
                                            Belum ada siswa yang terdaftar di sistem
                                        @endif
                                    </div>
                                </div>
                                @if(isset($search) && $search)
                                    <a href="{{ route('manage.siswa.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-redo"></i> Reset Pencarian
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
     <div class="pagination-container" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: center;">
    @if ($siswas->hasPages())
        <nav>
            <ul class="pagination" style="display: flex; gap: 8px; list-style: none; padding: 0; margin: 0;">
                
                {{-- Tombol Previous --}}
                @if ($siswas->onFirstPage())
                    <li style="opacity: 0.5; pointer-events: none;">
                        <span class="page-link" style="padding: 8px 14px; border-radius: 10px; background: var(--border-color); color: var(--text-light);">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li>
                        <a href="{{ $siswas->previousPageUrl() }}" class="page-link" style="padding: 8px 14px; border-radius: 10px; background: var(--primary-color); color: white; text-decoration: none;">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Nomor Halaman --}}
                @foreach ($siswas->getUrlRange(1, $siswas->lastPage()) as $page => $url)
                    @if ($page == $siswas->currentPage())
                        <li>
                            <span class="page-link active" style="padding: 8px 14px; border-radius: 10px; background: var(--primary-gradient); color: white; font-weight: bold;">
                                {{ $page }}
                            </span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $url }}" class="page-link" style="padding: 8px 14px; border-radius: 10px; background: var(--border-color); color: var(--text-color); text-decoration: none;">
                                {{ $page }}
                            </a>
                        </li>
                    @endif
                @endforeach

                {{-- Tombol Next --}}
                @if ($siswas->hasMorePages())
                    <li>
                        <a href="{{ $siswas->nextPageUrl() }}" class="page-link" style="padding: 8px 14px; border-radius: 10px; background: var(--primary-color); color: white; text-decoration: none;">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li style="opacity: 0.5; pointer-events: none;">
                        <span class="page-link" style="padding: 8px 14px; border-radius: 10px; background: var(--border-color); color: var(--text-light);">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                @endif

            </ul>
        </nav>
    @endif
</div>

<!-- Info Card -->
<div style="background: linear-gradient(135deg, rgba(0, 180, 219, 0.1) 0%, rgba(0, 131, 176, 0.1) 100%); padding: 20px 25px; border-radius: 15px; border-left: 4px solid #00b4db; margin-top: 20px;">
    <div style="display: flex; align-items: flex-start; gap: 12px;">
        <i class="fas fa-lightbulb" style="font-size: 1.5rem; color: #00b4db; margin-top: 2px;"></i>
        <div>
            <div style="font-weight: 600; color: var(--text-color); margin-bottom: 8px;">
                <i class="fas fa-info-circle"></i> Tips Manajemen Siswa
            </div>
            <ul style="margin: 0; padding-left: 20px; color: var(--text-light); font-size: 0.9rem; line-height: 1.6;">
                <li>Gunakan fitur <strong>Import</strong> untuk menambahkan banyak siswa sekaligus dari file Excel/CSV</li>
                <li>Gunakan fitur <strong>Export</strong> untuk mengunduh data siswa sebagai backup</li>
                <li>Siswa yang belum memiliki kelas akan ditampilkan dengan badge "Belum Ada"</li>
                <li>Gunakan kotak pencarian untuk menemukan siswa berdasarkan nama, NIS, atau kelas</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
/* Additional custom styles for Siswa Management */

/* Mobile Responsive - Card Style */
@media (max-width: 768px) {
    .table thead {
        display: none;
    }

    .table tbody tr {
        display: block;
        margin-bottom: 15px;
        border: 2px solid var(--border-color);
        border-radius: 15px;
        padding: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        background: white;
    }

    .table tbody tr:hover {
        background: white;
        box-shadow: 0 4px 12px rgba(17, 153, 142, 0.15);
    }

    .table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
        text-align: right;
    }

    .table td:last-child {
        border-bottom: none;
        padding-top: 15px;
    }

    .table td::before {
        content: attr(data-label);
        font-weight: 600;
        color: var(--text-color);
        text-align: left;
        padding-right: 15px;
        flex: 0 0 120px;
    }

    .table td[data-label="Foto"] {
        justify-content: center;
        padding: 15px 0;
    }

    .table td[data-label="Foto"]::before {
        display: none;
    }

    .table td[data-label="Aksi"] > div {
        width: 100%;
        justify-content: flex-end;
    }

    /* Mobile header adjustments */
    .welcome-card > div:first-child {
        padding: 20px !important;
    }

    .welcome-card > div:first-child > div:first-child {
        flex-direction: column;
        align-items: stretch !important;
    }

    .welcome-card > div:first-child > div:first-child > div:last-child {
        flex-direction: column;
        width: 100%;
    }

    .welcome-card > div:first-child > div:first-child > div:last-child .btn {
        width: 100%;
        justify-content: center;
    }

    /* Search form mobile */
    .welcome-card > div:first-child > div:last-child form {
        flex-direction: column;
    }

    .welcome-card > div:first-child > div:last-child form button {
        width: 100%;
    }

    /* Stats cards mobile */
    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .stats-container {
        grid-template-columns: 1fr;
    }

    .table td::before {
        flex: 0 0 100px;
        font-size: 0.85rem;
    }
}

/* Pagination styling enhancement */
.pagination {
    display: flex;
    gap: 5px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.pagination li {
    display: inline-block;
}

.pagination a,
.pagination span {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 8px;
    border: 2px solid var(--border-color);
    background: white;
    color: var(--text-color);
    text-decoration: none;
    transition: var(--transition);
    font-weight: 500;
    font-size: 0.9rem;
}

.pagination a:hover {
    background: var(--primary-gradient);
    color: white;
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(17, 153, 142, 0.3);
}

.pagination .active span {
    background: var(--primary-gradient);
    color: white;
    border-color: var(--primary-color);
    box-shadow: 0 4px 12px rgba(17, 153, 142, 0.3);
}

.pagination .disabled span {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Custom scrollbar for table */
.welcome-card > div:nth-child(2)::-webkit-scrollbar {
    height: 8px;
}

.welcome-card > div:nth-child(2)::-webkit-scrollbar-track {
    background: var(--bg-primary);
    border-radius: 10px;
}

.welcome-card > div:nth-child(2)::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 10px;
}

.welcome-card > div:nth-child(2)::-webkit-scrollbar-thumb:hover {
    background: var(--primary-dark);
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('.delete-form');

    deleteForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data siswa ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#11998e',
                confirmButtonText: '<i class="fas fa-trash"></i> Ya, hapus!',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit form
                    form.submit();
                }
            });
        });
    });

    // Auto-hide success message
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            customClass: {
                popup: 'colored-toast'
            }
        });
    @endif

    // Auto-hide error message
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true
        });
    @endif
});
</script>
@endpush