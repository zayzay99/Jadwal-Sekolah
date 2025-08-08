@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Edit Siswa</h2>
</div>

    <form action="{{ route('manage.siswa.update', $siswa->id) }}" method="POST" style="max-width:400px;">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="nama" value="{{ $siswa->nama }}" required class="form-control">
        </div>
        <div class="form-group">
            <label>NIS</label>
            <input type="text" name="nis" value="{{ $siswa->nis }}" required class="form-control">
        </div>
        <div class="form-group">
            <label>Kelas</label>
            <input type="text" name="kelas" value="{{ $siswa->kelas }}" required class="form-control">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ $siswa->email }}" required class="form-control">
        </div>
        <div class="form-group">
            <label>Password (isi jika ingin ganti)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('manage.guru.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
