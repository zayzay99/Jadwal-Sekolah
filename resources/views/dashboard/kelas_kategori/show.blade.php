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
        <div class="header-actions">
            <a href="{{ route('kelas.kategori') }}" class="btn btn-secondary btn-tiny">
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