@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 700; margin: 0 0 8px 0; color: var(--text-color);">
            Tetapkan Kategori ke Slot Waktu
        </h2>
        <p style="color: var(--text-light); margin: 0; font-size: 0.95rem;">
            Pilih slot waktu dan tetapkan kategori jadwal.
        </p>
    </div>
</div>

<div style="background: white; padding: 35px 40px; border-radius: 20px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color); margin-top: 25px;">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('manage.tabelj.storeAssignedCategory') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="jadwal_kategori_id">
                <i class="fas fa-tag" style="margin-right: 8px; color: var(--primary-color);"></i>
                Pilih Kategori
            </label>
            <select name="jadwal_kategori_id" id="jadwal_kategori_id" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach ($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" {{ old('jadwal_kategori_id') == $kategori->id ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>
                <i class="fas fa-clock" style="margin-right: 8px; color: var(--primary-color);"></i>
                Pilih Slot Waktu
            </label>
            <div style="max-height: 400px; overflow-y: auto; border: 2px solid var(--border-color); padding: 20px; border-radius: 15px; background-color: var(--bg-primary); margin-top: 10px;">
                @forelse ($tabeljs as $tabelj)
                    <div class="form-check" style="padding: 15px; margin-bottom: 12px; background: white; border-radius: 12px; transition: var(--transition); border: 2px solid transparent;">
                        <input class="form-check-input" type="checkbox" name="selected_slots[]" value="{{ $tabelj->id }}" id="slot_{{ $tabelj->id }}" {{ in_array($tabelj->id, old('selected_slots', [])) ? 'checked' : '' }} style="margin-top: 0;">
                        <label class="form-check-label" for="slot_{{ $tabelj->id }}" style="cursor: pointer; font-weight: 500; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-clock" style="color: var(--primary-color); font-size: 0.9rem;"></i>
                            <span>{{ Carbon\Carbon::parse($tabelj->jam_mulai)->format('H:i') }} - {{ Carbon\Carbon::parse($tabelj->jam_selesai)->format('H:i') }}</span>
                            <span class="badge" style="margin-left: auto; background: var(--primary-gradient); color: white; font-size: 0.75rem;">
                                {{ $tabelj->jadwalKategori->nama_kategori ?? 'Tidak ada kategori' }}
                            </span>
                        </label>
                    </div>
                @empty
                    <div style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                        <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                        <p style="margin: 0; font-size: 0.95rem;">Tidak ada slot waktu yang tersedia untuk ditetapkan kategori.</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <div class="form-actions" style="margin-top: 30px; padding-top: 25px; border-top: 1px solid var(--border-color);">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Tetapkan Kategori
            </button>
            <a href="{{ route('manage.tabelj.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.form-check:hover {
    border-color: var(--primary-color) !important;
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.15);
    transform: translateX(5px);
}

.form-check input[type="checkbox"]:checked + label {
    color: var(--primary-color);
    font-weight: 600;
}

.form-check input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}
</style>
@endpush