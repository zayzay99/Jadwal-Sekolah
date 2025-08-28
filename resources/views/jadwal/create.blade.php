{{-- filepath: resources/views/jadwal/create.blade.php --}}
@extends('dashboard.admin')
@section('content')
<div class="content-header">
    <h2>Tambah Jadwal untuk Kelas {{ $kelas->nama_kelas }}</h2>
</div>

<div class="form-container">
    <form action="{{ route('jadwal.store') }}" method="POST">
        @csrf
        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">

        <div class="form-group">
        <label>Guru</label> 
        <select name="guru_id" id="guru_id" class="form-control" required>
            <option value="">-- Pilih Guru --</option>
            @foreach($gurus as $g)
                <option value="{{ $g->id }}" data-pengampu="{{ $g->pengampu }}">{{ $g->nama }}</option>
            @endforeach
        </select>
        </div>

        <div class="form-group">
        <label>Mata Pelajaran</label>
        <input type="text" name="mapel" id="mapel" class="form-control" placeholder="Mata pelajaran akan terisi otomatis" required readonly>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Hari</label>
                <select name="hari" id="hari" class="form-control" required>
                    <option value="">-- Pilih Hari --</option>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                    <option value="Sabtu">Sabtu</option>
                </select>
            </div>

            <div class="form-group">
        <label>Jam</label>
        <input type="text" name="jam" class="form-control" placeholder="Misal: 08:00-09:00" required>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('guru_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var mapel = selectedOption.getAttribute('data-pengampu');
        document.getElementById('mapel').value = mapel;
    });

    // Script untuk validasi jam guru secara dinamis
    const guruSchedules = @json($guruSchedules);
    const guruSelect = document.getElementById('guru_id');
    const hariSelect = document.getElementById('hari');
    const guruOptions = Array.from(guruSelect.options);

    function updateGuruAvailability() {
        const selectedDay = hariSelect.value;
        const selectedGuruId = guruSelect.value;

        guruOptions.forEach(option => {
            // Lewati placeholder "-- Pilih Guru --"
            if (!option.value) return;

            const guruId = option.value;
            const schedule = guruSchedules[guruId];
            let isAvailable = true;
            let reason = '';

            // Kembalikan teks option ke nama asli guru (hapus peringatan sebelumnya)
            option.textContent = option.textContent.split(' (')[0];

            if (schedule) {
                // 1. Cek batas mingguan (48 jam) - dievaluasi selalu
                if (schedule.weekly_total >= 48) {
                    isAvailable = false;
                    reason = `(Penuh Mingguan: ${schedule.weekly_total}/48 jam)`;
                }
                // 2. Cek batas harian (8 jam) - hanya jika hari sudah dipilih
                else if (selectedDay && schedule.daily_counts[selectedDay] >= 8) {
                    isAvailable = false;
                    reason = `(Penuh Hari Ini: ${schedule.daily_counts[selectedDay]}/8 jam)`;
                }
            }

            // Nonaktifkan option jika tidak tersedia dan tambahkan alasannya
            option.disabled = !isAvailable;
            if (!isAvailable) {
                option.textContent += ` ${reason}`;
            }
        });

        // Jika guru yang sebelumnya dipilih menjadi tidak tersedia setelah filter, reset pilihan
        if (selectedGuruId && guruSelect.querySelector(`option[value="${selectedGuruId}"]`).disabled) {
            guruSelect.value = ""; // Reset ke "-- Pilih Guru --"
            document.getElementById('mapel').value = ''; // Kosongkan mapel
        }
    }

    // Jalankan fungsi saat dropdown 'Hari' berubah
    hariSelect.addEventListener('change', updateGuruAvailability);

    // Jalankan fungsi saat halaman pertama kali dimuat untuk memeriksa batas mingguan
    document.addEventListener('DOMContentLoaded', updateGuruAvailability);
</script>
@endsection
