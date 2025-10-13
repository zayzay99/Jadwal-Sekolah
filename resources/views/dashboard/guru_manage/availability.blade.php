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
                                <th style="text-align: center; min-width: 120px;">
                                    <i class="fas fa-calendar-day" style="margin-right: 6px; font-size: 0.85rem;"></i>
                                    {{ $day }}
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

            <div style="margin-top: 30px; padding: 20px; background: rgba(67, 233, 123, 0.1); border-radius: 12px; border-left: 4px solid #43e97b;">
                <p style="margin: 0; color: var(--text-color); font-size: 0.9rem;">
                    <i class="fas fa-check-circle" style="color: #43e97b; margin-right: 8px;"></i>
                    <strong>Tips:</strong> Pilih semua slot waktu di hari tertentu dengan mengklik checkbox secara berurutan, atau gunakan Ctrl+Click untuk memilih beberapa slot sekaligus.
                </p>
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