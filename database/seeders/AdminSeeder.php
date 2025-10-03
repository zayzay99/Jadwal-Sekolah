<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Gunakan updateOrCreate untuk membuat seeder idempotent (aman dijalankan berkali-kali).
        // Metode ini akan mencari user berdasarkan email, dan akan mengupdate datanya jika sudah ada,
        // atau membuatnya jika belum ada.
        User::updateOrCreate(
            ['email' => 'kesya@admin.com'], // Kunci unik untuk mencari user
            [
                'name' => 'Kesya Apri Pujiatmoko',
                'password' => Hash::make('admin123'),
                'nip' => 'ADM000',
                'pelajaran' => 'Critical Thinking',
            ]
        );
    }
}
