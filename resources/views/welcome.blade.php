<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/png" href="{{ asset('img/Tut Wuri Handayani.jpeg') }}">
  <title>Login Page</title>
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
  </style>
</head>
<body>

  <div class="circle"></div>

  <div class="container">
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
    Jika ada kendala silahkan hubungi <a href="#"><strong>Customer Service</strong></a><br>
   <strong> &copy; 2025. Semua hak dilindungi.</strong>
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
  </script>

</body>
</html>
