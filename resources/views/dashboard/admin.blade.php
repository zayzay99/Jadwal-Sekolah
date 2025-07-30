<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #e6f0fa;
      margin: 0;
      padding: 0;
    }
    .header {
      background-color: #2d6a4f;
      padding: 20px;
      text-align: center;
      font-size: 22px;
      color: white;
      border-radius: 0 0 20px 20px;
    }
    .sidebar {
      width: 200px;
      background-color: #f4f9fd;
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      padding-top: 80px;
      border-right: 1px solid #ccc;
    }
    .menu-item {
      display: block;
      padding: 15px;
      margin: 10px;
      background-color: #a9c7e3;
      text-align: center;
      border-radius: 10px;
      color: black;
      text-decoration: none;
      font-weight: bold;
    }
    .menu-item:hover {
      background-color: #87aed2;
    }
    .main-content {
      margin-left: 220px;
      padding: 30px;
    }
  </style>
</head>
<body>
  <div class="header">
    Dashboard Admin
  </div>
  <div class="sidebar">
    <a href="{{ route('admin.dashboard') }}" class="menu-item">Home Admin</a>
   <a href="{{ route('jadwal.pilihKelasLihat') }}" class="menu-item">Jadwal</a>
       <a href="{{ route('jadwal.pilihKelas') }}" class="menu-item">Manajemen Jadwal</a> 
    <a href="{{ route('manage.guru.index') }}" class="menu-item">Manajemen Guru</a>
    <a href="{{ route('manage.siswa.index') }}" class="menu-item">Manajemen Siswa</a>
    <a href="{{ route('logout') }}" class="menu-item" style="background:#f86a6a;color:white;">Logout</a>
  </div>
  <div class="main-content">
    @yield('content')
    <h2>Selamat Datang di Dashboard Admin</h2>
    <p>Kelola data guru, siswa, kelas, dan jadwal dengan mudah melalui menu di samping. Semangat bekerja, Admin!</p>
    @if(isset($guruCount))
    <div style="display:flex;gap:20px;margin-bottom:30px;">
      <div style="background:#fff;border-radius:12px;padding:20px 30px;box-shadow:0 2px 8px #0001;text-align:center;min-width:120px;">
        <div style="font-size:32px;font-weight:bold;color:#2d6a4f;">{{ $guruCount }}</div>
        <div style="font-size:15px;">Guru</div>
      </div>
      <div style="background:#fff;border-radius:12px;padding:20px 30px;box-shadow:0 2px 8px #0001;text-align:center;min-width:120px;">
        <div style="font-size:32px;font-weight:bold;color:#2d6a4f;">{{ $siswaCount }}</div>
        <div style="font-size:15px;">Siswa</div>
      </div>
      <div style="background:#fff;border-radius:12px;padding:20px 30px;box-shadow:0 2px 8px #0001;text-align:center;min-width:120px;">
        <div style="font-size:32px;font-weight:bold;color:#2d6a4f;">{{ $kelasCount }}</div>
        <div style="font-size:15px;">Kelas</div>
      </div>
      <div style="background:#fff;border-radius:12px;padding:20px 30px;box-shadow:0 2px 8px #0001;text-align:center;min-width:120px;">
        <div style="font-size:32px;font-weight:bold;color:#2d6a4f;">{{ $jadwalCount }}</div>
        <div style="font-size:15px;">Jadwal</div>
      </div>
    </div>
    @endif
    <div style="background:#e6f0fa;border-radius:12px;padding:18px 30px;margin-bottom:20px;box-shadow:0 1px 4px #0001;font-size:16px;color:#2d6a4f;max-width:500px;">
      <b>Info:</b> Data sekolah selalu up-to-date. Selamat bekerja dan semoga harimu menyenangkan!<br>
      <span style="font-size:13px;color:#555;">"Pendidikan adalah senjata paling ampuh untuk mengubah dunia."</span>
    </div>
  </div>
</body>
</html>
