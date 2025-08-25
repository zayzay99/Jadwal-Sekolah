<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Page</title>
  <style>
    * { /* Reset semua margin dan padding */
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body { /* Gaya umum untuk body */
      font-family: 'Segoe UI', sans-serif;
      background: no-repeat center center fixed;
      background-size: cover;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      position: relative;
      transition: background-image 1s ease-in-out;
    }

    .container { /* untuk membuat kontainer */
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

    .marquee { /* Gaya untuk teks berjalan */
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

    .marquee span { /* Gaya untuk teks di dalam marquee */
      display: inline-block;
      padding-left: 100%;
      animation: marqueeMove 10s linear infinite;
    }

    @keyframes marqueeMove { /* Animasi untuk teks berjalan */
      from {
        transform: translateX(0%);
      }
      to {
        transform: translateX(-100%);
      }
    }

    input[type="email"], 
    input[type="password"] { /* Gaya untuk input teks dan password */
      width: 100%;
      padding: 15px;
      margin-bottom: 20px;
      border: none;
      border-radius: 15px;
      background-color: #a7c2df;
      font-size: 1em;
    }

    button { /* Gaya untuk tombol */
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

    button:active { /* Gaya untuk tombol ketika diklik */
      transform: scale(0.95);
    }

    .circle { /* Lingkaran di kanan atas */
      width: 200px;
      height: 200px;
      background-image: url('/img/logo.png');
      background-size: cover;
      background-position: center;
      border-radius: 50%;
      position: absolute;
      top: 30px;
      right: 30px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      z-index: 2;
    }

    .footer { /* Footer di bawah */
      text-align: center;
      padding: 10px;
      font-size: 0.9em;
      color: #333;
      background-color: #cde8f6;
      border-top: 1px solid #ccc;
      margin-top: auto;
    }

    .footer a { /* Gaya untuk link di footer */
      color: #0066cc;
      text-decoration: none;
    }
  </style>
</head>
<body>

  <!-- Lingkaran kanan atas -->
  <div class="circle"></div>

  <div class="container">
    <!-- Teks berjalan -->
    <div class="marquee">
      <span>Selamat Datang!! Silakan login untuk melanjutkan.</span>
    </div>

    <!-- Form login -->
    <form method="POST" action="{{ route('login') }}">
      @csrf
      <input type="email" name="email" placeholder="Masukan E-mail" required>
      <input type="password" name="password" placeholder="Masukan Password" required>
      <button type="submit">Masuk</button>
    </form>
    @if($errors->has('login'))
    <div style="color:red">{{ $errors->first('login') }}</div>
@endif
  </div>
  

  <!-- Footer -->
  <div class="footer">
    🔒 Jika ada kendala silahkan hubungi <a href="#">Customer Service</a><br>
    &copy; 2025. Semua hak dilindungi. <br>Jangan menyalin atau menggunakan tanpa izin, ya.
  </div>

  <!-- Script Ganti Background Berdasarkan Waktu -->
  <script>
    const hour = new Date().getHours();
    let backgroundPath = '/img/Kantor Pagi.png'; // default

    if (hour >= 5 && hour < 9) {
      backgroundPath = '/img/Kantor Pagi.png'; // Pagi
    } else if (hour >= 9 && hour < 15) {
      backgroundPath = '/img/Kantor Siang.png'; // Siang
    } else if (hour >= 15 && hour < 18) {
      backgroundPath = '/img/Kantor Sore.png'; // Sore
    } else {
      backgroundPath = '/img/Kantor Malam.png'; // Malam
    }

    document.body.style.backgroundImage = `url('${backgroundPath}')`;
  </script>

</body>
</html>
