@extends('dashboard.admin')
@section('content')

<script>
document.addEventListener("DOMContentLoaded", function () {
    const words = [
        "Semoga harimu menyenangkan!☀️",
        "Sekecil apa pun usahamu hari ini, tetap langkah besar menuju kemajuan🚀",
        "Kerja dengan hati, hasilnya pasti ikut berkilau✨",
        "Semoga banyak hal baik datang padamu🌻",
        "Lelah boleh, tapi jangan lupa: kamu sedang bantu banyak orang lewat pekerjaanmu🌟"
    ];

    let i = 0;
    let j = 0;
    let currentWord = "";
    let isDeleting = false;

    const typeSpeed = 90;      // kecepatan ketik
    const deleteSpeed = 60;    // kecepatan hapus
    const holdTime = 2500;     // waktu tunggu sebelum menghapus (lebih lama)
    const nextDelay = 800;     // jeda antar kata
    const el = document.getElementById("typewriter");

    function type() {
        const fullWord = words[i];

        if (isDeleting) {
            currentWord = fullWord.substring(0, j--);
        } else {
            currentWord = fullWord.substring(0, j++);
        }

        el.textContent = currentWord;

        if (!isDeleting && j === fullWord.length) {
            // Teks sudah selesai ditulis → tunggu lebih lama
            setTimeout(() => {
                isDeleting = true;
                type();
            }, holdTime);
            return;
        }

        if (isDeleting && j === 0) {
            // Setelah dihapus → lanjut ke kata berikutnya
            isDeleting = false;
            i = (i + 1) % words.length;
            setTimeout(type, nextDelay);
            return;
        }

        // Kecepatan ketik/hapus
        const currentSpeed = isDeleting ? deleteSpeed : typeSpeed;
        setTimeout(type, currentSpeed);
    }

    type();
});
</script>



<!-- Welcome Card -->
<div class="welcome-card">
    <div class="welcome-text">
        <h2>Selamat datang di<br><strong>halaman Admin</strong></h2>
        <p>Selamat bekerja! <span id="typewriter"></span></p>
    </div>
    <div class="welcome-icon">
        <i class="fas fa-book-open"></i>
    </div>
</div>

<!-- Stats Cards -->
@if (isset($guruCount))
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="stat-value">{{ $guruCount }}</div>
            <div class="stat-label">GURU</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-value">{{ $siswaCount }}</div>
            <div class="stat-label">SISWA</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-building"></i></div>
            <div class="stat-value">{{ $kelasCount }}</div>
            <div class="stat-label">KELAS</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-value">{{ $jadwalCount }}</div>
            <div class="stat-label">JADWAL</div>
        </div>
    </div>
@endif

@endsection