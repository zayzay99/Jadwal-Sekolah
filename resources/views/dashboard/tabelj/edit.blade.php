@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2>Edit Slot Waktu</h2>
</div>

<div class="form-container" style="max-width: 800px; margin-bottom: 2rem;">
    <form action="{{ route('tabelj.update', $tabelj->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group">
                <label for="jam_mulai">Jam Mulai</label>
                <input type="time" id="jam_mulai" name="jam_mulai" class="form-control" value="{{ $tabelj->jam_mulai }}" required>
            </div>
            <div class="form-group">
                <label for="jam_selesai">Jam Selesai</label>
                <input type="time" id="jam_selesai" name="jam_selesai" class="form-control" value="{{ $tabelj->jam_selesai }}" required>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ route('manage.tabelj.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection