@extends('dashboard.siswa')
@section('content')
<div>
    <h2>Jadwal Pelajaran Saya</h2>

    <form method="GET" action="{{ route('siswa.jadwal') }}">
        <label for="tahun_ajaran">Pilih Tahun Ajaran:</label>
        <select name="tahun_ajaran_id" id="tahun_ajaran" onchange="this.form.submit()">
            @foreach($tahunAjarans as $tahun)
                <option value="{{ $tahun->id }}" {{ $tahun->id == $selectedTahunAjaranId ? 'selected' : '' }}>
                    {{ $tahun->tahun_ajaran }} {{ $tahun->semester }}
                </option>
            @endforeach
        </select>
    </form>

    <table border="1" cellpadding="10" style="margin-top:20px;width:100%;background:#fff;">
        <thead>
            <tr>
                <th>Hari</th>
                <th>Mata Pelajaran</th>
                <th>Guru</th>
                <th>Jam</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwals as $jadwal)
            <tr>
                <td>{{ $jadwal->hari }}</td>
                <td>{{ $jadwal->mapel }}</td>
                <td>{{ $jadwal->guru ? $jadwal->guru->nama : '-' }}</td>
                <td>{{ $jadwal->jam }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
