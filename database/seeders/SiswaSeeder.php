<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Guru;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        // Pastikan ada guru untuk wali kelas (diperlukan oleh model Kelas)
        $guru = Guru::firstOrCreate(['nip' => '19876543']);
        // Pastikan ada kelas untuk di-assign
        $kelas = Kelas::firstOrCreate(['nama_kelas' => 'XII - HTL'], ['guru_id' => $guru->id]);

        $siswa = Siswa::updateOrCreate(
            ['nis' => '12300978'],
            [
                'nama' => 'Maulana Al Maliki',
                'email' => 'maulana@gmail.com',
                'password' => Hash::make('password123'),
            ]
        );

        $siswa->kelas()->sync($kelas->id);
    }
}
