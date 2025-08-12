@extends('dashboard.admin')
@section('content')
<div>
    <h2>Tambah Siswa</h2>
    <form action="{{ route('manage.siswa.store') }}" method="POST" style="max-width:400px;">
        @csrf
        <div>
            <label>Nama</label>
            <input type="text" name="nama" required class="form-control">
        </div>
        <div>
            <label>NIS</label>
            <input type="text" name="nis" required class="form-control">
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
            <label>Email</label>
            <input type="email" name="email" required class="form-control">
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <button type="submit" class="menu-item" style="margin-top:15px;">Simpan</button>
    </form>
</div>
@endsection
