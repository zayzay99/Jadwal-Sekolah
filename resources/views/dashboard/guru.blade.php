<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Guru</title>
  <link rel="stylesheet" href="{{ asset('css/style2.css') }}">
</head>
<body>
  <div class="header">
    Selamat Datang di Meja Guru <span>{{ Auth::guard('guru')->user()->nama }}</span> 🎉🎉
  </div>

  <div class="sidebar">
    <h1 class="sidebar-title"><strong>Menu Guru</strong></h1>
    <div class="sidebar2">
      <a href="{{ asset('dashboard/guru') }}" class="menu-item">Dashboard</a>
    <a href="#" class="menu-item">Jadwal</a>
    <a href="#" class="menu-item">Siswa</a>
    <a href="{{ asset('profile/profile') }}" class="menu-item">Profile</a>
    <button class="logout-btn" onclick="window.location.href='{{ route('logout') }}'">Keluar</button>
    <div class="cs-btn">
      <img src="/img/CS.svg" alt="CS" width="20"> 
    </div>
    </div>
  </div>

  <div class="main-content">
    <div class="main-content2">
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
