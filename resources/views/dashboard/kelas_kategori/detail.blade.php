{{-- filepath: resources/views/dashboard/kelas_kategori/detail.blade.php --}}
@extends('dashboard.admin')
@section('content')
<h2>Daftar Siswa Kelas {{ $kelasObj->nama_kelas }}</h2>
<a href="{{ route('kelas.show', $kategori) }}" class="btn btn-success" style="margin-bottom:10px;">
    &larr; Kembali
</a>
<table style="width:100%;background:#fff;">
    <thead>
        <tr style="background:#217867;color:#fff;">
            <th style="padding:10px">No</th>
            <th style="padding:10px;">Nama</th>
            <th style="padding:10px;">NIS</th>
            <th style="padding:10px;">Email</th>
        </tr>
    </thead>
    <tbody>
        @forelse($siswas as $siswa)
        <tr>
            <td style="padding:10px">{{$siswa->id}}</td>
            <td style="padding:10px;">{{ $siswa->nama }}</td>
            <td style="padding:10px;">{{ $siswa->nis }}</td>
            <td style="padding:10px;">{{ $siswa->email }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="3" style="padding:10px;">Tidak ada siswa di kelas ini.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection