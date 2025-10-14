@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <div>
        <h2 style="font-size: 2rem; font-weight: 700; margin: 0 0 8px 0; color: var(--text-color);">
            Import Data Siswa
        </h2>
        <p style="color: var(--text-light); margin: 0; font-size: 0.95rem;">
            <i class="fas fa-file-upload"></i> Unggah file Excel atau CSV untuk menambahkan data siswa secara massal
        </p>
    </div>
    <a href="{{ route('manage.siswa.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<!-- Alert Messages -->
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

@if (session('import_errors'))
    <div class="alert alert-danger">
        <i class="fas fa-times-circle"></i>
        <strong>Ditemukan beberapa error saat impor:</strong>
        <ul style="margin: 10px 0 0 0; padding-left: 20px;">
            @foreach (session('import_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<!-- Stats Info -->
<div class="stats-container" style="margin-bottom: 25px;">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-file-excel"></i>
        </div>
        <div class="stat-label" style="margin-top: 10px;">Format Excel</div>
        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">.xlsx, .xls</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-label" style="margin-top: 10px;">Import Massal</div>
        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">Banyak Data</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-label" style="margin-top: 10px;">Cepat & Mudah</div>
        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">Otomatis</div>
    </div>
</div>

<!-- Main Import Card -->
<div class="welcome-card" style="flex-direction: column; align-items: stretch; max-width: 900px; margin: 0 auto;">
    <!-- Card Header -->
    <div style="padding: 30px; border-bottom: 1px solid var(--border-color); background: linear-gradient(to right, rgba(0, 180, 219, 0.05), transparent);">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="width: 50px; height: 50px; background: var(--accent-gradient); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0, 180, 219, 0.3);">
                <i class="fas fa-file-import" style="font-size: 1.5rem; color: white;"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.3rem; font-weight: 600; color: var(--text-color);">
                    Upload File Import
                </h3>
                <p style="margin: 5px 0 0; font-size: 0.9rem; color: var(--text-muted);">
                    Pilih file Excel atau CSV yang berisi data siswa
                </p>
            </div>
        </div>
    </div>

    <!-- Form Body -->
    <div style="padding: 35px;">
        <form action="{{ route('manage.siswa.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
            @csrf
            
            <!-- File Upload Section -->
            <div class="form-group">
                <label for="file" style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; font-weight: 600; color: var(--text-color); font-size: 0.95rem;">
                    <i class="fas fa-file-upload" style="color: var(--primary-color);"></i> Pilih File (Excel/CSV)
                </label>
                
                <div style="position: relative;">
                    <input type="file" id="file" name="file" required accept=".xlsx,.xls,.csv" style="display: none;">
                    <div id="fileDropZone" style="border: 3px dashed var(--border-color); border-radius: 15px; padding: 40px 20px; text-align: center; cursor: pointer; transition: var(--transition); background: linear-gradient(135deg, rgba(17, 153, 142, 0.02), rgba(56, 239, 125, 0.02));">
                        <div style="font-size: 3rem; color: var(--primary-color); margin-bottom: 15px;">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div style="font-size: 1.1rem; font-weight: 600; color: var(--text-color); margin-bottom: 8px;">
                            Klik untuk memilih file atau seret file ke sini
                        </div>
                        <div style="font-size: 0.9rem; color: var(--text-muted);">
                            Format: Excel (.xlsx, .xls) atau CSV (.csv)
                        </div>
                        <div id="fileName" style="margin-top: 15px; font-weight: 600; color: var(--primary-color); display: none;"></div>
                    </div>
                </div>

                <small class="text-muted" style="display: block; margin-top: 10px; padding: 10px 12px; background: rgba(0, 180, 219, 0.08); border-radius: 8px; border-left: 3px solid #00b4db;">
                    <i class="fas fa-info-circle" style="color: #00b4db;"></i> 
                    Pastikan file Anda memiliki kolom header: <strong>nis</strong>, <strong>nama</strong>, <strong>email</strong>, dan <strong>kelas</strong> (semua huruf kecil)
                </small>
            </div>

            <hr style="margin: 30px 0;">

            <!-- Instructions Section -->
            <div style="background: linear-gradient(135deg, rgba(67, 233, 123, 0.08), rgba(56, 249, 215, 0.08)); padding: 25px; border-radius: 15px; border-left: 4px solid #43e97b; margin-bottom: 25px;">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <i class="fas fa-clipboard-list" style="font-size: 1.5rem; color: #43e97b; margin-top: 2px;"></i>
                    <div>
                        <div style="font-weight: 600; color: var(--text-color); margin-bottom: 12px; font-size: 1rem;">
                            <i class="fas fa-check-circle"></i> Ketentuan Import Data
                        </div>
                        <ul style="margin: 0; padding-left: 20px; color: var(--text-light); font-size: 0.9rem; line-height: 1.8;">
                            <li>Jika nama kelas di kolom <strong>Kelas</strong> belum ada, sistem akan membuatnya secara otomatis</li>
                            <li>Kolom <strong>nis</strong> dan <strong>email</strong> harus unik untuk setiap siswa</li>
                            <li>Password akan dibuat secara otomatis menggunakan <strong>nis</strong> sebagai password default</li>
                            <li>File tidak boleh melebihi ukuran maksimal yang ditentukan sistem</li>
                            <li>Data duplikat akan diabaikan atau muncul sebagai error</li>
                        </ul>
                    </div>
                </div>
            </div>

            

            <!-- Submit Actions -->
            <div class="form-actions" style="margin-top: 30px;">
                <button type="submit" class="btn btn-success" id="submitBtn">
                    <i class="fas fa-file-import"></i> Import Data Sekarang
                </button>
                <a href="{{ route('manage.siswa.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Help Card -->
<div style="max-width: 900px; margin: 25px auto 0; background: linear-gradient(135deg, rgba(17, 153, 142, 0.05), rgba(56, 239, 125, 0.05)); padding: 20px 25px; border-radius: 15px; border-left: 4px solid var(--primary-color);">
    <div style="display: flex; align-items: flex-start; gap: 12px;">
        <i class="fas fa-question-circle" style="font-size: 1.5rem; color: var(--primary-color); margin-top: 2px;"></i>
        <div>
            <div style="font-weight: 600; color: var(--text-color); margin-bottom: 8px;">
                <i class="fas fa-lightbulb"></i> Tips Import Data
            </div>
            <ul style="margin: 0; padding-left: 20px; color: var(--text-light); font-size: 0.9rem; line-height: 1.8;">
                <li>Pastikan tidak ada baris kosong di antara data</li>
                <li>Hapus spasi berlebih di awal atau akhir data</li>
                <li>Gunakan format email yang valid (contoh: nama@email.com)</li>
                <li>NIS harus berupa angka atau teks tanpa karakter khusus</li>
                <li>Jika import gagal, periksa error message untuk mengetahui baris yang bermasalah</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* File Drop Zone Hover Effect */
#fileDropZone:hover {
    border-color: var(--primary-color);
    background: linear-gradient(135deg, rgba(17, 153, 142, 0.08), rgba(56, 239, 125, 0.08));
    transform: translateY(-2px);
}

#fileDropZone.dragover {
    border-color: var(--primary-color);
    background: linear-gradient(135deg, rgba(17, 153, 142, 0.15), rgba(56, 239, 125, 0.15));
    border-style: solid;
}

.form-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

/* Responsive */
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
    .welcome-card > div:last-child {
        padding: 20px !important;
    }

    #fileDropZone {
        padding: 30px 15px !important;
    }

    #fileDropZone > div:first-child {
        font-size: 2.5rem !important;
    }

    .form-actions {
        flex-direction: column;
    }

    .form-actions .btn {
        width: 100%;
        justify-content: center;
    }

    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }

    /* Template download section responsive */
    .welcome-card div[style*="justify-content: space-between"] {
        flex-direction: column;
        align-items: stretch !important;
    }

    .welcome-card div[style*="justify-content: space-between"] .btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .stats-container {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const fileDropZone = document.getElementById('fileDropZone');
    const fileName = document.getElementById('fileName');
    const submitBtn = document.getElementById('submitBtn');
    const importForm = document.getElementById('importForm');

    // Click to select file
    fileDropZone.addEventListener('click', function() {
        fileInput.click();
    });

    // File input change
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            displayFileName(this.files[0]);
        }
    });

    // Drag and drop functionality
    fileDropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('dragover');
    });

    fileDropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('dragover');
    });

    fileDropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            displayFileName(files[0]);
        }
    });

    // Display selected file name
    function displayFileName(file) {
        const validExtensions = ['.xlsx', '.xls', '.csv'];
        const fileExtension = file.name.substring(file.name.lastIndexOf('.')).toLowerCase();
        
        if (validExtensions.includes(fileExtension)) {
            fileName.innerHTML = '<i class="fas fa-check-circle"></i> File dipilih: ' + file.name;
            fileName.style.display = 'block';
            fileName.style.color = 'var(--primary-color)';
        } else {
            fileName.innerHTML = '<i class="fas fa-times-circle"></i> Format file tidak valid. Pilih file Excel atau CSV';
            fileName.style.display = 'block';
            fileName.style.color = '#d33';
            fileInput.value = '';
        }
    }

    // Form submission with loading state
    importForm.addEventListener('submit', function(e) {
        if (!fileInput.files || fileInput.files.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'File Belum Dipilih',
                text: 'Silakan pilih file terlebih dahulu',
                confirmButtonColor: '#11998e'
            });
            return false;
        }

        // Show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengimpor Data...';

        // Optional: Show loading dialog
        Swal.fire({
            title: 'Memproses Import',
            text: 'Mohon tunggu, data sedang diproses...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.3s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 8000); // 8 seconds for import messages
    });
});
</script>
@endpush