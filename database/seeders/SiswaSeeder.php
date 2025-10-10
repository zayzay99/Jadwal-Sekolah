<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\TahunAjaran;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        // Ambil tahun ajaran yang aktif (dibuat oleh TahunAjaranDefaultSeeder)
        $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
        if (!$activeTahunAjaran) {
            $this->command->error('Tidak ada tahun ajaran aktif. Jalankan TahunAjaranDefaultSeeder terlebih dahulu.');
            return;
        }

        // Pastikan ada guru untuk wali kelas (diperlukan oleh model Kelas)
        Guru::firstOrCreate(['nip' => '19876543'], ['nama' => 'Guru Wali Default', 'email' => 'wali@example.com', 'password' => Hash::make('password')]);
        // Pastikan ada kelas untuk di-assign
        $kelas = Kelas::firstOrCreate(['nama_kelas' => 'XII - HTL', 'tahun_ajaran_id' => $activeTahunAjaran->id]);

        $siswa = Siswa::updateOrCreate(
            ['nis' => '12300978'],
            [
                'nama' => 'Maulana Al Maliki',
                'email' => 'maulana@gmail.com',
                'password' => Hash::make('password123'),
            ]
        );

        // Saat sync, kita perlu menentukan tahun ajaran.
        $syncData = [$kelas->id => ['tahun_ajaran_id' => $activeTahunAjaran->id]];
        $siswa->kelas()->sync($syncData);
    }
}
