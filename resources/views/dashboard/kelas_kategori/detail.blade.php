{{-- filepath: resources/views/dashboard/kelas_kategori/detail.blade.php --}}
@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2 style="margin: 0; font-size: 1.8rem; font-weight: 700; color: var(--text-color);">
        Daftar Siswa Kelas {{ $kelasObj->nama_kelas }}
    </h2>
</div>

<!-- Breadcrumb -->
<div style="margin-bottom: 20px;">
    <a href="{{ route('kelas.show', $kategori) }}" style="color: var(--text-light); text-decoration: none; font-size: 0.9rem;">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kelas {{ $kategori }}
    </a>
</div>

<!-- Welcome Card -->
<div class="welcome-card" style="margin-bottom: 25px;">
    <div class="welcome-text" style="flex: 1;">
        <h2 style="font-size: 1.5rem; margin-bottom: 15px;">
            Detail Siswa <strong>{{ $kelasObj->nama_kelas }}</strong>
        </h2>
        <p style="color: var(--text-light); font-size: 0.95rem; margin: 0;">
            @if($kelasObj->guru)
                <i class="fas fa-user-tie" style="color: var(--primary-color); margin-right: 5px;"></i>
                Wali Kelas: <strong>{{ $kelasObj->guru->nama }}</strong>
            @else
                <i class="fas fa-info-circle" style="color: var(--text-muted); margin-right: 5px;"></i>
                Wali kelas belum diatur
            @endif
        </p>
    </div>
    <div class="welcome-icon">
        <i class="fas fa-user-graduate"></i>
    </div>
</div>

<!-- Table Container -->
<div class="table-container" style="background: white; border-radius: 20px; padding: 30px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color);">
    <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <h2 style="margin: 0; font-size: 1.3rem; font-weight: 700; color: var(--text-color);">
            <i class="fas fa-users" style="margin-right: 10px; color: var(--primary-color);"></i>
            Daftar Siswa
            <span class="badge" style="background: var(--accent-gradient); color: white; margin-left: 10px; padding: 6px 14px; border-radius: 20px; font-size: 0.85rem;">
                {{ count($siswas) }} Siswa
            </span>
        </h2>
    </div>

    <!-- Search Bar -->
    <div style="margin-bottom: 25px;">
        <form action="{{ route('kelas.detail', [$kategori, $kelasObj->nama_kelas]) }}" method="GET" class="search-form" style="display: flex; gap: 10px; align-items: center; background: var(--bg-primary); padding: 15px; border-radius: 15px; border: 2px solid var(--border-color);">
            <i class="fas fa-search" style="color: var(--primary-color); font-size: 1.2rem;"></i>
            <input type="text" name="search" class="form-control" placeholder="🔍 Cari nama, NIS, atau email siswa..." value="{{ request('search') }}" style="flex: 1; border: none; background: transparent; outline: none; font-size: 0.95rem;">
            <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-search"></i> Cari
            </button>
        </form>
    </div>
    
    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; background: white; border-radius: 15px; overflow: hidden;">
            <thead style="background: var(--primary-gradient);">
                <tr>
                    <th style="padding: 15px 20px; text-align: center; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; width: 8%;">No</th>
                    <th style="padding: 15px 20px; text-align: center; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; width: 10%;">Foto</th>
                    <th style="padding: 15px 20px; text-align: left; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Nama Lengkap</th>
                    <th style="padding: 15px 20px; text-align: center; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; width: 15%;">NIS</th>
                    <th style="padding: 15px 20px; text-align: left; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Email</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $siswa)
                <tr style="border-bottom: 1px solid var(--border-color); transition: var(--transition);">
                    <td data-label="No" style="padding: 18px 20px; text-align: center; font-weight: 600; color: var(--text-color);">
                        {{ $loop->iteration }}
                    </td>
                    <td data-label="Foto" style="padding: 18px 20px; text-align: center;">
                        <div style="display: flex; justify-content: center;">
                            <img src="{{ $siswa->profile_picture ? asset('storage/' . $siswa->profile_picture) : asset('img/Default-Profile.png') }}" 
                                alt="Foto {{ $siswa->nama }}" 
                                style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-color); box-shadow: 0 4px 10px rgba(17, 153, 142, 0.3);">
                        </div>
                    </td>
                    <td data-label="Nama" style="padding: 18px 20px; font-weight: 600; color: var(--text-color);">
                        <i class="fas fa-user" style="margin-right: 8px; color: var(--primary-color);"></i>
                        {{ $siswa->nama }}
                    </td>
                    <td data-label="NIS" style="padding: 18px 20px; text-align: center;">
                        <span class="badge" style="background: var(--success-gradient); color: white; padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                            <i class="fas fa-id-card" style="margin-right: 5px;"></i>
                            {{ $siswa->nis }}
                        </span>
                    </td>
                    <td data-label="Email" style="padding: 18px 20px; color: var(--text-light);">
                        <i class="fas fa-envelope" style="margin-right: 8px; color: var(--primary-color);"></i>
                        {{ $siswa->email }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 50px 20px; text-align: center;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                            <i class="fas fa-user-slash" style="font-size: 3rem; color: var(--text-muted); opacity: 0.5;"></i>
                            <p style="margin: 0; color: var(--text-muted); font-size: 1rem; font-weight: 500;">
                                @if(request('search'))
                                    Tidak ada siswa ditemukan untuk pencarian "{{ request('search') }}"
                                @else
                                    Belum ada siswa di kelas ini
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

<!-- Info Card -->
@if(count($siswas) > 0)
<div style="background: linear-gradient(135deg, rgba(17, 153, 142, 0.1), rgba(56, 239, 125, 0.1)); padding: 25px; border-radius: 20px; margin-top: 25px; border: 1px solid var(--border-color);">
    <div style="display: flex; align-items: start; gap: 15px;">
        <i class="fas fa-info-circle" style="color: var(--primary-color); font-size: 1.5rem; margin-top: 3px; flex-shrink: 0;"></i>
        <div style="flex: 1;">
            <h3 style="margin: 0 0 8px 0; font-size: 1rem; font-weight: 600; color: var(--text-color);">
                Informasi
            </h3>
            <p style="margin: 0; color: var(--text-light); font-size: 0.9rem; line-height: 1.6;">
                Total <strong>{{ count($siswas) }} siswa</strong> terdaftar di kelas <strong>{{ $kelasObj->nama_kelas }}</strong>. 
                Gunakan kolom pencarian untuk menemukan siswa berdasarkan nama, NIS, atau email.
            </p>
        </div>
    </div>
</div>
@endif
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

    /* Profile Picture Hover */
    .table tbody tr:hover img {
        transform: scale(1.1);
        border-color: var(--primary-dark);
    }

    .table tbody tr img {
        transition: var(--transition);
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

        .table td[data-label="Foto"] {
            justify-content: center;
        }

        .table td[data-label="Foto"]::before {
            display: none;
        }
    }
</style>
@endpush