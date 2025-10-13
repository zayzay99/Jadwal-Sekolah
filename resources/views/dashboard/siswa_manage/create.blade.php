@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <div>
        <h2 style="font-size: 2rem; font-weight: 700; margin: 0 0 8px 0; color: var(--text-color);">
            Tambah Siswa Baru
        </h2>
        <p style="color: var(--text-light); margin: 0; font-size: 0.95rem;">
            <i class="fas fa-user-plus"></i> Lengkapi formulir di bawah untuk menambahkan siswa baru
        </p>
    </div>
    <a href="{{ route('manage.siswa.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<!-- Alert Messages -->
@if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Terdapat kesalahan:</strong>
        <ul style="margin: 10px 0 0 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Form Card -->
<div class="welcome-card" style="flex-direction: column; align-items: stretch; max-width: 800px; margin: 0 auto;">
    <!-- Card Header -->
    <div style="padding: 30px; border-bottom: 1px solid var(--border-color); background: linear-gradient(to right, rgba(17, 153, 142, 0.05), transparent);">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="width: 50px; height: 50px; background: var(--success-gradient); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(67, 233, 123, 0.3);">
                <i class="fas fa-user-plus" style="font-size: 1.5rem; color: white;"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.3rem; font-weight: 600; color: var(--text-color);">
                    Formulir Data Siswa
                </h3>
                <p style="margin: 5px 0 0; font-size: 0.9rem; color: var(--text-muted);">
                    Semua field wajib diisi dengan benar
                </p>
            </div>
        </div>
    </div>

    <!-- Form Body -->
    <div style="padding: 35px;">
        <form action="{{ route('manage.siswa.store') }}" method="POST" id="createSiswaForm">
            @csrf
            
            <div class="form-group">
                <label for="nama">
                    <i class="fas fa-user"></i> Nama Lengkap
                </label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required class="form-control" placeholder="Masukkan nama lengkap siswa">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Masukkan nama lengkap sesuai identitas resmi
                </small>
            </div>

            <div class="form-group">
                <label for="nis">
                    <i class="fas fa-id-card"></i> NIS (Nomor Induk Siswa)
                </label>
                <input type="text" id="nis" name="nis" value="{{ old('nis') }}" required class="form-control" placeholder="Contoh: 12345678">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Nomor induk harus unik dan tidak boleh sama
                </small>
            </div>

            <div class="form-group">
                <label for="kelas_id">
                    <i class="fas fa-door-open"></i> Kelas
                </label>
                <select id="kelas_id" name="kelas_id" required class="form-control">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Pilih kelas yang sesuai untuk siswa ini
                </small>
            </div>

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="form-control" placeholder="contoh@email.com">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Email akan digunakan untuk login ke sistem
                </small>
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Password
                </label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" required class="form-control" placeholder="Minimal 8 karakter">
                    <button type="button" onclick="togglePassword('password')" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.1rem;">
                        <i class="fas fa-eye" id="password-eye"></i>
                    </button>
                </div>
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Password minimal 8 karakter, kombinasi huruf dan angka
                </small>
            </div>

            <hr style="margin: 30px 0;">

            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan Data
                </button>
                <a href="{{ route('manage.siswa.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tips Card -->
<div style="max-width: 800px; margin: 20px auto 0; background: linear-gradient(135deg, rgba(67, 233, 123, 0.1) 0%, rgba(56, 249, 215, 0.1) 100%); padding: 20px 25px; border-radius: 15px; border-left: 4px solid #43e97b;">
    <div style="display: flex; align-items: flex-start; gap: 12px;">
        <i class="fas fa-lightbulb" style="font-size: 1.5rem; color: #43e97b; margin-top: 2px;"></i>
        <div>
            <div style="font-weight: 600; color: var(--text-color); margin-bottom: 8px;">
                <i class="fas fa-info-circle"></i> Tips Input Data
            </div>
            <ul style="margin: 0; padding-left: 20px; color: var(--text-light); font-size: 0.9rem; line-height: 1.8;">
                <li>Pastikan NIS unik dan belum terdaftar di sistem</li>
                <li>Email harus valid dan aktif untuk keperluan login</li>
                <li>Password akan otomatis ter-enkripsi untuk keamanan</li>
                <li>Data siswa dapat diubah kembali setelah disimpan</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Form enhancements */
.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    font-weight: 600;
    color: var(--text-color);
    font-size: 0.95rem;
}

.form-group label i {
    color: var(--primary-color);
    font-size: 0.9rem;
}

.form-control::placeholder {
    color: var(--text-muted);
    opacity: 0.7;
}

.form-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .content-header {
        flex-direction: column;
        gap: 15px;
    }

    .content-header .btn {
        width: 100%;
        justify-content: center;
    }

    .welcome-card {
        margin: 0 !important;
    }

    .welcome-card > div:first-child {
        padding: 20px !important;
    }

    .welcome-card > div:last-child {
        padding: 20px !important;
    }

    .form-actions {
        flex-direction: column;
    }

    .form-actions .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Toggle password visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const eye = document.getElementById(inputId + '-eye');
    
    if (input.type === 'password') {
        input.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}

// Form validation
document.getElementById('createSiswaForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    
    if (password.length < 8) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Password Terlalu Pendek',
            text: 'Password minimal harus 8 karakter',
            confirmButtonColor: '#11998e'
        });
        return false;
    }
});

// Auto-hide success/error alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
</script>
@endpush