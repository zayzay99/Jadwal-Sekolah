@extends('dashboard.admin')
@section('content')
<div>
    <h2>Tambah Jadwal</h2>
    <form action="{{ route('jadwal.store') }}" method="POST" style="max-width:400px;">
        @csrf
        <div>
            <label>Mata Pelajaran</label>
            <input type="text" name="mapel" required class="form-control">
        </div>
        <div>
            <label>Kelas</label>
            <select name="kelas_id" required class="form-control">
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Guru</label>
            <select name="guru_id" required class="form-control">
                <option value="">-- Pilih Guru --</option>
                @foreach($gurus as $guru)
                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Hari</label>
            <input type="text" name="hari" required class="form-control">
        </div>
        <div>
            <label>Jam</label>
            <input type="text" name="jam" required class="form-control">
        </div>
        <button type="submit" class="menu-item" style="margin-top:15px;">Simpan</button>
    </form>
</div>
@endsection
