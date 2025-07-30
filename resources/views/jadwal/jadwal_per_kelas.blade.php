@extends('dashboard.admin')
@section('content')
    <h2>Jadwal Kelas {{ $kelas->nama_kelas }}</h2>
    <table border="1" cellpadding="10" style="margin-top:20px;width:100%;background:#fff;">
        <thead>
            <tr>
                <th>Mata Pelajaran</th>
                <th>Guru</th>
                <th>Hari</th>
                <th>Jam</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwals as $jadwal)
            <tr>
                <td>{{ $jadwal->mapel }}</td>
                <td>{{ $jadwal->guru->nama }}</td>
                <td>{{ $jadwal->hari }}</td>
                <td>{{ $jadwal->jam }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection