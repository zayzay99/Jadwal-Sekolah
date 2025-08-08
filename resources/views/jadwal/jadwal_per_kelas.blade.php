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
    {{-- <table border="1" cellpadding="10" style="margin-top:20px;width:100%;background:#fff;"> --}}
        <thead>
            <tr>
                <th>Mata Pelajaran</th>
                <th>Guru</th>
                <th>Hari</th>
                <th>Jam</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwals as $jadwal)
            <tr>
                <td>{{ $jadwal->mapel }}</td>
                <td>{{ $jadwal->guru->nama }}</td>
                <td>{{ $jadwal->hari }}</td>
                <td>{{ $jadwal->jam }}</td>
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
@endsection