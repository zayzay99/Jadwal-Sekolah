@extends('dashboard.admin')
@section('content')
<link rel="stylesheet" href="{{ asset('css/style3.css') }}">
<div class="content-header">
    <h1>Manajemen Guru</h1>
    <a href="{{ route('manage.guru.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>Tambah Guru</a>
</div>

<div class="table-container">
    <div class="table-header">
        <h1>Daftar guru</h1>
    </div>

    <div class="table-responsive">
    <table class="custom-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIP</th>
                <th>Pengampu</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gurus as $guru)
            <tr>
                <td>{{ $guru->nama }}</td>
                <td>{{ $guru->nip }}</td>
                <td>{{ $guru->pengampu }}</td>
                <td>{{ $guru->email }}</td>
                <td>
                    <div class="action-buttons">
                    <a href="{{ route('manage.guru.edit', $guru->id) }}" class="btn btn-sm btn-success"><i class="fas fa-edit"></i>Edit</a> |
                    <form action="{{ route('manage.guru.destroy', $guru->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus?')">
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
                        <i class="fas fa-info-circle"></i> Tidak ada data guru
                    </td>
                </tr>
                @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
