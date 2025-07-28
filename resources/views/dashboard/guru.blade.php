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
    Selamat Datang di Meja Guru <span>{{ Auth::guard('guru')->user()->nama }}</span> 🎉🎉
  </div>

  <div class="sidebar">
    <a href="#" class="menu-item">Jadwal</a>
    <a href="#" class="menu-item">Siswa</a>
    <a href="#" class="menu-item">Profile</a>
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
        <div><strong>Nama Guru:</strong> {{ Auth::guard('guru')->user()->nama }}</div>
        <div><strong>NIP Guru:</strong> {{ Auth::guard('guru')->user()->nip }}</div>

          <div><strong>Pengampu Pelajaran:</strong> {{ Auth::guard('guru')->user()->pengampu }}</div>
          <div><strong>E-mail:</strong> {{ Auth::guard('guru')->user()->email }}</div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
