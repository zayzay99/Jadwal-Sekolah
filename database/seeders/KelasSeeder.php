<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['nama_kelas' => 'X'],
            ['nama_kelas' => 'XI'],
            ['nama_kelas' => 'XII'],
        ];
        foreach ($data as $kelas) {
            Kelas::firstOrCreate($kelas);
        }
    }
}
