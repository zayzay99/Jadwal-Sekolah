{{-- resources/views/jadwal/pilih_kelas.blade.php --}}
@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2>Pilih Angkatan untuk Tambah Jadwal</h2>
</div>

<div class="table-container">
    <div class="table-header">
        <h2>Daftar Angkatan</h2>
    </div>
    
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="text-align: center;">Angkatan</th>
                    <th style="text-align: center;">Jumlah Kelas</th>
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
                            <a href="{{ route('jadwal.pilihSubKelas', $data->nama) }}" class="btn btn-primary btn-tiny">
                                <i class="fas fa-arrow-right"></i> Pilih
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