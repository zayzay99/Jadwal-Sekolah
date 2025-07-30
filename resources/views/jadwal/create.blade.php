{{-- filepath: resources/views/jadwal/create.blade.php --}}
@extends('dashboard.admin')
@section('content')
    <h2>Tambah Jadwal untuk Kelas {{ $kelas->nama_kelas }}</h2>
    <form action="{{ route('jadwal.store') }}" method="POST">
        @csrf
        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
        <label>Mata Pelajaran</label>
        <input type="text" name="mapel" required>
        <label>Guru</label>
        <select name="guru_id" required>
            <option value="">-- Pilih Guru --</option>
            @foreach($guru as $g)
                <option value="{{ $g->id }}">{{ $g->nama }}</option>
            @endforeach
        </select>
        <label>Hari</label>
        <input type="text" name="hari" required>
        <label>Jam</label>
        <input type="text" name="jam" required>
        <button type="submit">Simpan</button>
    </form>
@endsection