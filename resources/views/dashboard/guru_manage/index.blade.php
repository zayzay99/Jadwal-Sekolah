@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2>Manajemen Guru</h2>
</div>

<div class="table-container">
    <div class="table-header">
        <h2>Daftar Guru</h2>
        <div class="table-header-actions">
            <form action="{{ route('manage.guru.index') }}" method="GET" class="search-form">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, NIP, pengampu..." value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-primary btn-tiny">Cari</button>
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
        <table class="custom-table">
            <thead>
                <tr>

                    <th>Foto</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Pengampu</th>
                    <th>Jam Mengajar</th>
                    <th>Sisa Jam</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if(count($gurus) > 0)
                    @foreach($gurus as $guru)
                    <tr>
                        <td data-label="Foto">
                            <img src="{{ $guru->profile_picture ? asset('storage/' . $guru->profile_picture) : asset('storage/Default-Profile.png') }}" alt="Foto Profil" class="profile-picture">
                        </td>
                        <td data-label="Nama">{{ $guru->nama }}</td>
                        <td data-label="NIP">{{ $guru->nip }}</td>
                        <td data-label="Pengampu">{{ $guru->pengampu }}</td>
                        <td data-label="Jam Mengajar">{{ $guru->formatted_total_jam_mengajar }}</td>
                        <td data-label="Sisa Jam">{{ $guru->formatted_sisa_jam_mengajar }}</td>
                        <td data-label="Email">{{ $guru->email }}</td>
                        <td data-label="Aksi">
                            <div class="action-buttons">
                                <a href="{{ route('manage.guru.edit', $guru->id) }}" class="btn btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('manage.guru.availability.edit', $guru->id) }}" class="btn-jadwal" title="Atur Ketersediaan">
                                    <i class="fas fa-clock"></i> 
                                </a>
                                <form action="{{ route('manage.guru.destroy', $guru->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="no-data-cell">Tidak ada data guru yang ditemukan.</td>
                    </tr>
                @endif

                    
            

            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        {{ $gurus->links() }}
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
/* General Responsive Styles for Manage Pages */
.table-header-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.search-form {
    display: flex;
    gap: 10px;
    flex-grow: 1;
}

.profile-picture {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ddd;
    display: inline-block;
}

.no-data-cell {
    text-align: center;
    padding: 20px;
}

/* Responsive Styles for Mobile */
@media (max-width: 768px) {
    .table-header {
        flex-direction: column;
        align-items: stretch;
        gap: 15px;
    }
    
    .table-header h2 {
        font-size: 20px;
        text-align: center;
    }

    .table-header-actions {
        flex-direction: column;
        align-items: stretch;
        width: 100%;
    }

    .search-form {
        width: 100%;
    }

    .search-form .form-control {
        flex-grow: 1;
        font-size: 14px;
        padding: 10px 15px;
    }
    
    .table-header-actions .btn {
        width: 100%;
        justify-content: center;
    }

    .custom-table thead {
        display: none;
    }

    .custom-table, .custom-table tbody, .custom-table tr, .custom-table td {
        display: block;
        width: 100%;
    }

    .custom-table tr {
        margin-bottom: 15px;
        border: 1px solid #eee;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        padding: 15px;
    }

    .custom-table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
        text-align: right;
    }

    .custom-table td:last-child {
        border-bottom: none;
    }

    .custom-table td::before {
        content: attr(data-label);
        font-weight: bold;
        text-align: left;
        padding-right: 15px;
        color: #333;
    }

    .custom-table td[data-label="Foto"] {
        justify-content: center;
    }
    
    .custom-table td[data-label="Aksi"] .action-buttons {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        width: 100%;
    }

    /* Set a consistent size for all action buttons in this context */
    .custom-table td[data-label="Aksi"] .action-buttons .btn,
    .custom-table td[data-label="Aksi"] .action-buttons .btn-jadwal,
    .custom-table td[data-label="Aksi"] .action-buttons .delete-form .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0; /* Reset padding */
        height: 38px;
        width: 38px;
        font-size: 14px; /* Keep icon size reasonable */
        flex-shrink: 0;
        border-radius: 8px; /* Make it a circle or rounded square */
    }

    .custom-table td[data-label="Aksi"] .action-buttons .delete-form {
        margin: 0;
        padding: 0;
        line-height: 1;
    }
    
    .pagination-container .pagination {
        flex-wrap: wrap;
        justify-content: center;
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
                confirmButtonColor: '#d33',
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