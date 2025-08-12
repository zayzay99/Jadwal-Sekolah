{{-- filepath: resources/views/dashboard/kelas_manage/index.blade.php --}}
@extends('dashboard.admin')
@section('content')
<h2>Manajemen Kelas</h2>
<a href="{{ route('manage.kelas.create') }}" class="menu-item" style="margin-bottom:15px;display:inline-block;">Tambah Kelas</a>
@if(session('success'))
    <div style="color:green">{{ session('success') }}</div>
@endif
<table border="1" cellpadding="10" style="margin-top:20px;width:100%;background:#fff;">
    <thead>
        <tr>
            <th>Nama Kelas</th>
            <th>Wali Kelas</th>
            <th>Jumlah Siswa</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($kelas as $k)
        <tr>
            <td>{{ $k->nama_kelas }}</td>
            <td>{{ $k->guru ? $k->guru->nama : '-' }}</td>
            <td>{{ $k->siswas->count() }}</td>
            <td>
                <a href="{{ route('manage.kelas.edit', $k->id) }}">Edit</a> |
                <form action="{{ route('manage.kelas.destroy', $k->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Yakin hapus kelas ini?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection