@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <div>
        <h1>Pembangun Jadwal: <strong>{{ $kelas->nama_kelas }}</strong></h1>
        <p>Klik tombol `+` untuk menambah jadwal, atau kelola jam pelajaran.</p>
    </div>
    <div>
        <button id="addTimeBtn" class="btn btn-success">Tambah Jam</button>
        <a href="{{ route('jadwal.perKelas', $kelas->id) }}" class="btn btn-primary">Lihat Jadwal Selesai</a>
    </div>
</div>

{{-- Hidden input untuk menyimpan ID kelas yang akan digunakan oleh JavaScript --}}
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
                            <button class="delete-time-btn" data-id="{{ $time->id }}" title="Hapus jam ini">&times;</button>
                        </td>
                        @foreach($days as $hari)
                            <td data-hari="{{ $hari }}" data-jam="{{ $time->jam }}">
                                @if(isset($scheduleGrid[$hari][$time->jam]))
                                    @php $jadwal = $scheduleGrid[$hari][$time->jam]; @endphp
                                    <div class="schedule-item" data-jadwal-id="{{ $jadwal->id }}">
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
                    <option value="">-- Pilih Guru --</option>
                    @foreach($gurus as $guru)
                        <option value="{{ $guru->id }}" data-mapel="{{ $guru->pengampu }}">{{ $guru->nama }} ({{ $guru->pengampu }})</option>
                    @endforeach
                </select>
            </div>
             <div class="form-group">
                <label for="mapel-input">Mata Pelajaran:</label>
                <input type="text" id="mapel-input" class="form-control" readonly>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close-btn" data-modal-id="addScheduleModal">Batal</button>
            <button type="button" class="btn btn-primary" id="saveScheduleBtn">Simpan Jadwal</button>
        </div>
    </div>
</div>

<!-- Modal untuk Tambah Jam -->
<div id="addTimeModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Tambah Slot Jam Baru</h5>
            <button type="button" class="close-btn" data-modal-id="addTimeModal">&times;</button>
        </div>
        <form id="addTimeForm">
            <div class="modal-body">
                <div class="form-group">
                    <label for="jam_mulai">Jam Mulai:</label>
                    <input type="time" id="jam_mulai" name="jam_mulai" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="jam_selesai">Jam Selesai:</label>
                    <input type="time" id="jam_selesai" name="jam_selesai" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-modal-id="addTimeModal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Jam</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .content-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .schedule-grid th, .schedule-grid td { text-align: center; vertical-align: middle; height: 70px; padding: 4px; border: 1px solid #e9ecef; }
    .schedule-grid .time-col { font-weight: bold; width: 150px; background-color: #f8f9fa; position: relative; }
    .time-col span { display: inline-block; }
    .delete-time-btn { position: absolute; top: 2px; right: 2px; width: 20px; height: 20px; border-radius: 50%; background-color: #ff4d4d; color: white; border: none; font-size: 12px; cursor: pointer; display: none; align-items: center; justify-content: center; }
    .time-col:hover .delete-time-btn { display: flex; }
    .add-schedule-btn { width: 35px; height: 35px; border-radius: 50%; border: 2px dashed #adb5bd; background-color: #f8f9fa; color: #adb5bd; font-size: 20px; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; margin: auto; }
    .add-schedule-btn:hover { background-color: #e9ecef; color: #495057; border-style: solid; }
    .schedule-item { position: relative; padding: 8px; border-radius: 6px; background-color: #d4edda; border-left: 5px solid #28a745; text-align: left; height: 100%; display: flex; flex-direction: column; justify-content: center; }
    .schedule-item .mapel { font-size: 0.9em; color: #155724; display: block; font-weight: 600; }
    .delete-schedule-btn { position: absolute; top: -5px; right: -5px; width: 20px; height: 20px; border-radius: 50%; background-color: #dc3545; color: white; border: none; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s ease; }
    .schedule-item:hover .delete-schedule-btn { opacity: 1; }
    /* Modal Styles */
    .modal { position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; overflow: hidden; outline: 0; background-color: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; }
    .modal.show { display: flex; }
    .modal-content { position: relative; background-color: #fff; border-radius: .3rem; max-width: 500px; width: 100%; margin: 1.75rem auto; box-shadow: 0 0.5rem 1rem rgba(0,0,0,.5); }
    .modal-header { display: flex; align-items: flex-start; justify-content: space-between; padding: 1rem 1rem; border-bottom: 1px solid #dee2e6; }
    .modal-header h5 { margin-bottom: 0; line-height: 1.5; font-size: 1.25rem; }
    .modal-header .close-btn { padding: 1rem 1rem; margin: -1rem -1rem -1rem auto; background-color: transparent; border: 0; font-size: 1.5rem; font-weight: 700; line-height: 1; color: #000; text-shadow: 0 1px 0 #fff; opacity: .5; cursor: pointer; }
    .modal-body { position: relative; flex: 1 1 auto; padding: 1rem; }
    .modal-footer { display: flex; align-items: center; justify-content: flex-end; padding: 1rem; border-top: 1px solid #dee2e6; }
    .modal-footer .btn { margin-left: .25rem; }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .form-control {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        font-size: 1rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '{{ csrf_token() }}';
    const scheduleBody = document.getElementById('schedule-grid-body');
    const kelasId = document.getElementById('kelas_id').value;
    
    // --- Modal Management ---
    const modals = {
        addScheduleModal: document.getElementById('addScheduleModal'),
        addTimeModal: document.getElementById('addTimeModal')
    };

    function openModal(modalId) {
        console.log('Opening modal:', modalId); // Debug log
        if (modals[modalId]) {
            modals[modalId].style.display = 'flex';
            modals[modalId].classList.add('show');
        }
    }

    function closeModal(modalId) {
        console.log('Closing modal:', modalId); // Debug log
        if (modals[modalId]) {
            modals[modalId].style.display = 'none';
            modals[modalId].classList.remove('show');
        }
    }

    // Event listener untuk tombol "Tambah Jam"
    const addTimeBtn = document.getElementById('addTimeBtn');
    if (addTimeBtn) {
        addTimeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Tambah Jam button clicked'); // Debug log
            openModal('addTimeModal');
        });
    }

    // Event listeners untuk tombol close
    document.querySelectorAll('.close-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal-id');
            closeModal(modalId);
        });
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            const modalId = e.target.id;
            closeModal(modalId);
        }
    });

    // --- Time (Jam) Management ---
    const addTimeForm = document.getElementById('addTimeForm');
    if (addTimeForm) {
        addTimeForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Form submitted'); // Debug log
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            console.log('Sending data:', data); // Debug log

            try {
                const response = await fetch('{{ route("tabelj.store") }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': csrfToken, 
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify(data)
                });
                
                console.log('Response status:', response.status); // Debug log
                const result = await response.json();
                console.log('Response data:', result); // Debug log

                if (response.ok && result.success) {
                    Swal.fire({ 
                        toast: true, 
                        position: 'top-end', 
                        icon: 'success', 
                        title: result.message || 'Jam berhasil ditambahkan', 
                        showConfirmButton: false, 
                        timer: 2000 
                    });
                    
                    addTableRow(result.tabelj);
                    closeModal('addTimeModal');
                    this.reset();
                } else {
                    console.error('Server error:', result); // Debug log
                    let errorMessage = 'Gagal menyimpan jam';
                    if (result.errors) {
                        const errorMessages = Object.values(result.errors).map(e => Array.isArray(e) ? e[0] : e).join(', ');
                        errorMessage = errorMessages;
                    } else if (result.message) {
                        errorMessage = result.message;
                    }
                    
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Gagal Menyimpan', 
                        text: errorMessage
                    });
                }
            } catch (error) {
                console.error('Network error:', error); // Debug log
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Error', 
                    text: 'Tidak dapat terhubung ke server. Error: ' + error.message 
                });
            }
        });
    }

    function addTableRow(time) {
        console.log('Adding table row for time:', time); // Debug log
        const row = document.createElement('tr');
        row.dataset.timeId = time.id;
        
        const days = @json($days);
        let cells = `
            <td class="time-col">
                <span>${time.jam}</span>
                <button class="delete-time-btn" data-id="${time.id}" title="Hapus jam ini">&times;</button>
            </td>`;
        
        days.forEach(day => {
            cells += `<td data-hari="${day}" data-jam="${time.jam}"><button class="add-schedule-btn">+</button></td>`;
        });

        row.innerHTML = cells;
        scheduleBody.appendChild(row);
        sortAndReorderTable();
    }
    
    function sortAndReorderTable() {
        const rows = Array.from(scheduleBody.querySelectorAll('tr'));
        rows.sort((a, b) => {
            const timeA = a.querySelector('.time-col span').textContent.split('-')[0];
            const timeB = b.querySelector('.time-col span').textContent.split('-')[0];
            return timeA.localeCompare(timeB);
        });
        rows.forEach(row => scheduleBody.appendChild(row));
    }

    async function deleteTime(id) {
        try {
            const response = await fetch(`/tabelj/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (response.ok && result.success) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: result.message, showConfirmButton: false, timer: 2000 });
                const row = document.querySelector(`tr[data-time-id="${id}"]`);
                if (row) row.remove();
            } else {
                Swal.fire('Error', result.message || 'Gagal menghapus jam.', 'error');
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Tidak dapat terhubung ke server.' });
        }
    }

    // --- Schedule (Jadwal) Management ---
    const guruSelect = document.getElementById('guru-select');
    const mapelInput = document.getElementById('mapel-input');
    const modalInfo = document.getElementById('modal-info');
    let activeCell = null;

    if (guruSelect && mapelInput) {
        guruSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            mapelInput.value = selectedOption.dataset.mapel || '';
        });
    }

    const saveScheduleBtn = document.getElementById('saveScheduleBtn');
    if (saveScheduleBtn) {
        saveScheduleBtn.addEventListener('click', async function() {
            const guruId = guruSelect.value;
            const mapel = mapelInput.value;

            if (!guruId || !mapel) {
                Swal.fire('Error', 'Silakan pilih guru terlebih dahulu.', 'error');
                return;
            }

            const data = {
                kelas_id: kelasId,
                guru_id: guruId,
                mapel: mapel,
                hari: activeCell.dataset.hari,
                jam: activeCell.dataset.jam
            };

            try {
                const response = await fetch('{{ route("jadwal.store.ajax") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (response.ok && result.success) {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: result.message, showConfirmButton: false, timer: 2000 });
                    updateCellWithSchedule(activeCell, result.jadwal);
                    closeModal('addScheduleModal');
                } else {
                    const errorMessages = Object.values(result.errors).map(e => `<li>${e[0]}</li>`).join('');
                    Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', html: `<ul>${errorMessages}</ul>` });
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Tidak dapat terhubung ke server.' });
            }
        });
    }
    
    async function deleteSchedule(jadwalId, cell) {
        try {
            const response = await fetch(`/jadwal/${jadwalId}`, { 
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (response.ok && result.success) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: result.message, showConfirmButton: false, timer: 2000 });
                updateCellWithAddButton(cell);
            } else {
                Swal.fire('Error', result.message || 'Gagal menghapus jadwal.', 'error');
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Tidak dapat terhubung ke server.' });
        }
    }

    function updateCellWithSchedule(cell, jadwal) {
        cell.innerHTML = `
            <div class="schedule-item" data-jadwal-id="${jadwal.id}">
                <strong class="mapel">${jadwal.mapel}</strong>
                <button class="delete-schedule-btn" title="Hapus Jadwal">&times;</button>
            </div>
        `;
    }

    function updateCellWithAddButton(cell) {
        cell.innerHTML = `<button class="add-schedule-btn">+</button>`;
    }

    // --- CONSOLIDATED EVENT LISTENER FOR THE TABLE BODY ---
    if (scheduleBody) {
        scheduleBody.addEventListener('click', function(e) {
            const target = e.target;

            // Handle Add Schedule Button
            if (target.classList.contains('add-schedule-btn')) {
                activeCell = target.parentElement;
                const hari = activeCell.dataset.hari;
                const jam = activeCell.dataset.jam;
                if (modalInfo) modalInfo.textContent = `${hari}, Jam ${jam}`;
                if (guruSelect) guruSelect.value = '';
                if (mapelInput) mapelInput.value = '';
                openModal('addScheduleModal');
            }

            // Handle Delete Schedule Button
            if (target.classList.contains('delete-schedule-btn')) {
                const scheduleItem = target.parentElement;
                const jadwalId = scheduleItem.dataset.jadwalId;
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Jadwal ini akan dihapus.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteSchedule(jadwalId, scheduleItem.parentElement);
                    }
                });
            }

            // Handle Delete Time Button
            if (target.classList.contains('delete-time-btn')) {
                const timeId = target.dataset.id;
                Swal.fire({
                    title: 'Yakin ingin menghapus jam ini?',
                    text: "Semua jadwal di jam ini akan ikut terhapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteTime(timeId);
                    }
                });
            }
        });
    }
    
    // Initial sort on page load
    sortAndReorderTable();
    
    // Debug: Check if all elements are found
    console.log('Elements found:', {
        addTimeBtn: !!addTimeBtn,
        addTimeForm: !!addTimeForm,
        addTimeModal: !!modals.addTimeModal,
        scheduleBody: !!scheduleBody
    });
});
</script>
@endpush