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
            'nama' => 'Maulana Al Maliki',
            'nis' => '12300978',
            'kelas' => 'XII - HTL ',
            'email' => 'maulana@gmail.com',
            'password' => Hash::make('password123')
        ]);
    }
}
