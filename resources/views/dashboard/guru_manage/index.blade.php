@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Manajemen Guru</h2>
</div>
<div class="table-container">
<div class="table-container">
    <div class="table-header">
        <h2>Daftar Guru</h2>
        <a href="{{ route('manage.guru.create') }}" class="btn btn-success btn-tiny">
            <i class="fas fa-plus"></i> Tambah Guru
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="text-align: center">Foto</th>
                    <th style="text-align: center">Nama</th>
                    <th style="text-align: center">NIP</th>
                    <th style="text-align: center">Pengampu</th>
                    <th style="text-align: center">Jam Mengajar</th>
                    <th style="text-align: center">Sisa Jam Mengajar</th>
                    <th style="text-align: center">Email</th>
                    <th style="text-align: center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gurus as $guru)
                <tr>
                    <td style="text-align: center; vertical-align: middle;">
                        <img src="{{ $guru->profile_picture ? asset('storage/' . $guru->profile_picture) : asset('storage/Default-Profile.png') }}" alt="Foto Profil" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd; display: inline-block;">
                    </td>
                    <td style="text-align: center">{{ $guru->nama }}</td>
                    <td style="text-align: center">{{ $guru->nip }}</td>
                    <td style="text-align: center">{{ $guru->pengampu }}</td>
                    <td style="text-align: center">{{ $guru->formatted_total_jam_mengajar }}</td>
                    <td style="text-align: center">{{ $guru->formatted_sisa_jam_mengajar }}</td>
                    <td style="text-align: center">{{ $guru->email }}</td>
                    <td style="text-align: center; padding: 16px 20px;">
                        <div class="action-buttons">
                            <a href="{{ route('manage.guru.edit', $guru->id) }}" class="btn btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('manage.guru.availability.edit', $guru->id) }}" class="btn-jadwal " title="Atur Ketersediaan">
                                <i class="fas fa-clock"></i> 
                            </a>
                            <form action="{{ route('manage.guru.destroy', $guru->id) }}" method="POST" style="display:inline;" class="delete-form">
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
                text: "Data guru ini akan dihapus secara permanen!",
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
