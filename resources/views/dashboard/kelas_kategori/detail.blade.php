{{-- filepath: resources/views/dashboard/kelas_kategori/detail.blade.php --}}
@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Daftar Siswa Kelas {{ $kelasObj->nama_kelas }}</h2>
</div>

<div class="table-container">
    <div class="table-header">
        <h2>Detail Siswa</h2>
        <a href="{{ route('kelas.show', $kategori) }}" class="btn btn-secondary btn-tiny">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>NIS</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $siswa)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="text-align: center;">
                        <img src="{{ $siswa->profile_picture ? asset('storage/' . $siswa->profile_picture) : asset('storage/Default-Profile.png') }}" alt="Foto Profil" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    </td>
                    <td>{{ $siswa->nama }}</td>
                    <td>{{ $siswa->nis }}</td>
                    <td>{{ $siswa->email }}</td>
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