@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Manajemen Siswa</h2>
    <a href="{{ route('manage.siswa.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>Tambah Siswa</a>
</div>

<div class="table-container">
    <div class="table-header">
        <h1>Daftar siswa</h1>
    </div>

    <div class="table-responsive">
    <table class="custom-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIS</th>
                <th>Kelas</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswas as $siswa)
            <tr>
                <td>{{ $siswa->nama }}</td>
                <td>{{ $siswa->nis }}</td>
                <td>{{ $siswa->kelas ? $siswa->kelas->nama_kelas : '-' }}</td>
                <td>{{ $siswa->email }}</td>
                <td>
                    <div class="action-buttons">
                    <a href="{{ route('manage.siswa.edit', $siswa->id) }}" class="btn btn-sm btn-success"><i class="fas fa-edit"></i>>Edit</a> |
                    <form action="{{ route('manage.siswa.destroy', $siswa->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin hapus?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>Hapus</button>
                    </form>
                    </div>
                </td>
            </tr>
             @empty
            <tr>
                    <td colspan="6" class="no-data">
                        <i class="fas fa-info-circle"></i> Tidak ada data siswa
                    </td>
                </tr>
                @endforelse
            
        </tbody>
    </table>
</div>
</div>
@endsection
