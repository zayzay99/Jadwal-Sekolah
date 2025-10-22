{{-- filepath: resources/views/dashboard/jadwal/lihat.blade.php --}}
@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <div>
        <h1 class="text-2xl font-semibold">Lihat Jadwal Kelas {{ $kelas->nama_kelas }}</h1>
        <p class="text-muted">Daftar jadwal untuk kelas {{ $kelas->nama_kelas }}</p>
    </div>

    <div class="header-actions">
        <button class="btn btn-secondary btn-sm" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i> Kembali
        </button>
        <a href="{{ route('admin.jadwal.cetak', ['kelas' => $kelas->id]) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-print"></i> Cetak PDF
        </a>

        @if($is_management && $jadwals->count() > 0)
            <form id="delete-all-form" 
                  action="{{ route('jadwal.destroyAll', ['kelas_id' => $kelas->id]) }}" 
                  method="POST" 
                  style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-danger btn-sm" onclick="showDeleteAllConfirmation(event)">
                    <i class="fas fa-trash-alt"></i> Hapus Semua
                </button>
            </form>
        @endif
    </div>
</div>

<div class="table-container jadwal-table-container">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    @if($is_management)
                        <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if($jadwals->count() > 0)
                    @foreach($jadwals as $hari => $jadwalHarian)
                        @foreach($jadwalHarian as $index => $jadwal)
                            <tr>
                                @if($index === 0)
                                    <td rowspan="{{ count($jadwalHarian) }}"><strong>{{ $hari }}</strong></td>
                                @endif

                                <td>{{ $jadwal->jam }}</td>

                                @if($jadwal->kategori)
                                    <td colspan="2" style="text-align:center; font-weight:600;">
                                        {{ $jadwal->kategori->nama_kategori }}
                                    </td>
                                @else
                                    <td>{{ $jadwal->mapel }}</td>
                                    <td>{{ $jadwal->guru ? $jadwal->guru->nama : '-' }}</td>
                                @endif

                                @if($is_management)
                                    <td style="text-align: center;">
                                        <form action="{{ route('jadwal.destroy', $jadwal->id) }}" 
                                              method="POST" 
                                              style="display:inline;" 
                                              class="delete-single-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Jadwal">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="no-data text-center">
                            <i class="fas fa-info-circle"></i> Tidak ada jadwal untuk kelas ini
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

{{-- SweetAlert notification --}}
<script>
@if (session('success'))
    Swal.fire({
        position: "top-end",
        icon: "success",
        title: "Berhasil disimpan!",
        showConfirmButton: false,
        timer: 1500
    });
@endif

function showDeleteAllConfirmation(event) {
    event.preventDefault();
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Semua jadwal untuk kelas ini akan dihapus secara permanen! Tindakan ini tidak dapat dibatalkan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-all-form').submit();
        }
    });
}
</script>

{{-- Script untuk SweetAlert konfirmasi hapus jadwal individual --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteSingleForms = document.querySelectorAll('.delete-single-form');

    deleteSingleForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Jadwal ini akan dihapus secara permanen!",
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
@endsection
