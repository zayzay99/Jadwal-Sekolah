@extends('dashboard.admin')

@section('content')
    <div class="content-header">
        <div>
            <h2 style="font-size: 2rem; font-weight: 700; margin: 0 0 8px 0; color: var(--text-color);">
                Manajemen Jadwal Kelas: <strong style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">{{ $kelas->nama_kelas }}</strong>
            </h2>
            <p style="color: var(--text-light); margin: 0; font-size: 0.95rem;">
                <i class="fas fa-info-circle"></i> Klik pada slot waktu untuk mengisi atau mengedit jadwal. Jangan lupa simpan perubahan Anda.
            </p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button id="bulkSaveBtn" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Semua Jadwal
            </button>
            <a href="{{ route('jadwal.perKelas', $kelas->id) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> Lihat Jadwal
            </a>
            <a href="{{ route('jadwal.pilihKelas') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <input type="hidden" id="kelas_id" value="{{ $kelas->id }}">

    <!-- Stats Info Cards -->
    <div class="stats-container" style="margin-bottom: 25px;">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-value" id="filled-slots-count">0</div>
            <div class="stat-label">Slot Terisi</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <div class="stat-value" id="empty-slots-count">0</div>
            <div class="stat-label">Slot Kosong</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-value">{{ count($gurus) }}</div>
            <div class="stat-label">Guru Tersedia</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value">{{ count($timeSlots) }}</div>
            <div class="stat-label">Jam Pelajaran</div>
        </div>
    </div>

    <!-- Schedule Table -->
    <div class="welcome-card" style="flex-direction: column; align-items: stretch; padding: 0; overflow: hidden;">
        <div style="padding: 25px 30px; border-bottom: 1px solid var(--border-color); background: linear-gradient(to right, rgba(17, 153, 142, 0.05), transparent);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="font-size: 2rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    <i class="fas fa-table"></i>
                </div>
                <h3 style="margin: 0; font-size: 1.3rem; font-weight: 600; color: var(--text-color);">
                    Tabel Jadwal Pelajaran
                </h3>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="table" id="schedule-builder" style="margin: 0;">
                <thead>
                    <tr>
                        <th style="width: 150px; min-width: 150px; text-align: center;">
                            <i class="fas fa-clock"></i> Jam
                        </th>
                        @foreach ($days as $day)
                            <th style="min-width: 180px;">
                                <i class="fas fa-calendar-day"></i> {{ $day }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="schedule-body">
                    {{-- Rows will be dynamically inserted here by JavaScript --}}
                </tbody>
            </table>
        </div>
    </div>

    <!-- Legend -->
    <div style="margin-top: 25px; display: flex; flex-wrap: wrap; gap: 15px; align-items: center; padding: 20px; background: white; border-radius: 15px; border: 1px solid var(--border-color);">
        <div style="font-weight: 600; color: var(--text-color); margin-right: 10px;">
            <i class="fas fa-info-circle"></i> Keterangan:
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 20px; height: 20px; background: linear-gradient(135deg, rgba(17, 153, 142, 0.2), rgba(56, 239, 125, 0.2)); border: 2px solid var(--primary-color); border-radius: 6px;"></div>
            <span style="font-size: 0.9rem; color: var(--text-light);">Slot Terisi</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 20px; height: 20px; border: 2px dashed var(--border-color); border-radius: 6px;"></div>
            <span style="font-size: 0.9rem; color: var(--text-light);">Slot Kosong</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 6px;"></div>
            <span style="font-size: 0.9rem; color: var(--text-light);">Istirahat</span>
        </div>
    </div>

    <!-- Schedule Edit Modal with Separated Dropdowns -->
    <div id="schedule-modal" class="modal">
        <div class="modal-content" style="max-width: 550px;">
            <span class="modal-close-button" id="modal-close-x">&times;</span>
            
            <div class="modal-header" style="padding: 30px 35px 25px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                    <div style="width: 45px; height: 45px; background: var(--primary-gradient); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(17, 153, 142, 0.3);">
                        <i class="fas fa-calendar-plus" style="font-size: 1.3rem; color: white;"></i>
                    </div>
                    <div style="flex: 1;">
                        <h1 id="modal-title" style="font-size: 1.4rem; margin: 0; font-weight: 700;">Tambah Jadwal</h1>
                    </div>
                </div>
                <div id="modal-subtitle" style="margin: 0; padding: 12px 16px; background: linear-gradient(135deg, rgba(17, 153, 142, 0.1), rgba(56, 239, 125, 0.1)); border-radius: 10px; border-left: 3px solid var(--primary-color); font-size: 0.95rem; color: var(--text-color); font-weight: 500;">
                </div>
            </div>
            
            <div class="modal-body" style="padding: 25px 35px;">
                <form id="modal-form">
                    <input type="hidden" id="modal-day">
                    <input type="hidden" id="modal-jam">

                    <!-- Dropdown Guru dengan Search -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="modal-guru-select" style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 0.95rem;">
                            <i class="fas fa-chalkboard-teacher" style="color: var(--primary-color);"></i> 
                            <span>Pilih Guru & Mata Pelajaran</span>
                        </label>
                        <select id="modal-guru-select" class="form-control searchable-select" style="font-size: 0.95rem; padding: 13px 15px; width: 100%;">
                            <option value="">-- Kosong --</option>
                        </select>
                    </div>

                    <!-- Dropdown Kategori (Non-searchable) -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="modal-kategori-select" style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 0.95rem;">
                            <i class="fas fa-tag" style="color: var(--primary-color);"></i> 
                            <span>Atau Pilih Kategori Khusus</span>
                        </label>
                        <select id="modal-kategori-select" class="form-control" style="font-size: 0.95rem; padding: 13px 15px; width: 100%;">
                            <option value="">-- Tidak Ada --</option>
                        </select>
                        <div style="display: flex; align-items: flex-start; gap: 8px; margin-top: 10px; padding: 10px 12px; background: rgba(0, 180, 219, 0.08); border-radius: 8px; border-left: 3px solid #00b4db;">
                            <i class="fas fa-info-circle" style="color: #00b4db; margin-top: 2px; font-size: 0.9rem;"></i>
                            <small style="color: var(--text-light); font-size: 0.85rem; line-height: 1.5;">
                                Pilih guru untuk pelajaran regular atau kategori untuk kegiatan khusus (Sholat Dhuha, Istirahat, dll)
                            </small>
                        </div>
                    </div>
                </form>
            </div>
            
            <div style="padding: 20px 35px 30px; border-top: 1px solid var(--border-color); background: rgba(17, 153, 142, 0.02);">
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="button" id="modal-delete" class="btn btn-danger" style="margin-right: auto;">
                        <i class="fas fa-trash-alt"></i> Hapus
                    </button>
                    <button type="button" id="modal-cancel" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="button" id="modal-save" class="btn btn-success">
                        <i class="fas fa-check-circle"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Time Display */
        .time-display {
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            color: var(--primary-color);
            font-size: 0.9rem;
        }

        /* Schedule Cell Styling */
        .schedule-cell {
            cursor: pointer;
            min-height: 70px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 12px;
            border-radius: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px dashed var(--border-color);
            background: white;
        }
        
        .schedule-cell:hover {
            background: linear-gradient(135deg, rgba(17, 153, 142, 0.08), rgba(56, 239, 125, 0.08));
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(17, 153, 142, 0.15);
        }
        
        .schedule-cell.filled {
            background: linear-gradient(135deg, rgba(17, 153, 142, 0.1), rgba(56, 239, 125, 0.1));
            border: 2px solid var(--primary-color);
        }
        
        .schedule-cell.filled:hover {
            background: linear-gradient(135deg, rgba(17, 153, 142, 0.15), rgba(56, 239, 125, 0.15));
            box-shadow: 0 6px 16px rgba(17, 153, 142, 0.2);
        }
        
        .cell-guru {
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 4px;
            font-size: 0.95rem;
        }
        
        .cell-mapel {
            font-size: 0.85rem;
            color: var(--text-light);
            text-align: center;
        }
        
        .cell-kategori {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--primary-color);
            text-align: center;
        }
        
        .cell-placeholder {
            font-size: 0.9rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }
        
        .cell-placeholder i {
            font-size: 1rem;
            color: var(--primary-color);
        }

        /* Break Row Styling */
        .break-row {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .break-cell {
            text-align: center;
            font-weight: 600;
            color: var(--text-muted);
            padding: 12px;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }
        
        .break-cell i {
            margin-right: 6px;
            color: var(--primary-color);
        }

        /* Searchable Dropdown Styling */
        .searchable-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 40px !important;
            cursor: pointer;
        }

        .select-wrapper {
            position: relative;
        }

        .select-arrow {
            transition: transform 0.3s ease;
        }

        .form-control:focus + .select-arrow {
            transform: translateY(-50%) rotate(180deg);
        }

        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            height: auto !important;
            padding: 13px 15px !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important;
            font-size: 0.95rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding: 0 !important;
            line-height: normal !important;
            color: var(--text-color) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
            right: 10px !important;
        }

        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--primary-color) !important;
        }

        .select2-dropdown {
            border: 2px solid var(--primary-color) !important;
            border-radius: 10px !important;
            box-shadow: 0 8px 24px rgba(17, 153, 142, 0.2) !important;
        }

        .select2-search--dropdown .select2-search__field {
            border: none !important;
            border-bottom: 2px solid var(--border-color) !important;
            border-radius: 0 !important;
            padding: 12px 15px !important;
            font-size: 0.95rem !important;
        }

        .select2-search--dropdown .select2-search__field:focus {
            border-bottom-color: var(--primary-color) !important;
            outline: none !important;
        }

        .select2-results__option {
            padding: 12px 15px !important;
            font-size: 0.95rem !important;
        }

        .select2-results__option--highlighted {
            background: linear-gradient(135deg, rgba(17, 153, 142, 0.1), rgba(56, 239, 125, 0.1)) !important;
            color: var(--text-color) !important;
        }

        .select2-results__option--selected {
            background: linear-gradient(135deg, rgba(17, 153, 142, 0.15), rgba(56, 239, 125, 0.15)) !important;
        }

        .select2-container {
            width: 100% !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .schedule-cell {
                min-height: 60px;
                padding: 8px;
            }
            
            .cell-guru {
                font-size: 0.85rem;
            }
            
            .cell-mapel, .cell-kategori {
                font-size: 0.75rem;
            }
        }

        /* Custom Scrollbar */
        .modal-body::-webkit-scrollbar,
        .welcome-card > div::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        .modal-body::-webkit-scrollbar-track,
        .welcome-card > div::-webkit-scrollbar-track {
            background: var(--bg-primary);
            border-radius: 10px;
        }
        
        .modal-body::-webkit-scrollbar-thumb,
        .welcome-card > div::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }
        
        .modal-body::-webkit-scrollbar-thumb:hover,
        .welcome-card > div::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
            const modalDay = document.getElementById('modal-day');
            const modalJam = document.getElementById('modal-jam');
            const modalSaveBtn = document.getElementById('modal-save');
            const modalCancelBtn = document.getElementById('modal-cancel');
            const modalDeleteBtn = document.getElementById('modal-delete');
            const modalCloseX = document.getElementById('modal-close-x');

            // Guru & Kategori Dropdown Elements
            const modalGuruSelect = document.getElementById('modal-guru-select');
            const modalKategoriSelect = document.getElementById('modal-kategori-select');

            // --- STATS UPDATE FUNCTION ---
            function updateStats() {
                let filledCount = 0;
                let emptyCount = 0;
                
                timeSlots.forEach(slot => {
                    if (!slot.jadwal_kategori || slot.jadwal_kategori.nama_kategori !== 'Istirahat') {
                        const jam = `${slot.jam_mulai} - ${slot.jam_selesai}`;
                        days.forEach(day => {
                            if (scheduleData[jam] && scheduleData[jam][day]) {
                                filledCount++;
                            } else {
                                emptyCount++;
                            }
                        });
                    }
                });
                
                document.getElementById('filled-slots-count').textContent = filledCount;
                document.getElementById('empty-slots-count').textContent = emptyCount;
            }

            // --- TEMPLATE FUNCTIONS ---
            function populateGuruDropdown(day, jam) {
                // Destroy existing Select2 if initialized
                if ($(modalGuruSelect).hasClass("select2-hidden-accessible")) {
                    $(modalGuruSelect).select2('destroy');
                }

                let options = '<option value="">-- Kosong --</option>';
                if (availableGurus[day] && availableGurus[day][jam]) {
                    availableGurus[day][jam].forEach(guru => {
                        options += `<option value="guru-${guru.id}">${guru.nama} (${guru.pengampu})</option>`;
                    });
                }
                modalGuruSelect.innerHTML = options;
                
                // Initialize Select2 with search
                $(modalGuruSelect).select2({
                    placeholder: "-- Kosong --",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#schedule-modal'),
                    language: {
                        noResults: function() {
                            return "Tidak ditemukan";
                        },
                        searching: function() {
                            return "Mencari...";
                        }
                    }
                });
            }

            function populateKategoriDropdown() {
                let options = '<option value="">-- Tidak Ada --</option>';
                kategoris.forEach(kategori => {
                    options += `<option value="kategori-${kategori.id}">${kategori.nama_kategori}</option>`;
                });
                modalKategoriSelect.innerHTML = options;
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

                modalSubtitle.innerHTML = `<i class="fas fa-calendar-day"></i> ${day}, <i class="fas fa-clock"></i> Jam ${jam}`;
                
                // Populate both dropdowns
                populateGuruDropdown(day, jam);
                populateKategoriDropdown();

                // Reset selections
                modalGuruSelect.value = '';
                modalKategoriSelect.value = '';

                // Set current values if editing
                if (currentSchedule) {
                    if (currentSchedule.guru_id) {
                        const guruValue = `guru-${currentSchedule.guru_id}`;
                        $(modalGuruSelect).val(guruValue).trigger('change');
                    } else if (currentSchedule.jadwal_kategori_id) {
                        modalKategoriSelect.value = `kategori-${currentSchedule.jadwal_kategori_id}`;
                    }
                }

                modal.style.display = 'block';
                modal.classList.add('show');
                setTimeout(() => {
                    modal.style.opacity = '1';
                }, 10);
            }

            function closeModal() {
                modal.style.opacity = '0';
                setTimeout(() => {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                }, 300);
            }

            function saveModalData() {
                const day = modalDay.value;
                const jam = modalJam.value;
                const selectedGuru = modalGuruSelect.value;
                const selectedKategori = modalKategoriSelect.value;

                if (!scheduleData[jam]) scheduleData[jam] = {};

                // Priority: Guru over Kategori
                if (selectedGuru) {
                    const [type, id] = selectedGuru.split('-');
                    const guru = gurus.find(g => g.id == id);
                    
                    scheduleData[jam][day] = {
                        guru_id: id,
                        jadwal_kategori_id: null,
                        guru: guru,
                        kategori: null,
                        mapel: guru ? guru.pengampu : null
                    };
                } else if (selectedKategori) {
                    const [type, id] = selectedKategori.split('-');
                    const kategori = kategoris.find(k => k.id == id);
                    
                    scheduleData[jam][day] = {
                        guru_id: null,
                        jadwal_kategori_id: id,
                        guru: null,
                        kategori: kategori,
                        mapel: null
                    };
                } else {
                    scheduleData[jam][day] = null;
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

            function renderCellContent(day, jam) {
                const schedule = scheduleData[jam] && scheduleData[jam][day] ? scheduleData[jam][day] : null;
                const cell = scheduleBody.querySelector(`[data-day="${day}"][data-jam="${jam}"]`);
                if (!cell) return;

                let content = '';
                if (schedule) {
                    cell.classList.add('filled');
                    if (schedule.guru) {
                        content = `<div class="cell-guru"><i class="fas fa-user-tie"></i> ${schedule.guru.nama}</div><div class="cell-mapel">${schedule.guru.pengampu}</div>`;
                    } else if (schedule.kategori) {
                        content = `<div class="cell-kategori"><i class="fas fa-tag"></i> ${schedule.kategori.nama_kategori}</div>`;
                    }
                } else {
                    cell.classList.remove('filled');
                    content = '<div class="cell-placeholder"><i class="fas fa-plus-circle"></i> Tambah</div>';
                }
                cell.innerHTML = content;
                updateStats();
            }

            // --- INITIALIZE VIEW ---
            function initializeTable() {
                let tableHtml = '';
                timeSlots.forEach(slot => {
                    const jam = `${slot.jam_mulai} - ${slot.jam_selesai}`;
                    
                    if (slot.jadwal_kategori && slot.jadwal_kategori.nama_kategori === 'Istirahat') {
                        tableHtml += '<tr class="break-row">';
                        tableHtml += `<td><div class="time-display">${jam}</div></td>`;
                        tableHtml += `<td colspan="${days.length}" class="break-cell"><i class="fas fa-mug-hot"></i> ${slot.jadwal_kategori.nama_kategori}</td>`;
                        tableHtml += '</tr>';
                    } else {
                        tableHtml += '<tr>';
                        tableHtml += `<td><div class="time-display">${jam}</div></td>`;
                        days.forEach(day => {
                            tableHtml += `<td><div class="schedule-cell" data-day="${day}" data-jam="${jam}"></div></td>`;
                        });
                        tableHtml += '</tr>';
                    }
                });
                scheduleBody.innerHTML = tableHtml;

                timeSlots.forEach(slot => {
                    if (!slot.jadwal_kategori || slot.jadwal_kategori.nama_kategori !== 'Istirahat') {
                        const jam = `${slot.jam_mulai} - ${slot.jam_selesai}`;
                        days.forEach(day => {
                            renderCellContent(day, jam);
                        });
                    }
                });
                
                updateStats();
            }

            // --- EVENT LISTENERS ---
            
            // Schedule cell click
            scheduleBody.addEventListener('click', function(e) {
                const cell = e.target.closest('.schedule-cell');
                if (cell) {
                    openModal(cell.dataset.day, cell.dataset.jam);
                }
            });

            // When guru is selected, clear kategori
            $(document).on('change', '#modal-guru-select', function() {
                if ($(this).val()) {
                    modalKategoriSelect.value = '';
                }
            });

            // When kategori is selected, clear guru
            modalKategoriSelect.addEventListener('change', function() {
                if (this.value) {
                    $(modalGuruSelect).val('').trigger('change');
                }
            });

            // Modal buttons
            modalSaveBtn.addEventListener('click', saveModalData);
            modalDeleteBtn.addEventListener('click', deleteModalData);
            modalCancelBtn.addEventListener('click', closeModal);
            modalCloseX.addEventListener('click', closeModal);
            
            window.addEventListener('click', function(e) {
                if (e.target == modal) {
                    closeModal();
                }
            });

            // Bulk save
            bulkSaveBtn.addEventListener('click', async function() {
                this.disabled = true;
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

                const jamToTabeljId = timeSlots.reduce((acc, slot) => {
                    const jam = `${slot.jam_mulai} - ${slot.jam_selesai}`;
                    acc[jam] = slot.id;
                    return acc;
                }, {});

                const schedules = [];
                for (const jam in scheduleData) {
                    for (const day in scheduleData[jam]) {
                        const schedule = scheduleData[jam][day];
                        if (schedule) {
                            schedules.push({
                                hari: day,
                                jam: jam,
                                guru_id: schedule.guru_id,
                                tabelj_id: jamToTabeljId[jam] || null,
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
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: result.message,
                            confirmButtonColor: '#11998e'
                        });
                        window.location.href = '{{ route('jadwal.perKelas', $kelas->id) }}';
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: result.message || 'Terjadi kesalahan saat menyimpan.',
                            confirmButtonColor: '#11998e'
                        });
                    }
                } catch (error) {
                    console.error('Save error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Tidak dapat terhubung ke server.',
                        confirmButtonColor: '#11998e'
                    });
                } finally {
                    this.disabled = false;
                    this.innerHTML = originalText;
                }
            });

            // --- INITIALIZE ---
            initializeTable();
        });
    </script>
@endpush