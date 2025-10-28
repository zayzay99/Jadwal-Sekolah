@extends('dashboard.admin')

@section('content')
    <!-- Header: Tahun Ajaran + Kelola T.A -->
    <div class="content-header"></div>

    <!-- Welcome Card -->
    <div class="welcome-card">
        <div class="welcome-text">
            <h2>Selamat datang di<br><strong>halaman Admin</strong></h2>
            <p>Selamat bekerja! Semoga sehat selalu</p>
        </div>
        <div class="welcome-icon">
            <i class="fas fa-book-open"></i>
        </div>
    </div>

    <!-- Stats Cards -->
    @if (isset($guruCount))
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="stat-value">{{ $guruCount }}</div>
                <div class="stat-label">GURU</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-value">{{ $siswaCount }}</div>
                <div class="stat-label">SISWA</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-building"></i></div>
                <div class="stat-value">{{ $kelasCount }}</div>
                <div class="stat-label">KELAS</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-value">{{ $jadwalCount }}</div>
                <div class="stat-label">JADWAL</div>
            </div>
        </div>
    @endif
@endsection
