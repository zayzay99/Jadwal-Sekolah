{{-- resources/views/jadwal/tabel_pelajaran.blade.php --}}
@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2>Data Pelajaran</h2>
    <p>Daftar mata pelajaran yang tersedia</p>
</div>

<div class="table-container">
    <div class="table-header">
        <h2>Daftar Pelajaran</h2>
       <button type="button" class="btn btn-secondary" onclick="window.history.back()">Kembali</button>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Jam</th>
                    <th>Senin</th>
                    <th>Selasa</th>
                    <th>Rabu</th>
                    <th>Kamis</th>
                    <th>Jumat</th>
                    <th>Sabtu</th>
                    <th>Minggu</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tabeljs as $index => $tabelj)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $tabelj->kode_pelajaran }}</td>
                    <td>{{ $tabelj->nama_pelajaran }}</td>
                    <td>{{ $tabelj->guru->nama ?? 'Tidak Ada' }}</td>
                    <td class="action-buttons">
                        <a href="#" class="btn-warning btn-tiny">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="#" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger btn-tiny" onclick="return confirm('Yakin ingin menghapus?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="no-data">
                        <i class="fas fa-book"></i>
                        Tidak ada data pelajaran.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection