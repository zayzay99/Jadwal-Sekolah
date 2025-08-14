@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Manajemen Guru</h2>
</div>
<div class="table-container">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

<div class="table-container">
    <div class="table-header">
        <h2>Daftar Guru</h2>
        <a href="{{ route('manage.guru.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Guru
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="text-align: center">Nama</th>
                    <th style="text-align: center">NIP</th>
                    <th style="text-align: center">Pengampu</th>
                    <th style="text-align: center">Email</th>
                    <th style="text-align: center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gurus as $guru)
                <tr>
                    <td style="text-align: center">{{ $guru->nama }}</td>
                    <td style="text-align: center">{{ $guru->nip }}</td>
                    <td style="text-align: center">{{ $guru->pengampu }}</td>
                    <td style="text-align: center">{{ $guru->email }}</td>
                    <td style="text-align: center; padding: 16px 20px;">
                        <div class="action-buttons">
                            <a href="{{ route('manage.guru.edit', $guru->id) }}" class="btn btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>Edit
                            </a>
                            <form action="{{ route('manage.guru.destroy', $guru->id) }}" method="POST" style="display:inline;">
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