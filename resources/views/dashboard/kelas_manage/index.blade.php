@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2 style="margin: 0; font-size: 1.8rem; font-weight: 700; color: var(--text-color);">Manajemen Kelas</h2>
</div>

<!-- Welcome Card -->
<div class="welcome-card" style="margin-bottom: 25px;">
    <div class="welcome-text" style="flex: 1;">
        <h2 style="font-size: 1.5rem; margin-bottom: 15px;">
            Kelola <strong>Daftar Kelas</strong>
        </h2>
        <p style="color: var(--text-light); font-size: 0.95rem; margin: 0;">
            Atur ruang kelas, wali kelas, dan daftar siswa untuk setiap kelas dengan mudah.
        </p>
    </div>
    <div class="welcome-icon">
        <i class="fas fa-chalkboard-teacher"></i>
    </div>
</div>

<!-- Table Container -->
<div class="table-container" style="background: white; border-radius: 20px; padding: 30px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color);">
    
    <!-- Table Header -->
    <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <h2 style="margin: 0; font-size: 1.3rem; font-weight: 700; color: var(--text-color);">
            <i class="fas fa-list-alt" style="margin-right: 10px; color: var(--primary-color);"></i>
            Daftar Kelas
            @if(isset($kelas) && $kelas->count() > 0)
                <span class="badge" style="background: var(--accent-gradient); color: white; margin-left: 10px; padding: 6px 14px; border-radius: 20px; font-size: 0.85rem;">
                    {{ $kelas->count() }} Kelas
                </span>
            @endif
        </h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('manage.kelas.create') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 10px 24px; font-weight: 600;">
                <i class="fas fa-plus"></i> Tambah Kelas
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('manage.kelas.index') }}" class="mb-4">
        <div class="form-group" style="max-width: 300px;">
            <label for="kategori" style="font-weight: 600; color: var(--text-color); font-size: 0.9rem; margin-bottom: 8px;">Filter Tingkat Kelas</label>
            <select name="kategori" id="kategori" class="form-control" onchange="this.form.submit()" style="padding: 12px 15px; border-radius: 10px;">
                <option value="">Semua Tingkat</option>
                @foreach ($kategoriList as $kategori)
                    <option value="{{ $kategori }}" {{ $selectedKategori == $kategori ? 'selected' : '' }}>
                        Kelas {{ $kategori }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Table Responsive -->
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
                @if(isset($kelas) && $kelas->count() > 0)
                    @foreach($kelas as $k)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: var(--transition);">
                        <td style="padding: 20px; font-weight: 600; color: var(--text-color);">
                            <i class="fas fa-school" style="margin-right: 10px; color: var(--primary-color);"></i>
                            {{ $k->nama_kelas }}
                        </td>
                        <td style="padding: 20px; color: var(--text-light);">
                            {{ $k->guru->nama ?? '-' }}
                        </td>
                        <td style="padding: 20px; text-align: center;">
                            <span class="badge" style="background: var(--success-gradient); color: white; padding: 8px 18px; border-radius: 20px; font-size: 0.9rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fas fa-users"></i>
                                {{ $k->siswas->count() }}
                            </span>
                        </td>
                        <td style="padding: 20px; text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('manage.kelas.edit', $k->id) }}" class="btn btn-sm btn-info" title="Edit Kelas" style="background: var(--warning-gradient); box-shadow: none; padding: 8px 12px;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('manage.kelas.destroy', $k->id) }}" method="POST" style="display:inline; margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus Kelas" onclick="return confirm('Apakah Anda yakin ingin menghapus kelas ini? Ini tidak dapat dibatalkan.')" style="background: linear-gradient(135deg, #ff6b6b, #ff8e8e); box-shadow: none; padding: 8px 12px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="padding: 50px 20px; text-align: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                                <i class="fas fa-inbox" style="font-size: 3rem; color: var(--text-muted); opacity: 0.5;"></i>
                                <p style="margin: 0; color: var(--text-muted); font-size: 1rem; font-weight: 500;">
                                    Belum ada data kelas yang tersedia.
                                </p>
                                <p style="margin: 0; color: var(--text-muted); font-size: 0.9rem;">Coba filter tingkat kelas yang lain atau tambahkan kelas baru.</p>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table tbody tr:hover {
        background-color: rgba(17, 153, 142, 0.05);
        transform: scale(1.005);
    }
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endpush