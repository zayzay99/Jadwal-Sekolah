<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Siswa - Klipaa Solusi Indonesia</title>
    <link rel="stylesheet" href="{{ asset('css/siswa.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="icon" type="image/png" href="{{ asset('img/Klipaa Original.png') }}">
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
            @php
                $mailToBody = "Nama Pengguna: {$user?->nama}\n" .
                              "NIS Pengguna: {$user?->nis}\n" .
                              "Email Pengguna: {$user?->email}\n\n" .
                              "Sebutkan masalah dan lampirkan foto (jika ada):";
            @endphp
            <a href="{{ route('siswa.jadwal') }}" class="menu-btn">Jadwal</a>
            <a href="mailto:kesyapujiatmoko@gmail.com?subject=Laporan Masalah Pengguna (Siswa)&body={{ rawurlencode($mailToBody) }}" class="menu-btn" title="Hubungi Customer Service">
                <img src="/img/CS.svg" alt="Customer Service" width="24">
                <span>Bantuan</span>
            </a>

            <button class="logout-btn" data-url="{{ route('logout') }}" onclick="showLogoutConfirmation(event)">Keluar</button>
        </aside>

        <main class="main-content">
            <section class="greeting">
                <h3>Bagaimana kabarnya hari ini?</h3>
                <p>Tetap semangat ya anak-anak ...</p>
            </section>

            <section class="profile-section">
                <div class="profile-pic-container" onclick="document.getElementById('profile_picture_input').click();" title="Klik untuk ganti foto">
                    <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('storage/Default-Profile.png') }}" alt="Foto Profil" class="profile-pic-image">
                </div>
                <div class="profile-info">
                    <p><strong>Nama</strong>: {{ $user?->nama ?? '-' }}</p>
                    <p><strong>NIS</strong>: {{ $user?->nis ?? '-' }}</p>
                    <p><strong>Kelas</strong>: {{ $user?->kelas->first()?->nama_kelas ?? '-' }}</p>
                    <p><strong>Email</strong>: {{ $user?->email ?? '-' }}</p>
                </div>
            </section>

            <form id="profile-pic-form" action="{{ route('siswa.profile.update') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                @csrf
                <input type="file" id="profile_picture_input" name="profile_picture" accept="image/*" onchange="document.getElementById('profile-pic-form').submit();">
            </form>

            <section class="jadwal-section">
                <div class="jadwal-header">
                    <h4>Jadwal Pelajaran Untuk Kelas {{ $user?->kelas?->first()?->nama_kelas ?? '-' }}</h4>
                    <a href="{{ route('siswa.jadwal.cetak') }}" class="print-btn" target="_blank">Cetak Jadwal</a>
                </div>
                @if(isset($jadwals) && count($jadwals) > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru</th>
                                <th>Jam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwals as $hari => $jadwalHarian)
                                @foreach($jadwalHarian as $index => $jadwal)
                                    <tr>
                                        @if($index === 0)
                                            <td rowspan="{{ count($jadwalHarian) }}">{{ $hari }}</td>
                                        @endif
                                        @if($jadwal->kategori)
                                            <td colspan="2" style="text-align: center; font-weight: bold;">{{ $jadwal->kategori->nama_kategori }}</td>
                                        @else
                                            <td>{{ $jadwal->mapel }}</td>
                                            <td>{{ $jadwal->guru ? $jadwal->guru->nama : '-' }}</td>
                                        @endif
                                        <td>{{ $jadwal->jam }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>Belum ada jadwal untuk kelas ini.</p>
                @endif
            </section>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showLogoutConfirmation(event) {
            event.preventDefault();
            let url = event.currentTarget.getAttribute('data-url');
            Swal.fire({
                title: 'Yakin akan keluar?',
                text: "Anda akan keluar dari sesi ini.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, keluar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if(session('login_success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('login_success') }}',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true
                });
            @endif

            @if(session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengunggah',
                    html: '<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Tutup'
                });
            @endif
        });

        
    </script>
</body>
</html>
