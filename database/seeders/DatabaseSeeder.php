<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{
    $this->call([
        TahunAjaranDefaultSeeder::class, // Pindahkan ke atas agar tahun ajaran dibuat lebih dulu
        GuruSeeder::class,
        AdminSeeder::class,
        SiswaSeeder::class, // Pastikan seeder ini juga menggunakan firstOrCreate jika perlu
    ]);
}
}
