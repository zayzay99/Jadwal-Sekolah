<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Pelajaran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Arial', sans-serif;
            padding: 25px;
            background: #ffffff;
            color: #2c3e50;
            line-height: 1.6;
        }

        /* Header - Enhanced */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 30px 25px;
            background: linear-gradient(135deg, #11998e 0%, #0d7377 100%);
            border-radius: 10px;
            color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .header h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        .header .subtitle {
            font-size: 13px;
            opacity: 0.95;
            font-weight: 500;
        }

        /* Info Box - Improved */
        .info-box {
            background: linear-gradient(to right, #f0f8f7, #ffffff);
            border-left: 5px solid #11998e;
            padding: 18px 22px;
            margin-bottom: 12px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .info-box .info-label {
            font-size: 10px;
            color: #11998e;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .info-box .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
        }

        /* Section Divider - Cleaner */
        .section-divider {
            margin: 28px 0 20px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .section-divider::before,
        .section-divider::after {
            content: '';
            flex: 1;
            height: 2px;
            background: #11998e;
        }

        .section-divider span {
            font-size: 13px;
            font-weight: 700;
            color: #11998e;
            text-transform: uppercase;
            letter-spacing: 1px;
            white-space: nowrap;
        }

        /* Day Section */
        .day-section {
            page-break-inside: avoid;
            margin-bottom: 20px;
        }

        /* Table - Better Spacing */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            border-radius: 6px;
            overflow: hidden;
        }

        thead {
            background: #11998e;
            color: white;
        }

        th {
            padding: 14px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            text-align: left;
            border: none;
        }

        tbody tr {
            border-bottom: 1px solid #ecf0f1;
            transition: background 0.2s;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:nth-child(even) {
            background: #f8fafa;
        }

        tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        td {
            padding: 14px 16px;
            font-size: 13px;
            color: #2c3e50;
            vertical-align: middle;
        }

        /* Day Column */
        .day-column {
            background: #11998e;
            color: white;
            font-weight: 700;
            font-size: 13px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: 110px;
        }

        /* Break/Istirahat Cell */
        .kategori-cell {
            text-align: center;
            font-weight: 600;
            background: #fef3cd;
            color: #8a6d3b;
            font-size: 13px;
            border-radius: 4px;
            padding: 14px;
        }

        /* Subject Name */
        .mapel-cell {
            font-weight: 600;
            color: #11998e;
            font-size: 13px;
        }

        /* Teacher/Class Name */
        .teacher-cell {
            color: #555555;
            font-size: 13px;
            font-weight: 500;
        }

        /* Time */
        .time-cell {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #11998e;
            font-size: 12px;
            text-align: center;
            background: #f0f8f7;
            width: 140px;
            border-radius: 4px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 30px;
            background: #fef3cd;
            border-radius: 8px;
            border: 2px dashed #ffc107;
        }

        .empty-state p {
            font-size: 15px;
            color: #8a6d3b;
            font-weight: 500;
        }

        /* Legend */
        .legend {
            margin-top: 25px;
            padding: 18px 22px;
            background: #f8fafa;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .legend-title {
            font-size: 11px;
            font-weight: 700;
            color: #11998e;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .legend-content {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #495057;
            font-weight: 500;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 3px;
            flex-shrink: 0;
        }

        .legend-color.day {
            background: #11998e;
        }

        .legend-color.break {
            background: #ffc107;
        }

        .legend-color.subject {
            background: #11998e;
            opacity: 0.6;
        }

        /* Footer */
        .footer {
            margin-top: 35px;
            padding-top: 18px;
            border-top: 2px solid #11998e;
            text-align: center;
        }

        .footer .print-date {
            font-size: 11px;
            color: #11998e;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .footer .copyright {
            font-size: 10px;
            color: #999999;
        }

        /* Print Optimization */
        @media print {
            body {
                padding: 15px;
                background: white;
            }
            
            .header {
                page-break-inside: avoid;
            }
            
            .info-box {
                page-break-inside: avoid;
            }
            
            .day-section {
                page-break-inside: avoid;
            }
            
            table {
                page-break-after: auto;
            }
            
            thead {
                display: table-header-group;
            }

            tbody tr {
                page-break-inside: avoid;
            }

            .legend {
                page-break-inside: avoid;
                margin-top: 30px;
            }

            .footer {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        @if(isset($siswa))
            <h2>JADWAL PELAJARAN</h2>
            <div class="subtitle">Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</div>
        @elseif(isset($guru))
            <h2>JADWAL MENGAJAR</h2>
            <div class="subtitle">Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</div>
        @endif
    </div>

    <!-- Info Box -->
    @if(isset($siswa))
        <div class="info-box">
            <div class="info-label">Nama Siswa</div>
            <div class="info-value">{{ $siswa->nama }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Kelas</div>
            <div class="info-value">{{ $siswa->kelas->first()?->nama_kelas ?? 'Belum ada kelas' }}</div>
        </div>
    @elseif(isset($guru))
        <div class="info-box">
            <div class="info-label">Nama Guru</div>
            <div class="info-value">{{ $guru->nama }}</div>
        </div>
    @endif

    <!-- Section Divider -->
    <div class="section-divider">
        <span>Jadwal Mingguan</span>
    </div>

    <!-- Table -->
    @if(isset($jadwals) && count($jadwals) > 0)
        @foreach($jadwals as $hari => $jadwalHarian)
            <div class="day-section">
                <table>
                    <thead>
                        <tr>
                            <th>Hari</th>
                            <th>Mata Pelajaran</th>
                            @if(isset($guru))
                                <th>Kelas</th>
                            @else
                                <th>Guru Pengajar</th>
                            @endif
                            <th style="text-align: center;">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalHarian as $index => $jadwal)
                            <tr>
                                @if($index === 0)
                                    <td rowspan="{{ count($jadwalHarian) }}" class="day-column">{{ strtoupper($hari) }}</td>
                                @endif
                                
                                @if($jadwal->kategori)
                                    <td colspan="2" class="kategori-cell">
                                        {{ $jadwal->kategori->nama_kategori }}
                                    </td>
                                @else
                                    <td class="mapel-cell">
                                        {{ $jadwal->mapel ?? '-' }}
                                    </td>
                                    @if(isset($guru))
                                        <td class="teacher-cell">
                                            {{ $jadwal->kelas ? $jadwal->kelas->nama_kelas : '-' }}
                                        </td>
                                    @else
                                        <td class="teacher-cell">
                                            {{ $jadwal->guru ? $jadwal->guru->nama : '-' }}
                                        </td>
                                    @endif
                                @endif
                                
                                <td class="time-cell">
                                    {{ $jadwal->jam }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        <!-- Legend -->
        <div class="legend">
            <div class="legend-title">Keterangan</div>
            <div class="legend-content">
                <div class="legend-item">
                    <span class="legend-color day"></span>
                    Hari
                </div>
                <div class="legend-item">
                    <span class="legend-color break"></span>
                    Istirahat
                </div>
                <div class="legend-item">
                    <span class="legend-color subject"></span>
                    Mata Pelajaran
                </div>
            </div>
        </div>
    @else
        <div class="empty-state">
            <p>Jadwal belum tersedia</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div class="print-date">
            Dicetak: {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd, D MMMM YYYY - HH:mm') }} WIB
        </div>
        <div class="copyright">
            © {{ date('Y') }} Sistem Informasi Akademik - Klipaa Solusi Indonesia
        </div>
    </div>
</body>
</html>