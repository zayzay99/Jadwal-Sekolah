{{-- filepath: resources/views/dashboard/kelas_kategori/index.blade.php --}}
@extends('dashboard.admin')
@section('content')
<div>
    <h2>Lihat Kelas</h2>
    <table style="width:100%;background:#fff;">
        <thead>
            <tr style="background:#217867;color:#fff;">
                <th style="padding:10px;">Kelas</th>
                <th style="padding:10px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kategori as $kat)
            <tr>
                <td style="padding:10px;">{{ $kat }}</td>
                <td style="padding:10px;">
                    <a href="{{ route('kelas.show', $kat) }}" class="btn btn-success">Lihat</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection