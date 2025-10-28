{{-- resources/views/jadwal/pilih_kelas.blade.php --}}
@extends('dashboard.admin')

@section('content')

<div class="content-header">
    <h2 style="font-size: 1.8rem; font-weight: 700; color: var(--text-color); margin: 0;">
        <i class="fas fa-calendar-plus" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-right: 10px;"></i>
        Pilih Angkatan untuk Tambah Jadwal
    </h2>
</div>

<!-- Welcome Card -->
<div class="welcome-card" style="margin-bottom: 25px;">
    <div class="welcome-text">
        <h2>Tambah <strong>Jadwal Baru</strong></h2>
        <p>Pilih angkatan terlebih dahulu, kemudian pilih kelas untuk membuat jadwal pembelajaran baru</p>
    </div>
    <div class="welcome-icon">
        <i class="fas fa-tasks"></i>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-container" style="margin-bottom: 30px;">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-layer-group"></i>
        </div>
        <h3 class="stat-value">{{ count($kategori) }}</h3>
        <p class="stat-label">Total Angkatan</p>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <h3 class="stat-value">{{ collect($kategori)->sum('kelas_count') }}</h3>
        <p class="stat-label">Total Kelas</p>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-calendar-check"></i>
        </div>
        <p class="stat-label">Tahun Ajaran</p>
        @if($activeTahunAjaran)
        <h3 class="stat-value">{{ $activeTahunAjaran->tahun_ajaran }}</h3>
        <p class="stat-label">{{ $activeTahunAjaran->semester }}</p>
        @else
        <h3 class="stat-value" style="font-size: 1rem;">-</h3>
        <p class="stat-label">Tahun Ajaran Belum Aktif</p>
        @endif
    </div>
</div>

<!-- Main Table Card -->
<div style="background: white; border-radius: 20px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color); overflow: hidden;">
    
    <!-- Table Header -->
    <div style="padding: 25px 30px; border-bottom: 1px solid var(--border-color); background: linear-gradient(to right, rgba(17, 153, 142, 0.05), transparent);">
        <h2 style="font-size: 1.3rem; font-weight: 700; color: var(--text-color); margin: 0; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-list-ul" style="color: var(--primary-color);"></i>
            Daftar Angkatan
        </h2>
        <p style="margin: 8px 0 0 0; font-size: 0.9rem; color: var(--text-muted);">
            <i class="fas fa-info-circle"></i>
            Pilih salah satu angkatan untuk melanjutkan
        </p>
    </div>
    
    <!-- Table Content -->
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th style="text-align: center; width: 40%;">Angkatan</th>
                    <th style="text-align: center; width: 30%;">Jumlah Kelas</th>
                    <th style="text-align: center; width: 30%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategori as $data)
                <tr>
                    <td style="text-align: center;">
                        <div style="display: inline-flex; align-items: center; gap: 10px; padding: 8px 20px; background: linear-gradient(135deg, rgba(17, 153, 142, 0.1), rgba(56, 239, 125, 0.1)); border-radius: 25px;">
                            <i class="fas fa-graduation-cap" style="color: var(--primary-color); font-size: 1.1rem;"></i>
                            <span style="font-weight: 600; font-size: 1rem; color: var(--text-color);">
                                {{ $data->nama }}
                            </span>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <div style="display: inline-flex; align-items: center; gap: 8px;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 35px; height: 35px; background: var(--primary-gradient); color: white; border-radius: 10px; font-weight: 700; font-size: 1rem; box-shadow: 0 4px 10px rgba(17, 153, 142, 0.3);">
                                {{ $data->kelas_count }}
                            </span>
                            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">
                                Kelas
                            </span>
                        </div>
                    </td>
                    <td style="text-align: center; padding: 16px 20px;">
                        <a href="{{ route('jadwal.pilihSubKelas', $data->nama) }}" 
                           class="btn btn-primary" 
                           style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none;">
                            <span>Pilih</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 60px 20px; color: var(--text-muted);">
                        <i class="fas fa-inbox" style="font-size: 3.5rem; opacity: 0.3; display: block; margin-bottom: 20px;"></i>
                        <p style="margin: 0; font-size: 1.1rem; font-weight: 600;">Tidak Ada Data Angkatan</p>
                        <p style="margin: 8px 0 0 0; font-size: 0.9rem;">
                            Belum ada angkatan yang terdaftar dalam sistem
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Info Cards Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 30px;">
    <!-- Info Card 1 -->
    <div style="background: linear-gradient(135deg, rgba(17, 153, 142, 0.1), rgba(56, 239, 125, 0.1)); border-radius: 15px; padding: 20px 25px; border-left: 4px solid var(--primary-color); transition: var(--transition);" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(17, 153, 142, 0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
        <div style="display: flex; align-items: flex-start; gap: 15px;">
            <i class="fas fa-check-circle" style="font-size: 1.5rem; color: var(--primary-color); flex-shrink: 0;"></i>
            <div>
                <h4 style="margin: 0 0 8px 0; font-size: 1rem; font-weight: 600; color: var(--text-color);">Langkah 1: Pilih Angkatan</h4>
                <p style="margin: 0; font-size: 0.85rem; color: var(--text-light); line-height: 1.5;">
                    Pilih angkatan dari tabel di atas untuk melihat daftar kelas yang tersedia
                </p>
            </div>
        </div>
    </div>
    
    <!-- Info Card 2 -->
    <div style="background: linear-gradient(135deg, rgba(79, 172, 254, 0.1), rgba(0, 180, 219, 0.1)); border-radius: 15px; padding: 20px 25px; border-left: 4px solid #4facfe; transition: var(--transition);" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(79, 172, 254, 0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
        <div style="display: flex; align-items: flex-start; gap: 15px;">
            <i class="fas fa-list-check" style="font-size: 1.5rem; color: #4facfe; flex-shrink: 0;"></i>
            <div>
                <h4 style="margin: 0 0 8px 0; font-size: 1rem; font-weight: 600; color: var(--text-color);">Langkah 2: Pilih Kelas</h4>
                <p style="margin: 0; font-size: 0.85rem; color: var(--text-light); line-height: 1.5;">
                    Setelah memilih angkatan, pilih kelas spesifik untuk membuat jadwal
                </p>
            </div>
        </div>
    </div>
    
    <!-- Info Card 3 -->
    <div style="background: linear-gradient(135deg, rgba(67, 233, 123, 0.1), rgba(56, 249, 215, 0.1)); border-radius: 15px; padding: 20px 25px; border-left: 4px solid #43e97b; transition: var(--transition);" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(67, 233, 123, 0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
        <div style="display: flex; align-items: flex-start; gap: 15px;">
            <i class="fas fa-calendar-plus" style="font-size: 1.5rem; color: #43e97b; flex-shrink: 0;"></i>
            <div>
                <h4 style="margin: 0 0 8px 0; font-size: 1rem; font-weight: 600; color: var(--text-color);">Langkah 3: Buat Jadwal</h4>
                <p style="margin: 0; font-size: 0.85rem; color: var(--text-light); line-height: 1.5;">
                    Isi form jadwal dengan lengkap dan simpan untuk menyelesaikan proses
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Table row hover effect with scale */
.table tbody tr {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

.table tbody tr:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 25px rgba(17, 153, 142, 0.15);
    background: linear-gradient(to right, rgba(17, 153, 142, 0.02), transparent);
}

/* Button hover effect */
.btn-primary {
    position: relative;
    overflow: hidden;
}

.btn-primary::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn-primary:hover::after {
    width: 300px;
    height: 300px;
}

/* Badge animation */
@keyframes badgePulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.table tbody tr:hover td > div > span:first-child {
    animation: badgePulse 1s infinite;
}

/* Responsive */
@media (max-width: 768px) {
    .content-header h2 {
        font-size: 1.4rem !important;
    }
    
    .welcome-card {
        flex-direction: column;
        text-align: center;
        padding: 25px 20px;
    }
    
    .stats-container {
        grid-template-columns: 1fr;
    }
    
    .table {
        font-size: 0.85rem;
    }
    
    .table th, .table td {
        padding: 12px 8px;
    }
    
    .btn span:first-child {
        display: none;
    }
}

@media (max-width: 480px) {
    .table td:nth-child(1) > div {
        padding: 6px 12px !important;
        font-size: 0.9rem !important;
    }
    
    .table td:nth-child(2) > div {
        flex-direction: column;
        gap: 4px !important;
    }
}
</style>
@endpush