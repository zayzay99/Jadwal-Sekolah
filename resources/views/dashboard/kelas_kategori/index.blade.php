
@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2 style="margin: 0; font-size: 1.8rem; font-weight: 700; color: var(--text-color);">Lihat Kelas</h2>
</div>

<!-- Welcome Card -->
<div class="welcome-card" style="margin-bottom: 25px;">
    <div class="welcome-text" style="flex: 1;">
        <h2 style="font-size: 1.5rem; margin-bottom: 15px;">
            Jelajahi <strong>Daftar Kelas</strong>
        </h2>
        <p style="color: var(--text-light); font-size: 0.95rem; margin: 0;">
            Lihat kategori kelas dan jumlah ruang kelas yang tersedia di setiap tingkatan
        </p>
    </div>
    <div class="welcome-icon">
        <i class="fas fa-door-open"></i>
    </div>
</div>

<!-- Table Container -->
<div class="table-container" style="background: white; border-radius: 20px; padding: 30px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color);">
    <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <h2 style="margin: 0; font-size: 1.3rem; font-weight: 700; color: var(--text-color);">
            <i class="fas fa-list-alt" style="margin-right: 10px; color: var(--primary-color);"></i>
            Kategori Kelas
            <span class="badge" style="background: var(--accent-gradient); color: white; margin-left: 10px; padding: 6px 14px; border-radius: 20px; font-size: 0.85rem;">
                {{ count($kategoris ?? []) }} Tingkat
            </span>
        </h2>
    </div>
    
    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; background: white; border-radius: 15px; overflow: hidden;">
            <thead style="background: var(--primary-gradient);">
                <tr>
                    <th style="padding: 15px 20px; text-align: center; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; width: 50%;">Tingkat Kelas</th>
                    <th style="padding: 15px 20px; text-align: center; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; width: 25%;">Jumlah Ruang Kelas</th>
                    <th style="padding: 15px 20px; text-align: center; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; width: 25%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategori as $data)
                <tr style="border-bottom: 1px solid var(--border-color); transition: var(--transition);">
                    <td style="padding: 20px; text-align: center;">
                        <div style="display: inline-flex; align-items: center; gap: 10px; background: var(--bg-primary); padding: 12px 24px; border-radius: 12px;">
                            <i class="fas fa-graduation-cap" style="color: var(--primary-color); font-size: 1.3rem;"></i>
                            <span style="font-size: 1.1rem; font-weight: 600; color: var(--text-color);">Kelas {{ $data->nama }}</span>
                        </div>
                    </td>
                    <td style="padding: 20px; text-align: center;">
                        <span class="badge" style="background: var(--success-gradient); color: white; padding: 8px 18px; border-radius: 20px; font-size: 0.95rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-door-open"></i>
                            {{ $data->kelas_count }} Ruang
                        </span>
                    </td>
                    <td style="padding: 20px; text-align: center;">
                        <a href="{{ route('kelas.show', $data->nama) }}" class="btn btn-info" 
                            title="Lihat Detail Kelas {{ $data->nama }}" 
                            style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 10px 24px; font-weight: 600;">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="padding: 50px 20px; text-align: center;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: var(--text-muted); opacity: 0.5;"></i>
                            <p style="margin: 0; color: var(--text-muted); font-size: 1rem; font-weight: 500;">
                                Belum ada kategori kelas yang tersedia
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Info Card -->
@if(count($kategoris ?? []) > 0)
<div style="background: linear-gradient(135deg, rgba(17, 153, 142, 0.1), rgba(56, 239, 125, 0.1)); padding: 25px; border-radius: 20px; margin-top: 25px; border: 1px solid var(--border-color);">
    <div style="display: flex; align-items: start; gap: 15px;">
        <i class="fas fa-info-circle" style="color: var(--primary-color); font-size: 1.5rem; margin-top: 3px; flex-shrink: 0;"></i>
        <div style="flex: 1;">
            <h3 style="margin: 0 0 8px 0; font-size: 1rem; font-weight: 600; color: var(--text-color);">
                Informasi
            </h3>
            <p style="margin: 0; color: var(--text-light); font-size: 0.9rem; line-height: 1.6;">
                Klik tombol <strong>"Lihat Detail"</strong> untuk melihat daftar lengkap ruang kelas, wali kelas, dan siswa yang terdaftar di setiap tingkatan.
            </p>
        </div>
    </div>
</div>
@endif

@push('styles')
<style>
    /* Table Hover Effect */
    .table tbody tr:hover {
        background-color: rgba(17, 153, 142, 0.05);
        transform: scale(1.005);
    }

    /* Badge Animation */
    .badge {
        transition: var(--transition);
    }

    tr:hover .badge {
        transform: scale(1.05);
    }

    /* Button Hover */
    .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 180, 219, 0.4);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .welcome-card {
            flex-direction: column;
            text-align: center;
        }
        
        .welcome-card .welcome-text {
            text-align: left;
        }
        
        .welcome-icon {
            font-size: 2.5rem;
            margin-top: 15px;
        }
        
        .table-header {
            flex-direction: column;
            align-items: stretch !important;
        }
        
        .table {
            font-size: 0.85rem;
        }
        
        .table th,
        .table td {
            padding: 12px 10px !important;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
            font-size: 0.85rem !important;
            padding: 8px 16px !important;
        }

        .badge {
            font-size: 0.8rem !important;
            padding: 6px 12px !important;
        }

        td > div {
            font-size: 0.95rem !important;
            padding: 10px 16px !important;
        }

        td > div i {
            font-size: 1.1rem !important;
        }
    }

    @media (max-width: 480px) {
        .content-header h2 {
            font-size: 1.5rem !important;
        }
        
        .welcome-text h2 {
            font-size: 1.3rem !important;
        }
        
        .table-container {
            padding: 20px 15px !important;
        }

        .table th {
            font-size: 0.75rem !important;
            padding: 10px 8px !important;
        }
    }
</style>
@endpush
@endsection
