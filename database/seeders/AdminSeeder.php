<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::create([
            'nama' => 'Kesya Kurikulum',
            'email' => 'Kurikulumkesya@example.com',
            'password' => Hash::make('kesya123'),
            'nip' => 'ADM002',
            'pengampu' => 'Kurikulum',
        ]);
    }
}
