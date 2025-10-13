@extends('dashboard.admin')
@section('content')

<div class="content-header">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 700; margin: 0 0 8px 0; color: var(--text-color);">
            Edit Data Guru
        </h2>
        <p style="color: var(--text-light); margin: 0; font-size: 0.95rem;">
            Perbarui informasi guru yang dipilih
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

    <form action="{{ route('manage.guru.update', $guru->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="nama">
                    <i class="fas fa-user" style="margin-right: 8px; color: var(--primary-color);"></i>
                    Nama Lengkap
                </label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $guru->nama) }}" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="nip">
                    <i class="fas fa-id-card" style="margin-right: 8px; color: var(--primary-color);"></i>
                    NIP
                </label>
                <input type="text" id="nip" name="nip" value="{{ old('nip', $guru->nip) }}" required class="form-control">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="pengampu">
                    <i class="fas fa-book" style="margin-right: 8px; color: var(--primary-color);"></i>
                    Mata Pelajaran Pengampu
                </label>
                <input type="text" id="pengampu" name="pengampu" value="{{ old('pengampu', $guru->pengampu) }}" required class="form-control">
            </div>

            <div class="form-group">
                <label for="total_jam_mengajar">
                    <i class="fas fa-clock" style="margin-right: 8px; color: var(--primary-color);"></i>
                    Total Jam Mengajar (menit)
                </label>
                <input type="number" id="total_jam_mengajar" name="total_jam_mengajar" required class="form-control" value="{{ old('total_jam_mengajar', $guru->total_jam_mengajar) }}">
            </div>
        </div>

        <div style="padding: 20px; background: var(--bg-primary); border-radius: 15px; border: 2px dashed var(--border-color); margin-bottom: 20px;">
            <label style="font-weight: 600; color: var(--text-color); margin-bottom: 15px; display: block;">
                <i class="fas fa-cog" style="margin-right: 8px; color: var(--primary-color);"></i>
                Pengaturan Batas Mengajar (Opsional)
            </label>
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div class="form-group">
                    <label for="max_jp_per_minggu">Batas Mengajar per Minggu (JP)</label>
                    <input type="number" id="max_jp_per_minggu" name="max_jp_per_minggu" value="{{ old('max_jp_per_minggu', $guru->max_jp_per_minggu) }}" class="form-control" placeholder="Contoh: 24">
                    <small style="color: var(--text-muted); font-size: 0.8rem; margin-top: 5px; display: block;">
                        Maksimal jam pelajaran per minggu
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="max_jp_per_hari">Batas Mengajar per Hari (JP)</label>
                    <input type="number" id="max_jp_per_hari" name="max_jp_per_hari" value="{{ old('max_jp_per_hari', $guru->max_jp_per_hari) }}" class="form-control" placeholder="Contoh: 5">
                    <small style="color: var(--text-muted); font-size: 0.8rem; margin-top: 5px; display: block;">
                        Maksimal jam pelajaran per hari
                    </small>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="email">
                <i class="fas fa-envelope" style="margin-right: 8px; color: var(--primary-color);"></i>
                Email
            </label>
            <input type="email" id="email" name="email" value="{{ old('email', $guru->email) }}" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="password">
                <i class="fas fa-lock" style="margin-right: 8px; color: var(--primary-color);"></i>
                Password
            </label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
            <small style="color: var(--text-muted); font-size: 0.85rem; margin-top: 5px; display: block;">
                Isi hanya jika ingin mengubah password
            </small>
        </div>

        <div style="margin-top: 25px; padding: 20px; background: rgba(255, 193, 7, 0.1); border-radius: 12px; border-left: 4px solid #ffc107;">
            <p style="margin: 0; color: var(--text-color); font-size: 0.9rem;">
                <i class="fas fa-exclamation-triangle" style="color: #ffc107; margin-right: 8px;"></i>
                <strong>Perhatian:</strong> Perubahan data akan mempengaruhi jadwal yang sudah dibuat.
            </p>
        </div>
        
        <div class="form-actions" style="margin-top: 30px; padding-top: 25px; border-top: 1px solid var(--border-color);">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Perubahan
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