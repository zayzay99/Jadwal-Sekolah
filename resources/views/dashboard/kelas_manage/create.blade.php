@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Tambah Kelas</h2>
</div>
<div class="form-container">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

<div class="form-container">
    <form action="{{ route('manage.kelas.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nama_kelas">Nama Kelas</label>
            <input type="text" name="nama_kelas" id="nama_kelas" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="guru_id">Wali Kelas (Guru)</label>
            <select name="guru_id" id="guru_id" required class="form-control">
                <option value="">-- Pilih Guru --</option>
                @foreach($gurus as $g)
                    <option value="{{ $g->id }}">{{ $g->nama }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="siswa_ids">Siswa</label>
            <select name="siswa_ids[]" id="siswa_ids" multiple class="form-control">
                @foreach($siswas as $s)
                    <option value="{{ $s->id }}">{{ $s->nama }}</option>
                @endforeach
            </select>
            <small class="form-text">Pilih lebih dari satu dengan Ctrl/Shift</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('manage.kelas.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection