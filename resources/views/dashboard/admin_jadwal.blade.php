@extends('dashboard.admin')
@section('content')
<div>
    <h2>Daftar Jadwal</h2>
    <a href="{{ route('jadwal.create') }}" class="menu-item" style="width:fit-content;display:inline-block;">Tambah Jadwal</a>
    <table border="1" cellpadding="10" style="margin-top:20px;width:100%;background:#fff;">
        <thead>
            <tr>
                <th>Mata Pelajaran</th>
                <th>Kelas</th>
                <th>Guru</th>
                <th>Hari</th>
                <th>Jam</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwals as $jadwal)
            <tr>
                <td>{{ $jadwal->mapel }}</td>
                <td>{{ $jadwal->kelas->nama_kelas ?? '-' }}</td>
                <td>{{ $jadwal->guru->nama }}</td>
                <td>{{ $jadwal->hari }}</td>
                <td>{{ $jadwal->jam }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
