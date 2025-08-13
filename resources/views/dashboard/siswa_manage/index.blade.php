@extends('dashboard.admin')
@section('content')
<div>
    <h2>Manajemen Siswa</h2>
    <a href="{{ route('manage.siswa.create') }}" class="menu-item" style="width:fit-content;display:inline-block;">Tambah Siswa</a>
    <table border="1" cellpadding="10" style="margin-top:20px;width:100%;background:#fff;">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIS</th>
                <th>Kelas</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $siswa)
            <tr>
                <td>{{$siswa->id}}</td>
                <td>{{ $siswa->nama }}</td>
                <td>{{ $siswa->nis }}</td>
                <td>
    @if($siswa->kelas->count())
        {{ $siswa->kelas->pluck('nama_kelas')->join(', ') }}
    @else
        -
    @endif
                </td>
                <td>{{ $siswa->email }}</td>
                <td>
                    <a href="{{ route('manage.siswa.edit', $siswa->id) }}">Edit</a> |
                    <form action="{{ route('manage.siswa.destroy', $siswa->id) }}" method="POST" style="display:inline">
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