<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/Klipaa Original.png') }}">
  <title>Login Page - Klipaa Solusi Indonesia</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: no-repeat center center fixed;
      background-size: cover;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      position: relative;
      transition: background-image 1s ease-in-out;
    }

    .container {
      background-color: rgba(240, 248, 255, 0.26);
      padding: 40px;
      margin: auto;
      width: 400px;
      border-radius: 20px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
      text-align: center;
      position: relative;
      z-index: 1;
    }

    .marquee {
      overflow: hidden;
      white-space: nowrap;
      box-sizing: border-box;
      background-color: #73aace;
      color: black;
      padding: 12px;
      border-radius: 15px;
      margin-bottom: 30px;
      font-weight: bold;
    }

    .marquee span {
      display: inline-block;
      padding-left: 100%;
      animation: marqueeMove 10s linear infinite;
    }

    @keyframes marqueeMove {
      from {
        transform: translateX(0%);
      }
      to {
        transform: translateX(-100%);
      }
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 15px;
      margin-bottom: 20px;
      border: none;
      border-radius: 15px;
      background-color: #a7c2df;
      font-size: 1em;
    }

    button {
      padding: 10px 20px;
      background-color: #a7d6f5;
      color: black;
      font-weight: bold;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      font-size: 1em;
      transition: transform 0.1s ease;
    }

    button:active {
      transform: scale(0.95);
    }

    .circle {
      width: 250px;
      height: 100px;
      background-image: url('/img/klipaa_Student.png');
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
      margin: 0 auto 20px;
    }

    .footer {
      text-align: center;
      padding: 10px;
      font-size: 0.9em;
      color: #333;
      background-color: #cde8f6;
      border-top: 1px solid #ccc;
      margin-top: auto;
    }

    .footer a {
      color: #0066cc;
      text-decoration: none;
    }

    /* Credits/Info Button */
    .info-toggle {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 50px;
      height: 50px;
      background-color: #2d6a4f; /* Menggunakan warna tema dari dashboard */
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      cursor: pointer;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
      transition: transform 0.3s ease, background-color 0.3s ease;
      z-index: 1000;
    }

    .info-toggle:hover {
      transform: scale(1.1);
      background-color: #3a7d5d; /* Warna lebih terang saat hover */
    }

    /* Popup Box */
    .info-popup {
      position: fixed;
      bottom: 80px; /* Posisi di atas tombol toggle */
      right: 20px;
      width: 320px;
      background-color: white;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      padding: 20px;
      z-index: 999;
      opacity: 0;
      visibility: hidden;
      transform: translateY(10px);
      transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
      border: 1px solid #eee;
    }

    .info-popup.show {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .info-popup h4 {
        margin-bottom: 15px;
        color: #2d6a4f;
    }

    @media (max-width: 768px) { /* For tablets */
      .container {
        width: 90%;
        max-width: 400px;
        padding: 30px;
        background-color: rgba(255, 255, 255, 0.85); /* More opaque background */
      }
    }

    @media (max-width: 480px) { /* For phones */
      .container {
        padding: 20px;
      }
      .circle {
        width: 180px;
        height: 72px;
      }

      input[type="text"],
      input[type="password"] {
        padding: 12px;
        font-size: 0.9em;
      }

      button {
        padding: 10px 15px;
        font-size: 0.9em;
      }

      .info-popup {
          width: 90%;
          left: 5%;
          right: 5%;
          bottom: 80px;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="circle"></div>
    <div class="marquee">
      <span>Selamat Datang!! Silakan login untuk melanjutkan.</span>
    </div>

    <!-- Form login -->
    <form method="POST" action="{{ route('login') }}">
      @csrf
      <!-- Ganti name dari email ke nis -->
      <input type="text" name="nis" placeholder="Masukan NIS/NIP" required>
      <input type="password" name="password" placeholder="Masukan Password" required>
      <button type="submit">Masuk</button>
    </form>

  </div>

  <div class="footer">
    <strong>Klipaa Students</strong> <br>
    @php
        $mailToBody = "Nama/NIS/NIP Pengguna: [Silakan isi]\n\n" .
                      "Sebutkan masalah dan lampirkan foto (jika ada):";
    @endphp
    Jika ada kendala silahkan hubungi <a href="mailto:kesyapujiatmoko@gmail.com?subject=Laporan Masalah dari Halaman Login&body={{ rawurlencode($mailToBody) }}" title="Hubungi Customer Service"><strong>Customer Service</strong></a><br>
    <strong> &copy; 2025. Semua hak dilindungi.</strong>
  </div>

  <!-- Tombol Toggle Opsi -->
  <div class="info-toggle" id="info-toggle" title="Tentang Kami">
      <i class="fas fa-info-circle"></i>
  </div>

  <!-- Popup Opsi/Biodata -->
  <div class="info-popup" id="info-popup">
      {{-- <h4>Dibuat oleh Tim Prakerin SMK Wikrama 1 Garut & SMKN 1 Garut:</h4> --}}
      <h4>Dibuat oleh Tim PKL SMK Wikrama 1 Garut dan SMKN 1 Garut:</h4>
      <ul style="list-style: none; text-align: left; line-height: 1.8; padding: 0;">
          <li><strong style="color: #1c3d2e;">Kesya Apri Pujiatmoko</strong><br><small>UI/UX & Backend </small></li>
          <li><strong style="color: #1c3d2e;">Muhammad Zayyidan Al Kautsar</strong><br><small>Backend  & System Analyst</small></li>
          <li><strong style="color: #1c3d2e;">Alkayisa Nurhasya Lillah</strong><br><small>UI/UX & Frontend </small></li>
      </ul>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const hour = new Date().getHours();
    let backgroundPath = '/img/Kantor Pagi.png';

    if (hour >= 4 && hour < 8) {
      backgroundPath = '/img/Kantor Pagi.png';
    } else if (hour >= 8 && hour < 15) {
      backgroundPath = '/img/Kantor Siang.png';
    } else if (hour >= 15 && hour < 18) {
      backgroundPath = '/img/Kantor Sore.png';
    } else {
      backgroundPath = '/img/Kantor Malam.png';
    }

    document.body.style.backgroundImage = `url('${backgroundPath}')`;

    @if($errors->has('login'))
      Swal.fire({
        icon: 'error',
        title: 'Login Gagal',
        text: '{{ $errors->first("login") }}',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false
      });
    @endif

    @if(session('logout_success'))
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
        Toast.fire({ icon: 'success', title: '{{ session("logout_success") }}' });
    @endif

    // Logika untuk Popup Info
    const infoToggle = document.getElementById('info-toggle');
    const infoPopup = document.getElementById('info-popup');

    infoToggle.addEventListener('click', (event) => {
        // Mencegah event klik menyebar ke window
        event.stopPropagation();
        infoPopup.classList.toggle('show');
    });

    // Menutup popup jika pengguna mengklik di luar area popup
    window.addEventListener('click', (event) => {
        if (infoPopup.classList.contains('show') && !infoPopup.contains(event.target)) {
            infoPopup.classList.remove('show');
        }
    });
  </script>

</body>
</html>