
{{-- resources/views/jadwal/pilih_kelas.blade.php --}}
@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h1>Pilih Kelas untuk Tambah Jadwal</h1>
    <p>Pilih kelas untuk membuat jadwal baru</p>
</div>

<div class="form-container">
    <form>
        <div class="form-group">
            <label>Pilih Kelas</label>
            <select id="kelas_id" class="form-control" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <button type="button" class="btn btn-primary" onclick="if(document.getElementById('kelas_id').value) window.location.href='{{ url('/jadwal/create') }}/' + document.getElementById('kelas_id').value">Pilih</button>
    </form>
</div>

@endsection

