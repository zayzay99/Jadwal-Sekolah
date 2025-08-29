@extends('dashboard.guru')
@section('content')
<div>
    <h2>Jadwal Mengajar Saya</h2>
    <table border="1" cellpadding="10" style="margin-top:20px;width:100%;background:#fff;">
        <thead>
            <tr>
                <th>Hari</th>
                <th>Mata Pelajaran</th>
                <th>Kelas</th>
                <th>Jam</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwals as $jadwal)
            <tr>
                <td>{{ $jadwal->hari }}</td>
                <td>{{ $jadwal->mapel }}</td>
                <td>{{ $jadwal->kelas ? $jadwal->kelas->nama_kelas : '-' }}</td>
                <td>{{ $jadwal->jam }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
