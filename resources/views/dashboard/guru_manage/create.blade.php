@extends('dashboard.admin')
@section('content')
<div>
    <h2>Tambah Guru</h2>
    @if (session('success'))
        <div style="color: green; margin-bottom: 10px;">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div style="color: red; margin-bottom: 10px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('manage.guru.store') }}" method="POST" style="max-width:400px;">
        @csrf
        <div>
            <label>Nama</label>
            <input type="text" name="nama" required class="form-control">
        </div>
        <div>
            <label>NIP</label>
            <input type="text" name="nip" required class="form-control">
        </div>
        <div>
            <label>Pengampu</label>
            <input type="text" name="pengampu" required class="form-control">
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" required class="form-control">
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <button type="submit" class="menu-item" style="margin-top:15px;">Simpan</button>
    </form>
</div>
@endsection
