@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2>Manajemen Siswa</h2>
</div>

<div class="table-container">
    <div class="table-header">
        <h2>Daftar Siswa</h2>
        <div class="table-header-actions">
            <form action="{{ route('manage.siswa.index') }}" method="GET" class="search-form">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, NIS, kelas..." value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-primary btn-tiny">Cari</button>
            </form>
            <a href="{{ route('manage.siswa.create') }}" class="btn btn-success btn-tiny">
                <i class="fas fa-plus"></i> Tambah Siswa
            </a>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if(count($siswas) > 0)
                    @foreach($siswas as $siswa)
                    <tr>
                        <td data-label="Foto">
                            <img src="{{ $siswa->profile_picture ? asset('storage/' . $siswa->profile_picture) : asset('storage/Default-Profile.png') }}" alt="Foto Profil" class="profile-picture">
                        </td>
                        <td data-label="Nama">{{ $siswa->nama }}</td>
                        <td data-label="NIS">{{ $siswa->nis }}</td>
                        <td data-label="Kelas">{{ $siswa->kelas->first()?->nama_kelas ?? '-' }}</td>
                        <td data-label="Email">{{ $siswa->email }}</td>
                        <td data-label="Aksi">
                            <div class="action-buttons">
                                <a href="{{ route('manage.siswa.edit', $siswa->id) }}" class="btn btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('manage.siswa.destroy', $siswa->id) }}" method="POST" class="delete-form">
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
                        <td colspan="6" class="no-data-cell">Tidak ada data siswa yang ditemukan.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        {{ $siswas->links() }}
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
/* General Responsive Styles for Siswa Manage Page */
.table-header-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.search-form {
    display: flex;
    gap: 10px;
    flex-grow: 1; /* Allow search form to grow */
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
        font-size: 20px; /* Adjust header size on mobile */
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
        font-size: 14px; /* Smaller font */
        padding: 10px 15px; /* Adjust padding */
    }
    
    .table-header-actions .btn {
        width: 100%; /* Make buttons full-width */
        justify-content: center; /* Center text/icon in button */
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
        justify-content: center; /* Center the profile picture */
    }
    
    .custom-table td[data-label="Aksi"] .action-buttons {
        justify-content: flex-end;
        width: 100%;
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
                text: "Data siswa ini akan dihapus secara permanen!",
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