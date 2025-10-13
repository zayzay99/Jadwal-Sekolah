{{-- filepath: resources/views/dashboard/kelas_kategori/show.blade.php --}}
@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2 style="margin: 0; font-size: 1.8rem; font-weight: 700; color: var(--text-color);">
        Lihat Kelas {{ $kategori }}
    </h2>
</div>

<!-- Breadcrumb -->
<div style="margin-bottom: 20px;">
    <a href="{{ route('kelas.kategori') }}" style="color: var(--text-light); text-decoration: none; font-size: 0.9rem;">
        <i class="fas fa-arrow-left"></i> Kembali ke Kategori Kelas
    </a>
</div>

<!-- Welcome Card -->
<div class="welcome-card" style="margin-bottom: 25px;">
    <div class="welcome-text" style="flex: 1;">
        <h2 style="font-size: 1.5rem; margin-bottom: 15px;">
            Sub Kelas <strong>{{ $kategori }}</strong>
        </h2>
        <p style="color: var(--text-light); font-size: 0.95rem; margin: 0;">
            Lihat daftar ruang kelas, wali kelas, dan jumlah siswa untuk kelas {{ $kategori }}
        </p>
    </div>
    <div class="welcome-icon">
        <i class="fas fa-chalkboard"></i>
    </div>
</div>

<!-- Table Container -->
<div class="table-container" style="background: white; border-radius: 20px; padding: 30px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color);">
    <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <h2 style="margin: 0; font-size: 1.3rem; font-weight: 700; color: var(--text-color);">
            <i class="fas fa-list" style="margin-right: 10px; color: var(--primary-color);"></i>
            Daftar Sub Kelas
            <span class="badge" style="background: var(--accent-gradient); color: white; margin-left: 10px; padding: 6px 14px; border-radius: 20px; font-size: 0.85rem;">
                {{ count($subkelas) }} Kelas
            </span>
        </h2>
    </div>

    <!-- Search Bar -->
    <div style="margin-bottom: 25px;">
        <form action="{{ route('kelas.show', $kategori) }}" method="GET" class="search-form" style="display: flex; gap: 10px; align-items: center; background: var(--bg-primary); padding: 15px; border-radius: 15px; border: 2px solid var(--border-color);">
            <i class="fas fa-search" style="color: var(--primary-color); font-size: 1.2rem;"></i>
            <input type="text" name="search" class="form-control" placeholder="🔍 Cari kelas, wali kelas, atau jumlah siswa..." value="{{ request('search') }}" style="flex: 1; border: none; background: transparent; outline: none; font-size: 0.95rem;">
            <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-search"></i> Cari
            </button>
        </form>
    </div>
    
    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; background: white; border-radius: 15px; overflow: hidden;">
            <thead style="background: var(--primary-gradient);">
                <tr>
                    <th style="padding: 15px 20px; text-align: left; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Nama Kelas</th>
                    <th style="padding: 15px 20px; text-align: left; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Wali Kelas</th>
                    <th style="padding: 15px 20px; text-align: center; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Jumlah Siswa</th>
                    <th style="padding: 15px 20px; text-align: center; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subkelas as $k)
                <tr style="border-bottom: 1px solid var(--border-color); transition: var(--transition);">
                    <td data-label="Kelas" style="padding: 18px 20px; font-weight: 600; color: var(--text-color);">
                        <i class="fas fa-door-open" style="margin-right: 8px; color: var(--primary-color);"></i>
                        {{ $k->nama_kelas }}
                    </td>
                    <td data-label="Wali Kelas" style="padding: 18px 20px; color: var(--text-light);">
                        @if($k->guru)
                            <i class="fas fa-user-tie" style="margin-right: 8px; color: var(--primary-color);"></i>
                            {{ $k->guru->nama }}
                        @else
                            <span style="color: var(--text-muted); font-style: italic;">
                                <i class="fas fa-minus-circle" style="margin-right: 5px;"></i>
                                Belum diatur
                            </span>
                        @endif
                    </td>
                    <td data-label="Jumlah Siswa" style="padding: 18px 20px; text-align: center;">
                        <span class="badge" style="background: var(--success-gradient); color: white; padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                            <i class="fas fa-users" style="margin-right: 5px;"></i>
                            {{ $k->siswas_count }} Siswa
                        </span>
                    </td>
                    <td data-label="Aksi" style="padding: 18px 20px; text-align: center;">
                        <a href="{{ route('kelas.detail', [$kategori, $k->nama_kelas]) }}" class="btn btn-info" 
                            title="Lihat Detail Siswa" 
                            style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 8px 20px;">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding: 50px 20px; text-align: center;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: var(--text-muted); opacity: 0.5;"></i>
                            <p style="margin: 0; color: var(--text-muted); font-size: 1rem; font-weight: 500;">
                                @if(request('search'))
                                    Tidak ada hasil untuk pencarian "{{ request('search') }}"
                                @else
                                    Belum ada sub kelas untuk kategori ini
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Search Form Hover */
    .search-form:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(17, 153, 142, 0.15);
    }

    /* Table Hover Effect */
    .table tbody tr:hover {
        background-color: rgba(17, 153, 142, 0.05);
        transform: scale(1.005);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .search-form {
            flex-direction: column;
            padding: 12px;
        }

        .search-form input {
            width: 100%;
            margin-bottom: 10px;
        }

        .search-form button {
            width: 100%;
        }

        .table-header {
            flex-direction: column;
            align-items: stretch !important;
        }

        .table thead {
            display: none;
        }

        .table, .table tbody, .table tr, .table td {
            display: block;
            width: 100%;
        }

        .table tr {
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0 !important;
            border-bottom: 1px solid #f0f0f0;
            text-align: right !important;
        }

        .table td:last-child {
            border-bottom: none;
        }

        .table td::before {
            content: attr(data-label);
            font-weight: 600;
            text-align: left;
            padding-right: 15px;
            color: var(--text-color);
        }

        .table td[data-label="Aksi"] {
            justify-content: center;
        }

        .table td[data-label="Aksi"]::before {
            display: none;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush