@extends('dashboard.admin')

@section('content')
    <div class="content-header">
        <div>
            <h1>Manajemen Jadwal Untuk Kelas: <strong>{{ $kelas->nama_kelas }}</strong></h1>
            <p>Atur jadwal di bawah ini. Klik "Tambah Baris Jadwal" untuk menambah slot waktu baru.</p>
        </div>
        <div class="flex gap-4">
            <button id="add-row-btn" class="btn btn-success btn-tiny">Tambah Baris Jadwal</button>
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
                        <th style="width: 180px;">Jam</th>
                        @foreach ($days as $day)
                            <th>{{ $day }}</th>
                        @endforeach
                        <th style="width: 60px;">Aksi</th>
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

    /* Content styles */
    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .custom-table th, .custom-table td {
        vertical-align: middle;
        text-align: center;
        padding: 0.5rem;
    }
    
    .time-input-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .time-input {
        width: 80px;
        padding: 0.375rem 0.5rem;
    }
    
    .schedule-select {
        min-width: 150px;
    }
    
    .delete-row-btn {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        border: 1px solid #dc3545;
        background-color: #f8d7da;
        color: #721c24;
        font-size: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .delete-row-btn:hover {
        background-color: #dc3545;
        color: white;
    }

    /* Hover effects untuk tabel */
    .custom-table tbody tr {
        transition: all 0.3s ease;
    }

    .custom-table tbody tr:hover {
        background-color: #f8fff9;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    /* Button hover effects */
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #218838, #1e7e34);
    }

    

    

    .btn-secondary:hover {
        background: linear-gradient(135deg, #5a6268, #495057);
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
        .content-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px; /* Increased gap for better separation */
        }
        
        .content-header > div:last-child {
            display: grid; /* Use grid for button alignment */
            grid-template-columns: 1fr 1fr; /* Two neat columns */
            gap: 10px;
            width: 100%;
        }
        
        .btn-tiny {
            /* Remove flex: 1 as grid handles sizing */
            padding: 10px 12px !important; /* Slightly more padding */
            font-size: 12px !important;
            white-space: normal; /* Allow text to wrap if needed */
            text-align: center; /* Center text */
        }
        
        .table-container {
            padding: 0; /* Remove padding to allow table to use full width */
            margin: 0;
            overflow-x: auto;
        }
        
        .custom-table {
            font-size: 13px; /* Slightly smaller font for more density */
            min-width: 600px;
        }

        .custom-table th, .custom-table td {
            padding: 8px 10px; /* Adjust padding */
        }
        
        .time-input-container {
            flex-direction: column;
            gap: 5px;
        }
        
        .time-input {
            width: 90px; /* A bit more width for time inputs */
            padding: 0.375rem;
            font-size: 13px;
        }
        
        .schedule-select {
            min-width: 130px;
            font-size: 13px;
            padding: 0.375rem;
        }
        
        .delete-row-btn {
            width: 32px;
            height: 32px;
            font-size: 18px;
        } HEAD
        .delete-row-btn:hover {
            background-color: #dc3545;
            color: white;
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
    </style>
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = '{{ csrf_token() }}';
    const kelasId = document.getElementById('kelas_id').value;
    const scheduleBody = document.getElementById('schedule-body');
    const addRowBtn = document.getElementById('add-row-btn');
    const bulkSaveBtn = document.getElementById('bulkSaveBtn');

    const days = @json($days);
    const gurus = @json($gurus->values());
    const kategoris = @json($kategoris->values());
    const scheduleData = @json($scheduleGrid);
    const allSchedules = @json($allSchedules);

    // --- OPTIONS TEMPLATE ---
    function getSelectOptions() {
        let options = '<option value="">-- Kosong --</option>';
        options += '<optgroup label="Pelajaran">';
        gurus.forEach(guru => {
            options += `<option value="guru-${guru.id}" data-mapel="${guru.pengampu}">${guru.nama} (${guru.pengampu})</option>`;
        });
        options += '</optgroup>';
        options += '<optgroup label="Kategori Khusus">';
        kategoris.forEach(kategori => {
            options += `<option value="kategori-${kategori.id}">${kategori.nama_kategori}</option>`;
        });
        options += '</optgroup>';
        return options;
    }
    const selectOptionsHtml = getSelectOptions();

    // --- DATA HELPERS ---
    const teacherClashMap = {}; // { guru_id: { hari: { jam: 'Nama Kelas' } } }
    allSchedules.forEach(s => {
        if (!s.guru_id) return;
        if (!teacherClashMap[s.guru_id]) teacherClashMap[s.guru_id] = {};
        if (!teacherClashMap[s.guru_id][s.hari]) teacherClashMap[s.guru_id][s.hari] = {};
        teacherClashMap[s.guru_id][s.hari][s.jam] = s.kelas.nama_kelas;
    });

    const teacherLimits = {}; // { guru_id: max_jp_per_hari }
    gurus.forEach(g => {
        if (g.max_jp_per_hari) teacherLimits[g.id] = g.max_jp_per_hari;
    });

    // --- ROW TEMPLATE ---
    function createRow(jam = '', data = {}) {
        const tr = document.createElement('tr');
        tr.className = 'schedule-row';

        let timeParts = ['', ''];
        if (jam && jam.includes('-')) {
            timeParts = jam.split('-').map(t => t.trim());
        }

        let cells = `
            <td>
                <div class="time-input-container">
                    <input type="time" class="form-control time-input time-start" value="${timeParts[0] || ''}">
                    <span>-</span>
                    <input type="time" class="form-control time-input time-end" value="${timeParts[1] || ''}">
                </div>
            </td>
        `;

        days.forEach(day => {
            cells += `
                <td>
                    <select class="form-control schedule-select" data-day="${day}" data-jam="${jam}">
                        ${selectOptionsHtml}
                    </select>
                </td>
            `;
        });

        cells += `
            <td>
                <button class="delete-row-btn" title="Hapus Baris">&times;</button>
            </td>
        `;

        tr.innerHTML = cells;

        // Set selected values after innerHTML is processed
        days.forEach(day => {
            const scheduleItem = data[day];
            if (scheduleItem) {
                let selectedValue = '';
                if (scheduleItem.guru_id) {
                    selectedValue = `guru-${scheduleItem.guru_id}`;
                } else if (scheduleItem.jadwal_kategori_id) {
                    selectedValue = `kategori-${scheduleItem.jadwal_kategori_id}`;
                }
                if (selectedValue) {
                    tr.querySelector(`select[data-day="${day}"]`).value = selectedValue;
                }
            }
        });

        return tr;
    }

    // --- VALIDATION LOGIC ---
    function validateAllCells() {
        const teacherDailyJP = {}; // { guru_id: { hari: total_jp } }
        const rows = scheduleBody.querySelectorAll('.schedule-row');

        // Reset all previous errors
        document.querySelectorAll('.schedule-select.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
            el.title = '';
        });

        rows.forEach(row => {
            const startTime = row.querySelector('.time-start').value;
            const endTime = row.querySelector('.time-end').value;
            if (!startTime || !endTime || startTime >= endTime) return;

            const jam = `${startTime} - ${endTime}`;
            const durasiMenit = (new Date(`1970-01-01T${endTime}:00`) - new Date(`1970-01-01T${startTime}:00`)) / 60000;
            const jp = Math.floor(durasiMenit / 35); // 1 JP = 35 menit

            row.querySelectorAll('.schedule-select').forEach(select => {
                const selectedValue = select.value;
                if (!selectedValue || !selectedValue.startsWith('guru-')) return;

                const day = select.dataset.day;
                const guruId = selectedValue.split('-')[1];

                // 1. Check for clashes with other schedules
                if (teacherClashMap[guruId] && teacherClashMap[guruId][day] && teacherClashMap[guruId][day][jam]) {
                    const clashKelas = teacherClashMap[guruId][day][jam];
                    select.classList.add('is-invalid');
                    select.title = `Bentrok! Sudah mengajar di kelas ${clashKelas}.`;
                }

                // 2. Accumulate daily JP
                if (!teacherDailyJP[guruId]) teacherDailyJP[guruId] = {};
                if (!teacherDailyJP[guruId][day]) teacherDailyJP[guruId][day] = 0;
                teacherDailyJP[guruId][day] += jp;
            });
        });

        // 3. Check daily JP limits
        for (const guruId in teacherDailyJP) {
            const guruLimit = teacherLimits[guruId];
            if (!guruLimit) continue;

            for (const day in teacherDailyJP[guruId]) {
                const totalJP = teacherDailyJP[guruId][day];
                if (totalJP > guruLimit) {
                    // Find all selects for this guru on this day and mark them as invalid
                    rows.forEach(row => {
                        const select = row.querySelector(`select[data-day="${day}"]`);
                        if (select.value === `guru-${guruId}`) {
                            select.classList.add('is-invalid');
                            const currentTitle = select.title;
                            const newTitle = `Batas harian terlampaui! (${totalJP} dari maks ${guruLimit} JP).`;
                            select.title = currentTitle ? `${currentTitle}\n${newTitle}` : newTitle;
                        }
                    });
                }
            }
        }
    }

    // --- INITIALIZE VIEW ---
    function initialize() {
        // Group schedule data by jam
        const groupedByJam = {};
        for (const day in scheduleData) {
            for (const jam in scheduleData[day]) {
                if (!groupedByJam[jam]) {
                    groupedByJam[jam] = {};
                }
                groupedByJam[jam][day] = scheduleData[day][jam];
            }
        }

        // Get sorted time slots
        const sortedTimeSlots = Object.keys(groupedByJam).sort((a, b) => {
            return a.split(' - ')[0].localeCompare(b.split(' - ')[0]);
        });

        // Create and append rows for existing data
        sortedTimeSlots.forEach(jam => {
            const rowData = groupedByJam[jam];
            const newRow = createRow(jam, rowData);
            scheduleBody.appendChild(newRow);
        });

        // Add one empty row if no data exists
        if (sortedTimeSlots.length === 0) {
            scheduleBody.appendChild(createRow());
        }
        validateAllCells();
    }

    // --- EVENT LISTENERS ---
    addRowBtn.addEventListener('click', () => {
        scheduleBody.appendChild(createRow());
    });

    scheduleBody.addEventListener('click', (e) => {
        if (e.target.classList.contains('delete-row-btn')) {
            e.target.closest('.schedule-row').remove();
        }
    });

    scheduleBody.addEventListener('change', (e) => {
        if (e.target.classList.contains('schedule-select') || e.target.classList.contains('time-input')) {
            validateAllCells();
        }
    });

    bulkSaveBtn.addEventListener('click', async function() {
        this.disabled = true;
        this.textContent = 'Menyimpan...';

        const schedules = [];
        const rows = scheduleBody.querySelectorAll('.schedule-row');
        let validationError = false;

        if (document.querySelector('.schedule-select.is-invalid')) {
            Swal.fire('Validasi Gagal', 'Terdapat jadwal yang bentrok atau melebihi batas mengajar guru. Perbaiki isian yang ditandai merah.', 'error');
            validationError = true;
        }

        rows.forEach(row => {
            const startTime = row.querySelector('.time-start').value;
            const endTime = row.querySelector('.time-end').value;

            if (!startTime || !endTime) {
                Swal.fire('Error', 'Setiap baris harus memiliki Waktu Mulai dan Selesai.', 'error');
                validationError = true;
                return;
            }
            if (startTime >= endTime) {
                Swal.fire('Error', `Waktu Selesai harus setelah Waktu Mulai untuk baris (${startTime} - ${endTime}).`, 'error');
                validationError = true;
                return;
            }

            const jam = `${startTime} - ${endTime}`;

            row.querySelectorAll('.schedule-select').forEach(select => {
                const selectedValue = select.value;
                if (!selectedValue) return; // Skip if empty

                const day = select.dataset.day;
                const selectedOption = select.options[select.selectedIndex];
                const [type, id] = selectedValue.split('-');

                let scheduleData = {
                    kelas_id: kelasId,
                    hari: day,
                    jam: jam,
                    guru_id: null,
                    mapel: null,
                    jadwal_kategori_id: null
                };

                if (type === 'guru') {
                    scheduleData.guru_id = id;
                    scheduleData.mapel = selectedOption.dataset.mapel;
                } else if (type === 'kategori') {
                    scheduleData.jadwal_kategori_id = id;
                }
                schedules.push(scheduleData);
            });
        });

        if (validationError) {
            this.disabled = false;
            this.textContent = 'Simpan Semua Jadwal';
            return;
        }

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