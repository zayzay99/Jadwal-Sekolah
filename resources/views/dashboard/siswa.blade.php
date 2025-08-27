<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="{{ asset('css/siswa.css') }}">
</head>
<body>
    @php $user = Auth::guard('siswa')->user(); @endphp

    <header class="header">
        Selamat Datang di Ruang Kelas <strong>{{ $user?->nama ?? '-' }}</strong>
    </header>

    <div class="container">
        <aside class="sidebar">
            <h2>Menu</h2>
            <hr>
            <a href="{{ route('jadwal.siswa') }}" class="menu-btn">Jadwal</a>
            <button class="logout-btn" onclick="window.location.href='{{ route('logout') }}'">Keluar</button>
            <div class="cs-section">
                <img src="/img/CS.svg" alt="CS" width="30">
                <span>CS</span>
            </div>
        </aside>

        <main class="main-content">
            <section class="greeting">
                <h3>Bagaimana kabarnya hari ini?</h3>
                <p>Tetap semangat ya anak-anak ...</p>
            </section>

            <section class="profile-section">
                <div class="profile-pic">profile<br>150 × 150</div>
                <div class="profile-info">
                    <p><strong>Nama:</strong> {{ $user?->nama ?? '-' }}</p>
                    <p><strong>NIS:</strong> {{ $user?->nis ?? '-' }}</p>
                    <p><strong>Kelas:</strong> {{ $user?->kelas ?? '-' }}</p>
                    <p><strong>Email:</strong> {{ $user?->email ?? '-' }}</p>
                </div>
                <button class="profile-btn">Profile</button>
            </section>

            <section class="jadwal-section">
                <h4>Jadwal Pelajaran Untuk Kelas {{ $user?->kelas ?? '-' }}</h4>
                @if(isset($jadwals) && count($jadwals) > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th>Guru</th>
                                <th>Hari</th>
                                <th>Jam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwals as $jadwal)
                            <tr>
                                <td>{{ $jadwal->mapel }}</td>
                                <td>{{ $jadwal->guru->nama }}</td>
                                <td>{{ $jadwal->hari }}</td>
                                <td>{{ $jadwal->jam }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>Belum ada jadwal untuk kelas ini.</p>
                @endif
            </section>
        </main>
    </div>
</body>
</html>
