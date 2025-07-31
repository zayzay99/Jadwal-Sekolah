<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Guru;

class GuruSeeder extends Seeder
{
    public function run()
    {
        Guru::create([
            'nama' => 'Budi Setyawan',
            'nip' => '19876543',
            'pengampu' => 'Matematika',
            'email' => 'budisetya@gmail.com',
            'password' => Hash::make('password123')
        ]);
    }
}
