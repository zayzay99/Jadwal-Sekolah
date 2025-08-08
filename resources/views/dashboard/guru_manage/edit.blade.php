@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Edit Guru</h2>
</div>
    <form action="{{ route('manage.guru.update', $guru->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="nama" value="{{ $guru->nama }}" required class="form-control">
        </div>
        <div class="form-group">
            <label>NIP</label>
            <input type="text" name="nip" value="{{ $guru->nip }}" required class="form-control">
        </div>
        <div class="form-group">
            <label>Pengampu</label>
            <input type="text" name="pengampu" value="{{ $guru->pengampu }}" required class="form-control">
        </div>
        <div class="form-group">
            <label>Kelas</label>
            <select name="kelas_id" required class="form-control">
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}" {{ $guru->kelas_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ $guru->email }}" required class="form-control">
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
