@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Tambah Guru</h2>
</div>

<div class="form-container">
    <form action="{{ route('manage.guru.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="nip">NIP</label>
            <input type="text" id="nip" name="nip" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="pengampu">Pengampu</label>
            <input type="text" id="pengampu" name="pengampu" required class="form-control">
        </div>

        <div class="form-group">
            <label for="total_jam_mengajar">Total Jam Mengajar (menit)</label>
            <input type="number" id="total_jam_mengajar" name="total_jam_mengajar" required class="form-control" value="280">
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required class="form-control">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('manage.guru.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection