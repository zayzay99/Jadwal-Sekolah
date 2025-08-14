@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Edit Kelas</h2>
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
    <form action="{{ route('manage.kelas.update', $kelas->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nama_kelas">Nama Kelas</label>
            <input type="text" name="nama_kelas" id="nama_kelas" value="{{ $kelas->nama_kelas }}" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="guru_id">Wali Kelas (Guru)</label>
            <select name="guru_id" id="guru_id" required class="form-control">
                <option value="">-- Pilih Guru --</option>
                @foreach($gurus as $g)
                    <option value="{{ $g->id }}" {{ $kelas->guru_id == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="siswa_ids">Siswa</label>
            <select name="siswa_ids[]" id="siswa_ids" multiple class="form-control">
                @foreach($siswas as $s)
                    <option value="{{ $s->id }}" {{ $kelas->siswas->contains($s->id) ? 'selected' : '' }}>{{ $s->nama }}</option>
                @endforeach
            </select>
            <small class="form-text">Pilih lebih dari satu dengan Ctrl/Shift</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('manage.kelas.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection