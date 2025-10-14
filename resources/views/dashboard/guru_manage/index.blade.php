@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 700; margin: 0 0 8px 0; color: var(--text-color);">
            Manajemen Guru
        </h2>
        <p style="color: var(--text-light); margin: 0; font-size: 0.95rem;">
            Kelola data guru dan ketersediaan mengajar
        </p>
    </div>
</div>

<div style="background: white; padding: 30px 35px; border-radius: 20px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color); margin-top: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <h2 style="font-size: 1.3rem; font-weight: 600; margin: 0; color: var(--text-color);">
            <i class="fas fa-users" style="margin-right: 10px; color: var(--primary-color);"></i>
            Daftar Guru
        </h2>
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <form action="{{ route('manage.guru.index') }}" method="GET" style="display: flex; gap: 8px;">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, NIP, pengampu..." value="{{ $search ?? '' }}" style="min-width: 250px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
            <a href="{{ route('manage.guru.import.show') }}" class="btn btn-info btn-tiny">
                <i class="fas fa-file-import"></i> Import Guru
            </a>
            <a href="{{ route('manage.guru.create') }}" class="btn btn-success btn-tiny">
                <i class="fas fa-plus"></i> Tambah Guru
            </a>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 80px;">FOTO</th>
                    <th>NAMA</th>
                    <th>NIP</th>
                    <th>PENGAMPU</th>
                    <th style="white-space: nowrap;">JAM MENGAJAR</th>
                    <th style="white-space: nowrap;">SISA JAM</th>
                    <th>EMAIL</th>
                    <th style="text-align: center; width: 150px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @if(count($gurus) > 0)
                    @foreach($gurus as $guru)
                    <tr>
                        <td data-label="Foto" style="text-align: center;">
        <div style="display: inline-flex; align-items: center; justify-content: center;">
            @php
                // Tentukan path gambar
                if ($guru->profile_picture && $guru->profile_picture !== 'default-profile.jpg') {
                    $imagePath = asset('storage/' . $guru->profile_picture);
                } else {
                    $imagePath = asset('img/Default-Profile.png');
                }
            @endphp
            <img src="{{ $imagePath }}" 
                 alt="Foto {{ $guru->nama }}" 
                 style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-color); box-shadow: 0 2px 8px rgba(17, 153, 142, 0.2);"
                 onerror="this.src='{{ asset('img/Default-Profile.png') }}'">
        </div>
    </td>
                        <td data-label="Nama" style="font-weight: 500; color: var(--text-color);">{{ $guru->nama }}</td>
                        <td data-label="NIP" style="color: var(--text-light);">{{ $guru->nip }}</td>
                        <td data-label="Pengampu">
                            <span class="badge" style="background: var(--primary-gradient); color: white; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; white-space: nowrap;">
                                {{ $guru->pengampu }}
                            </span>
                        </td>
                        <td data-label="Jam Mengajar" style="color: var(--text-color); white-space: nowrap;">{{ $guru->formatted_total_jam_mengajar }}</td>
                        <td data-label="Sisa Jam">
                            <span class="badge" style="background: var(--success-gradient); color: white; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; white-space: nowrap;">
                                {{ $guru->formatted_sisa_jam_mengajar }}
                            </span>
                        </td>
                        <td data-label="Email" style="color: var(--text-light);">{{ $guru->email }}</td>
                        <td data-label="Aksi" style="text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                <a href="{{ route('manage.guru.edit', $guru->id) }}" 
                               class="btn btn-info btn-sm" 
                               title="Edit"
                               style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </a>
                                <a href="{{ route('manage.guru.availability.edit', $guru->id) }}" class="btn btn-info btn-sm" title="Atur Ketersediaan" style="padding: 8px 12px;">
                                    <i class="fas fa-clock"></i> 
                                </a>
                                <form action="{{ route('manage.guru.destroy', $guru->id) }}" method="POST" class="delete-form" style="display: inline; margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus" style="padding: 8px 12px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
                            <i class="fas fa-user-slash" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5; display: block;"></i>
                            <p style="margin: 0; font-size: 0.95rem;">Tidak ada data guru yang ditemukan.</p>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

   <div class="pagination-container" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: center;">
    @if ($gurus->hasPages())
        <nav>
            <ul class="pagination" style="display: flex; gap: 8px; list-style: none; padding: 0; margin: 0;">
                
                {{-- Tombol Previous --}}
                @if ($gurus->onFirstPage())
                    <li style="opacity: 0.5; pointer-events: none;">
                        <span class="page-link" style="padding: 8px 14px; border-radius: 10px; background: var(--border-color); color: var(--text-light);">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li>
                        <a href="{{ $gurus->previousPageUrl() }}" class="page-link" style="padding: 8px 14px; border-radius: 10px; background: var(--primary-color); color: white; text-decoration: none;">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Nomor Halaman --}}
                @foreach ($gurus->getUrlRange(1, $gurus->lastPage()) as $page => $url)
                    @if ($page == $gurus->currentPage())
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
                @if ($gurus->hasMorePages())
                    <li>
                        <a href="{{ $gurus->nextPageUrl() }}" class="page-link" style="padding: 8px 14px; border-radius: 10px; background: var(--primary-color); color: white; text-decoration: none;">
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

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
.table {
    border-collapse: separate;
    border-spacing: 0;
}

.table thead th {
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    padding: 16px 12px;
    font-weight: 700;
}

.table tbody td {
    padding: 18px 12px;
    vertical-align: middle;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: rgba(17, 153, 142, 0.03);
    transform: translateX(2px);
}

.btn-sm {
    font-size: 0.85rem;
    transition: all 0.2s ease;
}

.btn-sm:hover {
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .table thead {
        display: none;
    }

    .table, .table tbody, .table tr, .table td {
        display: block;
        width: 100%;
    }

    .table tr {
        margin-bottom: 20px;
        border: 2px solid var(--border-color);
        border-radius: 15px;
        padding: 20px;
        background: var(--bg-primary);
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
    }

    .table td::before {
        content: attr(data-label);
        font-weight: 600;
        text-align: left;
        padding-right: 15px;
        color: var(--text-color);
        font-size: 0.85rem;
    }

    .table td[data-label="Foto"] {
        justify-content: center;
    }
    
    .table td[data-label="Foto"]::before {
        display: none;
    }
    
    .table td[data-label="Aksi"] > div {
        width: 100%;
        justify-content: flex-end !important;
    }

    .table td[data-label="Aksi"] .btn-sm {
        padding: 8px 10px !important;
    }
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
                text: "Data guru ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f5576c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush