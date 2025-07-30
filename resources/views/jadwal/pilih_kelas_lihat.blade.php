{{-- filepath: resources/views/jadwal/pilih_kelas_lihat.blade.php --}}
@extends('dashboard.admin')
@section('content')
    <h2>Pilih Kelas untuk Melihat Jadwal</h2>
    <form>
        <select id="kelas_id" required>
            <option value="">-- Pilih Kelas --</option>
            @foreach($kelas as $k)
                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
            @endforeach
        </select>
        <button type="button" onclick="if(document.getElementById('kelas_id').value) window.location.href='{{ url('/jadwal/kelas') }}/' + document.getElementById('kelas_id').value">Lihat Jadwal</button>
    </form>
    {{-- <h2>Manajemen Jadwal</h2>
    <p>Tambah jadwal pelajaran untuk kelas tertentu:</p>
    <form>
        <select id="kelas_id_tambah" required>
            <option value="">-- Pilih Kelas --</option>
            @foreach($kelas as $k)
                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
            @endforeach
        </select>
        <button type="button" onclick="if(document.getElementById('kelas_id_tambah').value) window.location.href='{{ url('/jadwal/create') }}/' + document.getElementById('kelas_id_tambah').value">Tambah Jadwal</button>
    </form> --}}
@endsection