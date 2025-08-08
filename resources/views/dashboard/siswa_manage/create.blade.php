@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Tambah Siswa</h2>
    <form action="{{ route('manage.siswa.store') }}" method="POST">
        @csrf
        <div class="form-container">
            <div class="form-group">
            <label>Nama</label>
            <input type="text" name="nama" required class="form-control">
        </div>
        <div class="form-group">
            <label>NIS</label>
            <input type="text" name="nis" required class="form-control">
        </div>
        <div class="form-group">
            <label>Kelas</label>
            <select name="kelas_id" required class="form-control">
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required class="form-control">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('manage.guru.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@endsection
