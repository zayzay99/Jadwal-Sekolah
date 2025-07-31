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
    .header span {
      color: gold;
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

     .menu-itemtop {
      display: block;
      padding: 15px;
      margin: 10px;
      background-color: ;
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

    .profile {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .profile-pic {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      background-color: #ddd;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      color: #333;
    }

    .info-box {
      background-color: #f7fbff;
      padding: 15px;
      border-radius: 10px;
      margin-top: 15px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .info {
      display: grid;
      grid-template-columns: 160px 1fr;
      row-gap: 10px;
      column-gap: 10px;
      font-size: 16px;
    }

    .info span {
      color: #333;
    }
  </style>
</head>
<body>
  <div class="header">
   <strong> Dashboard Admin </strong>
  </div>
  <div class="sidebar">
    <div class="body">
   <strong class="menu-itemtop"><u>Menu Admin </u></strong>
   </div>
    <br><br>
    <a href="{{ route('dashboard.admin.index') }}" class="menu-item">Home Admin</a>
    <a href="#jadwal" class="menu-item">Jadwal</a>
    <a href="#guru" class="menu-item">Manajemen Guru</a>
    <a href="#siswa" class="menu-item">Manajemen Siswa</a>
    <a href="#profile" class="menu-item">Profile</a>
    <a href="{{ route('logout') }}" class="menu-item" style="background:#f86a6a;color:white;">Keluar</a>
  </div>
  @csrf
  <div class="main-content">
    @yield('content')
    <h2>Selamat Datang di Ruang Khusus Admin 🎉🎉</h2>
    <p>Kelola data guru, siswa, kelas, dan jadwal dengan mudah melalui menu di samping. Semangat bekerja Admin!</p>
   
    <div class="profile">
      <div class="profile-pic">
        profile<br>150 x 150
      </div>
      <div class="info-box">
        <div class="info">
          <strong>Nama Guru</strong> <span> <strong>:</strong> {{ Auth::guard('admin')->user()->nama }}</span>
          <strong>NIP Guru</strong> <span> <strong>:</strong> {{ Auth::guard('admin')->user()->nip }}</span>
          <strong>Pengampu Bidang</strong> <span> <strong>:</strong> {{ Auth::guard('admin')->user()->pengampu }}</span>
          <strong>E-mail</strong> <span> <strong>:</strong> {{ Auth::guard('admin')->user()->email }}</span>
        </div>
      </div>
    </div>

    <div style="background:#e6f0fa;border-radius:12px;padding:18px 30px;margin-bottom:20px;box-shadow:0 1px 4px #0001;font-size:16px;color:#2d6a4f;max-width:500px;">
      <b>Info:</b> Data sekolah selalu up-to-date. Selamat bekerja dan semoga harimu menyenangkan!<br>
      <span style="font-size:13px;color:#555;">"Pendidikan adalah senjata paling ampuh untuk mengubah dunia."</span>
    </div>
  </div>
</body>
</html>
