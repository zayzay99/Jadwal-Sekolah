<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Guru;

class GuruSeeder extends Seeder
{
    public function run()
    {
            // Hapus data guru dengan NIP yang sama jika sudah ada
    Guru::where('nip', '19876543')->delete();
        Guru::create([
            'nama' => 'Pak Budi',
            'nip' => '19876543',
            'pengampu' => 'Matematika',
            'email' => 'guru@example.com',
            'password' => Hash::make('password123')
        ]);
    }
}
