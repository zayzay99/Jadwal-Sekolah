{{-- filepath: resources/views/dashboard/kelas_kategori/show.blade.php --}}
@extends('dashboard.admin')
@section('content')
<h2>Lihat Kelas</h2>
<p>Lihat kelas {{ $kategori }}</p>
<a href="{{ route('kelas.kategori') }}" class="btn btn-success" style="margin-bottom:10px;">
    &larr; Kembali
</a>
<table style="width:100%;background:#fff;">
    <thead>
        <tr style="background:#217867;color:#fff;">
            <th style="padding:10px;">Kelas</th>
            <th style="padding:10px;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($subkelas as $k)
        <tr>
            <td style="padding:10px;">{{ $k->nama_kelas }}</td>
            <td style="padding:10px;">
                <a href="{{ route('kelas.detail', [$kategori, $k->nama_kelas]) }}" class="btn btn-success">
                    <i class="fas fa-search"></i> Lihat
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection