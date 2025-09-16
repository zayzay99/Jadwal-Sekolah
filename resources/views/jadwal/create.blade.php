@extends('dashboard.admin')

@section('content')
    <div class="content-header">
        <div>
            <h1>Manajemen Jadwal Untuk Kelas: <strong>{{ $kelas->nama_kelas }}</strong></h1>
            <p>Atur jadwal di bawah ini. Semua slot waktu yang tersedia ditampilkan secara default.</p>
        </div>
        <div class="flex gap-4">
            <button id="bulkSaveBtn" class="btn btn-info btn-tiny">Simpan Semua Jadwal</button>
            <a href="{{ route('jadwal.perKelas', $kelas->id) }}" class="btn btn-primary btn-tiny">Lihat Jadwal Selesai</a>
            <a href="{{ route('jadwal.pilihKelas') }}" class="btn btn-secondary btn-tiny">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <input type="hidden" id="kelas_id" value="{{ $kelas->id }}">

    <div class="table-container">
        <div class="table-responsive">
            <table class="custom-table" id="schedule-builder">
                <thead>
                    <tr>
                        <th style="width: 200px;">Jam</th>
                        @foreach ($days as $day)
                            <th>{{ $day }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="schedule-body">
                    {{-- Rows will be dynamically inserted here by JavaScript --}}
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .main-layout {
            display: flex;
        }

        .content {
            flex: 1;
            max-width: calc(100vw - 250px);
        }

        @media (max-width: 768px) {
            .content {
                max-width: 100vw;
            }
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .custom-table th,
        .custom-table td {
            vertical-align: middle;
            text-align: center;
            padding: 0.5rem;
        }

        /* PERBAIKAN UTAMA UNTUK KOLOM JAM */
        /* PERBAIKAN UTAMA UNTUK KOLOM JAM - Updated */
.custom-table th:first-child {
    width: 240px;
    min-width: 240px;
    max-width: 240px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.time-input-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 10px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 8px;
    border: 1px solid #dee2e6;
    min-height: 50px;
    width: 100%;
    box-sizing: border-box;
}

.time-input {
    width: 95px;
    min-width: 95px;
    max-width: 95px;
    padding: 8px 10px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 15px;
    font-weight: 600;
    text-align: center;
    border: 1px solid #ced4da;
    border-radius: 6px;
    background: white;
    color: #2d6a4f;
    box-sizing: border-box;
    line-height: 1.4;
    letter-spacing: 0.5px;
    margin: 0 2px;
}

.time-input:focus {
    outline: none;
    border-color: #2d6a4f;
    box-shadow: 0 0 0 2px rgba(45, 106, 79, 0.2);
}

/* Webkit specific fixes for time input */
.time-input::-webkit-datetime-edit {
    padding: 2px;
    color: #2d6a4f;
    font-weight: 600;
    font-size: 15px;
}

.time-input::-webkit-datetime-edit-fields-wrapper {
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.time-input::-webkit-datetime-edit-hour-field,
.time-input::-webkit-datetime-edit-minute-field {
    padding: 2px 3px;
    color: #2d6a4f;
    font-weight: 600;
    font-size: 15px;
    min-width: 20px;
    text-align: center;
}

.time-input::-webkit-datetime-edit-text {
    color: #2d6a4f;
    font-weight: 600;
    font-size: 15px;
    padding: 0 2px;
}

.time-separator {
    color: #2d6a4f;
    font-weight: bold;
    font-size: 16px;
    margin: 0 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 12px;
}

/* Mobile adjustments untuk kolom jam */
@media (max-width: 768px) {
    .custom-table th:first-child {
        width: 200px;
        min-width: 200px;
        max-width: 200px;
    }

    .time-input-container {
        flex-direction: column;
        gap: 5px;
        padding: 10px 8px;
        min-height: 70px;
    }

    .time-input {
        width: 90px;
        min-width: 90px;
        max-width: 90px;
        padding: 6px 8px;
        font-size: 14px;
    }

    .time-input::-webkit-datetime-edit,
    .time-input::-webkit-datetime-edit-hour-field,
    .time-input::-webkit-datetime-edit-minute-field,
    .time-input::-webkit-datetime-edit-text {
        font-size: 14px;
    }

    .time-separator {
        font-size: 14px;
        margin: 2px 0;
    }
}

/* Additional responsive for very small screens */
@media (max-width: 480px) {
    .custom-table th:first-child {
        width: 160px;
        min-width: 160px;
        max-width: 160px;
    }

    .time-input {
        width: 75px;
        min-width: 75px;
        max-width: 75px;
        font-size: 13px;
        padding: 4px 6px;
    }

    .time-input::-webkit-datetime-edit,
    .time-input::-webkit-datetime-edit-hour-field,
    .time-input::-webkit-datetime-edit-minute-field,
    .time-input::-webkit-datetime-edit-text {
        font-size: 13px;
    }

    .time-input-container {
        padding: 6px 4px;
        min-height: 60px;
    }

    .time-separator {
        font-size: 13px;
    }
}

        .schedule-select {
            min-width: 150px;
        }

        .custom-table tbody tr {
            transition: all 0.3s ease;
        }

        .custom-table tbody tr:hover {
            background-color: #f8fff9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #5a6268, #495057);
        }

        .schedule-cell-content {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .clear-selection-btn {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 11px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .clear-selection-btn:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .content-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .content-header>div:last-child {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                width: 100%;
            }

            .btn-tiny {
                padding: 10px 12px !important;
                font-size: 12px !important;
                white-space: normal;
                text-align: center;
            }

            .table-container {
                padding: 0;
                margin: 0;
                overflow-x: auto;
            }

            .custom-table {
                font-size: 13px;
                min-width: 700px;
            }

            .custom-table th,
            .custom-table td {
                padding: 8px 10px;
            }

            /* Mobile adjustments untuk kolom jam */
            .custom-table th:first-child {
                width: 180px;
                min-width: 180px;
                max-width: 180px;
            }

            .time-input-container {
                flex-direction: column;
                gap: 3px;
                padding: 8px 6px;
                min-height: 65px;
            }

            .time-input {
                width: 80px;
                min-width: 80px;
                max-width: 80px;
                padding: 5px 6px;
                font-size: 13px;
            }

            .time-separator {
                font-size: 13px;
                margin: 0;
            }

            .schedule-select {
                min-width: 130px;
                font-size: 13px;
                padding: 0.375rem;
            }

            .schedule-select.is-invalid {
                border-color: #dc3545;
                background-color: #f8d7da;
            }

            .cell-error-tooltip {
                position: absolute;
                background-color: #721c24;
                color: white;
                padding: 5px 10px;
                border-radius: 4px;
                font-size: 12px;
                z-index: 10;
                display: none;
            }
        }

        /* Additional responsive for very small screens */
        @media (max-width: 480px) {
            .custom-table {
                min-width: 650px;
            }

            .custom-table th:first-child {
                width: 140px;
                min-width: 140px;
                max-width: 140px;
            }

            .time-input {
                width: 65px;
                min-width: 65px;
                max-width: 65px;
                font-size: 11px;
                padding: 2px 3px;
            }

            .time-input-container {
                padding: 4px 2px;
                min-height: 55px;
            }

            .schedule-select {
                min-width: 110px;
                font-size: 12px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = '{{ csrf_token() }}';
            const kelasId = document.getElementById('kelas_id').value;
            const scheduleBody = document.getElementById('schedule-body');
            const bulkSaveBtn = document.getElementById('bulkSaveBtn');

            const days = @json($days);
            const kategoris = @json($kategoris->values());
            const scheduleData = @json($scheduleGrid);
            const allSchedules = @json($allSchedules);
            const availableGurus = @json($availableGurus);
            const gurus = @json($gurus);
            const timeSlots = @json($timeSlots);

            // --- OPTIONS TEMPLATE ---
            function getSelectOptions(day, jam) {
                let options = '<option value="">-- Kosong --</option>';
                options += '<optgroup label="Pelajaran">';
                if (availableGurus[day] && availableGurus[day][jam]) {
                    availableGurus[day][jam].forEach(guru => {
                        options += `<option value="guru-${guru.id}" data-mapel="${guru.pengampu}">${guru.nama} (${guru.pengampu})</option>`;
                    });
                }
                options += '</optgroup>';
                options += '<optgroup label="Kategori Khusus">';
                kategoris.forEach(kategori => {
                    options += `<option value="kategori-${kategori.id}">${kategori.nama_kategori}</option>`;
                });
                options += '</optgroup>';
                return options;
            }

            // --- DATA HELPERS ---
            const teacherClashMap = {};
            allSchedules.forEach(s => {
                if (!s.guru_id) return;
                if (!teacherClashMap[s.guru_id]) teacherClashMap[s.guru_id] = {};
                if (!teacherClashMap[s.guru_id][s.hari]) teacherClashMap[s.guru_id][s.hari] = {};
                teacherClashMap[s.guru_id][s.hari][s.jam] = s.kelas.nama_kelas;
            });

            const teacherLimits = {};
            gurus.forEach(g => {
                if (g.max_jp_per_hari) teacherLimits[g.id] = g.max_jp_per_hari;
            });

            // --- ROW TEMPLATE ---
            function createRow(jam, data, slot) {
                const tr = document.createElement('tr');
                tr.className = 'schedule-row';

                const startTime = slot.jam_mulai || '';
                const endTime = slot.jam_selesai || '';
                const timeRange = `${startTime} - ${endTime}`;

                let cells = `
    <td>
        <div class="time-display" style="padding: 8px; text-align: center;">
            ${timeRange}
        </div>
    </td>
`;

                days.forEach(day => {
                    cells += `
                        <td>
                            <div class="schedule-cell-content">
                                <select class="form-control schedule-select" data-day="${day}" data-jam="${timeRange}">
                                    ${getSelectOptions(day, jam)}
                                </select>
                                <button type="button" class="clear-selection-btn" title="Hapus Pilihan">&times;</button>
                            </div>
                        </td>
                    `;
                });

                tr.innerHTML = cells;

                // Set selected values after innerHTML is processed
                days.forEach(day => {
                    const scheduleItem = data[day];
                    const selectElement = tr.querySelector(`select[data-day="${day}"]`);

                    if (scheduleItem) {
                        let selectedValue = '';
                        if (scheduleItem.guru_id) {
                            selectedValue = `guru-${scheduleItem.guru_id}`;
                        } else if (scheduleItem.jadwal_kategori_id) {
                            selectedValue = `kategori-${scheduleItem.jadwal_kategori_id}`;
                        }
                        if (selectedValue) {
                            selectElement.value = selectedValue;
                        }
                    } else if (slot.jadwal_kategori_id) {
                        selectElement.value = `kategori-${slot.jadwal_kategori_id}`;
                    }
                });

                return tr;
            }

            // Fungsi untuk memformat waktu dari hh:mm:ss ke hh:mm
function formatTime(timeString) {
    if (!timeString) return '';
    const timeParts = timeString.split(':');
    return `${timeParts[0]}:${timeParts[1]}`;
}

            // --- VALIDATION LOGIC ---
function validateAllCells() {
    const teacherDailyJP = {};
    const rows = scheduleBody.querySelectorAll('.schedule-row');

    document.querySelectorAll('.schedule-select.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
        el.title = '';
    });

    rows.forEach(row => {
        // Gunakan format waktu yang sudah diformat
        const startTimeInput = row.querySelector('.time-start');
        const endTimeInput = row.querySelector('.time-end');
        
        // Jika menggunakan input time, ambil format hh:mm
        const startTime = startTimeInput ? formatTime(startTimeInput.value) : '';
        const endTime = endTimeInput ? formatTime(endTimeInput.value) : '';
        
        if (!startTime || !endTime || startTime >= endTime) return;

        const jam = `${startTime} - ${endTime}`;
        const durasiMenit = (new Date(`1970-01-01T${endTime}:00`) - new Date(`1970-01-01T${startTime}:00`)) / 60000;
        const jp = Math.floor(durasiMenit / 35);

        row.querySelectorAll('.schedule-select').forEach(select => {
            const selectedValue = select.value;
            if (!selectedValue || !selectedValue.startsWith('guru-')) return;

            const day = select.dataset.day;
            const guruId = selectedValue.split('-')[1];

            if (teacherClashMap[guruId] && teacherClashMap[guruId][day] &&
                teacherClashMap[guruId][day][jam]) {
                const clashKelas = teacherClashMap[guruId][day][jam];
                select.classList.add('is-invalid');
                select.title = `Bentrok! Sudah mengajar di kelas ${clashKelas}.`;
            }

            if (!teacherDailyJP[guruId]) teacherDailyJP[guruId] = {};
            if (!teacherDailyJP[guruId][day]) teacherDailyJP[guruId][day] = 0;
            teacherDailyJP[guruId][day] += jp;
        });
    });

    for (const guruId in teacherDailyJP) {
        const guruLimit = teacherLimits[guruId];
        if (!guruLimit) continue;

        for (const day in teacherDailyJP[guruId]) {
            const totalJP = teacherDailyJP[guruId][day];
            if (totalJP > guruLimit) {
                rows.forEach(row => {
                    const select = row.querySelector(`select[data-day="${day}"]`);
                    if (select.value === `guru-${guruId}`) {
                        select.classList.add('is-invalid');
                        const newTitle = `Batas harian terlampaui! (${totalJP} dari maks ${guruLimit} JP).`;
                        select.title = select.title ? `${select.title}\n${newTitle}` : newTitle;
                    }
                });
            }
        }
    }
}

            // --- INITIALIZE VIEW ---
            function initialize() {
                const scheduleByJam = {};
                for (const jamKey in scheduleData) {
                    scheduleByJam[jamKey] = {};
                    for(const dayKey in scheduleData[jamKey]) {
                         scheduleByJam[jamKey][dayKey] = scheduleData[jamKey][dayKey];
                    }
                }

                timeSlots.forEach(slot => {
                    const jam = slot.jam;
                    const rowData = scheduleByJam[jam] || {};
                    const newRow = createRow(jam, rowData, slot);
                    scheduleBody.appendChild(newRow);
                });

                if (timeSlots.length === 0) {
                    console.warn("Tidak ada slot waktu yang terdefinisi di database (tabelj).");
                    const tr = document.createElement('tr');
                    const td = document.createElement('td');
                    td.colSpan = days.length + 1;
                    td.textContent = 'Tidak ada slot waktu yang telah dibuat. Silakan buat terlebih dahulu di menu Manajemen Tabel Jam.';
                    td.style.textAlign = 'center';
                    td.style.padding = '20px';
                    tr.appendChild(td);
                    scheduleBody.appendChild(tr);
                }

                validateAllCells();
            }

            // --- EVENT LISTENERS ---
            scheduleBody.addEventListener('change', (e) => {
                if (e.target.classList.contains('schedule-select')) {
                    validateAllCells();
                }
            });
            
            scheduleBody.addEventListener('click', function(e) {
                if (e.target.classList.contains('clear-selection-btn')) {
                    const select = e.target.previousElementSibling;
                    if (select) {
                        select.value = '';
                        validateAllCells(); 
                    }
                }
            });

            bulkSaveBtn.addEventListener('click', async function() {
                this.disabled = true;
                this.textContent = 'Menyimpan...';

                if (document.querySelector('.schedule-select.is-invalid')) {
                    Swal.fire('Validasi Gagal', 'Terdapat jadwal yang bentrok atau melebihi batas mengajar guru. Perbaiki isian yang ditandai merah.', 'error');
                    this.disabled = false;
                    this.textContent = 'Simpan Semua Jadwal';
                    return;
                }

                const schedules = [];
                const rows = scheduleBody.querySelectorAll('.schedule-row');

                rows.forEach(row => {
                    const jam = row.querySelector('.schedule-select').dataset.jam;

                    row.querySelectorAll('.schedule-select').forEach(select => {
                        const selectedValue = select.value;
                        if (!selectedValue) return;

                        const day = select.dataset.day;
                        const [type, id] = selectedValue.split('-');

                        const guru = gurus.find(g => g.id == id);

                        schedules.push({
                            kelas_id: kelasId,
                            hari: day,
                            jam: jam,
                            guru_id: type === 'guru' ? id : null,
                            mapel: type === 'guru' && guru ? guru.pengampu : null,
                            jadwal_kategori_id: type === 'kategori' ? id : null
                        });
                    });
                });

                try {
                    const response = await fetch('{{ route('jadwal.bulkStore') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            schedules: schedules,
                            kelas_id: kelasId
                        })
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        await Swal.fire('Berhasil!', result.message, 'success');
                        window.location.href = '{{ route('jadwal.perKelas', $kelas->id) }}';
                    } else {
                        Swal.fire('Gagal!', result.message || 'Terjadi kesalahan saat menyimpan.', 'error');
                    }
                } catch (error) {
                    Swal.fire('Error!', 'Tidak dapat terhubung ke server.', 'error');
                } finally {
                    this.disabled = false;
                    this.textContent = 'Simpan Semua Jadwal';
                }
            });

            // --- INITIALIZE ---
            initialize();
        });
    </script>
@endpush