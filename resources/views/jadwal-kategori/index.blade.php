@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Manajemen Kategori Jadwal</h2>
</div>

<div class="table-container">
    <div class="table-header">
        <h2>Daftar Kategori</h2>
        <a href="{{ route('jadwal-kategori.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Kategori
        </a>
    </div>
    
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="text-align: center">Nama Kategori</th>
                    <th style="text-align: center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kategoris as $kategori)
                <tr>
                    <td style="text-align: center">{{ $kategori->nama_kategori }}</td>
                    <td style="text-align: center; padding: 16px 20px;">
                        <div class="action-buttons">
                            <a href="#" class="btn btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>Edit
                            </a>
                            <form action="#" method="POST" style="display:inline;" class="delete-form">
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
            </tbody>
        </table>
    </div>
</div>
@endsection

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
                text: "Anda tidak akan dapat mengembalikan ini!",
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
