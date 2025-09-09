<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="header">
        <strong>
        Profil Guru
        </strong>
    </div>

<div class="sidebar">
    <div class="sidebar2">
    <h1 class="sidebar-title"><u>Menu Guru</u></h1>
    <a href="{{ route('guru.dashboard') }}" class="menu-item">Dashboard</a>
    <a href="#" class="menu-item">Jadwal</a>
    <a href="#" class="menu-item">Siswa</a>
    <a href="{{ route('profile.show') }}" class="menu-item">Profile</a>
    @csrf
    <button class="logout-btn" onclick="window.location.href='{{ route('logout') }}'">Keluar</button>
    @php
        $guru = Auth::guard('guru')->user();
        $mailToBody = "Nama Pengguna: {$guru->nama}\nNIP Pengguna: {$guru->nip}\nEmail Pengguna: {$guru->email}\n\nSebutkan masalah dan lampirkan foto (jika ada):";
    @endphp
    <a href="mailto:cs@example.com?subject=Laporan Masalah Pengguna&body={{ rawurlencode($mailToBody) }}" class="cs-btn" title="Hubungi Customer Service">
      <img src="/img/CS.svg" alt="CS" width="20">
    </a>
    </div>
  </div>

<div class="main-content">
    <h2 class="main-title">Tambahkan Foto Profilmu</h2>
    <div class="main-content3">
        <div class="profile">
      
<div class="profile-pic">
    <img 
        src="{{ Auth::guard('guru')->user()->profile_picture ? asset('storage/' . Auth::guard('guru')->user()->profile_picture) : asset('storage/Default-Profile.png') }}" 
        alt="Profile Picture">
    
    <form method="POST" enctype="multipart/form-data" style="display:none;">
        @csrf
        <input type="file" id="foto" name="foto" accept="image/*" onchange="this.form.submit()">
    </form>
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
        <button 
        class="edit-btn" 
        onclick="document.getElementById('foto').click();">
        Edit
    </button>
    </div>
</div>

</body>
</html>