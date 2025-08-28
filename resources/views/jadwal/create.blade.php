{{-- filepath: resources/views/jadwal/create.blade.php --}}
@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Tambah Jadwal untuk Kelas {{ $kelas->nama_kelas }}</h2>
</div>

<div class="form-container">
    <form action="{{ route('jadwal.store') }}" method="POST">
        @csrf
        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">

        <div class="form-group">
        <label>Mata Pelajaran</label>
        <input type="text" name="mapel" class="form-control" placeholder="Masukkan nama mata pelajaran" required>
        </div>

        <div class="form-group">
        <label>Guru</label> 
        <select name="guru_id" class="form-control" required>
            <option value="">-- Pilih Guru --</option>
            @foreach($guru as $g)
                <option value="{{ $g->id }}">{{ $g->nama }}</option>
            @endforeach
        </select>
        </div>

        <div class="form-row">
            <div class="form-group">
        <label>Hari</label>
        <input type="text" name="hari" class="form-control" placeholder="Misal: Senin" required>
            </div>

            <div class="form-group">
        <label>Jam</label>
        <input type="text" name="jam" class="form-control" placeholder="Misal: 08:00-09:00" required>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
        </div>
    </form>
</div>
@endsection