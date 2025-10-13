@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 700; margin: 0 0 8px 0; color: var(--text-color);">
            Tambah Guru Baru
        </h2>
        <p style="color: var(--text-light); margin: 0; font-size: 0.95rem;">
            Isi formulir di bawah untuk menambahkan guru baru
        </p>
    </div>
</div>

<div style="background: white; padding: 35px 40px; border-radius: 20px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color); margin-top: 25px;">
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
    
    <form action="{{ route('manage.guru.store') }}" method="POST">
        @csrf
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="nama">
                    <i class="fas fa-user" style="margin-right: 8px; color: var(--primary-color);"></i>
                    Nama Lengkap
                </label>
                <input type="text" id="nama" name="nama" required class="form-control" placeholder="Masukkan nama lengkap guru" value="{{ old('nama') }}">
            </div>
            
            <div class="form-group">
                <label for="nip">
                    <i class="fas fa-id-card" style="margin-right: 8px; color: var(--primary-color);"></i>
                    NIP
                </label>
                <input type="text" id="nip" name="nip" required class="form-control" placeholder="Masukkan NIP" value="{{ old('nip') }}">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="pengampu">
                    <i class="fas fa-book" style="margin-right: 8px; color: var(--primary-color);"></i>
                    Mata Pelajaran Pengampu
                </label>
                <input type="text" id="pengampu" name="pengampu" required class="form-control" placeholder="Contoh: Matematika" value="{{ old('pengampu') }}">
            </div>

            <div class="form-group">
                <label for="total_jam_mengajar">
                    <i class="fas fa-clock" style="margin-right: 8px; color: var(--primary-color);"></i>
                    Total Jam Mengajar (menit)
                </label>
                <input type="number" id="total_jam_mengajar" name="total_jam_mengajar" required class="form-control" value="{{ old('total_jam_mengajar', 280) }}" placeholder="280">
                <small style="color: var(--text-muted); font-size: 0.85rem; margin-top: 5px; display: block;">
                    Total durasi mengajar per minggu dalam menit
                </small>
            </div>
        </div>
        
        <div class="form-group">
            <label for="email">
                <i class="fas fa-envelope" style="margin-right: 8px; color: var(--primary-color);"></i>
                Email
            </label>
            <input type="email" id="email" name="email" required class="form-control" placeholder="contoh@email.com" value="{{ old('email') }}">
            <small style="color: var(--text-muted); font-size: 0.85rem; margin-top: 5px; display: block;">
                Email akan digunakan untuk login ke sistem
            </small>
        </div>
        
        <div class="form-group">
            <label for="password">
                <i class="fas fa-lock" style="margin-right: 8px; color: var(--primary-color);"></i>
                Password
            </label>
            <input type="password" id="password" name="password" required class="form-control" placeholder="Minimal 8 karakter">
            <small style="color: var(--text-muted); font-size: 0.85rem; margin-top: 5px; display: block;">
                Password untuk akses sistem (minimal 8 karakter)
            </small>
        </div>

        <div style="margin-top: 25px; padding: 20px; background: var(--bg-primary); border-radius: 12px; border-left: 4px solid var(--primary-color);">
            <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">
                <i class="fas fa-info-circle" style="color: var(--primary-color); margin-right: 8px;"></i>
                <strong>Info:</strong> Setelah menambahkan guru, Anda dapat mengatur ketersediaan mengajar pada halaman daftar guru.
            </p>
        </div>
        
        <div class="form-actions" style="margin-top: 30px; padding-top: 25px; border-top: 1px solid var(--border-color);">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Data Guru
            </button>
            <a href="{{ route('manage.guru.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

@endsection

@push('styles')
<style>
@media (max-width: 768px) {
    div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endpush