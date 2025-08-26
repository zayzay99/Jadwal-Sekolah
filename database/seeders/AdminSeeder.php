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
            'nama' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'nip' => 'ADM001',
            'pelajaran' => null,
        ]);
    }
}
