{{-- filepath: resources/views/jadwal/create.blade.php --}}
@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <div>
        <h1>Pembangun Jadwal: <strong>{{ $kelas->nama_kelas }}</strong></h1>
        <p>Klik tombol `+` pada slot waktu yang diinginkan untuk menambahkan jadwal.</p>
    </div>
    <a href="{{ route('jadwal.perKelas', $kelas->id) }}" class="btn btn-primary">Lihat Jadwal Selesai</a>
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
                @foreach($timeSlots as $jam)
                    <tr>
                        <td class="time-col">{{ $jam }}</td>
                        @foreach($days as $hari)
                            <td data-hari="{{ $hari }}" data-jam="{{ $jam }}">
                                @if(isset($scheduleGrid[$hari][$jam]))
                                    @php $jadwal = $scheduleGrid[$hari][$jam]; @endphp
                                    <div class="schedule-item" data-jadwal-id="{{ $jadwal->id }}">
                                        <strong class="mapel">{{ $jadwal->mapel }}</strong>
                                        <span class="guru">{{ $jadwal->guru->nama }}</span>
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

<!-- Modal untuk memilih guru -->
<div id="addScheduleModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Set Waktu Pelajaran</h5>
            <button type="button" class="close-btn" id="closeModalBtn">&times;</button>
        </div>
        <div class="modal-body">
            <p>Anda akan menambahkan jadwal untuk: <strong id="modal-info"></strong></p>
            <div class="form-group">
                <label for="guru-select">Pilih Guru:</label>
                <select id="guru-select" class="form-control">
                    <option value="">-- Pilih Guru --</option>
                    @foreach($gurus as $guru)
                        <option value="{{ $guru->id }}" data-mapel="{{ $guru->pengampu }}">{{ $guru->nama }} ({{ $guru->pengampu }})</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancelModalBtn">Batal</button>
            <button type="button" class="btn btn-primary" id="saveScheduleBtn">Simpan Jadwal</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .schedule-grid th, .schedule-grid td { text-align: center; vertical-align: middle; height: 80px; padding: 4px; border: 1px solid #e9ecef; }
    .schedule-grid .time-col { font-weight: bold; width: 120px; background-color: #f8f9fa; }
    .add-schedule-btn { width: 40px; height: 40px; border-radius: 50%; border: 2px dashed #adb5bd; background-color: #f8f9fa; color: #adb5bd; font-size: 24px; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; margin: auto; }
    .add-schedule-btn:hover { background-color: #e9ecef; color: #495057; border-style: solid; }
    .schedule-item { position: relative; padding: 8px; border-radius: 6px; background-color: #d4edda; border-left: 5px solid #28a745; text-align: left; }
    .schedule-item .mapel { font-size: 0.9em; color: #155724; display: block; font-weight: 600; }
    .schedule-item .guru { font-size: 0.8em; color: #155724; display: block; margin-top: 4px; }
    .delete-schedule-btn { position: absolute; top: -5px; right: -5px; width: 20px; height: 20px; border-radius: 50%; background-color: #dc3545; color: white; border: none; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s ease; }
    .schedule-item:hover .delete-schedule-btn { opacity: 1; }
    /* Modal Styles */
    .modal { position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; overflow: hidden; outline: 0; background-color: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; }
    .modal-content { position: relative; background-color: #fff; border-radius: .3rem; max-width: 500px; width: 100%; margin: 1.75rem auto; box-shadow: 0 0.5rem 1rem rgba(0,0,0,.5); }
    .modal-header { display: flex; align-items: flex-start; justify-content: space-between; padding: 1rem 1rem; border-bottom: 1px solid #dee2e6; border-top-left-radius: .3rem; border-top-right-radius: .3rem; }
    .modal-header h5 { margin-bottom: 0; line-height: 1.5; font-size: 1.25rem; }
    .modal-header .close-btn { padding: 1rem 1rem; margin: -1rem -1rem -1rem auto; background-color: transparent; border: 0; font-size: 1.5rem; font-weight: 700; line-height: 1; color: #000; text-shadow: 0 1px 0 #fff; opacity: .5; cursor: pointer; }
    .modal-body { position: relative; flex: 1 1 auto; padding: 1rem; }
    .modal-footer { display: flex; align-items: center; justify-content: flex-end; padding: 1rem; border-top: 1px solid #dee2e6; }
    .modal-footer .btn { margin-left: .25rem; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('addScheduleModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    const saveScheduleBtn = document.getElementById('saveScheduleBtn');
    const guruSelect = document.getElementById('guru-select');
    const modalInfo = document.getElementById('modal-info');
    const scheduleBody = document.getElementById('schedule-grid-body');
    let activeCell = null;

    // Fungsi untuk membuka modal
    function openModal(cell) {
        activeCell = cell;
        const hari = cell.dataset.hari;
        const jam = cell.dataset.jam;
        modalInfo.textContent = `${hari}, Jam ${jam}`;
        guruSelect.value = ''; // Reset pilihan guru
        modal.style.display = 'flex';
    }

    // Fungsi untuk menutup modal
    function closeModal() {
        modal.style.display = 'none';
        activeCell = null;
    }

    // Event delegation untuk seluruh body tabel
    scheduleBody.addEventListener('click', function(e) {
        // Jika tombol 'tambah' (+) diklik
        if (e.target && e.target.classList.contains('add-schedule-btn')) {
            openModal(e.target.parentElement);
        }

        // Jika tombol 'hapus' (x) diklik
        if (e.target && e.target.classList.contains('delete-schedule-btn')) {
            const scheduleItem = e.target.parentElement;
            const jadwalId = scheduleItem.dataset.jadwalId;
            
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Jadwal ini akan dihapus dari grid.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteSchedule(jadwalId, scheduleItem.parentElement);
                }
            });
        }
    });

    // Event listener untuk tombol simpan di modal
    saveScheduleBtn.addEventListener('click', async function() {
        const guruId = guruSelect.value;
        const selectedOption = guruSelect.options[guruSelect.selectedIndex];
        const mapel = selectedOption.dataset.mapel;

        if (!guruId) {
            Swal.fire('Error', 'Silakan pilih guru terlebih dahulu.', 'error');
            return;
        }

        const data = {
            kelas_id: document.getElementById('kelas_id').value,
            guru_id: guruId,
            mapel: mapel,
            hari: activeCell.dataset.hari,
            jam: activeCell.dataset.jam
        };

        try {
            const response = await fetch('{{ route("jadwal.store.ajax") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const result = await response.json();

            if (response.ok && result.success) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: result.message, showConfirmButton: false, timer: 2000 });
                updateCellWithSchedule(activeCell, result.jadwal);
                closeModal();
            } else {
                const errorMessages = Object.values(result.errors).map(e => `<li>${e[0]}</li>`).join('');
                Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', html: errorMessages });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Tidak dapat terhubung ke server.' });
        }
    });
    
    async function deleteSchedule(jadwalId, cell) {
        try {
            const response = await fetch(`/jadwal/destroy/ajax/${jadwalId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            if (response.ok && result.success) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: result.message, showConfirmButton: false, timer: 2000 });
                updateCellWithAddButton(cell);
            } else {
                Swal.fire('Error', result.message || 'Gagal menghapus jadwal.', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Tidak dapat terhubung ke server.' });
        }
    }

    function updateCellWithSchedule(cell, jadwal) {
        cell.innerHTML = `
            <div class="schedule-item" data-jadwal-id="${jadwal.id}">
                <strong class="mapel">${jadwal.mapel}</strong>
                <span class="guru">${jadwal.guru.nama}</span>
                <button class="delete-schedule-btn" title="Hapus Jadwal">&times;</button>
            </div>
        `;
    }

    function updateCellWithAddButton(cell) {
        cell.innerHTML = `<button class="add-schedule-btn">+</button>`;
    }

    // Event listener untuk tombol-tombol di modal
    closeModalBtn.addEventListener('click', closeModal);
    cancelModalBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });
});
</script>
@endpush
