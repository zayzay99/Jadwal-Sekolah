<!DOCTYPE html>
<html lang="id">
  
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/png" href="{{ asset('img/Klipaa Original.png') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="/css/login.css">
  <title>Login Page - Klipaa Solusi Indonesia</title>
</head>

<body>

  <div class="container">
    <div class="circle"></div>
    <div class="marquee">
      <span>Selamat Datang!! Silhakan login untuk melanjutkan.</span>
    </div>

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <input type="text" name="nis" placeholder="Masukan NIS/NIP" required>
      <input type="password" name="password" placeholder="Masukan Password" required>
      <button type="submit">Masuk</button>
    </form>
  </div>

  <div class="footer">
    <strong>Klipaa Students</strong> <br>
    Jika ada kendala, hubungi
    <a href="mailto:kesyapujiatmoko@gmail.com?subject=Laporan Masalah Login&body=Nama/NIS/NIP: [isi di sini]%0D%0A%0D%0ADeskripsi masalah: [isi di sini]" title="Hubungi Customer Service"><strong>Customer Service</strong></a><br>
    <strong>&copy; 2025. Semua hak dilindungi.</strong>
  </div>

  <div class="info-toggle" id="info-toggle" title="Tentang Kami">
    <i class="fas fa-info-circle"></i>
  </div>

  <div class="info-popup" id="info-popup">
    <h4>Dibuat oleh Tim PKL SMK Wikrama 1 Garut dan SMKN 1 Garut:</h4>
    <ul style="list-style:none; text-align:left; line-height:1.8; padding:0;">
      <li><strong style="color:#1c3d2e;">Kesya Apri Pujiatmoko</strong><br><small>UI/UX & Backend</small></li>
      <li><strong style="color:#1c3d2e;">Muhammad Zayyidan Al Kautsar</strong><br><small>Backend & System Analyst</small></li>
      <li><strong style="color:#1c3d2e;">Alkayisa Nurhasya Lillah</strong><br><small>UI/UX & Frontend</small></li>
    </ul>
  </div>

  <script>
    // Ganti background berdasarkan waktu
    const hour = new Date().getHours();
    let backgroundPath = '/img/Kantor Pagi.png';
    if (hour >= 4 && hour < 8) backgroundPath = '/img/Kantor Pagi.png';
    else if (hour >= 8 && hour < 15) backgroundPath = '/img/Kantor Siang.png';
    else if (hour >= 15 && hour < 18) backgroundPath = '/img/Kantor Sore.png';
    else backgroundPath = '/img/Kantor Malam.png';
    document.body.style.backgroundImage = `url('${backgroundPath}')`;

    // 🔔 ALERT GAGAL LOGIN
    @if($errors->has('login'))
      Swal.fire({
        icon: 'error',
        title: 'Login Gagal',
        text: '{{ $errors->first("login") }}',
        background: '#fefefe',
        color: '#2d3436',
        confirmButtonColor: '#3085d6',
        showClass: { popup: 'animate__animated animate__shakeX' },
        hideClass: { popup: 'animate__animated animate__fadeOut' }
      });
    @endif

    // ✅ ALERT LOGOUT BERHASIL
    @if(session('logout_success'))
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '{{ session("logout_success") }}',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      });
    @endif

    // Popup Info tim
    const infoToggle = document.getElementById('info-toggle');
    const infoPopup = document.getElementById('info-popup');
    infoToggle.addEventListener('click', e => {
      e.stopPropagation();
      infoPopup.classList.toggle('show');
    });
    window.addEventListener('click', e => {
      if (infoPopup.classList.contains('show') && !infoPopup.contains(e.target)) {
        infoPopup.classList.remove('show');
      }
    });
  </script>
</body>
</html>
