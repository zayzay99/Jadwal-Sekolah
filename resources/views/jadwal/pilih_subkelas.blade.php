{{-- resources/views/jadwal/pilih_subkelas.blade.php --}}
@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Pilih Kelas untuk Angkatan {{ $kategori }}</h2>
    <p class="subtitle">Pilih kelas untuk membuat jadwal baru</p>
</div>

<div class="table-container">
    <div class="table-header">
        <h2>Daftar Kelas</h2>
        <div class="header-actions">
            <a href="{{ route('jadwal.pilihKelas') }}" class="btn btn-secondary btn-tiny">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Kelas</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subkelas as $k)
                <tr>
                    <td>{{ $k->nama_kelas }}</td>
                    <td style="text-align: center;">
                        <div class="action-buttons">
                            <a href="{{ route('jadwal.create', $k->id) }}" class="btn btn-primary btn-tiny">
                                <i class="fas fa-plus"></i> Buat Jadwal
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection