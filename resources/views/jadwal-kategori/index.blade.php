@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2 style="font-size: 1.8rem; font-weight: 700; color: var(--text-color); margin: 0;">
        <i class="fas fa-tags" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-right: 10px;"></i>
        Manajemen Kategori Jadwal
    </h2>
</div>

<!-- Welcome/Info Card -->
<div class="welcome-card" style="margin-bottom: 25px;">
    <div class="welcome-text">
        <h2>Kelola <strong>Kategori Jadwal</strong></h2>
        <p>Atur dan kelola kategori jadwal untuk sistem penjadwalan yang lebih terorganisir</p>
    </div>
    <div class="welcome-icon">
        <i class="fas fa-calendar-alt"></i>
    </div>
</div>

<!-- Alert Success -->
@if (session('success'))
    <div class="alert alert-success" style="display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-check-circle" style="font-size: 1.3rem;"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Main Table Card -->
<div style="background: white; border-radius: 20px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color); overflow: hidden;">
    
    <!-- Table Header -->
    <div style="padding: 25px 30px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; background: linear-gradient(to right, rgba(17, 153, 142, 0.05), transparent);">
        <h2 style="font-size: 1.3rem; font-weight: 700; color: var(--text-color); margin: 0; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-list" style="color: var(--primary-color);"></i>
            Daftar Kategori
        </h2>
        <a href="{{ route('jadwal-kategori.create') }}" class="btn btn-success" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
            <i class="fas fa-plus"></i>
            <span>Tambah Kategori</span>
        </a>
    </div>
    
    <!-- Table Content -->
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th style="text-align: center; width: 60%;">Nama Kategori</th>
                    <th style="text-align: center; width: 40%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategoris as $kategori)
                <tr>
                    <td style="text-align: center; font-weight: 500; color: var(--text-color);">
                        <i class="fas fa-tag" style="color: var(--primary-color); margin-right: 8px; font-size: 0.9rem;"></i>
                        {{ $kategori->nama_kategori }}
                    </td>
                    <td style="text-align: center; padding: 16px 20px;">
                        <div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
                            <a href="{{ route('jadwal-kategori.edit', $kategori->id) }}" 
                               class="btn btn-info btn-sm" 
                               title="Edit"
                               style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </a>
                            
                            <form action="{{ route('jadwal-kategori.destroy', $kategori->id) }}" 
                                  method="POST" 
                                  style="display:inline; margin: 0;" 
                                  class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-danger btn-sm" 
                                        title="Hapus"
                                        style="display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);">
                                    <i class="fas fa-trash"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                        <i class="fas fa-inbox" style="font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 15px;"></i>
                        <p style="margin: 0; font-size: 1rem;">Belum ada kategori yang tersedia</p>
                        <p style="margin: 5px 0 0 0; font-size: 0.9rem;">Silakan tambahkan kategori baru</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('.delete-form');

    deleteForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda tidak akan dapat mengembalikan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#11998e',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'animated-popup',
                    confirmButton: 'animated-button',
                    cancelButton: 'animated-button'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

<style>
/* SweetAlert2 Custom Animations */
.animated-popup {
    border-radius: 20px !important;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3) !important;
}

.animated-button {
    border-radius: 10px !important;
    font-weight: 600 !important;
    padding: 10px 24px !important;
    transition: all 0.3s ease !important;
}

.animated-button:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(0,0,0,0.2) !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .content-header h2 {
        font-size: 1.4rem !important;
    }
    
    .table {
        font-size: 0.85rem;
    }
    
    .btn-sm span {
        display: none;
    }
    
    .btn-sm {
        padding: 8px 12px !important;
    }
}
</style>
@endpush