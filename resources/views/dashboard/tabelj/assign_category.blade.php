@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2>Tetapkan Kategori ke Slot Waktu</h2>
    <p>Pilih slot waktu dan tetapkan kategori jadwal.</p>
</div>

<div class="form-container">
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
            <label for="jadwal_kategori_id">Pilih Kategori</label>
            <select name="jadwal_kategori_id" id="jadwal_kategori_id" class="form-control" required>
                <option value="">Pilih Kategori</option>
                @foreach ($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" {{ old('jadwal_kategori_id') == $kategori->id ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Pilih Slot Waktu:</label>
            <div class="time-slot-selection">
                @forelse ($tabeljs as $tabelj)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="selected_slots[]" value="{{ $tabelj->id }}" id="slot_{{ $tabelj->id }}" {{ in_array($tabelj->id, old('selected_slots', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="slot_{{ $tabelj->id }}">
                            {{ Carbon\Carbon::parse($tabelj->jam_mulai)->format('H:i') }} - {{ Carbon\Carbon::parse($tabelj->jam_selesai)->format('H:i') }} ({{ $tabelj->jadwalKategori->nama_kategori ?? 'Tidak ada kategori' }})
                        </label>
                    </div>
                @empty
                    <p>Tidak ada slot waktu yang tersedia untuk ditetapkan kategori.</p>
                @endforelse
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Tetapkan Kategori
            </button>
            <a href="{{ route('manage.tabelj.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.time-slot-selection {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #e2e8f0;
    padding: 10px;
    border-radius: 5px;
    background-color: #f8f9fa;
}
.form-check {
    margin-bottom: 5px;
}
</style>
@endpush
