@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2>Edit Slot Waktu</h2>
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

    <form action="{{ route('manage.tabelj.update', $tabelj->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="jam_mulai">Jam Mulai</label>
            <input type="time" name="jam_mulai" id="jam_mulai" required class="form-control" value="{{ old('jam_mulai', $tabelj->jam_mulai) }}">
        </div>
        <div class="form-group">
            <label for="jam_selesai">Jam Selesai</label>
            <input type="time" name="jam_selesai" id="jam_selesai" required class="form-control" value="{{ old('jam_selesai', $tabelj->jam_selesai) }}">
        </div>
        <div class="form-group">
            <label for="jadwal_kategori_id">Kategori Jadwal</label>
            <select name="jadwal_kategori_id" id="jadwal_kategori_id" class="form-control" required>
                <option value="">Pilih Kategori</option>
                @foreach ($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" {{ old('jadwal_kategori_id', $tabelj->jadwal_kategori_id) == $kategori->id ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ route('manage.tabelj.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection
