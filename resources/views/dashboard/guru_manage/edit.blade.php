@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Edit Guru</h2>
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
    <form action="{{ route('manage.guru.update', $guru->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" value="{{ $guru->nama }}" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="nip">NIP</label>
            <input type="text" id="nip" name="nip" value="{{ $guru->nip }}" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="pengampu">Pengampu</label>
            <input type="text" id="pengampu" name="pengampu" value="{{ $guru->pengampu }}" required class="form-control">
        </div>
        
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ $guru->email }}" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="password">Password (isi jika ingin ganti)</label>
            <input type="password" id="password" name="password" class="form-control">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('manage.guru.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection