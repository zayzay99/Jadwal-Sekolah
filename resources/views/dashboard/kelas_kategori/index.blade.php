{{-- filepath: resources/views/dashboard/kelas_kategori/index.blade.php --}}
@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Lihat Kelas</h2>
</div>

<div class="table-container">
    <div class="table-header">
        <h2>Daftar Kelas</h2>
    </div>
    
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="text-align: center;">Kelas</th>
                    <th style="text-align: center;">Jumlah Ruang Kelas</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kategori as $data)
                <tr>
                    <td style="text-align: center;">{{ $data->nama }}</td>
                    <td style="text-align: center;">{{ $data->kelas_count }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('kelas.show', $data->nama) }}" class="btn btn-success btn-tiny">
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