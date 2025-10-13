@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 700; margin: 0 0 8px 0; color: var(--text-color);">
            Edit Slot Waktu
        </h2>
        <p style="color: var(--text-light); margin: 0; font-size: 0.95rem;">
            Ubah informasi slot waktu yang dipilih.
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

    <form action="{{ route('manage.tabelj.update', $tabelj->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="jam_mulai">
                <i class="fas fa-clock" style="margin-right: 8px; color: var(--primary-color);"></i>
                Jam Mulai
            </label>
            <input type="time" name="jam_mulai" id="jam_mulai" required class="form-control" value="{{ old('jam_mulai', \Carbon\Carbon::parse($tabelj->jam_mulai)->format('H:i')) }}">
            <small style="color: var(--text-muted); font-size: 0.85rem; margin-top: 5px; display: block;">
                Tentukan waktu mulai untuk slot ini
            </small>
        </div>

        <div class="form-group">
            <label for="jam_selesai">
                <i class="fas fa-clock" style="margin-right: 8px; color: var(--primary-color);"></i>
                Jam Selesai
            </label>
            <input type="time" name="jam_selesai" id="jam_selesai" required class="form-control" value="{{ old('jam_selesai', \Carbon\Carbon::parse($tabelj->jam_selesai)->format('H:i')) }}">
            <small style="color: var(--text-muted); font-size: 0.85rem; margin-top: 5px; display: block;">
                Tentukan waktu selesai untuk slot ini
            </small>
        </div>

        <div class="form-group">
            <label for="jadwal_kategori_id">
                <i class="fas fa-tag" style="margin-right: 8px; color: var(--primary-color);"></i>
                Kategori Jadwal
            </label>
            <select name="jadwal_kategori_id" id="jadwal_kategori_id" class="form-control">
                <option value="">-- Tidak Ada Kategori --</option>
                @foreach ($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" {{ old('jadwal_kategori_id', $tabelj->jadwal_kategori_id) == $kategori->id ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>
            <small style="color: var(--text-muted); font-size: 0.85rem; margin-top: 5px; display: block;">
                Pilih kategori untuk slot waktu ini (opsional)
            </small>
        </div>

        <div style="margin-top: 25px; padding: 20px; background: var(--bg-primary); border-radius: 12px; border-left: 4px solid var(--primary-color);">
            <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">
                <i class="fas fa-info-circle" style="color: var(--primary-color); margin-right: 8px;"></i>
                <strong>Info:</strong> Perubahan akan mempengaruhi jadwal yang menggunakan slot waktu ini.
            </p>
        </div>
        
        <div class="form-actions" style="margin-top: 30px; padding-top: 25px; border-top: 1px solid var(--border-color);">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Perubahan
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
.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(17, 153, 142, 0.1);
}

select.form-control {
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2311998e' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 15px center;
    padding-right: 40px;
}

select.form-control:focus {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2311998e' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
}
</style>
@endpush