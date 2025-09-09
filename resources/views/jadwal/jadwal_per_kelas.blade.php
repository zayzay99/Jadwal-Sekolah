@extends('dashboard.admin')
@section('content')

<div class="content-header">
    <h1>Jadwal Kelas {{ $kelas->nama_kelas }}</h1>
    <p>Daftar jadwal untuk kelas {{ $kelas->nama_kelas }}</p>
</div>

<div class="table-container">
    <div class="table-header">
         <button class="btn btn-secondary" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i> Kembali
        </button>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th>Jam</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwals as $jadwal)
                <tr>
                    <td>{{ $jadwal->hari }}</td>
                    @if($jadwal->kategori)
                        <td colspan="2" style="text-align: center; font-weight: bold;">{{ $jadwal->kategori->nama_kategori }}</td>
                    @else
                        <td>{{ $jadwal->mapel }}</td>
                        <td>{{ $jadwal->guru ? $jadwal->guru->nama : '-' }}</td>
                    @endif
                    <td>{{ $jadwal->jam }}</td>
                    <td>
                        <form action="{{ route('jadwal.destroy', $jadwal->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" title="Hapus" onclick="showDeleteConfirmation(event)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="no-data">
                        <i class="fas fa-info-circle"></i> Tidak ada jadwal untuk kelas ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table> 
    </div>
</div>

<!-- Script SweetAlert untuk notifikasi sukses -->
<script>
    @if (session('success'))
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Your work has been saved",
            showConfirmButton: false,
            timer: 1500
        });
    @endif
</script>

@endsection