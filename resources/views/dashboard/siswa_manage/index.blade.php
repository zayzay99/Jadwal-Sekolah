@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Manajemen Siswa</h2>
</div>

<div class="table-container">
    <div class="table-header">
        <h2>Daftar Siswa</h2>
        <a href="{{ route('manage.siswa.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Siswa
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="text-align: center">Foto</th>
                    <th style="text-align: center">Nama</th>
                    <th style="text-align: center">NIS</th>
                    <th style="text-align: center">Kelas</th>
                    <th style="text-align: center">Email</th>
                    <th style="text-align: center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswas as $siswa)
                <tr>
                    <td style="text-align: center; vertical-align: middle;">
                        <img src="{{ $siswa->profile_picture ? asset('storage/' . $siswa->profile_picture) : asset('img/default-profile.png') }}" alt="Foto Profil" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd; display: inline-block;">
                    </td>
                    <td style="text-align: center">{{ $siswa->nama }}</td>
                    <td style="text-align: center">{{ $siswa->nis }}</td>
                    <td style="text-align: center">{{ $siswa->kelas->first()?->nama_kelas ?? '-' }}</td>
                    <td style="text-align: center">{{ $siswa->email }}</td>
                    <td style="text-align: center; padding: 16px 20px;">
                        <div class="action-buttons">
                            <a href="{{ route('manage.siswa.edit', $siswa->id) }}" class="btn btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>Edit
                            </a>
                            <form action="{{ route('manage.siswa.destroy', $siswa->id) }}" method="POST" style="display:inline;" class="delete-form">
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

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                text: "Data siswa ini akan dihapus secara permanen!",
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
