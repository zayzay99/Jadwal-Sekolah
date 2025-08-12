@extends('dashboard.admin')
@section('content')
<h2 style="margin-bottom:20px;">Tambah Kelas</h2>
<form action="{{ route('manage.kelas.store') }}" method="POST" style="max-width:400px; background:#fff; padding:24px; border-radius:8px; box-shadow:0 2px 8px #0001; margin-bottom:30px;">
    @csrf
    <div style="margin-bottom:15px;">
        <label for="nama_kelas" style="display:block; margin-bottom:5px;">Nama Kelas</label>
        <input type="text" name="nama_kelas" id="nama_kelas" required style="width:100%; padding:8px;">
    </div>
    <div style="margin-bottom:15px;">
        <label for="guru_id" style="display:block; margin-bottom:5px;">Wali Kelas (Guru)</label>
        <select name="guru_id" id="guru_id" required style="width:100%; padding:8px;">
            <option value="">-- Pilih Guru --</option>
            @foreach($gurus as $g)
                <option value="{{ $g->id }}">{{ $g->nama }}</option>
            @endforeach
        </select>
    </div>
    <div style="margin-bottom:15px;">
        <label for="siswa_ids" style="display:block; margin-bottom:5px;">Siswa</label>
        <select name="siswa_ids[]" id="siswa_ids" multiple style="width:100%; padding:8px; height:100px;">
            @foreach($siswas as $s)
                <option value="{{ $s->id }}">{{ $s->nama }}</option>
            @endforeach
        </select>
        <small>Pilih lebih dari satu dengan Ctrl/Shift</small>
    </div>
    <button type="submit" style="padding:8px 24px; background:#2d6a4f; color:#fff; border:none; border-radius:6px;">Simpan</button>
</form>
@endsection