@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2 style="margin: 0; font-size: 1.8rem; font-weight: 700; color: var(--text-color);">
        <i class="fas fa-plus-circle" style="color: var(--primary-color); margin-right: 10px;"></i>
        Tambah Kelas Baru
    </h2>
</div>

<!-- Breadcrumb -->
<div style="margin-bottom: 20px;">
    <a href="{{ route('manage.kelas.index') }}" style="color: var(--text-light); text-decoration: none; font-size: 0.9rem;">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kelas
    </a>
</div>

<!-- Form Container -->
<div class="form-container" style="background: white; border-radius: 20px; padding: 40px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color);">
    
    @if (session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('manage.kelas.store') }}" method="POST">
        @csrf
        
        <!-- Card: Info Kelas -->
        <div style="background: var(--bg-primary); padding: 25px; border-radius: 15px; margin-bottom: 25px; border-left: 4px solid var(--primary-color);">
            <h3 style="margin: 0 0 20px 0; font-size: 1.2rem; color: var(--text-color); display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-chalkboard-teacher" style="color: var(--primary-color);"></i>
                Informasi Kelas
            </h3>
            
            <div class="form-group">
                <label for="tingkat_kelas" style="font-weight: 600; color: var(--text-color); margin-bottom: 8px; display: block;">
                    <i class="fas fa-layer-group" style="color: var(--primary-color); margin-right: 5px;"></i>
                    Tingkat Kelas <span style="color: #e74c3c;">*</span>
                </label>
                <select name="tingkat_kelas" id="tingkat_kelas" required class="form-control">
                    <option value="">-- Pilih Tingkat Kelas --</option>
                    <option value="VII" {{ old('tingkat_kelas') == 'VII' ? 'selected' : '' }}>VII (Tujuh)</option>
                    <option value="VIII" {{ old('tingkat_kelas') == 'VIII' ? 'selected' : '' }}>VIII (Delapan)</option>
                    <option value="IX" {{ old('tingkat_kelas') == 'IX' ? 'selected' : '' }}>IX (Sembilan)</option>
                    <option value="X" {{ old('tingkat_kelas') == 'X' ? 'selected' : '' }}>X (Sepuluh)</option>
                    <option value="XI" {{ old('tingkat_kelas') == 'XI' ? 'selected' : '' }}>XI (Sebelas)</option>
                    <option value="XII" {{ old('tingkat_kelas') == 'XII' ? 'selected' : '' }}>XII (Dua Belas)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="sub_kelas" style="font-weight: 600; color: var(--text-color); margin-bottom: 8px; display: block;">
                    <i class="fas fa-tag" style="color: var(--primary-color); margin-right: 5px;"></i>
                    Nama Sub Kelas <span style="color: #e74c3c;">*</span>
                </label>
                <input type="text" name="sub_kelas" id="sub_kelas" required class="form-control" 
                    placeholder="Contoh: 1, A, IPA, atau Bahasa" value="{{ old('sub_kelas') }}">
                <small class="form-text text-muted" style="display: block; margin-top: 8px;">
                    <i class="fas fa-info-circle"></i> Format nama kelas: <strong>[Tingkat]-[Sub Kelas]</strong>, contoh: <strong>VII-1</strong> atau <strong>X-IPA</strong>
                </small>
            </div>
        </div>

        <!-- Card: Wali Kelas -->
        <div style="background: var(--bg-primary); padding: 25px; border-radius: 15px; margin-bottom: 25px; border-left: 4px solid var(--primary-color);">
            <h3 style="margin: 0 0 20px 0; font-size: 1.2rem; color: var(--text-color); display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-user-tie" style="color: var(--primary-color);"></i>
                Wali Kelas
            </h3>
            
            <div class="form-group">
                <label for="guru_id" style="font-weight: 600; color: var(--text-color); margin-bottom: 8px; display: block;">
                    <i class="fas fa-chalkboard-teacher" style="color: var(--primary-color); margin-right: 5px;"></i>
                    Pilih Guru sebagai Wali Kelas
                </label>
                <select name="guru_id" id="guru_id" class="form-control select2-guru">
                    <option value="">-- Pilih Wali Kelas (Opsional) --</option>
                    @foreach($gurus as $g)
                        <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>
                            {{ $g->nama }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted" style="display: block; margin-top: 8px;">
                    <i class="fas fa-info-circle"></i> Anda bisa mencari nama guru dengan mengetik di kolom ini
                </small>
            </div>
        </div>

        <!-- Card: Siswa -->
        <div style="background: var(--bg-primary); padding: 25px; border-radius: 15px; margin-bottom: 25px; border-left: 4px solid var(--primary-color);">
            <h3 style="margin: 0 0 20px 0; font-size: 1.2rem; color: var(--text-color); display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-users" style="color: var(--primary-color);"></i>
                Daftar Siswa
            </h3>
            
            <div class="form-group">
                <label for="siswa_ids" style="font-weight: 600; color: var(--text-color); margin-bottom: 8px; display: block;">
                    <i class="fas fa-user-graduate" style="color: var(--primary-color); margin-right: 5px;"></i>
                    Pilih Siswa untuk Kelas Ini
                </label>
                <select name="siswa_ids[]" id="siswa_ids" multiple class="form-control" 
                    style="height: 250px; border: 2px solid var(--border-color); border-radius: 10px; padding: 10px;">
                    @foreach($siswas as $s)
                        <option value="{{ $s->id }}" {{ in_array($s->id, old('siswa_ids', [])) ? 'selected' : '' }}>
                            {{ $s->nama }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted" style="display: block; margin-top: 8px;">
                    <i class="fas fa-info-circle"></i> 
                    Tekan <kbd>Ctrl</kbd> (Windows) atau <kbd>Cmd</kbd> (Mac) + klik untuk memilih beberapa siswa
                </small>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions" style="display: flex; gap: 12px; margin-top: 30px; padding-top: 25px; border-top: 2px solid var(--border-color);">
            <button type="submit" class="btn btn-success" style="flex: 1; padding: 14px 24px; font-size: 1rem;">
                <i class="fas fa-save"></i> Simpan Kelas
            </button>
            <a href="{{ route('manage.kelas.index') }}" class="btn btn-secondary" 
                style="flex: 1; padding: 14px 24px; font-size: 1rem; text-decoration: none; text-align: center;">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Custom Select2 Styling */
    .select2-container--default .select2-selection--single {
        height: 48px;
        border: 2px solid var(--border-color);
        border-radius: 10px;
        padding: 8px 15px;
        font-size: 0.95rem;
        transition: var(--transition);
    }
    
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(17, 153, 142, 0.1);
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px;
        color: var(--text-color);
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px;
    }
    
    .select2-dropdown {
        border: 2px solid var(--primary-color);
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(17, 153, 142, 0.15);
    }
    
    .select2-search--dropdown .select2-search__field {
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.9rem;
    }
    
    .select2-search--dropdown .select2-search__field:focus {
        border-color: var(--primary-color);
        outline: none;
    }
    
    .select2-results__option {
        padding: 10px 15px;
        font-size: 0.95rem;
    }
    
    .select2-results__option--highlighted {
        background-color: var(--primary-color) !important;
        color: white;
    }
    
    kbd {
        background: var(--primary-gradient);
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 for Guru dropdown with search
    $('.select2-guru').select2({
        placeholder: "🔍 Cari nama guru...",
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Tidak ada guru ditemukan";
            },
            searching: function() {
                return "Mencari...";
            }
        }
    });
});
</script>
@endpush