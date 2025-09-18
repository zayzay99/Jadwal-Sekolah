{{-- filepath: resources/views/dashboard/kelas_kategori/detail.blade.php --}}
@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Daftar Siswa Kelas {{ $kelasObj->nama_kelas }}</h2>
</div>

<div class="table-container">
    <div class="table-header">
        <h2>Detail Siswa</h2>
        <div class="table-header-actions" style="display: flex; align-items: center; width: 100%;">
            <form action="{{ route('kelas.detail', [$kategori, $kelasObj->nama_kelas]) }}" method="GET" class="search-form" style="display: flex; align-items: center; flex-grow: 1;">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, NIS, atau email..." value="{{ request('search') }}" style="margin-right: 10px;">
                <button type="submit" class="btn btn-primary btn-tiny">Cari</button>
            </form>
            <a href="{{ route('kelas.show', $kategori) }}" class="btn btn-secondary btn-tiny" style="margin-left: 10px;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="text-align: center;">No</th>
                    <th style="text-align: center;">Foto</th>
                    <th style="text-align: center;">Nama</th>
                    <th style="text-align: center;">NIS</th>
                    <th style="text-align: center;">Email</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $siswa)
                <tr>
                    <td data-label="No" style="text-align: center;">{{ $loop->iteration }}</td>
                    <td data-label="Foto" style="text-align: center;">
                        <img src="{{ $siswa->profile_picture ? asset('storage/' . $siswa->profile_picture) : asset('storage/Default-Profile.png') }}" alt="Foto Profil" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    </td>
                    <td data-label="Nama" style="text-align: center;">{{ $siswa->nama }}</td>
                    <td data-label="NIS" style="text-align: center;">{{ $siswa->nis }}</td>
                    <td data-label="Email" style="text-align: center;">{{ $siswa->email }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">
                        Tidak ada siswa di kelas ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
@media (max-width: 768px) {
    .table-header-actions {
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
    }
    .search-form {
        width: 100%;
    }
    .search-form .form-control {
        width: 100%;
        flex-grow: 1;
    }
    .table-header-actions .btn {
        width: 100%;
        justify-content: center;
    }
    .custom-table thead {
        display: none;
    }
    .custom-table, .custom-table tbody, .custom-table tr, .custom-table td {
        display: block;
        width: 100%;
    }
    .custom-table tr {
        margin-bottom: 15px;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .custom-table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
        text-align: right;
    }
    .custom-table td:last-child {
        border-bottom: none;
    }
    .custom-table td::before {
        content: attr(data-label);
        font-weight: bold;
        text-align: left;
        padding-right: 15px;
        color: #333;
    }
    .custom-table td[data-label="Foto"] {
        justify-content: center;
    }
}
</style>
@endpush