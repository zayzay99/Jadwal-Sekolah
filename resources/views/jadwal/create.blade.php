@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <div>
        <h1>Pembangun Jadwal: <strong>{{ $kelas->nama_kelas }}</strong></h1>
        <p>Klik tombol `+` untuk menambah jadwal, lalu klik "Simpan Semua Jadwal" jika sudah selesai.</p>
    </div>
    <div>
        <button id="bulkSaveBtn" class="btn btn-info">Simpan Semua Jadwal</button>
        <a href="{{ route('jadwal.perKelas', $kelas->id) }}" class="btn btn-primary">Lihat Jadwal Selesai</a>
    </div>
</div>

<input type="hidden" id="kelas_id" value="{{ $kelas->id }}">

<div class="table-container">
    <div class="table-responsive">
        <table class="custom-table schedule-grid" id="schedule-builder">
            <thead>
                <tr>
                    <th class="time-col">Jam</th>
                    @foreach($days as $day)
                        <th>{{ $day }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="schedule-grid-body">
                @foreach($timeSlots as $time)
                    <tr data-time-id="{{ $time->id }}">
                        <td class="time-col">
                            <span>{{ $time->jam }}</span>
                        </td>
                        @foreach($days as $hari)
                            <td data-hari="{{ $hari }}" data-jam="{{ $time->jam }}">
                                @if(isset($scheduleGrid[$hari][$time->jam]))
                                    @php $jadwal = $scheduleGrid[$hari][$time->jam]; @endphp
                                    <div class="schedule-item" data-guru-id="{{ $jadwal->guru_id }}">
                                        <strong class="mapel">{{ $jadwal->mapel }}</strong>
                                        <button class="delete-schedule-btn" title="Hapus Jadwal">&times;</button>
                                    </div>
                                @else
                                    <button class="add-schedule-btn">+</button>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal untuk Tambah Jadwal -->
<div id="addScheduleModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Set Mata Pelajaran</h5>
            <button type="button" class="close-btn" data-modal-id="addScheduleModal">&times;</button>
        </div>
        <div class="modal-body">
            <p>Anda akan menambahkan jadwal untuk: <strong id="modal-info"></strong></p>
            <div class="form-group">
                <label for="guru-select">Pilih Guru (Mata Pelajaran):</label>
                <select id="guru-select" class="form-control">
                    {{-- Options will be populated by JavaScript --}}
                </select>
            </div>
             <div class="form-group">
                <label for="mapel-input">Mata Pelajaran:</label>
                <input type="text" id="mapel-input" class="form-control" readonly>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close-btn" data-modal-id="addScheduleModal">Batal</button>
            <button type="button" class="btn btn-primary" id="setScheduleBtn">Set Jadwal</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .content-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .schedule-grid th, .schedule-grid td { text-align: center; vertical-align: middle; height: 70px; padding: 4px; border: 1px solid #e9ecef; }
    .schedule-grid .time-col { font-weight: bold; width: 150px; background-color: #f8f9fa; position: relative; }
    .add-schedule-btn { width: 35px; height: 35px; border-radius: 50%; border: 2px dashed #adb5bd; background-color: #f8f9fa; color: #adb5bd; font-size: 20px; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; margin: auto; }
    .add-schedule-btn:hover { background-color: #e9ecef; color: #495057; border-style: solid; }
    .schedule-item { position: relative; padding: 8px; border-radius: 6px; background-color: #d4edda; border-left: 5px solid #28a745; text-align: left; height: 100%; display: flex; flex-direction: column; justify-content: center; }
    .schedule-item .mapel { font-size: 0.9em; color: #155724; display: block; font-weight: 600; }
    .delete-schedule-btn { position: absolute; top: -5px; right: -5px; width: 20px; height: 20px; border-radius: 50%; background-color: #dc3545; color: white; border: none; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s ease; }
    .schedule-item:hover .delete-schedule-btn { opacity: 1; }
    .modal { position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; overflow: hidden; outline: 0; background-color: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; }
    .modal.show { display: flex; }
    .modal-content { position: relative; background-color: #fff; border-radius: .3rem; max-width: 500px; width: 100%; margin: 1.75rem auto; box-shadow: 0 0.5rem 1rem rgba(0,0,0,.5); }
    .modal-header { display: flex; align-items: flex-start; justify-content: space-between; padding: 1rem 1rem; border-bottom: 1px solid #dee2e6; }
    .modal-header h5 { margin-bottom: 0; line-height: 1.5; font-size: 1.25rem; }
    .modal-header .close-btn { padding: 1rem 1rem; margin: -1rem -1rem -1rem auto; background-color: transparent; border: 0; font-size: 1.5rem; font-weight: 700; line-height: 1; color: #000; text-shadow: 0 1px 0 #fff; opacity: .5; cursor: pointer; }
    .modal-body { position: relative; flex: 1 1 auto; padding: 1rem; }
    .modal-footer { display: flex; align-items: center; justify-content: flex-end; padding: 1rem; border-top: 1px solid #dee2e6; }
    .modal-footer .btn { margin-left: .25rem; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '{{ csrf_token() }}';
    const scheduleBody = document.getElementById('schedule-grid-body');
    const kelasId = document.getElementById('kelas_id').value;
    
    // --- STATE MANAGEMENT ---
    let tempSchedule = {};
    const initialGurus = @json($gurus->values()); // Ensure it's an array
    const daysOrder = @json($days);
    const timeSlotsOrder = @json($timeSlots->pluck('jam'));

    // Initialize tempSchedule with existing data from the server
    @foreach($timeSlots as $time)
        @foreach($days as $hari)
            @if(isset($scheduleGrid[$hari][$time->jam]))
                if (!tempSchedule['{{ $hari }}']) {
                    tempSchedule['{{ $hari }}'] = {};
                }
                tempSchedule['{{ $hari }}']['{{ $time->jam }}'] = {
                    guru_id: '{{ $scheduleGrid[$hari][$time->jam]->guru_id }}',
                    mapel: '{{ $scheduleGrid[$hari][$time->jam]->mapel }}',
                };
            @endif
        @endforeach
    @endforeach

    // --- Modal Management ---
    const addScheduleModal = document.getElementById('addScheduleModal');

    function openModal() {
        addScheduleModal.style.display = 'flex';
        addScheduleModal.classList.add('show');
    }

    function closeModal() {
        addScheduleModal.style.display = 'none';
        addScheduleModal.classList.remove('show');
    }

    document.querySelectorAll('.close-btn').forEach(btn => {
        btn.addEventListener('click', () => closeModal());
    });

    window.addEventListener('click', (e) => {
        if (e.target === addScheduleModal) closeModal();
    });

    // --- GURU & MAPEL MANAGEMENT ---
    const guruSelect = document.getElementById('guru-select');
    const mapelInput = document.getElementById('mapel-input');

    function updateAvailableGurus() {
        const guruCounts = {};
        Object.values(tempSchedule).forEach(daySchedule => {
            Object.values(daySchedule).forEach(entry => {
                guruCounts[entry.guru_id] = (guruCounts[entry.guru_id] || 0) + 1;
            });
        });

        const availableGurus = initialGurus.filter(guru => (guruCounts[guru.id] || 0) < 3);
        
        guruSelect.innerHTML = '<option value="">-- Pilih Guru --</option>';
        availableGurus.forEach(guru => {
            const option = document.createElement('option');
            option.value = guru.id;
            option.dataset.mapel = guru.pengampu;
            option.textContent = `${guru.nama} (${guru.pengampu})`;
            guruSelect.appendChild(option);
        });
        mapelInput.value = '';
    }

    guruSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        mapelInput.value = selectedOption ? (selectedOption.dataset.mapel || '') : '';
    });

    // --- SCHEDULE CELL MANAGEMENT ---
    const modalInfo = document.getElementById('modal-info');
    let activeCell = null;

    function updateCellWithSchedule(cell, jadwal) {
        cell.innerHTML = `
            <div class="schedule-item" data-guru-id="${jadwal.guru_id}">
                <strong class="mapel">${jadwal.mapel}</strong>
                <button class="delete-schedule-btn" title="Hapus Jadwal">&times;</button>
            </div>
        `;
    }

    function updateCellWithAddButton(cell) {
        cell.innerHTML = `<button class="add-schedule-btn">+</button>`;
    }

    // --- EVENT LISTENERS ---
    document.getElementById('setScheduleBtn').addEventListener('click', function() {
        const guruId = guruSelect.value;
        const selectedOption = guruSelect.options[guruSelect.selectedIndex];

        if (!guruId || !selectedOption) {
            Swal.fire('Error', 'Silakan pilih guru terlebih dahulu.', 'error');
            return;
        }
        
        const mapel = selectedOption.dataset.mapel;
        const hari = activeCell.dataset.hari;
        const jam = activeCell.dataset.jam;

        if (!tempSchedule[hari]) tempSchedule[hari] = {};
        tempSchedule[hari][jam] = { guru_id: guruId, mapel: mapel };

        updateCellWithSchedule(activeCell, tempSchedule[hari][jam]);
        closeModal();
    });

    scheduleBody.addEventListener('click', function(e) {
        const target = e.target;

        if (target.classList.contains('add-schedule-btn')) {
            activeCell = target.parentElement;
            modalInfo.textContent = `${activeCell.dataset.hari}, Jam ${activeCell.dataset.jam}`;
            updateAvailableGurus();
            openModal();
        }

        if (target.classList.contains('delete-schedule-btn')) {
            const cell = target.closest('td');
            const hari = cell.dataset.hari;
            const jam = cell.dataset.jam;

            if (tempSchedule[hari] && tempSchedule[hari][jam]) {
                delete tempSchedule[hari][jam];
            }

            updateCellWithAddButton(cell);
        }
    });

    // --- BULK SAVE ---
    document.getElementById('bulkSaveBtn').addEventListener('click', async function() {
        const orderedSchedule = [];
        daysOrder.forEach(hari => {
            if (tempSchedule[hari]) {
                timeSlotsOrder.forEach(jam => {
                    if (tempSchedule[hari][jam]) {
                        orderedSchedule.push({
                            kelas_id: kelasId,
                            guru_id: tempSchedule[hari][jam].guru_id,
                            mapel: tempSchedule[hari][jam].mapel,
                            hari: hari,
                            jam: jam
                        });
                    }
                });
            }
        });

        this.disabled = true;
        this.textContent = 'Menyimpan...';

        try {
            const response = await fetch('{{ route("jadwal.bulkStore") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ schedules: orderedSchedule, kelas_id: kelasId })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                await Swal.fire('Berhasil!', result.message, 'success');
                window.location.href = '{{ route("jadwal.perKelas", $kelas->id) }}';
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
});
</script>
@endpush
