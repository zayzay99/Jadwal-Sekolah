<?php

namespace App\Jobs;

use App\Models\Jadwal; // Asumsi Anda punya model Jadwal
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SimpanSemuaJadwal implements ShouldQueue
{
    use Queueable;

    protected $dataJadwal;

    /**
     * Create a new job instance.
     * Menerima data jadwal dari controller.
     */
    public function __construct(array $dataJadwal)
    {
        $this->dataJadwal = $dataJadwal;
    }

    /**
     * Execute the job.
     * Di sinilah logika untuk menyimpan semua jadwal akan dijalankan.
     */
    public function handle(): void
    {
        // Logika untuk menyimpan semua jadwal Anda pindahkan ke sini.
        // Ini HANYA CONTOH. Sesuaikan dengan struktur data dan logika Anda.
        Log::info('Memulai proses penyimpanan semua jadwal...');

        foreach ($this->dataJadwal as $jadwal) {
            // Contoh: Membuat record baru di database
            Jadwal::create([
                'hari' => $jadwal['hari'],
                'jam_mulai' => $jadwal['jam_mulai'],
                'jam_selesai' => $jadwal['jam_selesai'],
                'mapel_id' => $jadwal['mapel_id'],
                'guru_id' => $jadwal['guru_id'],
                'kelas_id' => $jadwal['kelas_id'],
            ]);
        }

        Log::info('Proses penyimpanan semua jadwal selesai.');
    }
}
