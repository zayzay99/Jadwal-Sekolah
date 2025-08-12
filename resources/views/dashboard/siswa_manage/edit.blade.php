@extends('dashboard.admin')
@section('content')
<div>
    <h2>Edit Siswa</h2>
    <form action="{{ route('manage.siswa.update', $siswa->id) }}" method="POST" style="max-width:400px;">
        @csrf
        @method('PUT')
        <div>
            <label>Nama</label>
            <input type="text" name="nama" value="{{ $siswa->nama }}" required class="form-control">
        </div>
        <div>
            <label>NIS</label>
            <input type="text" name="nis" value="{{ $siswa->nis }}" required class="form-control">
        </div>
<div>
    <label>Kelas</label>
    <select name="kelas_id" class="form-control" required>
        @foreach($kelas as $k)
            <option value="{{ $k->id }}"
                {{ $siswa->kelas->contains('id', $k->id) ? 'selected' : '' }}>
                {{ $k->nama_kelas }}
            </option>
        @endforeach
    </select>
</div>
        <div>
            <label>Email</label>
            <input type="email" name="email" value="{{ $siswa->email }}" required class="form-control">
        </div>
        <div>
            <label>Password (isi jika ingin ganti)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <button type="submit" class="menu-item" style="margin-top:15px;">Update</button>
    </form>
</div>
@endsection
