<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Pelajaran</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    @if(isset($siswa))
        <h2>Jadwal Pelajaran - {{ $siswa->nama }}</h2>
        <p>Kelas: {{ $siswa->kelas->first()?->nama_kelas ?? '-' }}</p>
    @elseif(isset($guru))
        <h2>Jadwal Mengajar - {{ $guru->nama }}</h2>
    @endif

    @if(isset($jadwals) && count($jadwals) > 0)
        <table>
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Mata Pelajaran</th>
                    @if(isset($guru))
                        <th>Kelas</th>
                    @else
                        <th>Guru</th>
                    @endif
                    <th>Jam</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jadwals as $hari => $jadwalHarian)
                    @foreach($jadwalHarian as $index => $jadwal)
                        <tr>
                            @if($index === 0)
                                <td rowspan="{{ count($jadwalHarian) }}">{{ $hari }}</td>
                            @endif
                            <td>{{ $jadwal->mapel }}</td>
                            @if(isset($guru))
                                <td>{{ $jadwal->kelas ? $jadwal->kelas->nama_kelas : '-' }}</td>
                            @else
                                <td>{{ $jadwal->guru->nama }}</td>
                            @endif
                            <td>{{ $jadwal->jam }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; color: red;">Jadwal hari ini belum ada.</p>
    @endif
</body>
</html>
