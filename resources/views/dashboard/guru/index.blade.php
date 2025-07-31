<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Guru</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #e6f0fa;
      margin: 0;
      padding: 0;
    }
    .header {
      background-color: #91b9e4;
      padding: 20px;
      text-align: center;
      font-size: 20px;
      color: white;
      border-radius: 0 0 20px 20px;
    }
    .header span {
      color: gold;
    }

    .sidebar {
      width: 180px;
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

    .logout-btn {
      background-color: #f86a6a;
      color: white;
      border: none;
      padding: 12px;
      margin: 20px auto;
      display: block;
      border-radius: 10px;
      width: 80%;
      cursor: pointer;
      font-weight: bold;
    }

    .cs-btn {
      background-color: #cde3f3;
      border-radius: 10px;
      margin: 10px auto;
      padding: 10px;
      width: 80%;
      text-align: center;
      cursor: pointer;
    }

    .main-content {
      margin-left: 200px;
      padding: 30px;
    }

    .greeting {
      background-color: #e8fce8;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
      font-size: 16px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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

    .info {
      flex: 1;
    }

    .info-box {
      background-color: #f7fbff;
      padding: 15px;
      border-radius: 10px;
      margin-top: 15px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .info-row {
      display: flex;
      margin-bottom: 6px;
    }

    .info-label {
      width: 160px;
      font-weight: bold;
    }

    .info-value {
      flex: 1;
    }
  </style>
</head>
<body>
  <div class="header">
    Selamat Datang di Meja Guru <span>{{ Auth::guard('guru')->user()->nama }}</span> 🎉🎉
  </div>

  <div class="sidebar">
   <h3> <strong class="menu-itemtop"><u>Menu </u></strong> </h3>
    <a href="{{ route('dashboard.guru.index') }}" class="menu-item">Halaman Guru</a>
    <a href="#Jadwal" class="menu-item">Jadwal</a>
    <a href="#Profile" class="menu-item">Profile</a>
    <a href="#Siswa" class="menu-item">Siswa</a>
    <button class="logout-btn" onclick="window.location.href='{{ route('logout') }}'">Keluar</button>
    <div class="cs-btn">
      <img src="/img/CS.svg" alt="CS" width="20"> 
    </div>
  </div>

  <div class="main-content">
    <div class="greeting">
      <strong>Bagaimana kabarnya hari ini?</strong> <br> Tetap semangat mengajar anak-anak ya...
    </div>

    <div class="profile">
      <div class="profile-pic">
        profile<br>150 x 150
      </div>
      <div class="info-box">
        <div class="info">
          <div class="info-row">
            <div class="info-label">Nama Guru</div>
            <div class="info-value"> <strong>:</strong> {{ Auth::guard('guru')->user()->nama }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">NIP Guru</div>
            <div class="info-value"> <strong>:</strong> {{ Auth::guard('guru')->user()->nip }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">Pengampu Pelajaran</div>
            <div class="info-value"> <strong>:</strong> {{ Auth::guard('guru')->user()->pengampu }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">E-mail</div>
            <div class="info-value"> <strong>:</strong> {{ Auth::guard('guru')->user()->email }}</div>
          </div>
        </div>
      </div>
    </div>

    @isset($jadwals)
    <div style="margin-top:30px">
      <h2>Jadwal Mengajar</h2>
      <table border="1" cellpadding="10" style="width:100%;background:#fff;">
        <thead>
          <tr>
            <th>Mata Pelajaran</th>
            <th>Kelas</th>
            <th>Hari</th>
            <th>Jam</th>
          </tr>
        </thead>
        <tbody>
          @foreach($jadwals as $jadwal)
          <tr>
            <td>{{ $jadwal->mapel }}</td>
            <td>{{ $jadwal->kelas ? $jadwal->kelas->nama_kelas : '-' }}</td>
            <td>{{ $jadwal->hari }}</td>
            <td>{{ $jadwal->jam }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endisset
  </div>
</body>
</html>
