@extends('dashboard.admin')
@section('content')
<div>
    <h2>Edit Guru</h2>
    <form action="{{ route('manage.guru.update', $guru->id) }}" method="POST" style="max-width:400px;">
        @csrf
        @method('PUT')
        <div>
            <label>Nama</label>
            <input type="text" name="nama" value="{{ $guru->nama }}" required class="form-control">
        </div>
        <div>
            <label>NIP</label>
            <input type="text" name="nip" value="{{ $guru->nip }}" required class="form-control">
        </div>
        <div>
            <label>Pengampu</label>
            <input type="text" name="pengampu" value="{{ $guru->pengampu }}" required class="form-control">
        </div>
        <div>
            <label>Kelas</label>
            <select name="kelas_id" required class="form-control">
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}" {{ $guru->kelas_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" value="{{ $guru->email }}" required class="form-control">
        </div>
        <div>
            <label>Password (isi jika ingin ganti)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <button type="submit" class="menu-item" style="margin-top:15px;">Update</button>
    </form>
</div>
@endsection
