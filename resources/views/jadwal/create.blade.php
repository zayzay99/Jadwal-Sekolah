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
                        <th style="width: 180px;">Jam</th>
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
    .main-layout { display: flex; }
    .content { flex: 1; max-width: calc(100vw - 250px); }
    @media (max-width: 768px) {
        .content { max-width: 100vw; }
    }
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
    .time-input { width: 80px; padding: 0.375rem 0.5rem; background-color: #e9ecef; }
    .schedule-select { min-width: 150px; }
    .custom-table tbody tr { transition: all 0.3s ease; }
    .custom-table tbody tr:hover {
        background-color: #f8fff9;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .btn-success:hover { background: linear-gradient(135deg, #218838, #1e7e34); }
    .btn-secondary:hover { background: linear-gradient(135deg, #5a6268, #495057); }
    @media (max-width: 768px) {
        .content-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }
        .content-header > div:last-child {
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
        .table-container { padding: 0; margin: 0; overflow-x: auto; }
        .custom-table { font-size: 13px; min-width: 600px; }
        .custom-table th, .custom-table td { padding: 8px 10px; }
        .time-input-container { flex-direction: column; gap: 5px; }
        .time-input { width: 90px; padding: 0.375rem; font-size: 13px; }
        .schedule-select { min-width: 130px; font-size: 13px; padding: 0.375rem; }
        .schedule-select.is-invalid { border-color: #dc3545; background-color: #f8d7da; }
        .cell-error-tooltip {
            position: absolute; background-color: #721c24; color: white;
            padding: 5px 10px; border-radius: 4px; font-size: 12px;
            z-index: 10; display: none;
        }
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
                    <input type="time" class="form-control time-input time-start" value="${timeParts[0]}" readonly>
                    <span>-</span>
                    <input type="time" class="form-control time-input time-end" value="${timeParts[1]}" readonly>
                </div>
            </td>
        `;

        days.forEach(day => {
            const currentJam = timeParts.join(' - ');
            cells += `
                <td>
                    <div class="schedule-cell-content">
                        <select class="form-control schedule-select" data-day="${day}" data-jam="${currentJam}">
                            ${getSelectOptions(day, currentJam)}
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
        const teacherDailyJP = {};
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
            const jp = Math.floor(durasiMenit / 35);

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
        const scheduleByJam = {};
        // Create a map of the existing schedule data for quick lookup
        for (const day in scheduleData) {
            for (const jam in scheduleData[day]) {
                if (!scheduleByJam[jam]) {
                    scheduleByJam[jam] = {};
                }
                scheduleByJam[jam][day] = scheduleData[day][jam];
            }
        }

        // Loop through all predefined time slots and create a row for each
        timeSlots.forEach(slot => {
            const jam = slot.jam;
            const rowData = scheduleByJam[jam] || {};
            const newRow = createRow(jam, rowData);
            scheduleBody.appendChild(newRow);
        });

        // If there are no predefined time slots, log a warning.
        if (timeSlots.length === 0) {
            console.warn("Tidak ada slot waktu yang terdefinisi di database (tabelj).");
        }
        
        validateAllCells();
    }

    // --- EVENT LISTENERS ---
    scheduleBody.addEventListener('change', (e) => {
        if (e.target.classList.contains('schedule-select')) {
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
            const jam = `${startTime} - ${endTime}`;

            row.querySelectorAll('.schedule-select').forEach(select => {
                const selectedValue = select.value;
                if (!selectedValue) return;

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
                    // Find the teacher from the main list to get the definitive subject name
                    const guru = gurus.find(g => g.id == id);
                    if (guru) {
                        scheduleData.mapel = guru.pengampu;
                    } else {
                        // Fallback for safety, though it should not be reached
                        scheduleData.mapel = selectedOption.dataset.mapel;
                    }
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