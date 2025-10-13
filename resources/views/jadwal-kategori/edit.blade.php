@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2 style="font-size: 1.8rem; font-weight: 700; color: var(--text-color); margin: 0;">
        <i class="fas fa-edit" style="background: var(--accent-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-right: 10px;"></i>
        Edit Kategori Jadwal
    </h2>
</div>

<!-- Breadcrumb Card -->
<div style="background: white; padding: 15px 25px; border-radius: 15px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 4px solid var(--primary-color);">
    <nav style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: var(--text-muted);">
        <a href="{{ route('jadwal-kategori.index') }}" style="color: var(--primary-color); text-decoration: none; font-weight: 500; transition: var(--transition);">
            <i class="fas fa-list"></i> Daftar Kategori
        </a>
        <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i>
        <span style="color: var(--text-color); font-weight: 600;">Edit Kategori</span>
    </nav>
</div>

<!-- Alert Error -->
@if ($errors->any())
    <div class="alert alert-danger" style="display: flex; gap: 15px; align-items: flex-start;">
        <i class="fas fa-exclamation-circle" style="font-size: 1.3rem; flex-shrink: 0; margin-top: 2px;"></i>
        <div style="flex: 1;">
            <strong style="display: block; margin-bottom: 8px; font-size: 1rem;">Terdapat beberapa kesalahan:</strong>
            <ul style="margin:0; padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li style="margin-bottom: 5px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<!-- Form Card -->
<div style="background: white; border-radius: 20px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color); overflow: hidden;">
    
    <!-- Card Header -->
    <div style="padding: 25px 30px; border-bottom: 1px solid var(--border-color); background: linear-gradient(to right, rgba(79, 172, 254, 0.05), transparent);">
        <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text-color); margin: 0; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-pen" style="color: var(--primary-color);"></i>
            Form Edit Kategori
        </h3>
        <p style="margin: 8px 0 0 0; font-size: 0.9rem; color: var(--text-muted);">
            Perbarui informasi kategori jadwal yang sudah ada
        </p>
    </div>

    <!-- Card Body -->
    <div style="padding: 30px;">
        <form action="{{ route('jadwal-kategori.update', $kategori->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Info Current Data -->
            <div style="background: linear-gradient(135deg, rgba(17, 153, 142, 0.05), rgba(56, 239, 125, 0.05)); border-radius: 12px; padding: 15px 20px; margin-bottom: 25px; border-left: 3px solid var(--primary-color);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-info-circle" style="color: var(--primary-color); font-size: 1.2rem;"></i>
                    <div>
                        <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Data Saat Ini:</p>
                        <p style="margin: 5px 0 0 0; font-size: 1rem; color: var(--text-color); font-weight: 700;">{{ $kategori->nama_kategori }}</p>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="nama_kategori" style="display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-tag" style="color: var(--primary-color); font-size: 0.9rem;"></i>
                    Nama Kategori Baru
                    <span style="color: #f5576c; font-weight: 700;">*</span>
                </label>
                <input type="text" 
                       name="nama_kategori" 
                       id="nama_kategori" 
                       required 
                       class="form-control" 
                       placeholder="Contoh: Istirahat, Upacara, Ekstrakurikuler" 
                       value="{{ old('nama_kategori', $kategori->nama_kategori) }}"
                       style="margin-top: 8px;">
                <small class="text-muted" style="display: block; margin-top: 8px;">
                    <i class="fas fa-info-circle"></i>
                    Ubah nama kategori sesuai kebutuhan
                </small>
            </div>
            
            <hr style="margin: 30px 0;">

            <div class="form-actions" style="display: flex; gap: 12px; flex-wrap: wrap;">
                <button type="submit" class="btn btn-success" style="display: flex; align-items: center; gap: 8px; flex: 1; justify-content: center; min-width: 150px;">
                    <i class="fas fa-save"></i>
                    <span>Simpan Perubahan</span>
                </button>
                <a href="{{ route('jadwal-kategori.index') }}" 
                   class="btn btn-secondary" 
                   style="display: flex; align-items: center; gap: 8px; flex: 1; justify-content: center; min-width: 150px; text-decoration: none;">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Warning Card -->
<div style="background: linear-gradient(135deg, rgba(242, 153, 74, 0.1), rgba(242, 201, 76, 0.1)); border-radius: 15px; padding: 20px 25px; margin-top: 20px; border-left: 4px solid #f2994a;">
    <div style="display: flex; align-items: flex-start; gap: 15px;">
        <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; color: #f2994a; flex-shrink: 0;"></i>
        <div>
            <h4 style="margin: 0 0 8px 0; font-size: 1rem; font-weight: 600; color: var(--text-color);">Perhatian!</h4>
            <p style="margin: 0; font-size: 0.9rem; color: var(--text-light); line-height: 1.6;">
                Perubahan pada kategori ini akan mempengaruhi semua jadwal yang menggunakan kategori ini. Pastikan perubahan yang Anda lakukan sudah benar sebelum menyimpan.
            </p>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Form Focus Animation */
.form-control:focus {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(17, 153, 142, 0.15) !important;
}

/* Button Hover Effects */
.btn {
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn:hover::before {
    width: 300px;
    height: 300px;
}

/* Responsive */
@media (max-width: 768px) {
    .content-header h2 {
        font-size: 1.4rem !important;
    }
    
    .form-actions {
        flex-direction: column !important;
    }
    
    .form-actions .btn {
        width: 100% !important;
    }
}
</style>
@endpush