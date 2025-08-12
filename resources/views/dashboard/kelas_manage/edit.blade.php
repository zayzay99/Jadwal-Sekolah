{{-- filepath: resources/views/dashboard/kelas_manage/edit.blade.php --}}
@extends('dashboard.admin')
@section('content')
<h2>Edit Kelas</h2>
<form action="{{ route('manage.kelas.update', $kelas->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div>
        <label>Nama Kelas</label>
        <input type="text" name="nama_kelas" value="{{ $kelas->nama_kelas }}" required>
    </div>
    <div>
        <label>Wali Kelas (Guru)</label>
        <select name="guru_id" required>
            <option value="">-- Pilih Guru --</option>
            @foreach($gurus as $g)
                <option value="{{ $g->id }}" {{ $kelas->guru_id == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label>Siswa</label>
        <select name="siswa_ids[]" multiple>
            @foreach($siswas as $s)
                <option value="{{ $s->id }}" {{ $kelas->siswas->contains($s->id) ? 'selected' : '' }}>{{ $s->nama }}</option>
            @endforeach
        </select>
        <small>Pilih lebih dari satu dengan Ctrl/Shift</small>
    </div>
    <button type="submit">Update</button>
</form>
@endsection