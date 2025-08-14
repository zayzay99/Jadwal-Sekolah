{{-- filepath: resources/views/dashboard/kelas_manage/index.blade.php --}}
@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Manajemen Kelas</h2>
</div>

<div class="table-container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-header">
        <h2>Daftar Kelas</h2>
        <a href="{{ route('manage.kelas.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Kelas
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="text-align: center">Nama Kelas</th>
                    <th style="text-align: center">Wali Kelas</th>
                    <th style="text-align: center">Jumlah Siswa</th>
                    <th style="text-align: center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kelas as $k)
                <tr>
                    <td style="text-align: center">{{ $k->nama_kelas }}</td>
                    <td style="text-align: center">{{ $k->guru ? $k->guru->nama : '-' }}</td>
                    <td style="text-align: center">{{ $k->siswas->count() }}</td>
                    <td style="text-align: center; padding: 16px 20px;">
                        <div class="action-buttons">
                            <a href="{{ route('manage.kelas.edit', $k->id) }}" class="btn btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>Edit
                            </a>
                            <form action="{{ route('manage.kelas.destroy', $k->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" title="Hapus" onclick="return confirm('Yakin hapus?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection