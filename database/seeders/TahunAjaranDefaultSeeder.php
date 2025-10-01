<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TahunAjaran;

class TahunAjaranDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat tahun ajaran default 2025/2026 jika belum ada
        // Ini akan menjadi data dasar/lama Anda
        TahunAjaran::firstOrCreate(
            [
                'tahun_ajaran' => '2025/2026',  
                'semester' => 'Ganjil'
            ],
            [
                'is_active' => true // Set aktif saat pertama kali dibuat
            ]
        );
    }
}