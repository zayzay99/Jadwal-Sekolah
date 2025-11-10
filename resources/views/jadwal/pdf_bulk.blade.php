<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Pelajaran Kelas</title>
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

        /* Footer */
        .footer {
            margin-top: 35px;
            padding-top: 18px;
            border-top: 2px solid #11998e;
            text-align: center;
            page-break-before: always; /* Ensure footer is on a new page if content is long */
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
            .class-section {
                page-break-after: always; /* Each class schedule on a new page */
            }
            .class-section:last-of-type {
                page-break-after: auto; /* No page break after the last class */
            }
            .header, .info-box, .section-divider, .day-section, .footer {
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
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>JADWAL PELAJARAN KELAS</h2>
        <div class="subtitle">Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</div>
    </div>

    @forelse($allKelasData as $data)
        <div class="class-section">
            <div class="info-box">
                <div class="info-label">Kelas</div>
                <div class="info-value">{{ $data['kelas']->nama_kelas }}</div>
            </div>

            <div class="section-divider">
                <span>Jadwal Mingguan</span>
            </div>

            @if($data['jadwals']->count() > 0)
                @foreach($data['jadwals'] as $hari => $jadwalHarian)
                    <div class="day-section">
                        <table>
                            <thead>
                                <tr>
                                    <th>Hari</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru Pengajar</th>
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
                                            <td class="teacher-cell">
                                                {{ $jadwal->guru ? $jadwal->guru->nama : '-' }}
                                            </td>
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
            @else
                <div class="empty-state">
                    <p>Jadwal belum tersedia untuk kelas {{ $data['kelas']->nama_kelas }}</p>
                </div>
            @endif
        </div>
    @empty
        <div class="empty-state">
            <p>Tidak ada kelas yang dipilih atau jadwal tidak tersedia.</p>
        </div>
    @endforelse

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