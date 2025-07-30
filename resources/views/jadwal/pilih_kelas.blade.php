{{-- filepath: resources/views/jadwal/pilih_kelas.blade.php --}}
@extends('dashboard.admin')
@section('content')
    <h2>Pilih Kelas untuk Tambah Jadwal</h2>
    <form>
        <select id="kelas_id" required>
            <option value="">-- Pilih Kelas --</option>
            @foreach($kelas as $k)
                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
            @endforeach
        </select>
        <button type="button" onclick="if(document.getElementById('kelas_id').value) window.location.href='{{ url('/jadwal/create') }}/' + document.getElementById('kelas_id').value">Pilih</button>
    </form>
@endsection