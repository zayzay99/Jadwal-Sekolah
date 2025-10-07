<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Pelajaran - {{ $user->nama }}</title>
    <style>
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 12px; 
            color: #333;
        }
        .header { 
            text-align: center; 
            margin-bottom: 25px; 
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 { 
            margin: 0; 
            font-size: 20px; 
        }
        .header p { 
            margin: 5px 0; 
            font-size: 14px;
        }
        .info { 
            margin-bottom: 20px; 
        }
        .info table { 
            width: auto; 
            border: none; 
        }
        .info td { 
            padding: 3px 5px; 
        }
        table.jadwal { 
            width: 100%; 
            border-collapse: collapse; 
        }
        table.jadwal th, table.jadwal td { 
            border: 1px solid #333; 
            padding: 8px; 
            text-align: left; 
        }
        table.jadwal th { 
            background-color: #e9ecef; 
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .no-jadwal { text-align: center; padding: 20px; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h1>JADWAL PELAJARAN</h1>
        @if($kelasSiswa && $kelasSiswa->tahunAjaran)
            <p>Tahun Ajaran: {{ $kelasSiswa->tahunAjaran->tahun_ajaran }} - Semester {{ $kelasSiswa->tahunAjaran->semester }}</p>
        @endif
    </div>

    <div class="info">
        <table>
            <tr>
                <td><strong>Nama Siswa</strong></td>
                <td>: {{ $user->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>NIS</strong></td>
                <td>: {{ $user->nis ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Kelas</strong></td>
                <td>: {{ $kelasSiswa->nama_kelas ?? 'Tidak terdaftar di kelas' }}</td>
            </tr>
        </table>
    </div>

    @if(isset($jadwals) && $jadwals->count() > 0)
        <table class="jadwal">
            <thead>
                <tr>
                    <th class="text-center" style="width: 15%;">Hari</th>
                    <th class="text-center" style="width: 20%;">Jam</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jadwals as $hari => $jadwalHarian)
                    @foreach($jadwalHarian as $index => $jadwal)
                        <tr>
                            @if($index === 0)
                                <td rowspan="{{ count($jadwalHarian) }}" class="text-center font-bold">{{ $hari }}</td>
                            @endif
                            <td class="text-center">{{ $jadwal->jam }}</td>
                            @if($jadwal->kategori)
                                <td colspan="2" class="text-center font-bold">{{ $jadwal->kategori->nama_kategori }}</td>
                            @else
                                <td>{{ $jadwal->mapel }}</td>
                                <td>{{ $jadwal->guru ? $jadwal->guru->nama : '-' }}</td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @else
        <p class="no-jadwal">Tidak ada data jadwal yang tersedia untuk dicetak.</p>
    @endif

</body>
</html>