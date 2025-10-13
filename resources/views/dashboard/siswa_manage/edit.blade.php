@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <div>
        <h2 style="font-size: 2rem; font-weight: 700; margin: 0 0 8px 0; color: var(--text-color);">
            Edit Data Siswa
        </h2>
        <p style="color: var(--text-light); margin: 0; font-size: 0.95rem;">
            <i class="fas fa-user-edit"></i> Perbarui informasi siswa: <strong style="color: var(--primary-color);">{{ $siswa->nama }}</strong>
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
    <div style="padding: 30px; border-bottom: 1px solid var(--border-color); background: linear-gradient(to right, rgba(0, 180, 219, 0.05), transparent);">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="width: 50px; height: 50px; background: var(--accent-gradient); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0, 180, 219, 0.3);">
                <i class="fas fa-user-edit" style="font-size: 1.5rem; color: white;"></i>
            </div>
            <div style="flex: 1;">
                <h3 style="margin: 0; font-size: 1.3rem; font-weight: 600; color: var(--text-color);">
                    Formulir Edit Data Siswa
                </h3>
                <p style="margin: 5px 0 0; font-size: 0.9rem; color: var(--text-muted);">
                    Perbarui data yang diperlukan, field kosong tidak akan diubah
                </p>
            </div>
        </div>
    </div>

    <!-- Student Info Summary -->
    <div style="padding: 20px 35px; background: linear-gradient(135deg, rgba(17, 153, 142, 0.05), rgba(56, 239, 125, 0.05)); border-bottom: 1px solid var(--border-color);">
        <div style="display: flex; flex-wrap: wrap; gap: 25px; align-items: center;">
            <div style="flex: 0 0 auto;">
                <img src="{{ $siswa->profile_picture ? asset('storage/' . $siswa->profile_picture) : asset('img/Default-Profile.png') }}" 
                     alt="Foto {{ $siswa->nama }}" 
                     style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-color); box-shadow: 0 4px 12px rgba(17, 153, 142, 0.3);">
            </div>
            <div style="flex: 1; min-width: 100px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                            <i class="fas fa-user"></i> Nama Saat Ini
                        </div>
                        <div style="font-weight: 600; color: var(--text-color); font-size: 1rem;">
                            {{ $siswa->nama }}
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                            <i class="fas fa-id-card"></i> NIS Saat Ini
                        </div>
                        <div style="font-weight: 600; color: var(--primary-color); font-size: 1rem; font-family: 'Courier New', monospace;">
                            {{ $siswa->nis }}
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                            <i class="fas fa-envelope"></i> Email Saat Ini
                        </div>
                        <div style="font-weight: 600; color: var(--text-color); font-size: 0.9rem;">
                            {{ $siswa->email }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Body -->
    <div style="padding: 35px;">
        <form action="{{ route('manage.siswa.update', $siswa->id) }}" method="POST" id="editSiswaForm">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="nama">
                    <i class="fas fa-user"></i> Nama Lengkap
                </label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $siswa->nama) }}" required class="form-control" placeholder="Masukkan nama lengkap siswa">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Perbarui nama lengkap sesuai identitas resmi
                </small>
            </div>

            <div class="form-group">
                <label for="nis">
                    <i class="fas fa-id-card"></i> NIS (Nomor Induk Siswa)
                </label>
                <input type="text" id="nis" name="nis" value="{{ old('nis', $siswa->nis) }}" required class="form-control" placeholder="Contoh: 12345678">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Nomor induk harus unik dan tidak boleh sama dengan siswa lain
                </small>
            </div>

            <div class="form-group">
                <label for="kelas_id">
                    <i class="fas fa-door-open"></i> Kelas
                </label>
                <select id="kelas_id" name="kelas_id" required class="form-control">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ old('kelas_id', optional($siswa->kelas)->first()?->id) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Kelas saat ini: <strong>{{ optional($siswa->kelas)->first()?->nama_kelas ?? 'Belum ada kelas' }}</strong>
                </small>
            </div>

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input type="email" id="email" name="email" value="{{ old('email', $siswa->email) }}" required class="form-control" placeholder="contoh@email.com">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Email akan digunakan untuk login ke sistem
                </small>
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Password Baru
                </label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                    <button type="button" onclick="togglePassword('password')" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.1rem;">
                        <i class="fas fa-eye" id="password-eye"></i>
                    </button>
                </div>
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> <strong>Kosongkan field ini jika tidak ingin mengubah password.</strong> Jika diisi, minimal 8 karakter
                </small>
            </div>

            <hr style="margin: 30px 0;">

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Data
                </button>
                <a href="{{ route('manage.siswa.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Warning Card -->
<div style="max-width: 800px; margin: 20px auto 0; background: linear-gradient(135deg, rgba(242, 153, 74, 0.1) 0%, rgba(242, 201, 76, 0.1) 100%); padding: 20px 25px; border-radius: 15px; border-left: 4px solid #f2994a;">
    <div style="display: flex; align-items: flex-start; gap: 12px;">
        <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; color: #f2994a; margin-top: 2px;"></i>
        <div>
            <div style="font-weight: 600; color: var(--text-color); margin-bottom: 8px;">
                <i class="fas fa-shield-alt"></i> Perhatian
            </div>
            <ul style="margin: 0; padding-left: 20px; color: var(--text-light); font-size: 0.9rem; line-height: 1.8;">
                <li>Pastikan data yang diubah sudah benar sebelum menyimpan</li>
                <li>Jika mengubah NIS, pastikan nomor baru belum terdaftar</li>
                <li>Perubahan email akan mempengaruhi kredensial login siswa</li>
                <li>Password lama akan tetap digunakan jika field password dikosongkan</li>
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

    .welcome-card > div:first-child,
    .welcome-card > div:nth-child(2),
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
document.getElementById('editSiswaForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    
    // Only validate if password field is filled
    if (password && password.length < 8) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Password Terlalu Pendek',
            text: 'Password minimal harus 8 karakter atau kosongkan jika tidak ingin mengubah',
            confirmButtonColor: '#11998e'
        });
        return false;
    }
});

// Confirmation before submit
document.getElementById('editSiswaForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    
    if (!this.dataset.confirmed) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Konfirmasi Perubahan',
            text: password ? 'Anda akan mengubah data siswa termasuk password. Lanjutkan?' : 'Anda akan mengubah data siswa. Lanjutkan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#11998e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check"></i> Ya, Update',
            cancelButtonText: '<i class="fas fa-times"></i> Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                this.dataset.confirmed = 'true';
                this.submit();
            }
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