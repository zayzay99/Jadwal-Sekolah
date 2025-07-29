<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Siswa</title>
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

    .sidebar h3 {
      text-align: center;
      color: #333;
      font-weight: bold;
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
  </style>
</head>
<body>
  <div class="header">
    Selamat Datang di Ruang Kelas <span>{{ Auth::guard('siswa')->user()->nama }}</span> 🎉🎉
  </div>
  
  <div class="sidebar">
    <a href="{{ route('siswa.jadwal') }}" class="menu-item">Jadwal</a>
    <a href="#" class="menu-item">Profile</a>
    <button class="logout-btn" onclick="window.location.href='{{ route('logout') }}'">Keluar</button>
    <div class="cs-btn">
      <img src="/img/CS.svg" alt="CS" width="20"> 
    </div>
  </div>

  <div class="main-content">
    <div class="greeting">
      <strong>Bagaimana kabarnya hari ini?</strong> <br> Tetap semangat ya anak-anak ...
    </div>

    <div class="profile">
      <div class="profile-pic">
        profile<br>150 x 150
      </div>

      <div class="info-box">
      <div class="info">
        <div><strong>Nama Siswa:</strong> {{ Auth::guard('siswa')->user()->nama }}</div>
        <div><strong>NIS Siswa:</strong> {{ Auth::guard('siswa')->user()->nis }}</div>
        <div><strong>Kelas:</strong> {{ Auth::guard('siswa')->user()->kelas }}</div>
        <div><strong>E-mail:</strong> {{ Auth::guard('siswa')->user()->email }}</div>
      </div>
      </div>
    </div>

    @if(isset($jadwals) && count($jadwals) > 0)
    <div style="margin-top:30px">
      <h2>Jadwal Pelajaran Kelas {{ $kelas }}</h2>
      <table border="1" cellpadding="10" style="width:100%;background:#fff;">
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
    </div>
    @else
    <div style="margin-top:30px">
      <h2>Jadwal Pelajaran Kelas {{ $kelas }}</h2>
      <p>Belum ada jadwal untuk kelas ini.</p>
    </div>
    @endif
  </div>
</body>
</html>
