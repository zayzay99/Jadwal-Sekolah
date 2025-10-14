@extends('dashboard.admin')

@section('content')
<div class="content-header" style="margin-bottom: 30px;">
    <h2 style="margin: 0; font-size: 1.8rem; font-weight: 700; color: var(--text-color);">
        <i class="fas fa-file-import" style="margin-right: 10px; color: var(--primary-color);"></i>
        Import Data Guru
    </h2>
</div>

<!-- Main Container -->
<div style="background: white; border-radius: 20px; padding: 40px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color);">
    
    <!-- Header Section -->
    <div style="margin-bottom: 35px; padding-bottom: 25px; border-bottom: 2px solid var(--border-color);">
        <h3 style="margin: 0 0 10px 0; font-size: 1.5rem; font-weight: 700; color: var(--text-color);">
            Formulir Import Data Guru
        </h3>
        <p style="margin: 0; color: var(--text-light); font-size: 0.95rem;">
            Unggah file Excel (.xlsx, .xls) untuk menambahkan beberapa guru sekaligus dengan mudah.
        </p>
    </div>

    <!-- Instructions Card -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; padding: 25px; margin-bottom: 30px; color: white;">
        <h4 style="margin: 0 0 15px 0; font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-lightbulb" style="font-size: 1.3rem;"></i>
            Petunjuk Penggunaan
        </h4>
        
        <ol style="margin: 0; padding-left: 20px; line-height: 1.8; font-size: 0.95rem;">
            <li style="margin-bottom: 10px;">
                <strong>Unduh Template:</strong> Klik tombol "Unduh Template" di bawah untuk mendapatkan file template Excel.
            </li>
            <li style="margin-bottom: 10px;">
                <strong>Isi Data:</strong> Isi data guru sesuai dengan kolom yang ada. Kolom <strong>NIP</strong> dan <strong>Email</strong> harus unik (tidak boleh ada yang sama).
            </li>
            <li style="margin-bottom: 10px;">
                <strong>Password Otomatis:</strong> Kolom <strong>Password</strong> akan diisi otomatis dengan nilai default `<code style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 4px;">password</code>` jika dikosongkan.
            </li>
            <li style="margin-bottom: 10px;">
                <strong>Jam Mengajar:</strong> Kolom <strong>Total Jam Mengajar</strong> adalah jumlah jam wajib mengajar per minggu.
            </li>
            <li>
                <strong>Format File:</strong> Pastikan format file adalah <strong>.xlsx</strong> atau <strong>.xls</strong>.
            </li>
        </ol>

        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.3);">
            <a href="{{ asset('templates/template_guru.xlsx') }}" class="btn btn-light" download style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; background: white; color: #667eea; padding: 10px 20px; border-radius: 8px; font-weight: 600; transition: all 0.3s;">
                <i class="fas fa-download"></i> Unduh Template Excel
            </a>
        </div>
    </div>

    <!-- Form Section -->
    <form action="{{ route('manage.guru.import.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Alert Errors -->
        @if ($errors->any())
            <div style="background: #ffe5e5; border: 2px solid #f5576c; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <i class="fas fa-exclamation-circle" style="color: #f5576c; font-size: 1.2rem;"></i>
                    <strong style="color: #f5576c; font-size: 1rem;">Terjadi Kesalahan!</strong>
                </div>
                <ul style="margin: 0; padding-left: 20px; color: #d32f2f;">
                    @foreach ($errors->all() as $error)
                        <li style="margin-bottom: 8px; font-size: 0.95rem;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- File Input -->
        <div style="margin-bottom: 30px;">
            <label for="file" style="display: block; margin-bottom: 12px; font-weight: 600; color: var(--text-color); font-size: 1rem;">
                <i class="fas fa-file-excel" style="margin-right: 8px; color: var(--primary-color);"></i>
                Pilih File Excel
            </label>
            
            <div style="position: relative;">
                <input type="file" name="file" id="file" required accept=".xlsx, .xls" 
                       style="position: absolute; width: 0; height: 0; opacity: 0; cursor: pointer;">
                
                <label for="file" style="display: flex; align-items: center; justify-content: center; gap: 15px; 
                                        border: 2px dashed var(--primary-color); border-radius: 12px; 
                                        padding: 40px; cursor: pointer; background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
                                        transition: all 0.3s ease;">
                    <div style="text-align: center;">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 10px; display: block;"></i>
                        <strong style="display: block; color: var(--text-color); margin-bottom: 5px; font-size: 1rem;">Drag & Drop File di Sini</strong>
                        <span style="color: var(--text-light); font-size: 0.9rem;">atau klik untuk memilih file</span>
                    </div>
                </label>

                <div id="file-name" style="margin-top: 12px; padding: 12px; background: #f0f4ff; border-radius: 8px; border-left: 4px solid var(--primary-color); display: none;">
                    <small style="color: var(--text-light);">File dipilih: <strong id="selected-file" style="color: var(--primary-color);"></strong></small>
                </div>
            </div>

            <small style="display: block; margin-top: 10px; color: var(--text-muted); font-size: 0.85rem;">
                <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
                Hanya file dengan format <strong>.xlsx</strong> atau <strong>.xls</strong> yang diterima.
            </small>
        </div>

        <!-- Form Actions -->
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <button type="submit" class="btn btn-success" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; font-weight: 600; border-radius: 10px; border: none; cursor: pointer; transition: all 0.3s;">
                <i class="fas fa-upload"></i> Unggah dan Import
            </button>
            <a href="{{ route('manage.guru.index') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; font-weight: 600; border-radius: 10px; text-decoration: none; transition: all 0.3s;">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

@endsection

@push('styles')
<style>
    /* File input drag & drop effect */
    #file:hover + label,
    #file:focus + label {
        border-color: var(--primary-color);
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        transform: translateY(-2px);
    }

    /* Button styling */
    .btn {
        transition: all 0.3s ease;
        border: none;
        font-size: 0.95rem;
    }

    .btn-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        [style*="padding: 40px"] {
            padding: 20px !important;
        }

        label[for="file"] {
            padding: 30px 15px !important;
        }

        .btn {
            width: 100%;
            justify-content: center !important;
        }

        h2 {
            font-size: 1.5rem !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const fileNameDisplay = document.getElementById('file-name');
    const selectedFileName = document.getElementById('selected-file');
    const fileLabel = document.querySelector('label[for="file"]');

    // Show file name when selected
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            selectedFileName.textContent = this.files[0].name;
            fileNameDisplay.style.display = 'block';
            fileLabel.style.borderColor = '#11998e';
        }
    });

    // Drag & drop functionality
    fileLabel.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = '#11998e';
        this.style.background = 'linear-gradient(135deg, rgba(17, 153, 142, 0.15), rgba(56, 239, 125, 0.15))';
    });

    fileLabel.addEventListener('dragleave', function() {
        this.style.borderColor = 'var(--primary-color)';
        this.style.background = 'linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05))';
    });

    fileLabel.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const files = e.dataTransfer.files;
        if (files && files[0]) {
            fileInput.files = files;
            const event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        }
    });
});
</script>
@endpush