<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::where('email', 'admin@example.com')->delete();

        User::create([
            'name' => 'Kesya Apri Pujiatmoko',
            'email' => 'kesya@admin.com',
            'password' => Hash::make('admin123'),
            'nip' => 'ADM000',
            'pelajaran' => 'Critical Thinking', // Bisa di isi sesuai kebutuhan dengan 'Mapel' atau null jika kosong
        ]);
    }
}
