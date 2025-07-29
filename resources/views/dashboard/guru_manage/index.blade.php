@extends('dashboard.admin')
@section('content')
<div>
    <h2>Manajemen Guru</h2>
    <a href="{{ route('manage.guru.create') }}" class="menu-item" style="width:fit-content;display:inline-block;">Tambah Guru</a>
    <table border="1" cellpadding="10" style="margin-top:20px;width:100%;background:#fff;">
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIP</th>
                <th>Pengampu</th>
                <th>Kelas</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gurus as $guru)
            <tr>
                <td>{{ $guru->nama }}</td>
                <td>{{ $guru->nip }}</td>
                <td>{{ $guru->pengampu }}</td>
                <td>{{ $guru->kelas ? $guru->kelas->nama_kelas : '-' }}</td>
                <td>{{ $guru->email }}</td>
                <td>
                    <a href="{{ route('manage.guru.edit', $guru->id) }}">Edit</a> |
                    <form action="{{ route('manage.guru.destroy', $guru->id) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Yakin hapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
