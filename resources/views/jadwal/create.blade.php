@extends('dashboard.admin')

@section('content')
    <div class="content-header">
        <div>
            <h1>Manajemen Jadwal Untuk Kelas: <strong>{{ $kelas->nama_kelas }}</strong></h1>
            <p>Atur jadwal di bawah ini. Klik "Tambah Baris Jadwal" untuk menambah slot waktu baru.</p>
        </div>
        <div>
            <button id="add-row-btn" class="btn btn-success">Tambah Baris Jadwal</button>
            <button id="bulkSaveBtn" class="btn btn-info">Simpan Semua Jadwal</button>
            <a href="{{ route('jadwal.perKelas', $kelas->id) }}" class="btn btn-primary">Lihat Jadwal Selesai</a>
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

        let timeParts = jam ? jam.split(' - ') : ['', ''];

        let cells = `
            <td>
                <div class="time-input-container">
                    <input type="time" class="form-control time-input time-start" value="${timeParts[0]}">
                    <span>-</span>
                    <input type="time" class="form-control time-input time-end" value="${timeParts[1]}">
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