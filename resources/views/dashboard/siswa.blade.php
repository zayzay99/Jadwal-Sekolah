<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Siswa - Klipaa Solusi Indonesia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/siswa.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="icon" type="image/png" href="{{ asset('img/Klipaa Original.png') }}">
</head>
<body>
    @php $user = Auth::guard('siswa')->user(); @endphp

    <div id="backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

    <header class="header flex justify-between items-center">
        <span>Selamat Datang di Ruang Kelas <strong>{{ $user?->nama ?? '-' }}</strong></span>
        <button id="menu-toggle" class="md:hidden p-2 rounded-md text-white hover:bg-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
    </header>

    <div class="container flex md:flex-row">
        <aside id="sidebar" class="sidebar fixed top-0 left-0 h-full z-50 w-3/4 max-w-xs bg-[#3b7ca5] p-5 transition-transform transform -translate-x-full md:relative md:w-[200px] md:translate-x-0 md:flex">
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

        <main class="main-content w-full">
            <section class="greeting">
                <h3>Bagaimana kabarnya hari ini?</h3>
                <p>Tetap semangat ya anak-anak ...</p>
            </section>

            <section class="profile-section flex-col sm:flex-row items-center text-center sm:text-left">
                <div class="profile-pic-container mb-4 sm:mb-0" onclick="document.getElementById('profile_picture_input').click();" title="Klik untuk ganti foto">
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
                    <div class="overflow-x-auto">
                        <table>
                            <thead>
                                <tr class="bg-[#8cb4d4] text-gray-700">
                                    <th class="py-3 px-4 text-center border">Hari</th>
                                    <th class="py-3 px-4 text-center border">Jam</th>
                                    <th class="py-3 px-4 text-center border">Mata Pelajaran</th>
                                    <th class="py-3 px-4 text-center border">Guru</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwals as $hari => $jadwalHarian)
                                    @foreach($jadwalHarian as $index => $jadwal)
                                        <tr>
                                            @if($index === 0)
                                                <td rowspan="{{ count($jadwalHarian) }}">{{ $hari }}</td>
                                            @endif
                                            <td>{{ $jadwal->jam }}</td>
                                            @if($jadwal->kategori)
                                                <td colspan="2" style="text-align: center; font-weight: bold;">{{ $jadwal->kategori->nama_kategori }}</td>
                                                @else
                                                <td>{{ $jadwal->mapel }}</td>
                                                <td>{{ $jadwal->guru ? $jadwal->guru->nama : '-' }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
            const menuToggle = document.getElementById('menu-toggle');
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('backdrop');

            function closeMenu() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }

            function openMenu() {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            }

            if (menuToggle && sidebar && backdrop) {
                menuToggle.addEventListener('click', function() {
                    if (sidebar.classList.contains('-translate-x-full')) {
                        openMenu();
                    } else {
                        closeMenu();
                    }
                });

                backdrop.addEventListener('click', function() {
                    closeMenu();
                });
            }

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