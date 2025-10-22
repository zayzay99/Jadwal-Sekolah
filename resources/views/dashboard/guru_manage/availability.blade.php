@extends('dashboard.admin')
@section('content')
    <div class="content-header">
        <div>
            <h2 style="font-size: 1.8rem; font-weight: 700; margin: 0 0 8px 0; color: var(--text-color);">
                Atur Ketersediaan Guru
            </h2>
            <p style="color: var(--text-light); margin: 0; font-size: 0.95rem;">
                <i class="fas fa-user" style="margin-right: 6px; color: var(--primary-color);"></i>
                <strong>{{ $guru->nama }}</strong> - {{ $guru->pengampu }}
            </p>
        </div>
    </div>

    <div style="background: white; padding: 30px 35px; border-radius: 20px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color); margin-top: 25px;">
        <div style="margin-bottom: 25px; padding: 18px; background: var(--bg-primary); border-radius: 12px; border-left: 4px solid var(--primary-color);">
            <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">
                <i class="fas fa-info-circle" style="color: var(--primary-color); margin-right: 8px;"></i>
                <strong>Petunjuk:</strong> Centang kotak pada hari dan jam ketika guru tersedia untuk mengajar. Guru hanya dapat dijadwalkan pada slot yang dipilih.
            </p>
        </div>

        <form action="{{ route('manage.guru.availability.update', $guru->id) }}" method="POST">
            @csrf
            <div style="overflow-x: auto;">
                <table class="table" style="min-width: 800px;">
                    <thead>
                        <tr>
                            <th style="min-width: 150px; position: sticky; left: 0; background: var(--primary-color); z-index: 10;">
                                <i class="fas fa-clock" style="margin-right: 8px;"></i>
                                Jam
                            </th>
                            @foreach ($days as $day)
                                <th style="text-align: center; min-width: 140px; vertical-align: middle;">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                        <span>
                                            <i class="fas fa-calendar-day" style="margin-right: 6px; font-size: 0.85rem;"></i>
                                            {{ $day }}
                                        </span>
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 4px; font-size: 0.8rem; font-weight: normal; color: white; margin-top: 4px;">
                                            <input type="checkbox" class="select-all-day" data-day="{{ $day }}" style="cursor: pointer; accent-color: #fff;"> Pilih Semua
                                        </label>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($timeSlots as $slot)
                            <tr>
                                <td style="font-weight: 600; background: var(--bg-primary); position: sticky; left: 0; z-index: 5;">
                                    <i class="fas fa-clock" style="margin-right: 8px; color: var(--primary-color); font-size: 0.85rem;"></i>
                                    {{ $slot->jam_mulai }} - {{ $slot->jam_selesai }}
                                </td>
                                @foreach ($days as $day)
                                    <td style="text-align: center; padding: 12px;">
                                        <label style="cursor: pointer; display: inline-block; position: relative;">
                                            <input type="checkbox" 
                                                class="availability-checkbox" data-day="{{ $day }}"
                                                data-jam="{{ $slot->jam_mulai }} - {{ $slot->jam_selesai }}"
                                                name="availability[{{ $day }}][]"
                                                value="{{ $slot->jam_mulai }} - {{ $slot->jam_selesai }}"
                                                @if (isset($availabilities[$day]) && in_array($slot->jam_mulai . ' - ' . $slot->jam_selesai, $availabilities[$day])) checked @endif
                                                style="width: 22px; height: 22px; cursor: pointer; accent-color: var(--primary-color);">
                                        </label>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-actions" style="margin-top: 30px; padding-top: 25px; border-top: 1px solid var(--border-color);">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan Ketersediaan
                </button>
                <a href="{{ route('manage.guru.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
.table tbody tr:hover {
    background-color: rgba(17, 153, 142, 0.05);
}

.table td input[type="checkbox"]:checked {
    transform: scale(1.1);
}

.table td label:hover input[type="checkbox"] {
    transform: scale(1.15);
    transition: transform 0.2s ease;
}

/* Responsive untuk mobile */
@media (max-width: 768px) {
    .table {
        font-size: 0.85rem;
    }
    
    .table th, .table td {
        padding: 8px 6px;
        min-width: 80px !important;
    }
    
    .table th:first-child,
    .table td:first-child {
        min-width: 120px !important;
    }
    
    .table td input[type="checkbox"] {
        width: 18px;
        height: 18px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentGuruId = {{ $guru->id }};
    const scheduleClashes = @json($scheduleClashData);

    // Fungsi untuk "Pilih Semua" per hari
    const selectAllCheckboxes = document.querySelectorAll('.select-all-day');
    selectAllCheckboxes.forEach(headerCheckbox => {
        headerCheckbox.addEventListener('change', function() {
            const day = this.getAttribute('data-day');
            const isChecked = this.checked;
            const dayCheckboxes = document.querySelectorAll(`.availability-checkbox[data-day="${day}"]`);
            
            dayCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;

                // Saat "Pilih Semua", kita tidak melakukan pengecekan bentrok satu per satu
                // karena bisa jadi terlalu banyak alert. Biarkan validasi server yang menangani.
                // Namun, jika Anda ingin validasi tetap berjalan, uncomment kode di bawah.
                /*
                if (isChecked) {
                    const jam = checkbox.getAttribute('data-jam');
                    const clash = scheduleClashes[day]?.[jam]?.[currentGuruId];
                    if (clash) {
                        checkbox.checked = false; // Batalkan centang jika bentrok
                        Swal.fire({
                            icon: 'warning',
                            title: 'Jadwal Bentrok!',
                            text: `Guru {{ $guru->nama }} sudah dijadwalkan di kelas ${clash} pada ${day}, jam ${jam}.`,
                        });
                    }
                }
                */
            });
        });
    });

    // Fungsi untuk mengupdate status checkbox "Pilih Semua"
    const availabilityCheckboxes = document.querySelectorAll('.availability-checkbox');
    availabilityCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const day = this.getAttribute('data-day');
            const jam = this.getAttribute('data-jam');

            // Cek bentrok hanya saat mencentang
            if (this.checked) {
                const clash = scheduleClashes[day]?.[jam]?.[currentGuruId];
                if (clash) {
                    // Batalkan aksi centang
                    this.checked = false;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Jadwal Bentrok!',
                        html: `Guru <strong>{{ $guru->nama }}</strong> sudah dijadwalkan di kelas <strong>${clash}</strong> pada hari <strong>${day}</strong>, jam <strong>${jam}</strong>.<br><br>Anda tidak dapat mengatur ketersediaan pada slot waktu ini.`,
                        confirmButtonColor: '#d33'
                    });
                }
            }
            updateSelectAllState(day);
        });
    });

    // Fungsi untuk memeriksa status saat halaman dimuat
    function updateSelectAllState(day) {
        const headerCheckbox = document.querySelector(`.select-all-day[data-day="${day}"]`);
        if (!headerCheckbox) return;

        const dayCheckboxes = document.querySelectorAll(`.availability-checkbox[data-day="${day}"]`);
        const total = dayCheckboxes.length;
        const checkedCount = document.querySelectorAll(`.availability-checkbox[data-day="${day}"]:checked`).length;

        headerCheckbox.checked = total > 0 && total === checkedCount;
        headerCheckbox.indeterminate = checkedCount > 0 && checkedCount < total;
    }

    // Inisialisasi status "Pilih Semua" saat halaman pertama kali dimuat
    const days = @json($days);
    days.forEach(day => updateSelectAllState(day));
});
</script>
@endpush