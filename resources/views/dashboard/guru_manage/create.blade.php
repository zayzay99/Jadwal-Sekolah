@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h1>Tambah Guru</h1>
</div>

<div class="form-container">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
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
                   <label for="email">Email</label>
            <input type="email" id="email" name="email" required class="form-control">
        </div>
        <div class="form-group">
   

          <label for="password">Password</label>
            <input type="password" id="password" name="password" required class="form-control">
        </div>
        
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('manage.guru.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

@endsection
