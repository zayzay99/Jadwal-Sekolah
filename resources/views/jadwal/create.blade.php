@extends('dashboard.admin')

@section('content')
    <div class="content-header">
        <div>
            <h1>Manajemen Jadwal Untuk Kelas: <strong>{{ $kelas->nama_kelas }}</strong></h1>
            <p>Klik pada slot waktu untuk mengisi atau mengisi jadwal. Jangan lupa simpan perubahan Anda.</p>
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
                        <th style="width: 150px;">Jam</th>
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

    <!-- Schedule Edit Modal -->
    <div id="schedule-modal" class="schedule-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Tambah Jadwal</h3>
                <p id="modal-subtitle"></p>
            </div>
            <div class="modal-body">
                <form id="modal-form">
                    <input type="hidden" id="modal-day">
                    <input type="hidden" id="modal-jam">

                    <div class="form-group">
                        <label for="modal-select">Pilih Jadwal</label>
                        <select id="modal-select" class="form-control"></select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="modal-cancel" class="btn btn-secondary btn-tiny">Batal</button>
                <button type="button" id="modal-delete" class="btn btn-danger btn-tiny">Hapus Jadwal</button>
                <button type="button" id="modal-save" class="btn btn-primary btn-tiny">Simpan</button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* General Layout */
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
        .custom-table th:first-child {
            width: 150px;
            min-width: 150px;
        }
        .time-display {
            padding: 8px;
            text-align: center;
            font-weight: 600;
            color: #2d6a4f;
        }

        /* Schedule Cell Styling */
        .schedule-cell {
            cursor: pointer;
            min-height: 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px;
            border-radius: 8px;
            transition: background-color 0.3s;
            border: 1px dashed transparent;
        }
        .schedule-cell:hover {
            background-color: #e9ecef;
            border-color: #2d6a4f;
        }
        .schedule-cell.filled {
            background-color: #e6f0fa;
            border: 1px solid #b6d4fe;
        }
        .cell-guru {
            font-weight: 600;
            color: #2d6a4f;
        }
        .cell-mapel, .cell-kategori {
            font-size: 12px;
            color: #555;
        }
        .cell-placeholder {
            font-size: 12px;
            color: #999;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .cell-placeholder i {
            font-size: 14px;
        }

        /* Modal Styles */
        .schedule-modal {
            display: none;
            position: fixed;
            z-index: 1060;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 25px;
            border: 1px solid #ddd;
            width: 90%;
            max-width: 500px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            animation: slideIn 0.3s;
        }
        .modal-header {
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e5e5;
            margin-bottom: 20px;
        }
        .modal-header h3 {
            margin: 0;
            font-size: 22px;
            color: #2d6a4f;
        }
        .modal-header p {
            margin: 5px 0 0;
            color: #666;
        }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- DATA & CONFIG ---
            const csrfToken = '{{ csrf_token() }}';
            const kelasId = document.getElementById('kelas_id').value;
            const scheduleBody = document.getElementById('schedule-body');
            const bulkSaveBtn = document.getElementById('bulkSaveBtn');

            const days = @json($days);
            const kategoris = @json($kategoris->values());
            const gurus = @json($gurus);
            const timeSlots = @json($timeSlots);
            const availableGurus = @json($availableGurus);
            
            // Local data store
            let scheduleData = @json($scheduleGrid);

            // --- MODAL ELEMENTS ---
            const modal = document.getElementById('schedule-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalSubtitle = document.getElementById('modal-subtitle');
            const modalSelect = document.getElementById('modal-select');
            const modalDay = document.getElementById('modal-day');
            const modalJam = document.getElementById('modal-jam');
            const modalSaveBtn = document.getElementById('modal-save');
            const modalCancelBtn = document.getElementById('modal-cancel');
            const modalDeleteBtn = document.getElementById('modal-delete');

            // --- TEMPLATE FUNCTIONS ---
            function getModalOptions(day, jam) {
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

            function renderCellContent(day, jam) {
                const schedule = scheduleData[jam] && scheduleData[jam][day] ? scheduleData[jam][day] : null;
                const cell = scheduleBody.querySelector(`[data-day="${day}"][data-jam="${jam}"]`);
                if (!cell) return;

                let content = '';
                if (schedule) {
                    cell.classList.add('filled');
                    if (schedule.guru) {
                        content = `<div class="cell-guru">${schedule.guru.nama}</div><div class="cell-mapel">${schedule.guru.pengampu}</div>`;
                    } else if (schedule.kategori) {
                        content = `<div class="cell-kategori">${schedule.kategori.nama_kategori}</div>`;
                    }
                } else {
                    cell.classList.remove('filled');
                    content = '<div class="cell-placeholder"><i class="fas fa-plus-circle"></i> Atur</div>';
                }
                cell.innerHTML = content;
            }

            // --- MODAL LOGIC ---
            function openModal(day, jam) {
                modalDay.value = day;
                modalJam.value = jam;

                const currentSchedule = scheduleData[jam] && scheduleData[jam][day] ? scheduleData[jam][day] : null;

                if (currentSchedule) {
                    modalTitle.textContent = 'Edit Jadwal';
                    modalDeleteBtn.style.display = 'inline-block';
                } else {
                    modalTitle.textContent = 'Tambah Jadwal';
                    modalDeleteBtn.style.display = 'none';
                }

                modalSubtitle.textContent = `${day}, Jam ${jam}`;
                
                modalSelect.innerHTML = getModalOptions(day, jam);

                if (currentSchedule) {
                    if (currentSchedule.guru_id) {
                        modalSelect.value = `guru-${currentSchedule.guru_id}`;
                    } else if (currentSchedule.jadwal_kategori_id) {
                        modalSelect.value = `kategori-${currentSchedule.jadwal_kategori_id}`;
                    }
                } else {
                    modalSelect.value = '';
                }

                modal.style.display = 'block';
            }

            function closeModal() {
                modal.style.display = 'none';
            }

            function saveModalData() {
                const day = modalDay.value;
                const jam = modalJam.value;
                const selectedValue = modalSelect.value;

                if (!scheduleData[jam]) scheduleData[jam] = {};

                if (!selectedValue) { // If "-- Kosong --" is selected
                    scheduleData[jam][day] = null;
                } else {
                    const [type, id] = selectedValue.split('-');
                    const guru = type === 'guru' ? gurus.find(g => g.id == id) : null;
                    const kategori = type === 'kategori' ? kategoris.find(k => k.id == id) : null;

                    scheduleData[jam][day] = {
                        guru_id: type === 'guru' ? id : null,
                        jadwal_kategori_id: type === 'kategori' ? id : null,
                        guru: guru,
                        kategori: kategori,
                        mapel: guru ? guru.pengampu : null
                    };
                }
                
                renderCellContent(day, jam);
                closeModal();
            }
            
            function deleteModalData() {
                const day = modalDay.value;
                const jam = modalJam.value;
                if (scheduleData[jam] && scheduleData[jam][day]) {
                    scheduleData[jam][day] = null;
                    renderCellContent(day, jam);
                }
                closeModal();
            }

            // --- INITIALIZE VIEW ---
            function initializeTable() {
                let tableHtml = '';
                timeSlots.forEach(slot => {
                    const jam = `${slot.jam_mulai} - ${slot.jam_selesai}`;
                    tableHtml += '<tr>';
                    tableHtml += `<td><div class="time-display">${jam}</div></td>`;
                    days.forEach(day => {
                        tableHtml += `<td><div class="schedule-cell" data-day="${day}" data-jam="${jam}"></div></td>`;
                    });
                    tableHtml += '</tr>';
                });
                scheduleBody.innerHTML = tableHtml;

                // Initial render of all cells
                timeSlots.forEach(slot => {
                    const jam = `${slot.jam_mulai} - ${slot.jam_selesai}`;
                    days.forEach(day => {
                        renderCellContent(day, jam);
                    });
                });
            }

            // --- EVENT LISTENERS ---
            scheduleBody.addEventListener('click', function(e) {
                const cell = e.target.closest('.schedule-cell');
                if (cell) {
                    openModal(cell.dataset.day, cell.dataset.jam);
                }
            });

            modalSaveBtn.addEventListener('click', saveModalData);
            modalDeleteBtn.addEventListener('click', deleteModalData);
            modalCancelBtn.addEventListener('click', closeModal);
            window.addEventListener('click', function(e) {
                if (e.target == modal) {
                    closeModal();
                }
            });

            bulkSaveBtn.addEventListener('click', async function() {
                this.disabled = true;
                this.textContent = 'Menyimpan...';

                const schedules = [];
                for (const jam in scheduleData) {
                    for (const day in scheduleData[jam]) {
                        const schedule = scheduleData[jam][day];
                        if (schedule) {
                            schedules.push({
                                hari: day,
                                jam: jam,
                                guru_id: schedule.guru_id,
                                mapel: schedule.mapel,
                                jadwal_kategori_id: schedule.jadwal_kategori_id
                            });
                        }
                    }
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
                    console.error('Save error:', error);
                    Swal.fire('Error!', 'Tidak dapat terhubung ke server.', 'error');
                } finally {
                    this.disabled = false;
                    this.textContent = 'Simpan Semua Jadwal';
                }
            });

            // --- INITIALIZE ---
            initializeTable();
        });
    </script>
@endpush
