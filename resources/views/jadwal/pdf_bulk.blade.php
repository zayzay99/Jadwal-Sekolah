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

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: 'Segoe UI', 'Arial', sans-serif;
            padding: 10px;
            background: #ffffff;
            color: #2c3e50;
            line-height: 1.2;
            font-size: 9px;
        }

        /* Main Header - Full Width */
        .main-header {
            text-align: center;
            margin-bottom: 12px;
            padding: 12px 15px;
            background: linear-gradient(135deg, #11998e 0%, #0d7377 100%);
            border-radius: 5px;
            color: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .main-header h2 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 3px;
            letter-spacing: 0.5px;
        }

        .main-header .subtitle {
            font-size: 9px;
            opacity: 0.95;
            font-weight: 500;
        }

        /* Container for 2 columns */
        .schedule-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        /* Class Section */
        .class-section {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        /* Info Box - Compact */
        .info-box {
            background: linear-gradient(to right, #f0f8f7, #ffffff);
            border-left: 3px solid #11998e;
            padding: 6px 10px;
            margin-bottom: 8px;
            border-radius: 3px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .info-box .info-label {
            font-size: 7px;
            color: #11998e;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 2px;
            font-weight: 700;
        }

        .info-box .info-value {
            font-size: 10px;
            font-weight: 600;
            color: #2c3e50;
        }

        /* Section Divider - Compact */
        .section-divider {
            margin: 10px 0 8px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-divider::before,
        .section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #11998e;
        }

        .section-divider span {
            font-size: 8px;
            font-weight: 700;
            color: #11998e;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            white-space: nowrap;
        }

        /* Table - Horizontal Format - Very Compact */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border-radius: 3px;
            overflow: hidden;
            font-size: 6.5px;
        }

        .schedule-table thead {
            background: #11998e;
            color: white;
        }

        .schedule-table th {
            padding: 5px 4px;
            font-size: 6px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: center;
            border-right: 1px solid rgba(255,255,255,0.2);
            vertical-align: middle;
        }

        .schedule-table th:first-child {
            text-align: left;
            padding-left: 6px;
            width: 55px;
        }

        .schedule-table th:last-child {
            border-right: none;
        }

        .schedule-table tbody tr {
            border-bottom: 1px solid #ecf0f1;
        }

        .schedule-table tbody tr:last-child {
            border-bottom: none;
        }

        .schedule-table tbody tr:nth-child(even) {
            background: #f8fafa;
        }

        .schedule-table tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        .schedule-table td {
            padding: 4px 3px;
            font-size: 6.5px;
            color: #2c3e50;
            vertical-align: top;
            border-right: 1px solid #ecf0f1;
            text-align: center;
        }

        .schedule-table td:first-child {
            text-align: left;
            font-weight: 600;
            color: #11998e;
            background: #f0f8f7;
            padding-left: 6px;
        }

        .schedule-table td:last-child {
            border-right: none;
        }

        /* Subject Cell */
        .subject-name {
            font-weight: 600;
            color: #11998e;
            margin-bottom: 2px;
            font-size: 6.5px;
            line-height: 1.3;
        }

        .teacher-name {
            font-size: 6px;
            color: #555555;
            font-weight: 500;
            line-height: 1.2;
        }

        /* Break Cell */
        .break-cell {
            background: #fef3cd !important;
            color: #8a6d3b;
            font-weight: 600;
            font-size: 6.5px;
            padding: 4px !important;
        }

        /* Empty Cell */
        .empty-cell {
            color: #bdc3c7;
            font-size: 6px;
            font-style: italic;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 20px 15px;
            background: #fef3cd;
            border-radius: 4px;
            border: 2px dashed #ffc107;
        }

        .empty-state p {
            font-size: 9px;
            color: #8a6d3b;
            font-weight: 500;
        }

        /* Footer */
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #11998e;
            text-align: center;
            clear: both;
        }

        .footer .print-date {
            font-size: 7px;
            color: #11998e;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .footer .copyright {
            font-size: 6px;
            color: #999999;
        }

        /* Page Break Control */
        .page-break {
            page-break-after: always;
            break-after: always;
        }

        /* Print Optimization */
        @media print {
            body {
                padding: 8px;
                background: white;
            }
            
            .main-header {
                margin-bottom: 10px;
                padding: 10px 12px;
            }
            
            .main-header h2 {
                font-size: 14px;
            }
            
            .main-header .subtitle {
                font-size: 8px;
            }
            
            .schedule-container {
                gap: 12px;
                margin-bottom: 12px;
            }
            
            .info-box {
                padding: 5px 8px;
                margin-bottom: 6px;
            }
            
            .section-divider {
                margin: 8px 0 6px 0;
            }
            
            .schedule-table {
                font-size: 6px;
            }
            
            .schedule-table th {
                padding: 4px 3px;
                font-size: 5.5px;
            }
            
            .schedule-table th:first-child {
                width: 50px;
            }
            
            .schedule-table td {
                padding: 3px 2px;
                font-size: 6px;
            }
            
            .subject-name {
                font-size: 6px;
            }
            
            .teacher-name {
                font-size: 5.5px;
            }
            
            .break-cell {
                font-size: 6px;
            }
            
            .footer {
                margin-top: 12px;
                padding-top: 8px;
            }
            
            .footer .print-date {
                font-size: 6px;
            }
            
            .footer .copyright {
                font-size: 5px;
            }

            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <div class="main-header">
        <h2>JADWAL PELAJARAN KELAS</h2>
        <div class="subtitle">Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</div>
    </div>

    @if(count($allKelasData) > 0)
        @foreach(array_chunk($allKelasData, 2) as $chunkIndex => $kelasChunk)
            <div class="schedule-container">
                @foreach($kelasChunk as $data)
                    <div class="class-section">
                        <div class="info-box">
                            <div class="info-label">Kelas</div>
                            <div class="info-value">{{ $data['kelas']->nama_kelas }}</div>
                        </div>

                        <div class="section-divider">
                            <span>Jadwal Mingguan</span>
                        </div>

                        @if($data['jadwals']->count() > 0)
                            @php
                                // Define the correct order of days
                                $daysOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                
                                // Get the days that actually have schedules from the data
                                $activeDays = $data['jadwals']->keys();
                                
                                // Sort the active days according to the defined order
                                $sortedActiveDays = collect($daysOrder)->filter(function($day) use ($activeDays) {
                                    return $activeDays->contains($day);
                                });

                                // Collect all unique time slots from ALL active days
                                $allTimeSlots = [];
                                foreach($data['jadwals'] as $hari => $jadwalHarian) {
                                    foreach($jadwalHarian as $jadwal) {
                                        $timeSlot = $jadwal->jam;
                                        if (!in_array($timeSlot, $allTimeSlots)) {
                                            $allTimeSlots[] = $timeSlot;
                                        }
                                    }
                                }
                                
                                // Sort time slots chronologically based on start time
                                usort($allTimeSlots, function($a, $b) {
                                    // Extracts HH:MM part for comparison
                                    $startTimeA = substr($a, 0, 5);
                                    $startTimeB = substr($b, 0, 5);
                                    return strtotime($startTimeA) - strtotime($startTimeB);
                                });
                                
                                // Create a mapping: [time][day] => jadwal object
                                $scheduleGrid = [];
                                foreach($data['jadwals'] as $hari => $jadwalHarian) {
                                    foreach($jadwalHarian as $jadwal) {
                                        $scheduleGrid[$jadwal->jam][$hari] = $jadwal;
                                    }
                                }
                            @endphp

                            <table class="schedule-table">
                                <thead>
                                    <tr>
                                        <th>JAM</th>
                                        @foreach($sortedActiveDays as $day)
                                            <th>{{ strtoupper($day) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allTimeSlots as $timeSlot)
                                        <tr>
                                            <td>{{ $timeSlot }}</td>
                                            @foreach($sortedActiveDays as $day)
                                                @if(isset($scheduleGrid[$timeSlot][$day]))
                                                    @php $jadwal = $scheduleGrid[$timeSlot][$day]; @endphp
                                                    
                                                    @if($jadwal->kategori)
                                                        <td class="break-cell">
                                                            {{ $jadwal->kategori->nama_kategori }}
                                                        </td>
                                                    @else
                                                        <td>
                                                            <div class="subject-name">{{ $jadwal->mapel ?? '-' }}</div>
                                                            <div class="teacher-name">{{ $jadwal->guru ? $jadwal->guru->nama : '-' }}</div>
                                                        </td>
                                                    @endif
                                                @else
                                                    <td class="empty-cell">-</td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state">
                                <p>Jadwal belum tersedia untuk kelas {{ $data['kelas']->nama_kelas }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            @if(!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @else
        <div class="empty-state">
            <p>Tidak ada kelas yang dipilih atau jadwal tidak tersedia.</p>
        </div>
    @endif

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