{{-- filepath: resources/views/dashboard/kelas_kategori/show.blade.php --}}
@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Lihat Kelas</h2>
    <p class="subtitle">Lihat kelas {{ $kategori }}</p>
</div>

<div class="table-container">
    <div class="table-header">
        <h2>Daftar Sub Kelas</h2>
        <div class="table-header-actions" style="display: flex; align-items: center; width: 100%;">
            <form action="{{ route('kelas.show', $kategori) }}" method="GET" class="search-form" style="display: flex; align-items: center; flex-grow: 1;">
                <input type="text" name="search" class="form-control" placeholder="Cari kelas, wali kelas, atau jumlah siswa..." value="{{ request('search') }}" style="margin-right: 10px;">
                <button type="submit" class="btn btn-primary btn-tiny">Cari</button>
            </form>
            <a href="{{ route('kelas.kategori') }}" class="btn btn-secondary btn-tiny" style="margin-left: 10px;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Kelas</th>
                    <th>Wali Kelas</th>
                    <th style="text-align: center;">Jumlah Siswa</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subkelas as $k)
                <tr>
                    <td data-label="Kelas">{{ $k->nama_kelas }}</td>
                    <td data-label="Wali Kelas">{{ $k->guru->nama ?? 'Belum diatur' }}</td>
                    <td data-label="Jumlah Siswa" style="text-align: center;">{{ $k->siswas_count }}</td>
                    <td data-label="Aksi" style="text-align: center;">
                        <div class="action-buttons">
                            <a href="{{ route('kelas.detail', [$kategori, $k->nama_kelas]) }}" class="btn btn-success btn-tiny">
                                <i class="fas fa-search"></i> Lihat
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
    .custom-table td[data-label="Aksi"] .action-buttons {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>
@endpush
