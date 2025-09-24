@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Manajemen Kelas</h2>
</div>

<div class="table-container">
    <div class="filter-bar">
        <a href="{{ route('manage.kelas.index') }}" class="btn btn-filter {{ !$selectedKategori ? 'active' : '' }}">Semua</a>
        @foreach($kategoriList as $kategori)
            <a href="{{ route('manage.kelas.index', ['kategori' => $kategori]) }}" class="btn btn-filter {{ $selectedKategori == $kategori ? 'active' : '' }}">{{ $kategori }}</a>
        @endforeach
    </div>

    <div class="table-header">
        <h2>Daftar Kelas</h2>
        <a href="{{ route('manage.kelas.create') }}" class="btn btn-success btn-tiny">
            <i class="fas fa-plus"></i> Tambah Kelas
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="text-align: center">Nama Kelas</th>
                    <th style="text-align: center">Wali Kelas</th>
                    <th style="text-align: center">Jumlah Siswa</th>
                    <th style="text-align: center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelas as $k)
                <tr>
                    <td style="text-align: center">{{ $k->nama_kelas }}</td>
                    <td style="text-align: center">{{ $k->guru ? $k->guru->nama : '-' }}</td>
                    <td style="text-align: center">{{ $k->siswas->count() }}</td>
                    <td style="text-align: center; padding: 16px 20px;">
                        <div class="action-buttons">
                            <a href="{{ route('manage.kelas.edit', $k->id) }}" class="btn btn-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('manage.kelas.destroy', $k->id) }}" method="POST" style="display:inline;" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="no-data">
                        <i class="fas fa-info-circle"></i> Tidak ada kelas untuk kategori ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .filter-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    .btn-filter {
        padding: 8px 15px;
        border: 1px solid #ddd;
        border-radius: 20px;
        text-decoration: none;
        color: #b1a1a1ff;
        background-color: #f8f9fa;
        transition: all 0.3s;
    }
    .btn-filter.active, .btn-filter:hover {
        background-color: #2d6a4f;
        color: white;
        border-color: #2d6a4f;
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
            event.preventDefault(); // Prevent the form from submitting immediately
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Menghapus kelas akan menghapus semua data terkait!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // If confirmed, submit the form
                }
            });
        });
    });
});
</script>
@endpush