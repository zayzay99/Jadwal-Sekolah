{{-- resources/views/jadwal/tabel_pelajaran.blade.php --}}
@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <div>
        <h2 style="font-size: 2rem; font-weight: 700; margin: 0 0 8px 0; color: var(--text-color);">
            Data Jadwal Pelajaran
        </h2>
        <p style="color: var(--text-light); margin: 0; font-size: 0.95rem;">
            Tabel jadwal mata pelajaran per hari
        </p>
    </div>
    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
        <i class="fas fa-arrow-left"></i> Kembali
    </button>
</div>

<!-- Welcome Card Style Container -->
<div class="welcome-card" style="flex-direction: column; align-items: stretch; margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="font-size: 2rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                <i class="fas fa-calendar-week"></i>
            </div>
            <h3 style="margin: 0; font-size: 1.3rem; font-weight: 600; color: var(--text-color);">
                Jadwal Pelajaran Mingguan
            </h3>
        </div>
    </div>

    <!-- Table with modern styling -->
    <div style="overflow-x: auto; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <table class="table">
            <thead>
                <tr>
                    <th style="text-align: center; min-width: 80px;">
                        <i class="fas fa-clock"></i> Jam
                    </th>
                    <th style="text-align: center; min-width: 140px;">
                        <i class="fas fa-calendar-day"></i> Senin
                    </th>
                    <th style="min-width: 140px;">
                        <i class="fas fa-calendar-day"></i> Selasa
                    </th>
                    <th style="min-width: 140px;">
                        <i class="fas fa-calendar-day"></i> Rabu
                    </th>
                    <th style="min-width: 140px;">
                        <i class="fas fa-calendar-day"></i> Kamis
                    </th>
                    <th style="min-width: 140px;">
                        <i class="fas fa-calendar-day"></i> Jumat
                    </th>
                    <th style="min-width: 140px;">
                        <i class="fas fa-calendar-day"></i> Sabtu
                    </th>
                    <th style="min-width: 140px;">
                        <i class="fas fa-calendar-day"></i> Minggu
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tabeljs as $index => $tabelj)
                <tr>
                    <td style="text-align: center; font-weight: 600; color: var(--primary-color);">
                        {{ $index + 1 }}
                    </td>
                    <td>
                        <div style="padding: 8px 0;">
                            <div style="font-weight: 600; color: var(--text-color); margin-bottom: 4px;">
                                {{ $tabelj->kode_pelajaran ?? '-' }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ $tabelj->nama_pelajaran ?? '-' }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="padding: 8px 0;">
                            <div style="font-weight: 600; color: var(--text-color); margin-bottom: 4px;">
                                {{ $tabelj->kode_pelajaran ?? '-' }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ $tabelj->nama_pelajaran ?? '-' }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="padding: 8px 0;">
                            <div style="font-weight: 600; color: var(--text-color); margin-bottom: 4px;">
                                {{ $tabelj->kode_pelajaran ?? '-' }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ $tabelj->nama_pelajaran ?? '-' }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="padding: 8px 0;">
                            <div style="font-weight: 600; color: var(--text-color); margin-bottom: 4px;">
                                {{ $tabelj->kode_pelajaran ?? '-' }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ $tabelj->nama_pelajaran ?? '-' }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="padding: 8px 0;">
                            <div style="font-weight: 600; color: var(--text-color); margin-bottom: 4px;">
                                {{ $tabelj->kode_pelajaran ?? '-' }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ $tabelj->nama_pelajaran ?? '-' }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="padding: 8px 0;">
                            <div style="font-weight: 600; color: var(--text-color); margin-bottom: 4px;">
                                {{ $tabelj->kode_pelajaran ?? '-' }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ $tabelj->nama_pelajaran ?? '-' }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="padding: 8px 0;">
                            <div style="font-weight: 600; color: var(--text-color); margin-bottom: 4px;">
                                {{ $tabelj->kode_pelajaran ?? '-' }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ $tabelj->nama_pelajaran ?? '-' }}
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 50px 20px;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                            <div style="font-size: 3rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; opacity: 0.5;">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <div>
                                <div style="font-size: 1.1rem; font-weight: 600; color: var(--text-color); margin-bottom: 5px;">
                                    Tidak ada jadwal pelajaran
                                </div>
                                <div style="font-size: 0.9rem; color: var(--text-muted);">
                                    Belum ada data jadwal yang tersedia saat ini
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Info Card -->
<div style="background: linear-gradient(135deg, rgba(17, 153, 142, 0.1) 0%, rgba(56, 239, 125, 0.1) 100%); padding: 20px 25px; border-radius: 15px; border-left: 4px solid var(--primary-color); margin-top: 20px;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-info-circle" style="font-size: 1.5rem; color: var(--primary-color);"></i>
        <div>
            <div style="font-weight: 600; color: var(--text-color); margin-bottom: 4px;">
                Informasi Jadwal
            </div>
            <div style="font-size: 0.9rem; color: var(--text-light);">
                Jadwal pelajaran ditampilkan per jam untuk setiap hari dalam seminggu. Data dapat berubah sesuai kebijakan sekolah.
            </div>
        </div>
    </div>
</div>

@endsection