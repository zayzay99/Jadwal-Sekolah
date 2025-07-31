<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Siswa;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        Siswa::create([
            'nama' => 'Kesya Apri Pujiatmoko',
            'nis' => '12300914',
            'kelas' => 'XII PPLG',
            'email' => 'kesyapujiatmoko@gmail.com',
            'password' => Hash::make('password123')
        ]);
    }
}
