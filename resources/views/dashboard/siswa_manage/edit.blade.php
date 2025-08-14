@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Edit Siswa</h2>
</div>
<div class="table-container">
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
    <form action="{{ route('manage.siswa.update', $siswa->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" value="{{ $siswa->nama }}" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="nis">NIS</label>
            <input type="text" id="nis" name="nis" value="{{ $siswa->nis }}" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="kelas_id">Kelas</label>
            <select id="kelas_id" name="kelas_id" required class="form-control">
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}"
                        {{ $siswa->kelas_id == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ $siswa->email }}" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="password">Password (isi jika ingin ganti)</label>
            <input type="password" id="password" name="password" class="form-control">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('manage.siswa.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection